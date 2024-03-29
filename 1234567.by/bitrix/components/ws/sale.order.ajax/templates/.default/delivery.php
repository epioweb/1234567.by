<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(!empty($arResult["DELIVERY"]))
{
	?>
	<label><?=GetMessage("SOA_TEMPL_DELIVERY")?></label>
	<table class="sale_order_full_table">
		<?
		foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
		{
			if ($delivery_id !== 0 && intval($delivery_id) <= 0)
			{
				?>
				<tr>
					<td colspan="2">
						<?=$arDelivery["TITLE"]?>
                        <?if (strlen($arDelivery["DESCRIPTION"]) > 0):?>
                            <div class="delivery-description">
						        <?=nl2br($arDelivery["DESCRIPTION"])?>
                            </div>
                        <?endif;?>
						<table border="0" cellspacing="0" cellpadding="3">
						<?
						foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
						{
							?>
							<tr>
								<td width="20" nowrap="nowrap">&nbsp;</td>
								<td width="0%" valign="top"><input type="radio" id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>" name="<?=$arProfile["FIELD_NAME"]?>" value="<?=$delivery_id.":".$profile_id;?>" <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?> onClick="submitForm();" /></td>
								<td width="50%" valign="top">
									<label for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>">
										<small><b><?=$arProfile["TITLE"]?></b><?if (strlen($arProfile["DESCRIPTION"]) > 0):?><br />
										<?=nl2br($arProfile["DESCRIPTION"])?><?endif;?></small>
									</label>
								</td>
								<td width="50%" valign="top" align="right">
								<?
									$APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
										"NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
										"DELIVERY" => $delivery_id,
										"PROFILE" => $profile_id,
										"ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
										"ORDER_PRICE" => $arResult["ORDER_PRICE"],
										"LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
										"LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
										"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
									), null, array('HIDE_ICONS' => 'Y'));
								?>
								
								</td>
							</tr>
							<?
						} // endforeach
						?>
						</table>
					</td>
				</tr>
				<?
			}	
			else
			{
				?>
				<tr>
					<td valign="top" width="0%">
						<input type="radio" id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>" name="<?=$arDelivery["FIELD_NAME"]?>" value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?> onclick="submitForm();">
					</td>
					<td valign="top" width="100%">
						<label for="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>">
						<?= $arDelivery["NAME"] ?>
                            <?if($arDelivery["PRICE"] > 0):?>
                                <span style="background-color:#f2f2f2; color:#484847; font-size: 10px; padding:3px;">&nbsp;<?=GetMessage("SALE_DELIV_PRICE");?> <?=$arDelivery["PRICE_FORMATED"]?></span>
                            <?endif?>
                        <div class="delivery-description" style="margin-top: 5px">
                            <?if (strlen($arDelivery["PERIOD_TEXT"])>0):?>
                                <?=$arDelivery["PERIOD_TEXT"];?>
                            <?endif?>

                            <?if (strlen($arDelivery["DESCRIPTION"])>0):?>
                                <?=nl2br($arDelivery["DESCRIPTION"])?><br />
                            <?endif?>
                        </div>
						</label>
					</td>
				</tr>
				<?
			}
		}
		?>
	</table>
	<?
}
?>