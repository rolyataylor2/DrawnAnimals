<?php
if (!LoggedIn()) {
    die('<script>window.location.href="http://PokeWorlds.com/register.php";</script>');
}
include_once 'html/_php/class-items.php';
include 'html/_php/class-html-code-table.php';
function SaveItemData($item) {
    if ($item->UserId() !== PLAYERCLASS::byMe()->Id()) die('You do not have permissions to edit this item.');
//    if ($item->Permissions(PLAYERCLASS::byMe(),'w')===false) die('You do not have permissions to edit this item.');
    $item->Name($_POST['name']);
    $item->Description($_POST['description']);
    $item->_save();
}
if (isset($_GET['id'])) {
    if ($_GET['id'] == -1) {
        if (VerifyPostToken()) {
            $item = CREATEITEMCLASS::byNew(PLAYERCLASS::byMe()->Id(),$_POST['name']);
            SaveItemData($item);
            die('<script>history.go(-2);</script>');
        }
    }
    
    $item = CREATEITEMCLASS::byId($_GET['id']);
    $item->_load();
    if (VerifyPostToken()) {
        SaveItemData($item);
        die('<script>history.go(-2);</script>');
    }
    
    $table = HTMLCODETABLECLASS::byNew('evolutionCode');
    $table->createFunction('Heal Target')
            ->addTitle('Heal Target')
            ->addText('Heal the target by ')
            ->addInput(100)
            ->addSelect(['Hp','Percent'])
            ->addCodeTranslation(function($argument) {
                return '$target->Hp('.intval($argument[0]).');';
            });
    
    $arguments['CODETABLE'] = $table->renderHTML();
    
    $arguments['ITEM'] = (isset($item->data)?$item->data:array());
    $arguments['EDIT'] = ($item->UserId() === PLAYERCLASS::byMe()->Id());
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
}
