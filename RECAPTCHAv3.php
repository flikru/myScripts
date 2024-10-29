<script src='https://www.google.com/recaptcha/api.js?render=6Lf5Y24qAAAAAIUxeqXKFxuGIGM6nvCRHu6C17LI' async></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var reCAPTCHA_site_key='6Lf5Y24qAAAAAIUxeqXKFxuGIGM6nvCRHu6C17LI';
        function reCAPTCHA_execute () {
            console.log("ЗАГРУЖАЮ ГУГЛКАПЧУУУУУ")
            grecaptcha.execute(reCAPTCHA_site_key, { action: 'submit' }).then(function (token) {
                console.log("ЗАГРУЖАЮ ГУГЛКАПЧУУУУУ")
                $('form').append(
                    $('<input type="hidden">').attr({
                        name: 'g-recaptcha-response',
                        value: token
                    })
                );
            }, function (reason) {
                console.log(reason);
            });
        }
        if (typeof grecaptcha !== 'undefined' && typeof reCAPTCHA_site_key !== 'undefined') {
            grecaptcha.ready(reCAPTCHA_execute);
            setInterval(reCAPTCHA_execute, 60000);
        }
    });
</script>
<?
function checkgRecaptcha($grecaptcharesponse){
    //$_POST['g-recaptcha-response'] == $grecaptcharesponse
    $res=false;
    $secret_key_v3 = '6Lf5Y24qAAAAANALTFAO_FVWhF2xxBpAHxMVQf4j';
    $query = "https://www.google.com/recaptcha/api/siteverify" . '?secret=' . $secret_key_v3 . '&response=' . $grecaptcharesponse . '&remoteip=' . $_SERVER['REMOTE_ADDR'];
    $response = file_get_contents($query);

    $data = json_decode($response,true);
    if($data["success"]==false){
        $res = false;
    }else{
        $res = true;
    }
    return $data["success"]==false?false:true;
}
?>
