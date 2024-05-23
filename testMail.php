<?php 
    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );
    $from = "emailtest@YOURDOMAIN";
    //$to = "frostt315@gmail.com";
    $to = "developer@di74.ru";
    $subject = "PHP Mail Test script";
    $message = "This is a test to check the PHP Mail functionality";
    $headers = "From:" . $from;
   var_dump(mail($to,$subject,$message, $headers));
    echo "Test email sent";
?>
