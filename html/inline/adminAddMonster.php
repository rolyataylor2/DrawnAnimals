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
    if (empty($player->Id())) die('Invalid Player ID.');
    $monster = CREATEMONSTERCLASS::bySpecies($_POST['species'])[0];
    if (empty($monster->Id())) die('Invalid monster name.');
    MONSTERCLASS::byNew($player,$monster,intval($_POST['level']));
    die('Successfully added');
}
$token = $_SESSION['token'] = uniqid();
?>
<h1>Give User A Monster</h1>
<form onsubmit="inlinePopupSubmit($(this),'adminAddMonster'); return false;">
    <input type='hidden' name="userid" value="<?php echo $_GET['arguments'][0]; ?>"/>
    <input type='hidden' name="token" value="<?php echo $token; ?>"/>
    <label>
        <b>Species</b>
        <input type='text' name="species"/>
        <sub>Enter the name in lowercase, if it can't be found it will not work.</sub>
    </label>
    <label>
        <b>Level</b>
        <input type='text' name="level" value="5"/>
        <sub>Enter the level that the Monster will be, Level 1 will be an egg (which is the preferred way to give out monsters)</sub>
    </label>
    <label><input type="submit" value="Create Monster"/></label>
</form>