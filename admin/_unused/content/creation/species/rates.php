<?php
    include_once 'include/SQL-Var.php';
    include_once 'include/PLYR-Class.php';
    include_once 'include/PKMN-Class.php';
    
    $arguments = array();
    $drawnimal = PKMNBASEOBJ($_GET['species'], $_GET['form']);
    $arguments['SCRIPT_OUTPUT'] = '';
    $arguments['SCRIPT_ERROR'] = '';
    if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $drawnimal->GenderRate($_POST['rate_gender']);
        $drawnimal->HatchRate($_POST['rate_hatch']);
        $drawnimal->CatchRate($_POST['rate_catch']);
        $drawnimal->LevelRate($_POST['rate_level']);
        $drawnimal->Hunger($_POST['rate_hunger']);
        $drawnimal->Energy($_POST['rate_energy']);
        $drawnimal->Friendship($_POST['rate_friendship']);
        $arguments['SCRIPT_ERROR'] = $drawnimal->_save();
        if (strlen($arguments['SCRIPT_ERROR']) === 0) {
            $arguments['SCRIPT_OUTPUT'] = 'Drawnimal Data Saved Successfully!';
        }
    }
    $arguments['SPECIES'] = $drawnimal->Species();
    $arguments['FORM'] = $drawnimal->Form();
    $arguments['RATE_GENDER'] = $drawnimal->GenderRate();
    $arguments['RATE_HATCH'] = $drawnimal->HatchRate();
    $arguments['RATE_CATCH'] = $drawnimal->CatchRate();
    $arguments['RATE_LEVEL'] = $drawnimal->LevelRate();
    
    $arguments['RATE_HUNGER'] = $drawnimal->Hunger();
    $arguments['RATE_ENERGY'] = $drawnimal->Energy();
    $arguments['RATE_FRIENDSHIP'] = $drawnimal->Friendship();
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
    
    include_once 'include/TWIG-Var.php';
    $twigfile = str_replace('/var/www/html/content/', '', str_replace('.php', '.twig', __FILE__));
    echo TWIG()->render($twigfile, $arguments);