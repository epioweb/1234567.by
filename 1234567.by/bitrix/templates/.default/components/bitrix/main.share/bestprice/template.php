<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (strlen($arResult["PAGE_URL"]) > 0):?>
	<div class="social">
		<? if (is_array($arResult["BOOKMARKS"]) && count($arResult["BOOKMARKS"]) > 0): ?>
			<table cellspacing="0" cellpadding="0" border="0" class="bookmarks-table">
                <tr>
                <?foreach($arResult["BOOKMARKS"] as $name => $arBookmark):?>
                    <td class="bookmarks"><?=$arBookmark["ICON"]?></td>
                <?endforeach?>
                </tr>
			</table>	
		<? endif; ?>
	</div>
<?endif;?>