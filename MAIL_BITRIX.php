<?php
AddEventHandler("main", "OnBeforeEventSend", "SendSubscribeMail");
//AddEventHandler("main", "OnBeforeEventAdd", "SendSubscribeMail");
function SendSubscribeMail($arFields){
    //file_put_contents($_SERVER['DOCUMENT_ROOT']."/logmail.txt",print_r($arFields,true), FILE_APPEND);
    if(isset($arFields["SUBSCR_SECTION"]) && isset($arFields["EMAIL"])){

        file_put_contents($_SERVER['DOCUMENT_ROOT']."/logmail.txt","Ща отправим\n", FILE_APPEND);
        $email = $arFields["EMAIL"];
        //$email ="developer@di74.ru";
        $arEventFields = array(
            "USER_EMAIL" => $email
        );
        if(CEvent::Send("NEW_SUBSCRIBE", "s1", $arEventFields,"Y")){
            file_put_contents($_SERVER['DOCUMENT_ROOT']."/logmail.txt","отправлено\n\n", FILE_APPEND);
        }
    }
    return;
}
?>
