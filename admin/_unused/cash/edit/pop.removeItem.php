<?php
    include_once 'include/TWIG-Var.php';
    $arguments=array();
    $twigfile = str_replace('/var/www/html/ajax/','',str_replace('.php','.twig',__FILE__));
    echo TWIG()->render($twigfile,$arguments);