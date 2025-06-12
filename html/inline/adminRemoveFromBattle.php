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
include_once 'html/_php/class-monsters.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-updates.php';

if (!PLAYERCLASS::byMe()->isAdmin('userAdmin')) die('You are not a userAdmin!');
if (VerifyPostToken()) {
    $player = PLAYERCLASS::byId($_POST['userid']);
    if (empty($player->Id())) die('Unknown user id.');
    $player->BattleId(-1);
    $player->_save();
    die('<h1>Remove this user from battle?</h1><sub>User has been removed from battle.</sub>');
}
$player = PLAYERCLASS::byId($_GET['arguments'][0]);
$token = $_SESSION['token'] = uniqid();
?>
<h1>Remove this user from battle?</h1>
<sub>This user is currently in battle #<?php echo $player->BattleId(); ?></sub>
<a href="javascript:" onclick="inlinePopupSubmit({'userid':<?php echo $_GET['arguments'][0]; ?>,'token':'<?php echo $token; ?>'},'adminRemoveFromBattle');">Yes, Remove this user from battle.</a>