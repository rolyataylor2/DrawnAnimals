<?php
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/TWIG-Var.php';
    
    if (isset($_POST['month']) && isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $player = PLAYERCLASS::byMe();
        $time = strtotime($_POST['month'].'/'.$_POST['day'].'/'.$_POST['year']);
        if ($time !== false) {
            $player->Birthday($time);
            $player->_save();
            die('Reloading... <script>location.reload(true);</script>');
        } else echo '<div class="error">Could not read the date provided</div>';
    }
    $_SESSION['token'] = uniqid();
?>
<h1>What is your birthday?</h1>
<sub>Gameplay can be affected depending on your choice.</sub>
<form onsubmit="inlinePopupSubmit($(this),'editProfileBirthday'); return false;">
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>"/>
    <select name="month">
        <option value="1">January</option>
        <option value="2">February</option>
        <option value="3">March</option>
        <option value="4">April</option>
        <option value="5">May</option>
        <option value="6">June</option>
        <option value="7">July</option>
        <option value="8">August</option>
        <option value="9">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
    </select>
    <select name="day">
        <?php for($i=1;$i<32;$i++): ?>
            <option><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
    <select name="year">
        <?php for($i=1969;$i<2014;$i++): ?>
            <option><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
    <input type="submit" value="Save"/>
</form>