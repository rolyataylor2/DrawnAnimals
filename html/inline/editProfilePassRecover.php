<?php
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    $arguments=array();
    if (isset($_POST['username'])) {
        if (empty($_POST['password']) ||
            strlen($_POST['password']) < 5 ||
            preg_replace("/[^A-Za-z]/", '', $_POST['password']) === $_POST['password']) {
            $error = 'Please Enter A Valid Password, Must be at least 5 characters long and contain at least one number.';
        } elseif ($_POST['password'] !== $_POST['passwordconfirm']) {
            $error = 'Passwords do not match.';
        } elseif (empty($_POST['email']) ||
            strpos($_POST['email'], '@') === false) {
            $error = 'Please Enter A Valid Email, Must contain a @ symbol.';
        } else {
            $player = PLAYERCLASS::byUsername($_POST['username']);
            if (empty($player->Id())) {
                $error = 'Cannot find username/email.';
            } elseif (strcmp($player->Email(),$_POST['email']) === 0 || strpos($player->Email(),'@') === false ) {
                $player->Email($_POST['email']);
                $player->Password('',$_POST['password']);
                $player->_save();
                die('<h1>Password Recovery</h1><sub>Your Password has been reset, You can log in with your new password.</sub>');
            } else {
                $error = 'The email we have on record does not match the one provided...';
            }
            
        }
    }


?>
<h1>Password Recovery</h1>
<sub>
    If your account isnt old you will not be able to reset your password here.
    If your account is old, Enter your username, email on file, and your new password. Your account password will be reset.
</sub>
<form onsubmit="inlinePopupSubmit($(this),'editProfilePassRecover'); return false">
    <label>Username <input type='text' name='username'/></label>
    <label>Email <input type='text' name='email'/></label>
    <label>New Password <input type='text' name='password'/></label>
    <label>Confirm Password <input type='text' name='passwordconfirm'/></label>
    <label><input type='submit' value='Reset Password'/></label>
</form>