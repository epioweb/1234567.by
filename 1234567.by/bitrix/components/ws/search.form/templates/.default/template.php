<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
	$(document).ready(function(){
		$(".search-txt").suggest("<?=$templateFolder?>/suggest.php",{
			dataContainer: "#searchForm .search-body",
			attachObject: ".search-body table",
			delimiter: "#end#",
			dataDelimiter: "#cell#",
			delay : 400,
			loaderAnimObj: "#loader-info",
			dfltValue: "<?=GetMessage("DEFAULT_MESSAGE")?>",
            onSelect: function(link, force){
                if(link && force) {
                    location.href = link;
                }
                return true;
            },
            selectClass: "selected"
		});
	});
</script>

<form action="/search/" id="searchForm">
    <div class="head">
        <input type="text" class="search-txt" value="<?echo strlen($arParams["Q"])?$arParams["Q"]:"";?>" name="q" />
        <button onclick="document.getElementById('searchForm').submit();"><span>Найти</span></button>
    </div>
    <div id="loader-info" style="display:none">поиск...</div>
    <div class="search-body">
        <table>
            <tr>
                <td>введите запрос</td>
            </tr>
        </table>
    </div>
</form> 
