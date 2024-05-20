<?php
$menu = dataCache('menuPlanning', "menuLoad", 60*60, 'planning',0 );

function menuLoad(){
    $data = new Services();
    $servs = $data->getServices();
    $menu = Services::buildMenu($servs);
    return $menu;
}

use \Bitrix\Main\Data\Cache;

function dataCache($cacheKey, $function, $cacheTime = 60*60*4, $cachePath='allCache',$reset=0){
    if($reset==1) $cacheTime=1;
    $cache = Cache::createInstance(); // Служба кеширования

    $cacheTtl = $cacheTime; // срок годности кеша (в секундах)
    $cacheKey = $cacheKey; // имя кеша
    $cachePath = $cachePath; // папка, в которой лежит кеш

    if ($cache->initCache($cacheTtl, $cacheKey, $cachePath))
    {
        $vars = $cache->getVars(); // Получаем переменные
        $cache->output(); // Выводим HTML пользователю в браузер
    }
    elseif ($cache->startDataCache())
    {
        $vars = $function();
        // Если что-то пошло не так и решили кеш не записывать
        $cacheInvalid = false;
        if ($cacheInvalid or empty($vars))
        {
            $cache->abortDataCache();
        }
        // Всё хорошо, записываем кеш
        $cache->endDataCache($vars);
    }
    return $vars;
}
?>
