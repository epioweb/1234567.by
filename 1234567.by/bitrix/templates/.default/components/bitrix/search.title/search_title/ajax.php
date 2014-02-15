<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
$(document).ready(function() {
		var first_lv=$(document).find('div .search-body-query')
		//var second_lv=first_lv.children();
		first_lv.css('display','none');
		//second_lv.css('display','block');
		return false;
});
</script>


<div class="search-body">
	<table>
<?
if(!empty($arResult["CATEGORIES"])):?>

		<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
			<?foreach($arCategory["ITEMS"] as $i => $arItem):?>
			<tr>
				<th>
				<?if(isset($arItem["ICON"])):?>
					<div class="img"><a href="<?echo $arItem["URL"]?>" target="_blank"><img src="<?echo $arItem["ICON"]?>"></a></div>
				<?endif;?>
				</th>
					<td><p><a href="<?echo $arItem["URL"]?>" target="_blank"><?echo $arItem["NAME"]?></a></p></td>
					<td><h5><?=$arItem["PRICES"]["PRINT_USD_VALUE"]?></h5><h6><?=$arItem["PRICES"]["PRINT_VALUE"]?></h6></td>
			</tr>
			<?endforeach;?>
		<?endforeach;?>
	</table>
	<div class="title-search-fader"></div>
	</div>

<?else:?>
    <td>
	    <?=GetMessage("SEARCH_NOTHING_TO_FOUND");?>
    </td>
	</table>
	</div>
<?endif;
//echo "<pre>",htmlspecialcharsbx(print_r($arResult,1)),"</pre>";
?>