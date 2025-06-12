<?php
    include_once 'include/SQL-Var.php';
    include_once 'include/PLYR-Class.php';
    include_once 'include/PKMN-Class.php';
    
    $arguments = array();
    $arguments['YOURREGIONLIST'] = '<option value="0">NONE</option>';
    $arguments['SCRIPT_OUTPUT'] = '';
    $arguments['SCRIPT_ERROR'] = '';
    
    if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $_POST['species'] = strtolower($_POST['species']);
        $_POST['form'] = intval($_POST['form']);
        $_POST['region'] = intval($_POST['region']);
        
        $drawnimal = new PKMNBASECLASS(-1);
        if (is_uploaded_file($_FILES['image']['tmp_name'])) {
            $imagepath = 'sub/drw/'.uniqid().'.png';
            $arguments['IMGPATH'] = $drawnimal->ImageRaw($imagepath);
        }
        $arguments['SPECIES'] = $drawnimal->Species($_POST['species']);
        $arguments['FORM'] = $drawnimal->Form($_POST['form']);
        $arguments['REGION'] = $drawnimal->AppearanceRegion($_POST['region']);
        $arguments['SHORTDESCRIPTION'] = $drawnimal->DescriptionShort($_POST['shortdescription']);
        $arguments['DESCRIPTION'] = $drawnimal->Description($_POST['description']);
        
        $arguments['SCRIPT_ERROR'] = $drawnimal->_save();
        
        if (strlen($arguments['SCRIPT_ERROR']) === 0) {
            if (is_uploaded_file($_FILES['image']['tmp_name'])) {
                copy($_FILES['image']['tmp_name'], '/var/www/html/'.$imagepath);
            }
            $arguments['SCRIPT_OUTPUT'] = 'Successfully Created Your Drawnimal!';
        }  
        
    }
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
    
    include_once 'include/TWIG-Var.php';
    $twigfile = str_replace('/var/www/html/content/','',str_replace('.php','.twig',__FILE__));
    echo TWIG()->render($twigfile, $arguments);
