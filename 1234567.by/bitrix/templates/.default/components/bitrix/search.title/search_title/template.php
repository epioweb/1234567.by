<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
$(document).ready(function() {
	$(".search-txt").focus(function(){
		var first_lv=$(document).find('div .search-body-query')
		//var second_lv=first_lv.children();
		first_lv.css('display','block');
		//second_lv.css('display','block');
		return false;
	});
	$(".search-txt").blur(function(){
		var first_lv=$(document).find('div .search-body-query')
		//var second_lv=first_lv.children();
		first_lv.css('display','none');
		//second_lv.css('display','none');
		return false;
	});
});
</script>

<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if($arParams["SHOW_INPUT"] !== "N"):?>
<div id="<?echo $CONTAINER_ID?>"  class="search">
<form action="<?echo $arResult["FORM_ACTION"]?>" id="searchForm">
    <div class="head">
        <input id="<?echo $INPUT_ID?>" type="text" class="search-txt" value="" name="q"  autocomplete="on"/>
		<input class="button" name="s" type="submit" value="<?=GetMessage("BSF_T_SEARCH_BUTTON");?>" />
    </div>
    <div id="loader-info" style="display:none">поиск...</div>
    <div class="search-body-query" style="width:100% !important; z-index:300;">
        <table>
            <tr>
                <td>введите запрос</td>
            </tr>
        </table>
    </div>
</form> 
</div>
<?endif?>
<script type="text/javascript">
var jsControl = new JCTitleSearch({
	'WAIT_IMAGE': '/bitrix/themes/.default/images/wait.gif',
	'AJAX_PAGE' : '<?echo CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
	'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
	'INPUT_ID': '<?echo $INPUT_ID?>',
	'MIN_QUERY_LEN': 2
});
</script>