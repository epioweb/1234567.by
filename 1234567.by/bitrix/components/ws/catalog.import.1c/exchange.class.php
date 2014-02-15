<?php
class CExIBlockCMLImport extends CIBlockCMLImport
{
    function __construct()
    {
        $this->sectionIndex = 0;
        $this->catalogPriceID = 3; // тип цены, используемый при продаже
    }

    /**
     * Переопределена функция  импорта раздела
     *
     */
    function ImportSection($xml_tree_id, $IBLOCK_ID, $parent_section_id)
    {
        global $DB, $USER_FIELD_MANAGER;

        static $arUserFields;
        if($parent_section_id === false)
        {
            $arUserFields = array();
            foreach($USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$IBLOCK_ID."_SECTION") as $FIELD_ID => $arField)
            {
                if(strlen($arField["XML_ID"]) <= 0)
                    $arUserFields[$FIELD_ID] = $arField;
                else
                    $arUserFields[$arField["XML_ID"]] = $arField;
            }
        }

        $this->next_step["section_sort"] += 10;
        $arSection = array(
            "IBLOCK_SECTION_ID" => $parent_section_id,
            "ACTIVE" => "Y",
        );
        $rsS = $this->_xml_file->GetList(
            array("ID" => "asc"),
            array("PARENT_ID" => $xml_tree_id)
        );
        $XML_SECTIONS_PARENT = false;
        $XML_PROPERTIES_PARENT = false;
        $XML_SECTION_PROPERTIES = false;
        while($arS = $rsS->Fetch())
        {
            if(isset($arS["VALUE_CLOB"]))
                $arS["VALUE"] = $arS["VALUE_CLOB"];

            if($arS["NAME"]==GetMessage("IBLOCK_XML2_ID"))
                $arSection["XML_ID"] = $arS["VALUE"];
            elseif($arS["NAME"]==GetMessage("IBLOCK_XML2_NAME"))
                $arSection["NAME"] = $arS["VALUE"];
            elseif($arS["NAME"]==GetMessage("IBLOCK_XML2_SECTION_DESCRIPTION"))
            {
                $arSection["DESCRIPTION"] = $arS["VALUE"];
                $arSection["DESCRIPTION_TYPE"] = "html";
            }
            elseif($arS["NAME"]==GetMessage("IBLOCK_XML2_GROUPS"))
                $XML_SECTIONS_PARENT = $arS["ID"];
            elseif($arS["NAME"]==GetMessage("IBLOCK_XML2_PROPERTIES_VALUES"))
                $XML_PROPERTIES_PARENT = $arS["ID"];
            elseif($arS["NAME"]==GetMessage("IBLOCK_XML2_BX_SORT"))
                $arSection["SORT"] = intval($arS["VALUE"]);
            elseif($arS["NAME"]==GetMessage("IBLOCK_XML2_BX_CODE"))
                $arSection["CODE"] = $arS["VALUE"];
            elseif($arS["NAME"] == GetMessage("IBLOCK_XML2_BX_PICTURE"))
            {
                if(strlen($arS["VALUE"]) > 0)
                    $arSection["PICTURE"] = $this->MakeFileArray($arS["VALUE"]);
                else
                    $arSection["PICTURE"] = $this->MakeFileArray($this->_xml_file->GetAllChildrenArray($arS["ID"]));
            }
            elseif($arS["NAME"] == GetMessage("IBLOCK_XML2_BX_DETAIL_PICTURE"))
            {
                if(strlen($arS["VALUE"]) > 0)
                    $arSection["DETAIL_PICTURE"] = $this->MakeFileArray($arS["VALUE"]);
                else
                    $arSection["DETAIL_PICTURE"] = $this->MakeFileArray($this->_xml_file->GetAllChildrenArray($arS["ID"]));
            }
            elseif($arS["NAME"] == GetMessage("IBLOCK_XML2_BX_ACTIVE"))
                $arSection["ACTIVE"] = ($arS["VALUE"]=="true") || intval($arS["VALUE"])? "Y": "N";
            elseif($arS["NAME"] == GetMessage("IBLOCK_XML2_SECTION_PROPERTIES"))
                $XML_SECTION_PROPERTIES = $arS["ID"];
        }

        if($XML_PROPERTIES_PARENT)
        {
            $rs = $this->_xml_file->GetList(
                array("ID" => "asc"),
                array("PARENT_ID" => $XML_PROPERTIES_PARENT),
                array("ID")
            );
            while($ar = $rs->Fetch())
            {
                $arXMLProp = $this->_xml_file->GetAllChildrenArray($ar["ID"]);
                if(
                    array_key_exists(GetMessage("IBLOCK_XML2_ID"), $arXMLProp)
                    && array_key_exists($arXMLProp[GetMessage("IBLOCK_XML2_ID")], $arUserFields)
                )
                {
                    $FIELD_NAME = $arUserFields[$arXMLProp[GetMessage("IBLOCK_XML2_ID")]]["FIELD_NAME"];
                    $MULTIPLE = $arUserFields[$arXMLProp[GetMessage("IBLOCK_XML2_ID")]]["MULTIPLE"];
                    $IS_FILE = $arUserFields[$arXMLProp[GetMessage("IBLOCK_XML2_ID")]]["USER_TYPE"]["BASE_TYPE"] === "file";

                    unset($arXMLProp[GetMessage("IBLOCK_XML2_ID")]);
                    $arProp = array();
                    $i = 0;
                    foreach($arXMLProp as $value)
                    {
                        if($IS_FILE)
                            $arProp["n".($i++)] = $this->MakeFileArray($value);
                        else
                            $arProp["n".($i++)] = $value;
                    }

                    if($MULTIPLE == "N")
                        $arSection[$FIELD_NAME] = array_pop($arProp);
                    else
                        $arSection[$FIELD_NAME] = $arProp;
                }
            }
        }

        $obSection = new CIBlockSection;
        $rsSection = $obSection->GetList(array(), array("IBLOCK_ID"=>$IBLOCK_ID, "XML_ID"=>$arSection["XML_ID"]), false);
        if($arDBSection = $rsSection->Fetch())
        {

            if(!array_key_exists("CODE", $arSection) && is_array($this->translit_on_update))
                $arSection["CODE"] = CUtil::translit($arSection["NAME"], LANGUAGE_ID, $this->translit_on_update);

            $bChanged = false;
            foreach($arSection as $key=>$value)
            {
                if(is_array($arDBSection[$key]) || ($arDBSection[$key] != $value))
                {
                    $bChanged = true;
                    break;
                }
            }
            if($bChanged || strlen($arDBSection["CODE"]) <= 0)
            {
                if(strlen($arDBSection["CODE"]) > 0 && $arDBSection["CODE"] != $arSection["CODE"])
                    $checkCode = $arDBSection["CODE"];
                else
                    $checkCode = $arSection["CODE"];

                $rsCodeExists = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $arDBSection["IBLOCK_ID"], "=CODE" => $checkCode), false, array("ID"));
                if($arCodeExists = $rsCodeExists->Fetch())
                {
                    $foundSectionCode = false;
                    if($arCodeExists["ID"] != $arDBSection["ID"]) {
                        $arCodes = array();
                        $rsCodeLike = CIBlockSection::GetList(array(), array(
                            "IBLOCK_ID" => $arDBSection["IBLOCK_ID"],
                            "CODE" => $checkCode."%",
                        ), false, array("ID", "CODE"));
                        while($ar = $rsCodeLike->Fetch()) {
                            if($ar["ID"] == $arDBSection["ID"]) {
                                $arSection["CODE"] = $ar["CODE"];
                                $foundSectionCode = true;
                            }
                            else
                                $arCodes[$ar["CODE"]] = $ar["ID"];
                        }

                        if(!$foundSectionCode) {
                            $i = 1;
                            while(array_key_exists($arSection["CODE"]."_".$i, $arCodes))
                                $i++;

                            $arSection["CODE"] .= "_".$i;
                        }
                    } else {
                    $arSection["CODE"] = $checkCode;
                }
                } else {
                    $arSection["CODE"] = $checkCode;
                }

