<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once 'vendor/autoload.php';
global $USER;
if (!$USER->IsAdmin()) {
    header('HTTP/1.0 403 Forbidden');
    echo '<pre>';print_r('FORBIDDEN');echo '</pre>';
    exit;
}
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

$fileShortName="catalogh1page.xlsx";
$shortName=0;
echo "Загрузка коротких имен из файла - ".$fileShortName." <a href='?short=1'>Запустить</a> <br>";
if($_GET["short"]==1) {
    echo "Загрузка коротких имен<br>";
    if (CModule::IncludeModule("iblock")) {
        $reader = new Xlsx();
        $spreadsheet = $reader->load($fileShortName);

        $cells = $spreadsheet->getSheet(6)->toArray();
        foreach ($cells as $key=>$cell) {
            dd($cell);
            $str="";
            $str="Строка: ".$key;
            if($key==0 || empty($cell[0])|| empty($cell[2])) continue;
            $url = $cell[0];
            $short = $cell[2];
            $code = clearCode($url);
            $arFilter = array(
                "IBLOCK_ID" => 8,
                "CODE" => $code,
                //"SECTION_ID" => 1012,
                //"INCLUDE_SUBSECTIONS" => 'Y',
            );

            $arSelect = Array("ID","CODE","NAME","PROPERTY_SHORT_NAME","PROPERTY_SHORT_NAME_ON");
            $res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, Array("nPageSize"=>25), $arSelect);
            $prod=0;
            while($ob = $res->GetNextElement()){
                $prod++;
                $arFields = $ob->GetFields();
                if($prod>2){
                    break;
                }
                $prop = [
                    "SHORT_NAME" => $short,
                    "SHORT_NAME_ON" => "1",
                ];
                $rez = CIBlockElement::SetPropertyValuesEx($arFields['ID'], 8, $prop);
            }
            if($prod==1){
                $str .="; ID товара: ".$arFields['ID'];
            }elseif($prod>1){
                $str .="; ВНИМАНИЕ более 1 товара ПО КОДУ ".$code;
            }else{
                $str .="; ВНИМАНИЕ ТОВАРОВ НЕ НАЙДЕНО ПО КОДУ ".$code;
            }
            echo $str."<br>";
        }
    }
}

echo "<br>Загрузка новых названий товаров по символьному коду из файла - ".$fileShortName." <a href='?long=1'>Запустить</a> <br>";
if($_GET["long"]==1) {
    echo "Загрузка новых названий<br>";
    if (CModule::IncludeModule("iblock")) {
        $reader = new Xlsx();
        $spreadsheet = $reader->load($fileShortName);

        $cells = $spreadsheet->getSheet(5)->toArray();
        $objElement = new CIBlockElement;
        foreach ($cells as $key=>$cell) {
            if($key==0 || empty($cell[0])|| empty($cell[1])) continue;
            $str="";
            $str="Строка: ".$key;
            $url = $cell[0];
            $name = $cell[1];
            $code = clearCode($url);
            $arFilter = array(
                "IBLOCK_ID" => 8,
                "CODE" => $code,
                //"SECTION_ID" => 1012,
                //"INCLUDE_SUBSECTIONS" => 'Y',
            );
            $update=['NAME'=>$name];
            $arSelect = Array("ID","CODE","NAME");
            $res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, Array("nPageSize"=>25), $arSelect);
            $prod=0;
            while($ob = $res->GetNextElement()){
                $prod++;
                $arFields = $ob->GetFields();
                $resUpdate = $objElement->Update($arFields['ID'], $update);
                if($prod>2){
                    break;
                }
            }
            if($prod==1){
                $str .="; ID товара: ".$arFields['ID'];
            }elseif($prod>1){
                $str .="; ВНИМАНИЕ более 1 товара ПО КОДУ ".$code;
            }else{
                $str .="; ВНИМАНИЕ ТОВАРОВ НЕ НАЙДЕНО ПО КОДУ ".$code;
            }
            echo $str."<br>";
        }
    }
}
function clearCode($url){
    $code = str_replace("https://titan-lock.shop/product/","", $url);
    $code = str_replace("/","", $code);
    return $code;
}


?>
