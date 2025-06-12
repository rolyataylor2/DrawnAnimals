<?php
require_once 'include/Twig/Autoloader.php';
Twig_Autoloader::register();

$TWIG_LANGUAGE_DIR = '/forums/';
$TWIG_TEMPLATE_DIRECTORY = '/var/www/templates';

$GLOBALS['TWIGLOADER'] = new Twig_Loader_Filesystem($TWIG_TEMPLATE_DIRECTORY.$TWIG_LANGUAGE_DIR);
$GLOBALS['TWIG'] = new Twig_Environment($GLOBALS['TWIGLOADER']);

function TWIG() {return $GLOBALS['TWIG'];}
