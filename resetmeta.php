<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<div>
    СБРОС СЕО У ИНФОБЛОКА
</div>
<div>
    Для запуска очистки СЕО нажать:
    <ul>
        <li><a href="?type=section">У разделов</a></li>
        <li><a href="?type=element">У элементов</a></li>
    </ul>
</div>
<?php
$blid=27;
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
