<?php
/*
 * Copyright (c) 2014 User.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    User - initial API and implementation and/or initial documentation
 * 

 */
session_start();
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-messages.php';

$arguments = array();
$arguments['SENDTO'] = (!isset($_GET['arguments'][0]) ? (!isset($_POST['sendTo']) ? '' : $_POST['sendTo']) : $_GET['arguments'][0]);
$arguments['MESSAGE'] = (!isset($_GET['arguments'][1]) ? (!isset($_POST['message']) ? '' : $_POST['message']) : $_GET['arguments'][1]);
$arguments['ERROR'] = '';

if (isset($_POST['token']) && strcmp($_POST['token'], $_SESSION['token']) === 0) {
    $to = (is_numeric($_POST['sendTo']) ? PLAYERCLASS::byId($_POST['sendTo']) : PLAYERCLASS::byUsername($_POST['sendTo']) );
    if ($to->Id() !== 0) {
        $from = PLAYERCLASS::byMe();
        $message = $_POST['message'];
        if (strlen($message) > 2) {
            MESSAGECLASS::byNew($from->Id(), $to->Id(), $message);
            die('Your message has been sent!');
        } else $arguments['ERROR'] = '<sub>Needs more content</sub>';
    } else $arguments['ERROR'] = '<sub>User not found</sub>';
    
}
$_SESSION['token'] = uniqid();
?>
<?php echo $arguments['ERROR']; ?>
<form onsubmit="inlinePopupSubmit($(this), 'messageSend'); return false;">
    <input type="hidden" name="token" value='<?php echo $_SESSION['token']; ?>'/>
    <label>Send To:<input name="sendTo" type="text" value='<?php echo $arguments['SENDTO']; ?>'/></label>
    <label>Content<textarea name="message"><?php echo $arguments['MESSAGE']; ?></textarea></label>
    <label><input type="submit"/></label>
</form>

