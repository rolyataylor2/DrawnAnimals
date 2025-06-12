<?php
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/TWIG-Var.php';

    if (isset($_POST['avatarid']) && isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $player = PLAYERCLASS::byMe();
        $player->Avatar($_POST['avatarid']);
        $player->_save();
        die('Reloading...<script>location.reload(true);</script>');
    }
    $_SESSION['token'] = uniqid();
?>
<h1>Choose an Avatar</h1>
<sub>Click an avatar to change your current Profile Avatar.</sub><br/><br/>
<?php for($i=0;$i<80;$i++): ?>
    <img style="cursor:pointer; margin:6px;" 
         width="64" 
         onclick="inlinePopupSubmit({'token':'<?php echo $_SESSION['token']; ?>','avatarid':<?php echo $i; ?>},'editProfileAvatar');" 
         src='img/profile_images/<?php echo $i; ?>.png'/>
<?php endfor; ?>