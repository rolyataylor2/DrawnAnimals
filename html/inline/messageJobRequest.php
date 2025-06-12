<?php

session_start();
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-messages.php';
include_once 'include/recaptchalib.php';

if (isset($_POST['token']) && strcmp($_POST['token'], $_SESSION['token']) === 0) {
    if (strlen($_POST['name']) < 2) die('Error: Invalid Name');
    if (strlen($_POST['email']) < 10 || strpos($_POST['email'],'@') === false) die('Error: Invalid Email');

    $privatekey = "6LeivdYSAAAAAO0IG8h-JuHD8CIcfy9R-UORKCRv";
    $resp = recaptcha_check_answer ($privatekey,
                                  $_SERVER["REMOTE_ADDR"],
                                  $_POST["recaptcha_challenge_field"],
                                  $_POST["recaptcha_response_field"]);
    if ($resp->is_valid) {
        $message = 'JR FROM: ';
        $message .= $_POST['name'].' ('.$_POST['expertise'].') '.' - ';
        $message .= $_POST['email'].';\r\n';
        $message .= $_POST['projecturl'].'\r\n';
        $message .= $_POST['description'].'\r\n';
        MESSAGECLASS::byNew(0, 11, $message);
        die('Your message has been sent!');
    }
    die('Error: Invalid Captcha');
    
}