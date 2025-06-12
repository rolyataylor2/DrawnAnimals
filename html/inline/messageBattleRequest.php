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
 */
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-battle.php';
include_once 'html/_php/class-monsters.php';
include_once 'html/_php/class-battle-request.php';

if (isset($_GET['arguments'])) {
    $player = PLAYERCLASS::byUsername($_GET['arguments'][0]);
} else { $player = PLAYERCLASS::byId($_POST['uid']); }

BATTLEREQUESTCLASS::doClearExpired($player->Id(),PLAYERCLASS::byMe()->Id());
/////// ALREADY SENT A REQUEST
$requestMe = BATTLEREQUESTCLASS::byUserIdByRequestIdByActive($player->id(),PLAYERCLASS::byMe()->Id());
if (!empty($requestMe->Id())) { 
    if (VerifyPostToken()) {
        $requestMe->_delete();
        die('<h1>Send Battle Request</h1><sub>Your request has been withdrawn</sub>');
    }
    $token = $_SESSION['token'] = uniqid();
?>
    <h1>Send Battle Request</h1>
    <sub>You have already sent a active battle request to this person.<br/>The current request expires in: <?php echo ceil((300-(Time()-$requestMe->Time()))/60); ?> Minutes</sub>
    <a href='javascript:' onclick='inlinePopupSubmit({"uid":"<?php echo $player->Id(); ?>","token":"<?php echo $token; ?>"},"messageBattleRequest");'>Withdrawl Request...</a>
<?php
    die();
}


///// RECIEVED A REQUEST
$currentRequest = BATTLEREQUESTCLASS::byUserIdByRequestIdByActive(PLAYERCLASS::byMe()->Id(),$player->id());
if (!empty($currentRequest->Id())) {
    if (VerifyPostToken()) {
        if ($_POST['action'] == 1) {
            $currentRequest->_delete();
            die('<h1>Send Battle Request</h1><sub>The other users request has been denied.</sub>');
        } else {
            // BATTLE BEGIN
            $Player1 = PLAYERCLASS::byId($currentRequest->UserId());
            $Player2 = PLAYERCLASS::byId($currentRequest->UserFrom());
            $battle = BATTLECLASS::byNew();
            $battle->SetupAddPlayer($Player1);
            $battle->SetupAddPlayer($Player2);
            $battle->SetupFinalize();
            $currentRequest->_delete();
            die('<h1>Send Battle Request</h1><sub>Your battle has begun! Close this popup to begin.</sub><script>inlinePopupClose = function() {window.location.href="/location.php";}</script>');
        }
    }
    $token = $_SESSION['token'] = uniqid();
?>
    <h1>Send Battle Request</h1>
    <sub>You have been challanged to a battle!</sub>
    <a href='javascript:' onclick='inlinePopupSubmit({"action":0,"uid":"<?php echo $player->Id(); ?>","token":"<?php echo $token; ?>"},"messageBattleRequest");'>Battle Other Trainer!</a>
    <a href='javascript:' onclick='inlinePopupSubmit({"action":1,"uid":"<?php echo $player->Id(); ?>","token":"<?php echo $token; ?>"},"messageBattleRequest");'>Deny Request</a>
<?php
    die();
}


/// NO REQUESTS
if (VerifyPostToken()) {
    BATTLEREQUESTCLASS::byNew($player->Id());
    die('<h1>Send Battle Request</h1><sub>Your request has been made, This request will expire in 5 Minutes</sub>');
}
$token = $_SESSION['token'] = uniqid();
?>

<h1>Send Battle Request?</h1>
<sub>Battle requests are valid for 5 minutes, After that you will have to send another one.</sub>
<a href="javascript:" onclick='inlinePopupSubmit({"uid":"<?php echo $player->Id(); ?>","token":"<?php echo $token; ?>"},"messageBattleRequest");'>Send Battle Request!</a>