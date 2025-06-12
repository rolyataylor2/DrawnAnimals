<?php

    include_once 'include/TWIG-Var.php';
    
    $twigfile = str_replace('/var/www/html/content/','',str_replace('.php','.twig',__FILE__));
    echo TWIG()->render($twigfile);
