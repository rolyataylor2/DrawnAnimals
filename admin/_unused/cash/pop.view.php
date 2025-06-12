<?php
    include_once 'include/PLYR-Class.php';
    include_once 'include/ITEM-Class.php';
    include_once 'include/TWIG-Var.php';
    
    $arguments = array();
    $shop = new SHOPCLASS($_GET['sid']);
    $item = $shop->Item($_GET['id']);
    $base = new ITEMBASECLASS($item['type']);
    $arguments['QUANTITY'] = 1;
    $arguments['ITEM'] = $item;
    $arguments['PLYR'] = array('cash'=>PLYR()->Cash());
    $arguments['DESCRIPTION'] = $base->Description();
    $arguments['SID'] = $shop->_var('id');
    Tracking('Cash Shop - View Item',1,'Cash Shop Item Viewed',$base->Name());
    
    if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        if (isset($_POST['agree'])) {
            if (PLYR()->Cash() >= $item['price']*intval($_POST['quantity'])) {
                if ($item['quantity'] > 0) {
                    $purchase = new TRANSACTIONCLASS(-1,PLYR()->id, 'Purchased '.$_POST['quantity'].' '.ucwords($base->Name()), 0, -$item['price']*$_POST['quantity']);
                    while($_POST['quantity']--) {
                        PLYR()->Cash(-$item['price']);
                        PLYR()->Inventory()->Add($item['type']);
                        $shop->Remove($item['type'],1);
                    }
                    $arguments['SCRIPT_OUTPUT'] = 'ID ('.$purchase->_var('id').')';
                } else {
                    $arguments['SCRIPT_ERROR'] = 'This item just sold out.';
                }
            } else {
                $arguments['SCRIPT_ERROR'] = 'You do not have enough Drawnimals Dollars to purchase this item.';
            }
        } else {
            $arguments['SCRIPT_ERROR'] = 'You must agree to the purchase terms to make a purchase.';
        }
        $arguments['QUANTITY'] = $_POST['quantity'];
    }
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
    

    $twigfile = str_replace('/var/www/html/ajax/', '', str_replace('.php', '.twig', __FILE__));
    echo TWIG()->render($twigfile, $arguments);
