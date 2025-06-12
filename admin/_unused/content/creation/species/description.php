<?php
    include_once 'include/SQL-Var.php';
    include_once 'include/PLYR-Class.php';
    include_once 'include/PKMN-Class.php';
    
    $arguments = array();
    $drawnimal = PKMNBASEOBJ($_GET['species'], $_GET['form']);
    $arguments['SCRIPT_OUTPUT'] = '';
    $arguments['SCRIPT_ERROR'] = '';
    if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        if (isset($_FILES['image']) && file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])){
            $imagepath = 'sub/drw/'.uniqid().'.png';
            $drawnimal->ImageRaw($imagepath);
        }
        $drawnimal->Description($_POST['description']);
        $drawnimal->DescriptionShort($_POST['shortdescription']);
        $drawnimal->AppearanceRegion($_POST['region']);
        $arguments['SCRIPT_ERROR'] = $drawnimal->_save();
        if (strlen($arguments['SCRIPT_ERROR']) === 0) {
            if (isset($_FILES['image']) && file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                copy($_FILES['image']['tmp_name'], '/var/www/html/'.$imagepath);
            }
            if (isset($_POST['approved']) && PLYR()->IsAdmin('adminapprove')) {
                copy($_FILES['image']['tmp_name'], '/var/www/html/'.$imagepath);
            }
            $arguments['SCRIPT_OUTPUT'] = 'Successfully Updated Your Drawnimal!';
        }
    }
    $arguments['SPECIES'] = $drawnimal->Species();
    $arguments['FORM'] = $drawnimal->Form();
    $arguments['IMGPATH'] = $drawnimal->ImageRaw();
    $arguments['DESCRIPTION'] = $drawnimal->Description();
    $arguments['DESCRIPTIONSHORT'] = $drawnimal->DescriptionShort();
    $arguments['YOURREGIONLIST'] = '<option value=0>NONE</option>';
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
    
    include_once 'include/TWIG-Var.php';
    $twigfile = str_replace('/var/www/html/content/', '', str_replace('.php', '.twig', __FILE__));
    echo TWIG()->render($twigfile, $arguments);