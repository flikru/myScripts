<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<div>
    СБРОС СЕО У ИНФОБЛОКА<br><br>
</div>
<div>
    Для запуска очистки СЕО нажать:
    <form action="" method="get">
        Номер инфоблока<br>
        <input type="text" name="iblock" value="<?=isset($_GET['iblock'])?$_GET['iblock']:''?>"><br><br>
        Что чистим<br>
        <select name="type">
            <option value="section" selected>У разделов</option>
            <option value="element">У элементов</option>
        </select>
        <br>
        <br>
        <input type="submit" value="ОЧИСТИТЬ">
    </form>
</div>
<?php
if(isset($_GET['iblock']) && !empty($_GET['iblock'])){
    $blid=$_GET['iblock'];
}else{
    return;
}
CModule::IncludeModule('iblock');
$arFilter = array('IBLOCK_ID' => $blid);

/*
 * Очистка разделов от МЕТА
 * */
if($_GET['type']=='section'):
    $res = CIBlockSection::GetList(false, $arFilter, array('IBLOCK_ID','ID'));
    while($el = $res->GetNext()):
        $arSectionsID[] = $el['ID'];
    endwhile;
    foreach($arSectionsID as $key):
        $ipropTemplates = new \Bitrix\Iblock\InheritedProperty\SectionTemplates ($blid, $key);
        $ipropTemplates->set(array(
            "SECTION_META_TITLE" => "",
            "SECTION_META_KEYWORDS" => "",
            "SECTION_META_DESCRIPTION" => "",
            "SECTION_PAGE_TITLE" => "",
            "ELEMENT_META_TITLE" => "",
            "ELEMENT_META_KEYWORDS" => "",
            "ELEMENT_META_DESCRIPTION" => "",
            "ELEMENT_PAGE_TITLE" => "",
        ));
        echo "ОЧИЩЕН РАЗДЕЛ - $key<BR>";
    endforeach;
    return;
endif;
?>

<?php
/*
 * Очистка элементов от МЕТА
 * */
if($_GET['type']=='element'):
        $res = CIBlockElement::GetList(false, $arFilter, array('IBLOCK_ID','ID'));
        while($el = $res->GetNext()):
            $arElementsID[] = $el['ID'];
        endwhile;
        foreach($arElementsID as $key):
            $ipropTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates ($blid, $key); //еще раз уточняем ID инфоблока
            $ipropTemplates->set(array(
                "ELEMENT_META_TITLE" => "",
                "ELEMENT_META_KEYWORDS" => "",
                "ELEMENT_META_DESCRIPTION" => "",
                "ELEMENT_PAGE_TITLE" => "",
            ));
            echo "ОЧИЩЕН ЭЛЕМЕНТ - $key<BR>";
        endforeach;
    return;
endif;

?>
