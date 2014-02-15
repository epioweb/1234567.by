<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
	$(document).ready(function(){
		$(".search-input").suggest("<?=$templateFolder?>/suggest.php",{
			dataContainer: ".search-results",
			attachObject: ".search-results",
			delimiter: "#end#",
			dataDelimiter: "#end#",
			delay : 500,
			loaderAnimObj: "#loader-img"
		});
	});
</script>

<div class="new-search">
	<div class="search-area-class">
		<input type="text" name="q" class="search-input">
		<input type="submit" class="btn button" value="Найти">
		<input type="submit" class="btn button close-search"  value="х">
		&nbsp;<img src="<?=MAIN_TEMPLATE_PATH?>/i/loadinfo.gif" width="24" height="24" id="loader-img" style="display:none; vertical-aling:middle;" />
	</div>
	
	<div style="display: none;" class="search-results">
		<div class="inner-search-results"></div>
	</div>
</div>
