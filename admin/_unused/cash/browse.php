<?php
    include_once 'include/ITEM-Class.php';
    include_once 'include/TWIG-Var.php';
    Tracking('Cash Shop - Index');
    $arguments=array();
    $shop = new SHOPCLASS(1);
    $arguments['SID'] = $shop->_var('id');
    $arguments['NAME'] = $shop->Name();
    $arguments['GREETING'] = $shop->Greeting();
    $arguments['CURRENCY'] = '<img src="img/sit/'.( $shop->Currency() == 0 ? 'coin.png':'dollar.png').'"/>';
    $arguments['ITEMS'] = $shop->Inventory();
    $arguments['CATAGORIES'] = $shop->Catagories();
    $twigfile = str_replace('/var/www/html/ajax/','',str_replace('.php','.twig',__FILE__));
    echo TWIG()->render($twigfile,$arguments);