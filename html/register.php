<?php
    session_start();
    include_once '_php/mysqli.php';
    include_once '_php/class-player.php';
    include_once '_php/class-updates.php';
    include_once '_php/TWIG-Var.php';
    include_once '_php/recaptchalib.php';
 
    /**
     * Variables
     */
    $arguments = array();
    
    
    include_once 'html/_templates/_plugin/notifications.php';
    
    if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        if (empty($_POST['username']) ||
            strlen($_POST['username']) < 5 ||
            strlen($_POST['username']) > 12 ||
            preg_replace("/[^A-Za-z0-9]/", '', $_POST['username']) !== $_POST['username']) {
            $arguments['SCRIPT_ERROR'] = 'Please Enter A Valid Username. Must be between 5-12 characters long and cannot contain any non-alphanumeric characters (A-Z1-9 no_spaces).';
        } elseif (empty($_POST['password']) ||
            strlen($_POST['password']) < 5 ||
            preg_replace("/[^A-Za-z]/", '', $_POST['password']) === $_POST['password']) {
            $arguments['SCRIPT_ERROR'] = 'Please Enter A Valid Password, Must be at least 5 characters long and contain at least one number.';
        } elseif ($_POST['password'] !== $_POST['passwordconfirm'] ||
            preg_replace("/[^A-Za-z]/", '', $_POST['passwordconfirm']) === $_POST['passwordconfirm']) {
            $arguments['SCRIPT_ERROR'] = 'Passwords do not match';
        } elseif (empty($_POST['email']) ||
            strpos($_POST['email'], '@') === false) {
            $arguments['SCRIPT_ERROR'] = 'Please Enter A Valid Email, Must contain a @ symbol.';
        } elseif ($_POST['email'] !== $_POST['emailconfirm']) {
            $arguments['SCRIPT_ERROR'] = 'Emails did not match';
        } elseif (empty($_POST['agree'])) {
            $arguments['SCRIPT_ERROR'] = 'Please Agree to the Site Rules';
        } else {
            $privatekey = "6LeivdYSAAAAAO0IG8h-JuHD8CIcfy9R-UORKCRv";
            $resp = recaptcha_check_answer ($privatekey,
                                          $_SERVER["REMOTE_ADDR"],
                                          $_POST["recaptcha_challenge_field"],
                                          $_POST["recaptcha_response_field"]);
            if (!$resp->is_valid) {
              $script_output = 'Invalid Captcha';
            } else {
                $player = PLAYERCLASS::byNew($_POST['username'], $_POST['password'], $_POST['email']);
                UPDATECLASS::byNew($player->Id(),'',8);
                $exp = PLAYERCLASSEXPERIENCE::byNew($player->Id());
                $exp->Coins(2000);
                $exp->Cash(150);
                $exp->_save();
                $player->AvatarOw('01');
                $player->_save();
                MESSAGECLASS::byNew(11,$player->Id(),'<h1>Hi and welcome to Drawnimals</h1><br/><br/>This site is still in beta, and is not very functional. Thanks for testing tho and there will be more to come later on!');
                MONSTERCLASS::byNew($player->Id(),CREATEMONSTERCLASS::byId(148),4);
                $arguments['SCRIPT_OUTPUT'] = 'Account Created Successfully, Please login to confirm your account.';
            }

        }
    }
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
    if (isset($_POST) && isset($_POST['username']) && isset($_POST['email'])) {
        $arguments['FORMUSERNAME'] = $_POST['username'];
        $arguments['EMAIL'] = $_POST['email'];
    }
    
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    $arguments['TOOLBAR'] = include '_templates/_plugin/toolbar.php';
    $arguments['BODY'] = TWIG()->render('/html/_templates/register.twig', $arguments);
    
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);