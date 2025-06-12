<?php
    /** Needed Includes */
    require_once 'PLYR-Class.php';
    require_once 'ITEM-Class.php';
    
    /** GET/POST Handling */
    (isset($_GET['id']) ? $_GET['id'] = intval($_GET['id']) : die('Invalid ID'));
    $item = new ITEMCLASS($_GET['id']);
    if (isset($_GET['token']) && $_GET['token'] == $_SESSION['token']) {
        $script_output = eval($item->ScriptExecute());
    }
    
    /** Template Class */
    echo TWIG()->renderFind(__FILE__,array(  "NAME" => ucwords($item->Name()),
                             "IMAGE" => '<img src="'.$item->Name().'" alt="Item Image"/>',
                             "DESCRIPTION" => $item->Description(),
                             "SELECT_SCRIPT" => $item->ScriptSelect(),
                             "SCRIPT_OUTPUT" => $script_output
                           ));
?>
