<?// require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>
<pre>
<?php

//https://app.abstractapi.com/api/email-validation/tester

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://emailvalidation.abstractapi.com/v1/?api_key=6079c49fa94a44a3b2c0c3dfe39d3571&email=frostt315@gmail.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$data = curl_exec($ch);
curl_close($ch);

var_dump(json_decode($data, true));
?>
<?// require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>
