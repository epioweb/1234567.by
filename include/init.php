<?
//настройки PHP
header("Content-Type: text/html; charset=utf-8");
$start_time = microtime(true);
error_reporting(-1);
ini_set('allow_url_fopen', '1');
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set('memory_limit', '1024M');
set_time_limit(0);

$annTable = "announcements";
$annImagesTable='annImages';

$imagePath = '../images/';
//Настройки пагинатора
$similar=5; //Количетво похожих объявлений, выводимых внизу детальной страницы
$addPages = 10; //количество номеров страниц по бокам от текущей 
$range =50; //кол-во объявлений на странице

$site = 'http://www.avito.ru';//сайт, который парсим

//Массив названий подразделов
//(чтобы вырезать из заголовков страниц)
$arSubSections=array(
    "Автомобили с пробегом",
    "Новые автомобили",
    "Мотоциклы и мототехника",
    "Грузовики и спецтехника",
    "Водный транспорт",
    "Запчасти и аксессуары"
);
