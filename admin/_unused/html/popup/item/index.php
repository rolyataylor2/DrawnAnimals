<?php
    // Gather Information
    $item = new ITEMCLASS($_GET['id']);
    
    // Execute Item Code
    if (isset($_POST['token'])) {
        if ($_POST['token'] === $_SESSION['token']) {
            $script_output = eval($item->ScriptExecute());
        } else {
            $script_output = "Invalid Token";
        }
    } else {
        $script_output = $item->ScriptSelect();
    }
    
    // Process view
    $_SESSION['token'] = uniqid();
    echo TWIG()->render('popup/item/index.twig',array( "TOKEN" => $_SESSION['token'],
                                                       "NAME" => $item->Name(),
                                                       "IMAGE" => 'img/items/'.$item->Name().'.png',
                                                       "DESCRIPTION" => $item->Description(),
                                                       "CATAGORY" => $item->Catagory(),
                                                       "SCRIPT_OUTPUT" => $script_output));