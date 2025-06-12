<?php
session_start();
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-regions.php';

if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
    $region = CREATEREGIONCLASS::byId($_POST['itemId']);
    if ($region->UserId() === PLAYERCLASS::byMe()->Id()) {
        $region->_delete();
        die('Reloading... <script>location.reload(true);</script>');
    }
    die('You are not the creator of this region...');
}

$token = $_SESSION['token'] = uniqid();
?>
<h1>Delete This REGION?!?!?!?</h1>
<sub>Are you sure you want to remove this monster from the global dex and delete all data associated with it (THINK OF THE PEOPLE)?</sub><br/>
<button type="button" onclick="inlinePopupSubmit({token:'<?php echo $token; ?>', itemId:<?php echo $_GET['arguments'][0]; ?>},'Regions/regionDelete')">
    Ok, Delete this region
</button>