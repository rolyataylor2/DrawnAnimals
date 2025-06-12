<?php
require_once 'include/Twig/Autoloader.php';
Twig_Autoloader::register();

$TWIG_LANGUAGE_DIR = '';
$TWIG_TEMPLATE_DIRECTORY = '/var/www/';

$GLOBALS['TWIGLOADER'] = new Twig_Loader_Filesystem($TWIG_TEMPLATE_DIRECTORY.$TWIG_LANGUAGE_DIR);
$GLOBALS['TWIG'] = new Twig_Environment($GLOBALS['TWIGLOADER']);
$GLOBALS['TWIG']->addFilter('floor', new Twig_Filter_Function('floor'));

$GLOBALS['TWIGSTRINGER'] = new Twig_Loader_String();
$GLOBALS['TWIGSTRING'] = new Twig_Environment($GLOBALS['TWIGSTRINGER']);
$GLOBALS['TWIGSTRING']->addFilter('floor', new Twig_Filter_Function('floor'));

function TWIG() {return $GLOBALS['TWIG'];}
function TWIGSTRING() {return $GLOBALS['TWIGSTRING'];}
