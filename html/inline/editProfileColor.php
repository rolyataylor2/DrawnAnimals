<?php
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    $player = PLAYERCLASS::byMe();
    if (VerifyPostToken()) {
        $player->Color($_POST['color']);
        $player->_save();
        die('Reloading... <script>location.reload(true);</script>');
    }
    $_SESSION['token'] = uniqid();
    $colors = [];
    $colors[] = '#3399FF';
?>
<h1>Choose Your color!</h1>
<?php for($i=0;$i<180;$i++): ?>
    <?php $color = hsl2Rgb($i*2,0.67,0.60); ?>
    <?php $color = rgb2Hex($color[0],$color[1],$color[2]); ?>
    <a href="javascript:" style="background-color:<?php echo $color; ?>; width:24px; height:24px; margin:2px; padding:0px; display:inline-block; box-shadow:2px 2px 2px black;" onclick="inlinePopupSubmit({'token':'<?php echo $_SESSION['token']; ?>',
                                                  'color':'<?php echo $color; ?>'},'editProfileColor');"></a>
<?php endfor; ?>
<br/><br/>
<sub style="clear:both;">Or enter it manually</sub>
<form onsubmit="inlinePopupSubmit($(this),'editProfileAboutMe'); return false;">
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>"/>
    <label><input name="color" value="<?php echo $player->Color(); ?>"/></label>									
		
    <input type="submit" value="Save"/>
</form>
