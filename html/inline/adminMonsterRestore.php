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
    $monster = MONSTERCLASS::byId($_POST['mid']);
    if (empty($monster->Id())) die('Unknown Monster id.');
    switch($_POST['action']) {
        case 0:
            $monster->Hp(9999999);
            break;
        case 1:
            $monster->Ailments()->Reset();
            break;
        case 2:
            $monster->MovePP(0,1000);
            $monster->MovePP(1,1000);
            $monster->MovePP(2,1000);
            $monster->MovePP(3,1000);
            break;
    }
    $monster->_save();
    die('<h1>Restore this Monster?</h1><sub>Monster has been restored.</sub>');
}
$monster = MONSTERCLASS::byId($_GET['arguments'][0]);
$token = $_SESSION['token'] = uniqid();
?>
<h1>Restore this Monster?</h1>
<sub>Current Monster Has Id: <?php $monster->id(); ?></sub>
<a href="javascript:" onclick="inlinePopupSubmit({'action':0,'mid':<?php echo $_GET['arguments'][0]; ?>,'token':'<?php echo $token; ?>'},'adminMonsterRestore');">Restore Health</a>
<a href="javascript:" onclick="inlinePopupSubmit({'action':1,'mid':<?php echo $_GET['arguments'][0]; ?>,'token':'<?php echo $token; ?>'},'adminMonsterRestore');">Restore Ailments</a>
<a href="javascript:" onclick="inlinePopupSubmit({'action':2,'mid':<?php echo $_GET['arguments'][0]; ?>,'token':'<?php echo $token; ?>'},'adminMonsterRestore');">Restore PP</a>