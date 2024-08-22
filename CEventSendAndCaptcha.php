<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
//Tkachenko_N@privod.ru
global $APPLICATION;

$error=[];
if ($_REQUEST['g-recaptcha-response']) {
    $httpClient = new \Bitrix\Main\Web\HttpClient;
    $result = $httpClient->post(
        'https://www.google.com/recaptcha/api/siteverify',
        array(
            'secret' => '6LcpDj4UAAAAADSWJPKXPZ6nOOnTHvZKafJls-vY',
            'response' => $_REQUEST['g-recaptcha-response'],
            'remoteip' => $_SERVER['HTTP_X_REAL_IP']
        )
    );
    $result = json_decode($result, true);
    if ($result['success'] !== true) {
        $response["error"][]="captcha";
        $response["error"][]="captcha2";
        echo json_encode($response);
        return false;
    }
} else {
    $response["error"][]="captcha";
    $response["error"][]="captcha2";
    echo json_encode($response);
    return false;
}

//Array ( [name] => [company] => [email] => [idbook] => 5968 [titlebook] => Электрические двигатели [type_button] => buy )
foreach ($_GET as $key => $get){
    if(empty($get) && $key != 'g-recaptcha-response'){
        $response['error'][]=$key;
        echo json_encode($response);
        return false;
    }
    $data[$key] = trim(htmlspecialchars($get));
}

$type=($data['type_button']=="buy")? "Покупка бумажной версии книги" : "Скачать бесплатно PDF";

$arEventFields = array(
    "NAME" => $data['name'],
    "COMPANY" => $data['company'],
    "EMAIL" => $data['email'],
    "ID_BOOK" => $data['idbook'],
    "TITLEBOOK" => $data['titlebook'],
    "TYPE" => $type,
);

if(CEvent::Send("EVENT_BOOK_BUY", "s1", $arEventFields,"Y")){
    $response["success"]=1;
}

echo json_encode($response);
return false;

?>
