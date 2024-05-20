<?
//component.php
//после 154 стр
if($b404 && CModule::IncludeModule("iblock")){
/* ------ Настройка ручного ЧПУ фильтра ------ */
if ($arParams['IBLOCK_ID'] == 26)
{
    // Проверка на нахождение на старой ссылке (Редирект на новую)
    $catalogUrl  = $APPLICATION->GetCurDir();
    $arFilter    = array('IBLOCK_ID' => 38, 'ACTIVE' => 'Y', 'PROPERTY_URL' => $catalogUrl);
    $arCustomUrl = CIBlockElement::GetList([], $arFilter, false, false, array('ID', 'PROPERTY_CUSTOM_URL'))->Fetch();
 
 
    if (!empty($arCustomUrl['PROPERTY_CUSTOM_URL_VALUE']) && !isset($_GET['ajax'])) {
        LocalRedirect($arCustomUrl['PROPERTY_CUSTOM_URL_VALUE'], false, '301 Moved permanently');
    } else {
        // Проверка на нахождение на новой ссылке (Формирование массива параметров)
        $arFilter    = array('IBLOCK_ID' => 38, 'ACTIVE' => 'Y', 'PROPERTY_CUSTOM_URL' => $catalogUrl);
        $arSelect    = array('*', 'PROPERTY_URL', 'PROPERTY_CUSTOM_URL', 'PROPERTY_TITLE', 'PROPERTY_DESC');
        $arCustomUrl = CIBlockElement::GetList([], $arFilter, false, false, $arSelect)->Fetch();
 
        if (!empty($arCustomUrl['ID']))
        {
            $arCatalogUrl = explode('/', $catalogUrl);
            $sectionCode  = $arCatalogUrl[count($arCatalogUrl) - 3];
            $arFilter     = array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'CODE' => $sectionCode);
            $arSection    = CIBlockSection::GetList([], $arFilter)->Fetch();
 
            // Полный путь из разделов каталога до текущего
            $codePath     = str_replace('/catalog/', '', makeFullPath($arSection['ID']));
            $codePath     = str_replace($arSection['CODE'].'/', $arSection['CODE'], $codePath);
 
            // Параметры фильтра
            $filterPath   = str_replace('/apply/', '', explode('/filter/', $arCustomUrl['PROPERTY_URL_VALUE'])[1]);
 
            $arVariables  = array(
                'SECTION_CODE_PATH' => $codePath,
                'SECTION_ID'        => $arSection['ID'],
                'SECTION_CODE'      => $arSection['CODE'],
                'SMART_FILTER_PATH' => $filterPath,
                'IS_CUSTOM_CPU'     => 'Y',
                'CUSTOM_VALUES'     => array(
                    'CUSTOM_URL' => $arCustomUrl['PROPERTY_CUSTOM_URL_VALUE'],
                    'PAGE_META'  => array(
                        'TITLE'  => $arCustomUrl['PROPERTY_TITLE_VALUE'],
                        'DESC'   => $arCustomUrl['PROPERTY_DESC_VALUE']
                    )
                ),
            );
 
            $b404          = false;
            $componentPage = 'section';
        }
    }
}
/* ------ END Настройка ручного ЧПУ фильтра ------ */

  //section.php
  /* ------ Установка ручных мета ЧПУ фильтра ------ */
if ($arResult['VARIABLES']['IS_CUSTOM_CPU'] == 'Y')
{
    $customMeta = $arResult['VARIABLES']['CUSTOM_VALUES']['PAGE_META'];
    if (!empty($customMeta['TITLE'])) {
        $APPLICATION->SetPageProperty('title', $customMeta['TITLE']);
    }
    if (!empty($customMeta['DESC'])) {
        $APPLICATION->SetPageProperty('description', $customMeta['DESC']);
    }
}
/* ------ /Установка ручных мета ЧПУ фильтра ------ */


  

//-------- Получение полного пути до категории ----------
function makeFullPath($id) {
 
   CModule::IncludeModule('iblock');
   $path = '/catalog/';
   $rs   = CIBlockSection::GetNavChain(IBLOCK_ID, $id, array('ID','CODE'));
 
   while ($ar = $rs->Fetch()) {
       $path .= $ar['CODE'].'/';
   }
 
   return $path;
}
  
  //ASPRO 
  //Smartseo::class

  /* ------ Проеверка на наличие ЧПУ ссылки для сформированной ------ */
CModule::IncludeModule('iblock');
$arFilter    = array('IBLOCK_ID' => 38, 'ACTIVE' => 'Y', 'PROPERTY_URL' => $url);
$arCustomUrl = CIBlockElement::GetList([], $arFilter, false, false, array('ID', 'PROPERTY_CUSTOM_URL'))->Fetch();
 
if (!empty($arCustomUrl['PROPERTY_CUSTOM_URL_VALUE'])) {
    $url = $arCustomUrl['PROPERTY_CUSTOM_URL_VALUE'];
}
/* ------ END Проеверка на наличие ЧПУ ссылки для сформированной ------ */
?>
