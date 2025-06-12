<?php
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/class-items.php';
    include_once 'html/_php/class-messages.php';
    if (isset($_POST['token']) && strcmp($_SESSION['token'],$_POST['token']) === 0) {
        $message = MESSAGECLASS::byId($_POST['id']);
        if (isset($_POST['remItem'])) {
            $item = ITEMCLASS::byId($message->Item());
            $item->UserId($message->To());
            $item->_save();
            $message->Item(0);
            $message->_save();
            die('Reloading... <script>location.reload(true);</script>');
        } else {
            MESSAGECLASS::byNew($message->To(),$message->From(),$_POST['reply']);
            die('Reply Sent');
        }
    }
    $message = MESSAGECLASS::byId($_GET['arguments'][0]);
    $user = PLAYERCLASS::byId($message->From());
    $token = $_SESSION['token'] = uniqid();

?>
<h1>Message From <?php echo $user->Username(); ?></h1>
<?php echo $message->Message(); ?>
<br/>
<?php if ($message->Item() !== 0): ?>
    <button type='button' onclick="inlinePopupSubmit({id:<?php echo $message->Id(); ?>,token:'<?php echo $token; ?>',remItem:true},'messageView');">Remove Attached Item</Button>
<?php endif; ?>
<br/><br/>
<form onsubmit="inlinePopupSubmit($(this),'messageView'); return false;">
    <input type='hidden' name='id' value='<?php echo $message->Id(); ?>'/>
    <input type='hidden' name='token' value='<?php echo $token; ?>'/>
    <textarea name="reply"></textarea>
    <input type='submit' value='Send Reply'/>
</form>

