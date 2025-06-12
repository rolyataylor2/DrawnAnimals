<?php
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/TWIG-Var.php';

    if (isset($_POST['avatarid']) && isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $player = PLAYERCLASS::byMe();
        $player->AvatarOw($_POST['avatarid']);
        $player->_save();
        die('Reloading...<script>location.reload(true);</script>');
    }
    $_SESSION['token'] = uniqid();
?>
<h1>Choose an Avatar</h1>
<sub>Click an avatar to change your current Profile Avatar.</sub><br/><br/>
<?php for($i=1;$i<81;$i++): ?>
    <div class="tempAvatarContainer">
        <img onclick="inlinePopupSubmit({'token':'<?php echo $_SESSION['token']; ?>','avatarid':'<?php echo str_pad($i,2,'0',STR_PAD_LEFT); ?>'},'editProfileRpgOw');" 
            src='img/a/<?php echo str_pad($i,2,'0',STR_PAD_LEFT); ?>.png'/>
    </div>
<?php endfor; ?>
<style>
    .tempAvatarContainer {
        overflow:hidden; width:64px; height:64px; position:relative; margin:4px; display:inline-block;
    }
    .tempAvatarContainer:hover {
        outline:2px solid yellow;
    }
    .tempAvatarContainer img {
        position:absolute;
        top:-64px;
        left:0px;
    }
</style>