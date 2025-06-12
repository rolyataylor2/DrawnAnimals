<?php

    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/class-regions.php';
    
    $player = PLAYERCLASS::byMe();
    if (isset($_POST['name']) && isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $region = CREATEREGIONCLASS::byId($_POST['id']);
        if (PLAYERCLASS::byMe()->Id() === $region->UserId()) {
            $region->Name($_POST['name']);
            $region->_save();
            die('Reloading... <script>location.reload(true);</script>');
        }
    }
    $region = CREATEREGIONCLASS::byId($_GET['arguments'][0]);
    $_SESSION['token'] = uniqid();

?>
<form onsubmit='inlinePopupSubmit($(this),"Regions/regionEdit"); return false;'>
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>"/>
    <input type="hidden" name="id" value="<?php echo $region->Id(); ?>"/>
    <label>Name<input type='text' name='name' value='<?php echo $region->Name(); ?>'/></label>
    <input type="submit" value="Save"/>
</form>