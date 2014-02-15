<?$APPLICATION->IncludeComponent("bitrix:main.share", "bestprice", Array(
	"HIDE" => "N",	// Скрыть панель закладок по умолчанию
	"HANDLERS" => array(	// Используемые соц. закладки и сети
		0 => "vk",
        1 => "odnoklassniki",
		2 => "facebook",
		3 => "twitter"
	),
	"PAGE_URL" => "",	// URL страницы относительно корня сайта
	"PAGE_TITLE" => "",	// Заголовок страницы
	),
	false
);?>