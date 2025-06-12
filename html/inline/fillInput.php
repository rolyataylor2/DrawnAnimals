<?php

include_once 'include/Core/index.php';
include_once 'include/TWIG-Var.php';

$arguments = array();
if (!isset($_POST['o'])) { $_POST['o'] = 0; }
$_GET['o'] = $_POST['o'];
$offset = intval($_POST['o']);

if (!isset($_POST['type'])) $_POST['type'] = $_GET['arguments'][0];

$items = array();
switch ($_POST['type']) {
    case 'sfx':
        $items = array_slice(scandir('/var/www/html/sfx/battle'),intval($_POST['o'])+2,30);
        break;
    case 'battleimages':
        $items = array_slice(scandir('/var/www/images/b'),intval($_POST['o'])+2,30);
        break;
    case 'ailments':
        $items = array();
        foreach(CATALOGAILMENTCLASS::byAll() as $ii) {
            $items[] = $ii->Name();
        }
        break;
}


foreach($items as $i) {?>
    <a href="#" onclick="$(this).replaceWith('<audio controls><source src=\'http://PokeWorlds.com/sfx/battle/'+$(this).next().html()+'\' type=\'audio/ogg\'></audio>');">Load</a>
    <a href="#" onclick="codeEditorFillInput.val('<?php echo $i; ?>'); inlinePopupClose();"><?php echo $i; ?></a>
    <br/>
<?php } ?>
<div style="float:left; width:100%; text-align:center;">
    Page <?php echo $offset/30 ?>
    <?php if ($offset > 29): ?>
        <button type="button" style="float:left;" onclick="inlinePopupSubmit( {'o' : <?php echo $offset-30; ?>,'type':'<?php echo $_POST['type'];?>' },'fillInput');">Prev</button>
    <?php endif; ?>
    <button type="button" style="float:right;" onclick="inlinePopupSubmit( {'o' : <?php echo $offset+30; ?>,'type':'<?php echo $_POST['type'];?>'  },'fillInput');">Next</button>
</div>
