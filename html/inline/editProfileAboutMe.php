<?php
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    $player = PLAYERCLASS::byMe();
    if (isset($_POST['aboutme']) && isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $player->AboutMe($_POST['aboutme']);
        $player->_save();
        die('Reloading... <script>location.reload(true);</script>');
    }
    $_SESSION['token'] = uniqid();
?>
<h1>Talk a little about yourself</h1>
<form onsubmit="inlinePopupSubmit($(this),'editProfileAboutMe'); return false;">
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>"/>
    <label>About You:<br/><textarea name="aboutme"><?php echo $player->AboutMe(); ?></textarea></label>									
    <label><input type="submit" value="Save"/></label>
</form>