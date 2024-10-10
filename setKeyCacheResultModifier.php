if ($arResult['UF_NAMES_PRODUCTS'])
{
    $component->arResult['UF_NAMES_PRODUCTS'] = $arResult['UF_NAMES_PRODUCTS'];
}

$component->SetResultCacheKeys(
	array(
		'AJAX_ID',
		'BACKGROUND_COLOR',
		'UF_NAMES_PRODUCTS',
	)
);