                foreach($arUserFields as $arField)
                {
                    if($arField["USER_TYPE"]["BASE_TYPE"] == "file")
                    {
                        $sectionUF = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$IBLOCK_ID."_SECTION", $arDBSection["ID"]);
                        foreach($sectionUF as $arField)
                        {
                            if(
                                $arField["USER_TYPE"]["BASE_TYPE"] == "file"
                                && isset($arSection[$arField["FIELD_NAME"]])
                            )
                            {
                                if($arField["MULTIPLE"] == "Y" && is_array($arField["VALUE"]))
                                    foreach($arField["VALUE"] as $i => $old_file_id)
                                        $arSection[$arField["FIELD_NAME"]][] = array("del"=>true,"old_id"=>$old_file_id);
                                elseif($arField["MULTIPLE"] == "N" && $arField["VALUE"] > 0)
                                    $arSection[$arField["FIELD_NAME"]]["old_id"] = $arField["VALUE"];
                            }
                        }
                        break;
                    }
                }

                // Сохраняем старые урлы товаров в текущем разделе, если изменилось название
                if(CModule::IncludeModule("oldurl") && $arDBSection["NAME"] != $arSection["NAME"])
                {
                    //AddMessage2Log("save section url".$arDBSection["NAME"]);
                    COldUrlAll::SaveSectionUrl($arDBSection["ID"]);
                }

                $res = $obSection->Update($arDBSection["ID"], $arSection);
                if(!$res)
                {
                    $this->LAST_ERROR = $obSection->LAST_ERROR;
                    return $this->LAST_ERROR;
                }
                else
                {
                    //Очищаем кеш раздела
                    if(strlen($arDBSection["CODE"]) > 0 && CModule::IncludeModule("nscache"))
                    {

                        $rsCache = CExCacheM::GetBySectionCode($arDBSection["CODE"]);
                        $cacheObj = new CExCacheM;
                        while($arCache = $rsCache->GetNext())
                        {
                            $cacheObj->Clean($arCache["CACHE_ID"]);
                        }
                    }
                }
            }
            $arSection["ID"] = $arDBSection["ID"];
        }
        else
        {
            $arSection["IBLOCK_ID"] = $IBLOCK_ID;

            if(!array_key_exists("CODE", $arSection) && is_array($this->translit_on_add))
                $arSection["CODE"] = CUtil::translit($arSection["NAME"], LANGUAGE_ID, $this->translit_on_add);

            $rsCodeExists = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $arSection["IBLOCK_ID"], "=CODE" => $arSection["CODE"]), false, array("ID"));
            if($arCodeExists = $rsCodeExists->Fetch())
            {
                $foundSectionCode = false;
                if($arCodeExists["ID"] != $arDBSection["ID"]) {
                    $arCodes = array();
                    $rsCodeLike = CIBlockSection::GetList(array(), array(
                        "IBLOCK_ID" => $arSection["IBLOCK_ID"],
                        "CODE" => $arSection["CODE"]."%",
                    ), false, array("ID", "CODE"));
                    while($ar = $rsCodeLike->Fetch()) {
                        if($ar["ID"] == $arDBSection["ID"]) {
                            $arSection["CODE"] = $ar["CODE"];
                            $foundSectionCode = true;
                        }
                        else
                            $arCodes[$ar["CODE"]] = $ar["ID"];
                    }

                    if(!$foundSectionCode) {
                        $i = 1;
                        while(array_key_exists($arSection["CODE"]."_".$i, $arCodes))
                            $i++;

                        $arSection["CODE"] .= "_".$i;
                    }
                }
            }


            if(!isset($arSection["SORT"]))
                $arSection["SORT"] = $this->next_step["section_sort"];

            $arSection["ID"] = $obSection->Add($arSection);

            if(!$arSection["ID"])
            {
                $this->LAST_ERROR = $obSection->LAST_ERROR;
                return $this->LAST_ERROR;
            }
        }

        if($XML_SECTION_PROPERTIES)
        {
            $this->ImportSectionProperties($XML_SECTION_PROPERTIES, $IBLOCK_ID, $arSection["ID"]);
        }

        if($arSection["ID"])
            $this->_xml_file->Add(array("PARENT_ID" => 0, "LEFT_MARGIN" => $arSection["ID"]));

        if($XML_SECTIONS_PARENT)
        {
            $rs = $this->_xml_file->GetList(
                array("ID" => "asc"),
                array("PARENT_ID" => $XML_SECTIONS_PARENT),
                array("ID")
            );
            while($ar = $rs->Fetch())
            {
                $result = $this->ImportSection($ar["ID"], $IBLOCK_ID, $arSection["ID"]);
                if($result !== true)
                    return $result;
            }
        }

        return true;
    }

    /**
     * пересчет счетчиков количества в разделах
     * Возвращает true в случае успеха
     *
     * @return bool
     * @author  vmakaed@gmail.com
     */

    function RecalcSectionQuantity($sectionID = 0)
    {

        if(!CModule::IncludeModule("iblock"))
            return 0;

        // ищем в разделе свойство с количеством элементов
        $arFilter = array(
            "IBLOCK_ID" => $_SESSION["BX_CML2_IMPORT"]["NS"]["IBLOCK_ID"],
            //"SECTION_ID" => $sectionID
        );
        if($sectionID > 0)
            $arFilter["SECTION_ID"] = $sectionID;

        $arSelect = array(
            "ID",
            "IBLOCK_ID",
            "NAME",
            "DEPTH_LEVEL",
            "UF_*"
        );

        $arSort = array(
            "LEFT_MARGIN" => "ASC"
        );

        $section = new CIBlockSection;
        $quantity = 0;

        $rsSection = CIBlockSection::GetList($arSort, $arFilter, false, $arSelect);
        while($arSection = $rsSection->GetNext())
        {
            $this->sectionIndex++;
            if($arSection["DEPTH_LEVEL"] == 1)
                $quantity = 0;

            $arFilter = array(
                "IBLOCK_ID" => $arSection["IBLOCK_ID"],
                "SECTION_ID" => $arSection["ID"],
                "INCLUDE_SUBSECTIONS" => "Y",
                "ACTIVE" => "Y",
                ">CATALOG_QUANTITY" => 0,
                ">CATALOG_PRICE_".$this->catalogPriceID => 0
            );
            $rsEl = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, array(), array("nTopCount"=> 1), array("ID"));
            $arFields = array(
                "IBLOCK_ID" => $arSection["IBLOCK_ID"],
                "UF_QUANTITY" => $rsEl
            );
            $uf_updated = $GLOBALS["USER_FIELD_MANAGER"]->Update("IBLOCK_".$arSection["IBLOCK_ID"]."_SECTION", $arSection["ID"], $arFields);

            $quantity += $rsEl;

            /*else
            {

                $rsChildSection = CIBlockSection::GetList(array("LEGT_MARGIN"=>"ASC"), array("IBLOCK_ID" => $this->structureIBID,"SECTION_ID"=>$arSection["ID"]));
                if($rsChildSection->SelectedRowsCount() > 0)
                {
                    $sectQuantity = $this->RecalcSectionQuantity($arSection['ID']);

                    $arFields = array(
                        "IBLOCK_ID" => $this->structureIBID,
                        "UF_QUANTITY" => $sectQuantity
                    );

                    $time = microtime();
                    $uf_updated = $GLOBALS["USER_FIELD_MANAGER"]->Update("IBLOCK_".$arSection["IBLOCK_ID"]."_SECTION", $arSection["ID"], $arFields);

                    $quantity += $sectQuantity;
                }
            }*/
        }

        return $quantity;
    }

    function ImportElement($arXMLElement, &$counter, $bWF, $arParent)
    {

        global $USER;
        $USER_ID = is_object($USER)? intval($USER->GetID()): 0;

        $arElement = array(
            "ACTIVE" => "Y",
            "TMP_ID" => $this->GetElementCRC($arXMLElement),
            "PROPERTY_VALUES" => array(),
        );
        if(isset($arXMLElement[GetMessage("IBLOCK_XML2_ID")]))
            $arElement["XML_ID"] = $arXMLElement[GetMessage("IBLOCK_XML2_ID")];

        $obElement = new CIBlockElement;
        $obElement->CancelWFSetMove();
        $rsElement = $obElement->GetList(
            Array("ID"=>"asc"),
            Array("=XML_ID" => $arElement["XML_ID"], "IBLOCK_ID" => $this->next_step["IBLOCK_ID"]),
            false, false,
            Array("ID", "TMP_ID", "ACTIVE", "CODE", "NAME")
        );

        $bMatch = false;
        if($arDBElement = $rsElement->Fetch())
            $bMatch = ($arElement["TMP_ID"] == $arDBElement["TMP_ID"]);

        if($bMatch && $this->use_crc)
        {
            //Check Active flag in XML is not set to false
            if($this->CheckIfElementIsActive($arXMLElement))
            {
                //In case element is not active in database we have to activate it and its offers
                if($arDBElement["ACTIVE"] != "Y")
                {
                    $obElement->Update($arDBElement["ID"], array("ACTIVE"=>"Y"), $bWF);
                    $this->ChangeOffersStatus($arDBElement["ID"], "Y", $bWF);
                    $counter["UPD"]++;
                }
            }
            $arElement["ID"] = $arDBElement["ID"];
        }
        else
        {
            if($arDBElement)
            {
                $rsProperties = $obElement->GetProperty($this->next_step["IBLOCK_ID"], $arDBElement["ID"], "sort", "asc");
                while($arProperty = $rsProperties->Fetch())
                {
                    if(!array_key_exists($arProperty["ID"], $arElement["PROPERTY_VALUES"]))
                        $arElement["PROPERTY_VALUES"][$arProperty["ID"]] = array(
                            "bOld" => true,
                        );

                    $arElement["PROPERTY_VALUES"][$arProperty["ID"]][$arProperty['PROPERTY_VALUE_ID']] = array(
                        "VALUE"=>$arProperty['VALUE'],
                        "DESCRIPTION"=>$arProperty["DESCRIPTION"]
                    );
                }
            }

            if($this->bCatalog && $this->next_step["bOffer"])
            {
                $p = strpos($arXMLElement[GetMessage("IBLOCK_XML2_ID")], "#");
                if($p !== false)
                    $link_xml_id = substr($arXMLElement[GetMessage("IBLOCK_XML2_ID")], 0, $p);
                else
                    $link_xml_id = $arXMLElement[GetMessage("IBLOCK_XML2_ID")];
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_LINK"]] = $this->GetElementByXML_ID($this->arProperties[$this->PROPERTY_MAP["CML2_LINK"]]["LINK_IBLOCK_ID"], $link_xml_id);
            }

            if(isset($arXMLElement[GetMessage("IBLOCK_XML2_NAME")]))
                $arElement["NAME"] = $arXMLElement[GetMessage("IBLOCK_XML2_NAME")];
            if(array_key_exists(GetMessage("IBLOCK_XML2_BX_TAGS"), $arXMLElement))
                $arElement["TAGS"] = $arXMLElement[GetMessage("IBLOCK_XML2_BX_TAGS")];
            if(array_key_exists(GetMessage("IBLOCK_XML2_DESCRIPTION"), $arXMLElement))
            {
                if(strlen($arXMLElement[GetMessage("IBLOCK_XML2_DESCRIPTION")]) > 0)
                    $arElement["DETAIL_TEXT"] = $arXMLElement[GetMessage("IBLOCK_XML2_DESCRIPTION")];
                else
                    $arElement["DETAIL_TEXT"] = "";

                if(preg_match('/<[a-zA-Z0-9]+.*?>/', $arElement["DETAIL_TEXT"]))
                    $arElement["DETAIL_TEXT_TYPE"] = "html";
                else
                    $arElement["DETAIL_TEXT_TYPE"] = "text";
            }

            if(array_key_exists(GetMessage("IBLOCK_XML2_PREVIEW_TEXT"), $arXMLElement))
            {
                if(strlen($arXMLElement[GetMessage("IBLOCK_XML2_PREVIEW_TEXT")]) > 0)
                    $arElement["PREVIEW_TEXT"] = $arXMLElement[GetMessage("IBLOCK_XML2_PREVIEW_TEXT")];
                else
                    $arElement["PREVIEW_TEXT"] = "";

                if(preg_match('/<[a-zA-Z0-9]+.*?>/', $arElement["PREVIEW_TEXT"]))
                    $arElement["PREVIEW_TEXT_TYPE"] = "html";
                else
                    $arElement["PREVIEW_TEXT_TYPE"] = "text";
            }
            if(array_key_exists(GetMessage("IBLOCK_XML2_FULL_TITLE"), $arXMLElement) && !isset($arElement["PREVIEW_TEXT"]))
            {
                if(strlen($arXMLElement[GetMessage("IBLOCK_XML2_FULL_TITLE")]) > 0)
                    $arElement["PREVIEW_TEXT"] = $arXMLElement[GetMessage("IBLOCK_XML2_FULL_TITLE")];
                else
                    $arElement["PREVIEW_TEXT"] = "";

                if(preg_match('/<[a-zA-Z0-9]+.*?>/', $arElement["PREVIEW_TEXT"]))
                    $arElement["PREVIEW_TEXT_TYPE"] = "html";
                else
                    $arElement["PREVIEW_TEXT_TYPE"] = "text";
            }
            if(array_key_exists(GetMessage("IBLOCK_XML2_BAR_CODE"), $arXMLElement))
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BAR_CODE"]] = $arXMLElement[GetMessage("IBLOCK_XML2_BAR_CODE")];
            if(array_key_exists(GetMessage("IBLOCK_XML2_BAR_CODE2"), $arXMLElement))
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BAR_CODE"]] = $arXMLElement[GetMessage("IBLOCK_XML2_BAR_CODE2")];
            if(array_key_exists(GetMessage("IBLOCK_XML2_ARTICLE"), $arXMLElement))
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_ARTICLE"]] = $arXMLElement[GetMessage("IBLOCK_XML2_ARTICLE")];

            if(array_key_exists(GetMessage("IBLOCK_XML2_PICTURE"), $arXMLElement))
            {
                $rsFiles = $this->_xml_file->GetList(
                    array("ID" => "asc"),
                    array("PARENT_ID" => $arParent["ID"], "NAME" => GetMessage("IBLOCK_XML2_PICTURE"))
                );
                $arFile = $rsFiles->Fetch();
                if($arFile)
                {
                    $description = false;
                    if(strlen($arFile["ATTRIBUTES"]))
                    {
                        $arAttributes = unserialize($arFile["ATTRIBUTES"]);
                        if(is_array($arAttributes) && array_key_exists(GetMessage("IBLOCK_XML2_DESCRIPTION"), $arAttributes))
                            $description = $arAttributes[GetMessage("IBLOCK_XML2_DESCRIPTION")];
                    }

                    if(strlen($arFile["VALUE"]) > 0)
                    {
                        $file = $this->MakeFileArray($arFile["VALUE"]);
                        $arElement["DETAIL_PICTURE"] = $this->ResizePicture($arFile["VALUE"], $this->detail);

                        if($description !== false && is_array($arElement["DETAIL_PICTURE"]))
                            $arElement["DETAIL_PICTURE"]["description"] = $description;

                        if(is_array($this->preview))
                        {
                            $arElement["PREVIEW_PICTURE"] = $this->ResizePicture($arFile["VALUE"], $this->preview);
                            if($description !== false && is_array($arElement["PREVIEW_PICTURE"]))
                                $arElement["PREVIEW_PICTURE"]["description"] = $description;
                        }
                    }
                    else
                    {
                        $arElement["DETAIL_PICTURE"] = $this->MakeFileArray($this->_xml_file->GetAllChildrenArray($arFile["ID"]));

                        if($description !== false && is_array($arElement["DETAIL_PICTURE"]))
                            $arElement["DETAIL_PICTURE"]["description"] = $description;
                    }

                    $prop_id = $this->PROPERTY_MAP["CML2_PICTURES"];
                    if($prop_id > 0)
                    {
                        $i = 1;
                        while($arFile = $rsFiles->Fetch())
                        {
                            $description = false;
                            if(strlen($arFile["ATTRIBUTES"]))
                            {
                                $arAttributes = unserialize($arFile["ATTRIBUTES"]);
                                if(is_array($arAttributes) && array_key_exists(GetMessage("IBLOCK_XML2_DESCRIPTION"), $arAttributes))
                                    $description = $arAttributes[GetMessage("IBLOCK_XML2_DESCRIPTION")];
                            }

                            if(strlen($arFile["VALUE"]) > 0)
                                $arFile = $this->ResizePicture($arFile["VALUE"], $this->detail);
                            else
                                $arFile = $this->MakeFileArray($this->_xml_file->GetAllChildrenArray($arFile["ID"]));

                            if($description !== false && is_array($arFile))
                                $arFile = array(
                                    "VALUE" => $arFile,
                                    "DESCRIPTION" => $description,
                                );
                            $arElement["PROPERTY_VALUES"][$prop_id]["n".$i] = $arFile;
                            $i++;
                        }

                        if(is_array($arElement["PROPERTY_VALUES"][$prop_id]))
                        {
                            foreach($arElement["PROPERTY_VALUES"][$prop_id] as $PROPERTY_VALUE_ID => $PROPERTY_VALUE)
                            {
                                if(!$PROPERTY_VALUE_ID)
                                    unset($arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID]);
                                elseif(substr($PROPERTY_VALUE_ID, 0, 1)!=="n")
                                    $arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID] = array(
                                        "tmp_name" => "",
                                        "del" => "Y",
                                    );
                            }
                            unset($arElement["PROPERTY_VALUES"][$prop_id]["bOld"]);
                        }
                    }
                }
            }

            if(
                array_key_exists(GetMessage("IBLOCK_XML2_FILE"), $arXMLElement)
                && strlen($this->PROPERTY_MAP["CML2_FILES"]) > 0
            )
            {
                $prop_id = $this->PROPERTY_MAP["CML2_FILES"];
                $rsFiles = $this->_xml_file->GetList(
                    array("ID" => "asc"),
                    array("PARENT_ID" => $arParent["ID"], "NAME" => GetMessage("IBLOCK_XML2_FILE"))
                );
                $i = 1;
                while($arFile = $rsFiles->Fetch())
                {

                    if(strlen($arFile["VALUE"]) > 0)
                        $file = $this->MakeFileArray($arFile["VALUE"]);
                    else
                        $file = $this->MakeFileArray($this->_xml_file->GetAllChildrenArray($arFile["ID"]));

                    $arElement["PROPERTY_VALUES"][$prop_id]["n".$i] = array(
                        "VALUE" => $file,
                        "DESCRIPTION" => $file["description"],
                    );
                    if(strlen($arFile["ATTRIBUTES"]))
                    {
                        $desc = unserialize($arFile["ATTRIBUTES"]);
                        if(is_array($desc) && array_key_exists(GetMessage("IBLOCK_XML2_DESCRIPTION"), $desc))
                            $arElement["PROPERTY_VALUES"][$prop_id]["n".$i]["DESCRIPTION"] = $desc[GetMessage("IBLOCK_XML2_DESCRIPTION")];
                    }
                    $i++;
                }

                if(is_array($arElement["PROPERTY_VALUES"][$prop_id]))
                {
                    foreach($arElement["PROPERTY_VALUES"][$prop_id] as $PROPERTY_VALUE_ID => $PROPERTY_VALUE)
                    {
                        if(!$PROPERTY_VALUE_ID)
                            unset($arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID]);
                        elseif(substr($PROPERTY_VALUE_ID, 0, 1)!=="n")
                            $arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID] = array(
                                "tmp_name" => "",
                                "del" => "Y",
                            );
                    }
                    unset($arElement["PROPERTY_VALUES"][$prop_id]["bOld"]);
                }
            }

            if(isset($arXMLElement[GetMessage("IBLOCK_XML2_GROUPS")]))
            {
                $arElement["IBLOCK_SECTION"] = array();
                foreach($arXMLElement[GetMessage("IBLOCK_XML2_GROUPS")] as $key=>$value)
                {
                    if(array_key_exists($value, $this->SECTION_MAP))
                        $arElement["IBLOCK_SECTION"][] = $this->SECTION_MAP[$value];
                }
            }
            if(isset($arXMLElement[GetMessage("IBLOCK_XML2_PRICES")]))
            {//Collect price information for future use
                $arElement["PRICES"] = array();
                foreach($arXMLElement[GetMessage("IBLOCK_XML2_PRICES")] as $key=>$price)
                {
                    if(isset($price[GetMessage("IBLOCK_XML2_PRICE_TYPE_ID")]) && array_key_exists($price[GetMessage("IBLOCK_XML2_PRICE_TYPE_ID")], $this->PRICES_MAP))
                    {
                        $price["PRICE"] = $this->PRICES_MAP[$price[GetMessage("IBLOCK_XML2_PRICE_TYPE_ID")]];
                        $arElement["PRICES"][] = $price;
                    }
                }

                $arElement["DISCOUNTS"] = array();
                if(isset($arXMLElement[GetMessage("IBLOCK_XML2_DISCOUNTS")]))
                {
                    foreach($arXMLElement[GetMessage("IBLOCK_XML2_DISCOUNTS")] as $key=>$discount)
                    {
                        if(
                            isset($discount[GetMessage("IBLOCK_XML2_DISCOUNT_CONDITION")])
                            && $discount[GetMessage("IBLOCK_XML2_DISCOUNT_CONDITION")]===GetMessage("IBLOCK_XML2_DISCOUNT_COND_VOLUME")
                        )
                        {
                            $discount_value = $this->ToInt($discount[GetMessage("IBLOCK_XML2_DISCOUNT_COND_VALUE")]);
                            $discount_percent = $this->ToFloat($discount[GetMessage("IBLOCK_XML2_DISCOUNT_COND_PERCENT")]);
                            if($discount_value > 0 && $discount_percent > 0)
                                $arElement["DISCOUNTS"][$discount_value] = $discount_percent;
                        }
                    }
                }
            }



            if(isset($arXMLElement[GetMessage("IBLOCK_XML2_AMOUNT")]))
                $arElement["QUANTITY"] = $this->ToFloat($arXMLElement[GetMessage("IBLOCK_XML2_AMOUNT")]);
            else
                $arElement["QUANTITY"] = 0;

            if(isset($arXMLElement[GetMessage("IBLOCK_XML2_ITEM_ATTRIBUTES")]))
            {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_ATTRIBUTES"]] = array();
                $i = 0;
                foreach($arXMLElement[GetMessage("IBLOCK_XML2_ITEM_ATTRIBUTES")] as $key => $value)
                {
                    $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_ATTRIBUTES"]]["n".$i] = array(
                        "VALUE" => $value[GetMessage("IBLOCK_XML2_VALUE")],
                        "DESCRIPTION" => $value[GetMessage("IBLOCK_XML2_NAME")],
                    );
                    $i++;
                }
            }
            if(isset($arXMLElement[GetMessage("IBLOCK_XML2_TRAITS_VALUES")]))
            {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TRAITS"]] = array();
                $i = 0;
                foreach($arXMLElement[GetMessage("IBLOCK_XML2_TRAITS_VALUES")] as $key => $value)
                {
                    if(
                        !array_key_exists("PREVIEW_TEXT", $arElement)
                        && $value[GetMessage("IBLOCK_XML2_NAME")] == GetMessage("IBLOCK_XML2_FULL_TITLE2")
                    )
                    {
                        $arElement["PREVIEW_TEXT"] = $value[GetMessage("IBLOCK_XML2_VALUE")];
                        if(strpos($arElement["PREVIEW_TEXT"], "<")!==false)
                            $arElement["PREVIEW_TEXT_TYPE"] = "html";
                        else
                            $arElement["PREVIEW_TEXT_TYPE"] = "text";
                    }
                    elseif(
                        $value[GetMessage("IBLOCK_XML2_NAME")] == GetMessage("IBLOCK_XML2_HTML_DESCRIPTION")
                    )
                    {
                        if(strlen($value[GetMessage("IBLOCK_XML2_VALUE")]) > 0)
                        {
                            $arElement["DETAIL_TEXT"] = $value[GetMessage("IBLOCK_XML2_VALUE")];
                            $arElement["DETAIL_TEXT_TYPE"] = "html";
                        }
                    }
                    else
                    {
                        if($value[GetMessage("IBLOCK_XML2_NAME")] == GetMessage("IBLOCK_XML2_WEIGHT"))
                        {
                            $arElement["BASE_WEIGHT"] = $this->ToFloat($value[GetMessage("IBLOCK_XML2_VALUE")])*1000;
                        }

                        $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TRAITS"]]["n".$i] = array(
                            "VALUE" => $value[GetMessage("IBLOCK_XML2_VALUE")],
                            "DESCRIPTION" => $value[GetMessage("IBLOCK_XML2_NAME")],
                        );
                        $i++;
                    }
                }
            }
            if(isset($arXMLElement[GetMessage("IBLOCK_XML2_TAXES_VALUES")]))
            {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TAXES"]] = array();
                $i = 0;
                foreach($arXMLElement[GetMessage("IBLOCK_XML2_TAXES_VALUES")] as $key => $value)
                {
                    $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TAXES"]]["n".$i] = array(
                        "VALUE" => $value[GetMessage("IBLOCK_XML2_TAX_VALUE")],
                        "DESCRIPTION" => $value[GetMessage("IBLOCK_XML2_NAME")],
                    );
                    $i++;
                }
            }

            if(isset($arXMLElement[GetMessage("IBLOCK_XML2_BASE_UNIT")]))
            {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BASE_UNIT"]] = $arXMLElement[GetMessage("IBLOCK_XML2_BASE_UNIT")];
            }

            if(isset($arXMLElement[GetMessage("IBLOCK_XML2_PROPERTIES_VALUES")]))
            {
                foreach($arXMLElement[GetMessage("IBLOCK_XML2_PROPERTIES_VALUES")] as $key=>$value)
                {
                    if(!array_key_exists(GetMessage("IBLOCK_XML2_ID"), $value))
                        continue;

                    $prop_id = $value[GetMessage("IBLOCK_XML2_ID")];
                    unset($value[GetMessage("IBLOCK_XML2_ID")]);

                    //Handle properties which is actually element fields
                    if(!array_key_exists($prop_id, $this->PROPERTY_MAP))
                    {
                        if($prop_id == "CML2_CODE")
                            $arElement["CODE"] = isset($value[GetMessage("IBLOCK_XML2_VALUE")])? $value[GetMessage("IBLOCK_XML2_VALUE")]: "";
                        elseif($prop_id == "CML2_ACTIVE")
                        {
                            $value = array_pop($value);
                            $arElement["ACTIVE"] = ($value=="true") || intval($value)? "Y": "N";
                        }
                        elseif($prop_id == "CML2_SORT")
                            $arElement["SORT"] = array_pop($value);
                        elseif($prop_id == "CML2_ACTIVE_FROM")
                            $arElement["ACTIVE_FROM"] = CDatabase::FormatDate(array_pop($value), "YYYY-MM-DD HH:MI:SS", CLang::GetDateFormat("FULL"));
                        elseif($prop_id == "CML2_ACTIVE_TO")
                            $arElement["ACTIVE_TO"] = CDatabase::FormatDate(array_pop($value), "YYYY-MM-DD HH:MI:SS", CLang::GetDateFormat("FULL"));
                        elseif($prop_id == "CML2_PREVIEW_TEXT")
                        {
                            if(array_key_exists(GetMessage("IBLOCK_XML2_VALUE"), $value))
                            {
                                if(isset($value[GetMessage("IBLOCK_XML2_VALUE")]))
                                    $arElement["PREVIEW_TEXT"] = $value[GetMessage("IBLOCK_XML2_VALUE")];
                                else
                                    $arElement["PREVIEW_TEXT"] = "";

                                if(isset($value[GetMessage("IBLOCK_XML2_TYPE")]))
                                    $arElement["PREVIEW_TEXT_TYPE"] = $value[GetMessage("IBLOCK_XML2_TYPE")];
                                else
                                    $arElement["PREVIEW_TEXT_TYPE"] = "html";
                            }
                        }
                        elseif($prop_id == "CML2_DETAIL_TEXT")
                        {
                            if(array_key_exists(GetMessage("IBLOCK_XML2_VALUE"), $value))
                            {
                                if(isset($value[GetMessage("IBLOCK_XML2_VALUE")]))
                                    $arElement["DETAIL_TEXT"] = $value[GetMessage("IBLOCK_XML2_VALUE")];
                                else
                                    $arElement["DETAIL_TEXT"] = "";

                                if(isset($value[GetMessage("IBLOCK_XML2_TYPE")]))
                                    $arElement["DETAIL_TEXT_TYPE"] = $value[GetMessage("IBLOCK_XML2_TYPE")];
                                else
                                    $arElement["DETAIL_TEXT_TYPE"] = "html";
                            }
                        }
                        elseif($prop_id == "CML2_PREVIEW_PICTURE")
                        {
                            if(!is_array($this->preview) || !$arElement["PREVIEW_PICTURE"])
                            {
                                $arElement["PREVIEW_PICTURE"] = $this->MakeFileArray($value[GetMessage("IBLOCK_XML2_VALUE")]);
                                $arElement["PREVIEW_PICTURE"]["COPY_FILE"] = "Y";
                            }
                        }

                        continue;
                    }

                    $prop_id = $this->PROPERTY_MAP[$prop_id];
                    $prop_type = $this->arProperties[$prop_id]["PROPERTY_TYPE"];

                    if(!array_key_exists($prop_id, $arElement["PROPERTY_VALUES"]))
                        $arElement["PROPERTY_VALUES"][$prop_id] = array();

                    //check for bitrix extended format
                    if(array_key_exists(GetMessage("IBLOCK_XML2_PROPERTY_VALUE"), $value))
                    {
                        $i = 1;
                        $strPV = GetMessage("IBLOCK_XML2_PROPERTY_VALUE");
                        $lPV = strlen($strPV);
                        foreach($value as $k=>$prop_value)
                        {
                            if(substr($k, 0, $lPV) === $strPV)
                            {
                                if(array_key_exists(GetMessage("IBLOCK_XML2_SERIALIZED"), $prop_value))
                                    $prop_value[GetMessage("IBLOCK_XML2_VALUE")] = $this->Unserialize($prop_value[GetMessage("IBLOCK_XML2_VALUE")]);
                                if($prop_type=="F")
                                {
                                    $prop_value[GetMessage("IBLOCK_XML2_VALUE")] = $this->MakeFileArray($prop_value[GetMessage("IBLOCK_XML2_VALUE")]);
                                }
                                elseif($prop_type=="G")
                                    $prop_value[GetMessage("IBLOCK_XML2_VALUE")] = $this->GetSectionByXML_ID($this->arProperties[$prop_id]["LINK_IBLOCK_ID"], $prop_value[GetMessage("IBLOCK_XML2_VALUE")]);
                                elseif($prop_type=="E")
                                    $prop_value[GetMessage("IBLOCK_XML2_VALUE")] = $this->GetElementByXML_ID($this->arProperties[$prop_id]["LINK_IBLOCK_ID"], $prop_value[GetMessage("IBLOCK_XML2_VALUE")]);
                                elseif($prop_type=="L")
                                    $prop_value[GetMessage("IBLOCK_XML2_VALUE")] = $this->GetEnumByXML_ID($this->arProperties[$prop_id]["ID"], $prop_value[GetMessage("IBLOCK_XML2_VALUE")]);

                                if(array_key_exists("bOld", $arElement["PROPERTY_VALUES"][$prop_id]))
                                {
                                    if($prop_type=="F")
                                    {
                                        foreach($arElement["PROPERTY_VALUES"][$prop_id] as $PROPERTY_VALUE_ID => $PROPERTY_VALUE)
                                            $arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID] = array(
                                                "tmp_name" => "",
                                                "del" => "Y",
                                            );
                                        unset($arElement["PROPERTY_VALUES"][$prop_id]["bOld"]);
                                    }
                                    else
                                        $arElement["PROPERTY_VALUES"][$prop_id] = array();
                                }

                                $arElement["PROPERTY_VALUES"][$prop_id]["n".$i] = array(
                                    "VALUE" => $prop_value[GetMessage("IBLOCK_XML2_VALUE")],
                                    "DESCRIPTION" => $prop_value[GetMessage("IBLOCK_XML2_DESCRIPTION")],
                                );
                            }
                            $i++;
                        }
                    }
                    else
                    {
                        if($prop_type == "L" && !array_key_exists(GetMessage("IBLOCK_XML2_VALUE_ID"), $value))
                            $l_key = GetMessage("IBLOCK_XML2_VALUE");
                        else
                            $l_key = GetMessage("IBLOCK_XML2_VALUE_ID");

                        foreach($value as $k=>$prop_value)
                        {
                            if(array_key_exists("bOld", $arElement["PROPERTY_VALUES"][$prop_id]))
                            {
                                if($prop_type=="F")
                                {
                                    foreach($arElement["PROPERTY_VALUES"][$prop_id] as $PROPERTY_VALUE_ID => $PROPERTY_VALUE)
                                        $arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID] = array(
                                            "tmp_name" => "",
                                            "del" => "Y",
                                        );
                                    unset($arElement["PROPERTY_VALUES"][$prop_id]["bOld"]);
                                }
                                else
                                {
                                    $arElement["PROPERTY_VALUES"][$prop_id] = array();
                                }
                            }

                            if($prop_type == "L" && $k == $l_key)
                            {
                                $prop_value = $this->GetEnumByXML_ID($this->arProperties[$prop_id]["ID"], $prop_value);
                            }
                            elseif($prop_type == "N" && isset($this->next_step["sdp"]))
                            {
                                $prop_value = $this->ToFloat($prop_value);
                            }

                            $arElement["PROPERTY_VALUES"][$prop_id][] = $prop_value;
                        }
                    }
                }
            }

            //If there is no BaseUnit specified check prices for it
            if(
                (
                    !array_key_exists($this->PROPERTY_MAP["CML2_BASE_UNIT"], $arElement["PROPERTY_VALUES"])
                    || (
                        is_array($arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BASE_UNIT"]])
                        && array_key_exists("bOld", $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BASE_UNIT"]])
                    )
                )
                && isset($arXMLElement[GetMessage("IBLOCK_XML2_PRICES")])
            )
            {
                foreach($arXMLElement[GetMessage("IBLOCK_XML2_PRICES")] as $key => $price)
                {
                    if(
                        isset($price[GetMessage("IBLOCK_XML2_PRICE_TYPE_ID")])
                        && array_key_exists($price[GetMessage("IBLOCK_XML2_PRICE_TYPE_ID")], $this->PRICES_MAP)
                        && array_key_exists(GetMessage("IBLOCK_XML2_MEASURE"), $price)
                    )
                    {
                        $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BASE_UNIT"]] = $price[GetMessage("IBLOCK_XML2_MEASURE")];
                        break;
                    }
                }
            }

            if($arDBElement)
            {
                // Если было изменено название товара, сохраняем его урл
                if($arDBElement["NAME"] != $arElement["NAME"])
                {
                    if(CModule::IncludeModule("oldurl"))
                    {
                        COldUrlAll::SaveElementUrl($arDBElement["ID"]);
                    }
                }

                foreach($arElement["PROPERTY_VALUES"] as $prop_id=>$prop)
                {
                    if(is_array($arElement["PROPERTY_VALUES"][$prop_id]) && array_key_exists("bOld", $arElement["PROPERTY_VALUES"][$prop_id]))
                    {
                        if($this->arProperties[$prop_id]["PROPERTY_TYPE"]=="F")
                            unset($arElement["PROPERTY_VALUES"][$prop_id]);
                        else
                            unset($arElement["PROPERTY_VALUES"][$prop_id]["bOld"]);
                    }
                }

                if(intval($arElement["MODIFIED_BY"]) <= 0 && $USER_ID > 0)
                    $arElement["MODIFIED_BY"] = $USER_ID;

                if(!array_key_exists("CODE", $arElement) && is_array($this->translit_on_update))
                {
                    $arElement["CODE"] = CUtil::translit($arElement["NAME"], LANGUAGE_ID, $this->translit_on_update);
                    //Check if name was not changed in a way to update CODE
                    if($arDBElement["CODE"] === $arElement["CODE"])
                    {
                        unset($arElement["CODE"]);
                    }
                    else
                    {
                        $rsCodeExists = CIBlockElement::GetList(array(), array(
                            "IBLOCK_ID" => $this->next_step["IBLOCK_ID"],
                            "=CODE" => $arElement["CODE"],
                        ), false, false, array("ID"));
                        if($rsCodeExists->Fetch())
                        {
                            $arCodes = array();
                            $rsCodeLike = CIBlockElement::GetList(array(), array(
                                "IBLOCK_ID" => $this->next_step["IBLOCK_ID"],
                                "CODE" => $arElement["CODE"]."%",
                            ), false, false, array("ID", "CODE"));
                            while($ar = $rsCodeLike->Fetch())
                                $arCodes[$ar["CODE"]] = $ar["ID"];

                            $i = 1;
                            while(array_key_exists($arElement["CODE"]."_".$i, $arCodes))
                                $i++;

                            $arElement["CODE"] .= "_".$i;
                        }
                    }
                }

                if($arDBElement["ID"] > 0)
                {
                    // инициируем сброс кеша перед обновлением элемента.
                    if(CModule::IncludeModule("nscache"))
                    {
                        $exChache = new CExCacheM();
                        $exChache->Init($arDBElement["ID"], 1, "EL", true);
                    }
                }

                $obElement->Update($arDBElement["ID"], $arElement, $bWF, true, $this->iblock_resize);
                //In case element was not active in database we have to activate its offers
                if($arDBElement["ACTIVE"] != "Y")
                {
                    $this->ChangeOffersStatus($arDBElement["ID"], "Y", $bWF);
                }
                $arElement["ID"] = $arDBElement["ID"];
                if($arElement["ID"])
                {
                    $counter["UPD"]++;
                }
                else
                {
                    $this->LAST_ERROR = $obElement->LAST_ERROR;
                    $counter["ERR"]++;
                }
            }
            else
            {
                if(!array_key_exists("CODE", $arElement) && is_array($this->translit_on_add))
                {
                    $arElement["CODE"] = CUtil::translit($arElement["NAME"], LANGUAGE_ID, $this->translit_on_add);
                    $rsCodeExists = CIBlockElement::GetList(array(), array(
                        "IBLOCK_ID" => $this->next_step["IBLOCK_ID"],
                        "=CODE" => $arElement["CODE"],
                    ), false, false, array("ID"));
                    if($rsCodeExists->Fetch())
                    {
                        $arCodes = array();
                        $rsCodeLike = CIBlockElement::GetList(array(), array(
                            "IBLOCK_ID" => $this->next_step["IBLOCK_ID"],
                            "CODE" => $arElement["CODE"]."%",
                        ), false, false, array("ID", "CODE"));
                        while($ar = $rsCodeLike->Fetch())
                            $arCodes[$ar["CODE"]] = $ar["ID"];

                        $i = 1;
                        while(array_key_exists($arElement["CODE"]."_".$i, $arCodes))
                            $i++;

                        $arElement["CODE"] .= "_".$i;
                    }
                }

                $arElement["IBLOCK_ID"] = $this->next_step["IBLOCK_ID"];
                $arElement["ID"] = $obElement->Add($arElement, $bWF, true, $this->iblock_resize);
                if($arElement["ID"])
                {
                    $counter["ADD"]++;
                }
                else
                {
                    $this->LAST_ERROR = $obElement->LAST_ERROR;
                    $counter["ERR"]++;
                }
            }

            if($arElement["ID"] && $this->bCatalog && $this->next_step["bOffer"])
            {
                $CML_LINK = $this->PROPERTY_MAP["CML2_LINK"];

                $arProduct = array(
                    "ID" => $arElement["ID"],
                );
                if(isset($arElement["QUANTITY"]))
                    $arProduct["QUANTITY"] = $arElement["QUANTITY"];

                if(isset($arElement["BASE_WEIGHT"]))
                {
                    $arProduct["WEIGHT"] = $arElement["BASE_WEIGHT"];
                }
                else
                {
                    $rsWeight = CIBlockElement::GetProperty($this->arProperties[$CML_LINK]["LINK_IBLOCK_ID"], $arElement["PROPERTY_VALUES"][$CML_LINK], array(), array("CODE" => "CML2_TRAITS"));
                    while($arWheight = $rsWeight->Fetch())
                    {
                        if($arWheight["DESCRIPTION"] == GetMessage("IBLOCK_XML2_WEIGHT"))
                            $arProduct["WEIGHT"] = $this->ToFloat($arWheight["VALUE"])*1000;
                    }
                }

                if(isset($arElement["PRICES"]))
                {
                    //Here start VAT handling

                    //Check if all the taxes exists in BSM catalog
                    $arTaxMap = array();
                    $rsTaxProperty = CIBlockElement::GetProperty($this->arProperties[$CML_LINK]["LINK_IBLOCK_ID"], $arElement["PROPERTY_VALUES"][$CML_LINK], "sort", "asc", array("CODE" => "CML2_TAXES"));
                    while($arTaxProperty = $rsTaxProperty->Fetch())
                    {
                        if(
                            strlen($arTaxProperty["VALUE"]) > 0
                            && strlen($arTaxProperty["DESCRIPTION"]) > 0
                            && !array_key_exists($arTaxProperty["DESCRIPTION"], $arTaxMap)
                        )
                        {
                            $arTaxMap[$arTaxProperty["DESCRIPTION"]] = array(
                                "RATE" => $this->ToFloat($arTaxProperty["VALUE"]),
                                "ID" => $this->CheckTax($arTaxProperty["DESCRIPTION"], $this->ToFloat($arTaxProperty["VALUE"])),
                            );
                        }
                    }

                    //First find out if all the prices have TAX_IN_SUM true
                    $TAX_IN_SUM = "Y";
                    foreach($arElement["PRICES"] as $key=>$price)
                    {
                        if($price["PRICE"]["TAX_IN_SUM"] !== "true")
                        {
                            $TAX_IN_SUM = "N";
                            break;
                        }
                    }
                    //If there was found not insum tax we'll make shure
                    //that all prices has the same flag
                    if($TAX_IN_SUM === "N")
                    {
                        foreach($arElement["PRICES"] as $key=>$price)
                        {
                            if($price["PRICE"]["TAX_IN_SUM"] !== "false")
                            {
                                $TAX_IN_SUM = "Y";
                                break;
                            }
                        }
                        //Check if there is a mix of tax in sum
                        //and correct it by recalculating all the prices
                        if($TAX_IN_SUM === "Y")
                        {
                            foreach($arElement["PRICES"] as $key=>$price)
                            {
                                if($price["PRICE"]["TAX_IN_SUM"] !== "true")
                                {
                                    $TAX_NAME = $price["PRICE"]["TAX_NAME"];
                                    if(array_key_exists($TAX_NAME, $arTaxMap))
                                    {
                                        $PRICE_WO_TAX = $this->ToFloat($price[GetMessage("IBLOCK_XML2_PRICE_FOR_ONE")]);
                                        $PRICE = $PRICE_WO_TAX + ($PRICE_WO_TAX / 100.0 * $arTaxMap[$TAX_NAME]["RATE"]);
                                        $arElement["PRICES"][$key][GetMessage("IBLOCK_XML2_PRICE_FOR_ONE")] = $PRICE;
                                    }
                                }
                            }
                        }
                    }
                    foreach($arElement["PRICES"] as $key=>$price)
                    {
                        $TAX_NAME = $price["PRICE"]["TAX_NAME"];
                        if(array_key_exists($TAX_NAME, $arTaxMap))
                        {
                            $arProduct["VAT_ID"] = $arTaxMap[$TAX_NAME]["ID"];
                            break;
                        }
                    }
                    $arProduct["VAT_INCLUDED"] = $TAX_IN_SUM;
                }

                CCatalogProduct::Add($arProduct);

                if(isset($arElement["PRICES"]))
                    $this->SetProductPrice($arElement["ID"], $arElement["PRICES"], $arElement["DISCOUNTS"]);

            }
        }

        $totalAmountForCache = 1;
        if(isset($arXMLElement[GetMessage("IBLOCK_XML2_STORE_AMOUNT_LIST")]))
        {

            $arElement["STORE_AMOUNT"]=array();
            foreach($arXMLElement[GetMessage("IBLOCK_XML2_STORE_AMOUNT_LIST")] as $key=>$storeAmount)
            {
                if(isset($storeAmount[GetMessage("IBLOCK_XML2_STORE_ID")]))
                {
                    $storeXMLID = $storeAmount[GetMessage("IBLOCK_XML2_STORE_ID")];
                    $amount = $storeAmount[GetMessage("IBLOCK_XML2_AMOUNT")];
                    $arElement["STORE_AMOUNT"][]=array($storeXMLID => $amount);
                    $totalAmountForCache += $amount;
                }
            }
        }
        if(isset($arElement["STORE_AMOUNT"]))
            $this->ImportStoresAmount($arElement["STORE_AMOUNT"], $arElement["ID"], $counter);

        // удаляем дубли товаров и сбрасываем кеш

        if((is_array($arDBElement) || $arElement["ID"] > 0) && CModule::IncludeModule("oldurl") && CModule::IncludeModule("nscache"))
        {
            if(intval($arDBElement["IBLOCK_ID"]) > 0)
                $arData = $arDBElement;
            else
                $arData = $arElement;

            if(intval($arData["IBLOCK_ID"]) == 0)
            {
                $rsEl = CIBlockElement::GetList(array("ID"=>"ASC"), array("ID"=>$arData["ID"]), false, array("nTopCount"=>1), array("IBLOCK_ID"));
                if($arEl = $rsEl->GetNext())
                    $arData["IBLOCK_ID"] = $arEl["IBLOCK_ID"];
            }

            COldUrlAll::RemoveDoubleElement($arData["XML_ID"], $arData["IBLOCK_ID"]);

            $exChache = new CExCacheM();
            $exChache->Init($arDBElement["ID"], $totalAmountForCache, "EL", true);
        }

        return $arElement["ID"];
    }

    function ImportElementPrices($arXMLElement, &$counter, $bWF)
    {
        $arElement = array(
            "ID" => 0,
            "XML_ID" => $arXMLElement[GetMessage("IBLOCK_XML2_ID")],
        );

        $obElement = new CIBlockElement;
        $rsElement = $obElement->GetList(
            Array("ID"=>"asc"),
            Array("=XML_ID" => $arElement["XML_ID"], "IBLOCK_ID" => $this->next_step["IBLOCK_ID"]),
            false, false,
            Array("ID", "TMP_ID", "ACTIVE")
        );

        if($arDBElement = $rsElement->Fetch())
        {
            $arElement["ID"] = $arDBElement["ID"];

            if(isset($arXMLElement[GetMessage("IBLOCK_XML2_PRICES")]))
            {//Collect price information for future use
                $arElement["PRICES"] = array();
                foreach($arXMLElement[GetMessage("IBLOCK_XML2_PRICES")] as $key=>$price)
                {
                    if(isset($price[GetMessage("IBLOCK_XML2_PRICE_TYPE_ID")]) && array_key_exists($price[GetMessage("IBLOCK_XML2_PRICE_TYPE_ID")], $this->PRICES_MAP))
                    {
                        $price["PRICE"] = $this->PRICES_MAP[$price[GetMessage("IBLOCK_XML2_PRICE_TYPE_ID")]];
                        $arElement["PRICES"][] = $price;

                    }
                }

                $arElement["DISCOUNTS"] = array();
                if(isset($arXMLElement[GetMessage("IBLOCK_XML2_DISCOUNTS")]))
                {
                    foreach($arXMLElement[GetMessage("IBLOCK_XML2_DISCOUNTS")] as $key=>$discount)
                    {
                        if(
                            isset($discount[GetMessage("IBLOCK_XML2_DISCOUNT_CONDITION")])
                            && $discount[GetMessage("IBLOCK_XML2_DISCOUNT_CONDITION")]===GetMessage("IBLOCK_XML2_DISCOUNT_COND_VOLUME")
                        )
                        {
                            $discount_value = $this->ToInt($discount[GetMessage("IBLOCK_XML2_DISCOUNT_COND_VALUE")]);
                            $discount_percent = $this->ToFloat($discount[GetMessage("IBLOCK_XML2_DISCOUNT_COND_PERCENT")]);
                            if($discount_value > 0 && $discount_percent > 0)
                                $arElement["DISCOUNTS"][$discount_value] = $discount_percent;
                        }
                    }
                }
            }
            if(isset($arXMLElement[GetMessage("IBLOCK_XML2_AMOUNT")]))
                $arElement["QUANTITY"] = $this->ToFloat($arXMLElement[GetMessage("IBLOCK_XML2_AMOUNT")]);
            else
                $arElement["QUANTITY"] = 0;

            if(isset($arElement["PRICES"]) && $this->bCatalog)
            {
                $arProduct = array(
                    "ID" => $arElement["ID"],
                );
                if(isset($arElement["QUANTITY"]))
                    $arProduct["QUANTITY"] = $arElement["QUANTITY"];

                //Get weight from element traits
                $rsWeight = CIBlockElement::GetProperty($this->next_step["IBLOCK_ID"], $arElement["ID"], array(), array("ID"=>$this->PROPERTY_MAP["CML2_TRAITS"]));
                while($arWheight = $rsWeight->Fetch())
                {
                    if($arWheight["DESCRIPTION"] == GetMessage("IBLOCK_XML2_WEIGHT"))
                        $arProduct["WEIGHT"] = $this->ToFloat($arWheight["VALUE"])*1000;
                }

                //Here start VAT handling

                //Check if all the taxes exists in BSM catalog
                $arTaxMap = array();
                $rsTaxProperty = CIBlockElement::GetProperty($this->next_step["IBLOCK_ID"], $arElement["ID"], "sort", "asc", array("CODE" => "CML2_TAXES"));
                while($arTaxProperty = $rsTaxProperty->Fetch())
                {
                    if(
                        strlen($arTaxProperty["VALUE"]) > 0
                        && strlen($arTaxProperty["DESCRIPTION"]) > 0
                        && !array_key_exists($arTaxProperty["DESCRIPTION"], $arTaxMap)
                    )
                    {
                        $arTaxMap[$arTaxProperty["DESCRIPTION"]] = array(
                            "RATE" => $this->ToFloat($arTaxProperty["VALUE"]),
                            "ID" => $this->CheckTax($arTaxProperty["DESCRIPTION"], $this->ToFloat($arTaxProperty["VALUE"])),
                        );
                    }
                }

                //First find out if all the prices have TAX_IN_SUM true
                $TAX_IN_SUM = "Y";
                foreach($arElement["PRICES"] as $key=>$price)
                {
                    if($price["PRICE"]["TAX_IN_SUM"] !== "true")
                    {
                        $TAX_IN_SUM = "N";
                        break;
                    }
                }
                //If there was found not insum tax we'll make shure
                //that all prices has the same flag
                if($TAX_IN_SUM === "N")
                {
                    foreach($arElement["PRICES"] as $key=>$price)
                    {
                        if($price["PRICE"]["TAX_IN_SUM"] !== "false")
                        {
                            $TAX_IN_SUM = "Y";
                            break;
                        }
                    }
                    //Check if there is a mix of tax in sum
                    //and correct it by recalculating all the prices
                    if($TAX_IN_SUM === "Y")
                    {
                        foreach($arElement["PRICES"] as $key=>$price)
                        {
                            if($price["PRICE"]["TAX_IN_SUM"] !== "true")
                            {
                                $TAX_NAME = $price["PRICE"]["TAX_NAME"];
                                if(array_key_exists($TAX_NAME, $arTaxMap))
                                {
                                    $PRICE_WO_TAX = $this->ToFloat($price[GetMessage("IBLOCK_XML2_PRICE_FOR_ONE")]);
                                    $PRICE = $PRICE_WO_TAX + ($PRICE_WO_TAX / 100.0 * $arTaxMap[$TAX_NAME]["RATE"]);
                                    $arElement["PRICES"][$key][GetMessage("IBLOCK_XML2_PRICE_FOR_ONE")] = $PRICE;
                                }
                            }
                        }
                    }
                }
                foreach($arElement["PRICES"] as $key=>$price)
                {
                    $TAX_NAME = $price["PRICE"]["TAX_NAME"];
                    if(array_key_exists($TAX_NAME, $arTaxMap))
                    {
                        $arProduct["VAT_ID"] = $arTaxMap[$TAX_NAME]["ID"];
                        break;
                    }
                }
                $arProduct["VAT_INCLUDED"] = $TAX_IN_SUM;

                CCatalogProduct::Add($arProduct);

                $this->SetProductPrice($arElement["ID"], $arElement["PRICES"], $arElement["DISCOUNTS"]);

                $counter["UPD"]++;
            }
        }

        if(isset($arXMLElement[GetMessage("IBLOCK_XML2_STORE_AMOUNT_LIST")]))
        {
            $arElement["STORE_AMOUNT"]=array();
            foreach($arXMLElement[GetMessage("IBLOCK_XML2_STORE_AMOUNT_LIST")] as $key=>$storeAmount)
            {
                if(isset($storeAmount[GetMessage("IBLOCK_XML2_STORE_ID")]))
                {
                    $storeXMLID = $storeAmount[GetMessage("IBLOCK_XML2_STORE_ID")];
                    $amount = $storeAmount[GetMessage("IBLOCK_XML2_AMOUNT")];
                    $arElement["STORE_AMOUNT"][]=array($storeXMLID => $amount);
                }
            }
        }
        if(isset($arElement["STORE_AMOUNT"]))
            $this->ImportStoresAmount($arElement["STORE_AMOUNT"], $arElement["ID"], $counter);

        if(CModule::IncludeModule("nscache"))
        {
            //AddMessage2Log("starting clear price cache");
            $exChache = new CExCacheM();
            $exChache->Init($arElement["ID"],  $arElement["QUANTITY"], "EL_PRICE", true);
        }

        return $arElement["ID"];
    }
}