<?php
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/TWIG-Var.php';
    
    if (isset($_POST['gender']) && isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $player = PLAYERCLASS::byMe();
        $player->Gender($_POST['gender']);
        $player->_save();
        die('Reloading... <script>location.reload(true);</script>');
    }
    $_SESSION['token'] = uniqid();
?>
<h1>Choose Your Gender</h1>
<sub>Click One of the following options.<br/>There are no wrong choices.</sub><br/><br/>
<div style="width:306px; margin:0 auto;">
    <a href="javascript:" class="genderchoice" onclick="inlinePopupSubmit({'token':'<?php echo $_SESSION['token']; ?>','gender':0},'editProfileGender');">
        <img src="http://PokeWorlds.com/img/gender0.png"/> Male</a>
    <a href="javascript:" class="genderchoice" onclick="inlinePopupSubmit({'token':'<?php echo $_SESSION['token']; ?>','gender':1},'editProfileGender');">
        <img src="http://PokeWorlds.com/img/gender1.png"/>Female</a>
    <a href="javascript:" class="genderchoice" onclick="inlinePopupSubmit({'token':'<?php echo $_SESSION['token']; ?>','gender':2},'editProfileGender');">
        <img src="http://PokeWorlds.com/img/gender2.png"/>Unknown</a>
</div>