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
include_once 'html/_php/class-monsters.php';
include_once 'html/_php/class-monsters-learnset.php';
$sfxscript = "<script>$(game.sound.sounds['bgm']).animate({volume: 0}, 500,function() {game.sound.play('sfx/itemlevel.wav'); $(game.sound.sounds['bgm']).delay(1000).animate({volume: 0.5}, 500);});</script>";
if (VerifyPostToken()) {
    $monster = MONSTERCLASS::byId($_POST['id']);
    $movelearn = $monster->Move(4);
    if ($_POST['forget'] == 4) {
        $monster->Move(4,-1);
        $monster->_save();
        die('<h1>Learning Move</h1><sub>'.$monster->Nickname().' gave up learning '.$movelearn->Name().'.</sub>');
    }
    $moveforget = $monster->Move($_POST['forget']);
    $monster->Move($_POST['forget'],$movelearn->Id());
    $monster->Move(4,-1);
    $monster->_save();
    die('<h1>Learned Move</h1><sub>1... 2... 3... poof! '.$monster->Nickname().' forgot "'.$moveforget->Name().'" and Learned "'.$movelearn->Name().'"</sub>'.$sfxscript);
}
$monster = MONSTERCLASS::byId($_GET['arguments'][0]);
$moves = [];
$moves[] = $monster->Move(0);
$moves[] = $monster->Move(1);
$moves[] = $monster->Move(2);
$moves[] = $monster->Move(3);
foreach($moves as $index=>$i) {
    if (empty($i->Id())) {
        $movelearn = $monster->Move(4);
        $monster->Move($index,$monster->Move(4)->Id());
        $monster->Move(4,-1);
        $monster->_save();
        die('<h1>Learned Move</h1><sub>'.$monster->Nickname().' has learned "'.$movelearn->Name().'"!</sub>'.$sfxscript);
    }
}
$moves[] = $monster->Move(4);

$token = $_SESSION['token'] = uniqid();
?>
<h1>Learning Move</h1>
<?php 
    if (empty($monster->Id())) {
        die('<sub>No Drawnimal Selected</sub>');
    }
    if (empty($moves[4]->Id())) {
        die('<sub>Your '.$monster->Nickname().' is not learning a move right now????</sub>');
    }
?>
<sub>
    Your <?php echo $monster->Nickname().'('.$monster->Id().')'; ?> wants to learn <?php echo $moves[4]->Name(); ?> but it cannot learn more then 4 moves.<br/>
    Select a move that you want to forget.
</sub>
<form onsubmit='inlinePopupSubmit($(this),"monsterLearnMove"); return false;'>
    <input type='hidden' name='token' value='<?php echo $token; ?>'/>
    <input type='hidden' name='id' value='<?php echo $monster->Id(); ?>'/>
    <?php foreach($moves as $index=>$i): ?>
        <label><input type='radio' name='forget' value='<?php echo $index; ?>'/><?php echo $i->Name(); ?></label>
    <?php endforeach; ?>
    <label><input type='submit' value='Forget Selected Move'/></label>
        
</form>

    