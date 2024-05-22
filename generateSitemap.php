<?
function generateSitemap(&$arFields=[])
{
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/logtest.txt',print_r($arFields,true));
if($arFields['IBLOCK_ID']==70):
    CModule::IncludeModule("iblock");
    $arSelect = array(
        "ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_URL",
        "PROPERTY_CUSTOM_URL"
        );
    $arFilter = array("IBLOCK_ID" => 70, "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        if($arFields['PROPERTY_URL_VALUE']){
            $url = $_SERVER['HTTP_HOST'].$arFields['PROPERTY_URL_VALUE'];
        }elseif($arFields['PROPERTY_CUSTOM_URL_VALUE']){
            $url = $_SERVER['HTTP_HOST'].$arFields['PROPERTY_CUSTOM_URL_VALUE'];
        }else{
            continue;
        }
        $elements[] = [
            "date"=>date("Y-m-d\Th-i-s+3:00"),
            "id"=>$url,
        ];
    }

    $dom = new DOMDocument('1.0', 'utf-8');
    $urlset = $dom->createElement('urlset');
    $urlset->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');

    foreach($elements as $row) {
        // Дата изменения статьи.
        $date = $row['date'];

        $url = $dom->createElement('url');

        // Элемент <loc> - URL статьи.
        $loc = $dom->createElement('loc');
        $text = $dom->createTextNode(
            htmlentities('https://example.com/articles/' . $row['id'] . '.html', ENT_QUOTES)
        );
        $loc->appendChild($text);
        $url->appendChild($loc);

        // Элемент <lastmod> - дата последнего изменения статьи.
        $lastmod = $dom->createElement('lastmod');
        $text = $dom->createTextNode($date);
        $lastmod->appendChild($text);
        $url->appendChild($lastmod);

        $urlset->appendChild($url);
    }

    $dom->appendChild($urlset);

    $dom->save( $_SERVER['DOCUMENT_ROOT'].'/sitemaptest.xml');
    echo $dom->saveXML();
endif;
}
?>
