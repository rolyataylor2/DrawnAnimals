<?php
if (!isset($_GET['p']))  $_GET['p'] = 'index';
$_GET['g'] = str_replace('.','',$_GET['g']);
$_GET['p'] = str_replace('.','',$_GET['p']);
if (file_exists('/var/www/html/_templates/_locations/' . $_GET['g'] . '/'.$_GET['p'].'.php')) {
    include '/var/www/html/_templates/_locations/' . $_GET['g'] . '/'.$_GET['p'].'.php';
    return TWIG()->render('/html/_templates/_locations/' . $_GET['g'] . '/_t/'.$_GET['p'].'.twig', $arguments);
} else  {
    return 'Invalid Location';
}

