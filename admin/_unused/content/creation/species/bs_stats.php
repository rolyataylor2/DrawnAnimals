<?php
    include_once 'include/SQL-Var.php';
    include_once 'include/PLYR-Class.php';
    include_once 'include/PKMN-Class.php';
    
    $arguments = array();
    $drawnimal = PKMNBASEOBJ($_GET['species'], $_GET['form']);
    $arguments['SCRIPT_OUTPUT'] = '';
    $arguments['SCRIPT_ERROR'] = '';
    if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $drawnimal->Hp($_POST['bs_hp']);
        $drawnimal->Ev('hp',$_POST['ev_hp']);
        $drawnimal->Atk($_POST['bs_atk']);
        $drawnimal->Ev('atk',$_POST['ev_atk']);
        $drawnimal->Def($_POST['bs_def']);
        $drawnimal->Ev('def',$_POST['ev_def']);
        $drawnimal->SpAtk($_POST['bs_spatk']);
        $drawnimal->Ev('spatk',$_POST['ev_spatk']);
        $drawnimal->SpDef($_POST['bs_spdef']);
        $drawnimal->Ev('spdef',$_POST['ev_spdef']);
        $drawnimal->Speed($_POST['bs_speed']);
        $drawnimal->Ev('speed',$_POST['ev_speed']);
        $arguments['SCRIPT_ERROR'] = $drawnimal->_save();
        if (strlen($arguments['SCRIPT_ERROR']) === 0) {
            $arguments['SCRIPT_OUTPUT'] = 'Drawnimal Data Saved Successfully!';
        }
    }
    $arguments['SPECIES'] = $drawnimal->Species();
    $arguments['FORM'] = $drawnimal->Form();
    $arguments['BS_HP'] = $drawnimal->Hp();
    $arguments['EV_HP'] = $drawnimal->Ev('hp');
    $arguments['BS_ATK'] = $drawnimal->Atk();
    $arguments['EV_ATK'] = $drawnimal->Ev('atk');
    $arguments['BS_DEF'] = $drawnimal->Def();
    $arguments['EV_DEF'] = $drawnimal->Ev('def');
    $arguments['BS_SPATK'] = $drawnimal->SpAtk();
    $arguments['EV_SPATK'] = $drawnimal->Ev('spatk');
    $arguments['BS_SPDEF'] = $drawnimal->SpDef();
    $arguments['EV_SPDEF'] = $drawnimal->Ev('spdef');
    $arguments['BS_SPEED'] = $drawnimal->Speed();
    $arguments['EV_SPEED'] = $drawnimal->Ev('speed');
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
    
    include_once 'include/TWIG-Var.php';
    $twigfile = str_replace('/var/www/html/content/', '', str_replace('.php', '.twig', __FILE__));
    echo TWIG()->render($twigfile, $arguments);
