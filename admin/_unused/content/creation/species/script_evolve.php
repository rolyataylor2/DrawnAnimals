<?php
    include_once 'include/SQL-Var.php';
    include_once 'include/PLYR-Class.php';
    include_once 'include/PKMN-Class.php';
    
    $arguments = array();
    $drawnimal = PKMNBASEOBJ($_GET['species'], $_GET['form']);
    $arguments['SCRIPT_OUTPUT'] = '';
    $arguments['SCRIPT_ERROR'] = '';
    if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $_POST['evolvecoderaw'] = urldecode($_POST['evolvecoderaw']);
        $drawnimal->EvolveScriptRaw($_POST['evolvecoderaw']);
        
        $i=0; $codestring = '';
        while(isset($_POST['evolvecode'][$i])) {
            switch($_POST['evolvecode'][$i++]) {
                case 'iflevel': 
                    switch($_POST['evolvecode'][$i++]) {
                        case 0: $greater = '>='; break;
                        case 1: $greater = '<='; break;
                        case 2: $greater = '=='; break;
                        default: $greater = '=='; break;
                    }
                    $level = intval($_POST['evolvecode'][$i++]);
                    $codestring .= " if (\$this->Level() $greater $level): ";
                    break;
                case 'ifhour': 
                    switch($_POST['evolvecode'][$i++]) {
                        case 0: $greater = '>='; break;
                        case 1: $greater = '<='; break;
                        case 2: $greater = '=='; break;
                        default: $greater = '=='; break;
                    }
                    $hour = intval($_POST['evolvecode'][$i++]);
                    $codestring .= " if (GLSS()->GetHour() $greater $hour): ";
                    break;
                case 'iffriendship': 
                    switch($_POST['evolvecode'][$i++]) {
                        case 0: $greater = '>='; break;
                        case 1: $greater = '<='; break;
                        case 2: $greater = '=='; break;
                        default: $greater = '=='; break;
                    }
                    $friendship = intval($_POST['evolvecode'][$i++]);
                    $codestring .= " if (\$this->Mood()->Friendship() $greater $friendship): ";
                    break;
                case 'endif': 
                    $codestring .= ' endif; ';
                    break;
                case 'return':
                    $evolve = strtolower(preg_replace("/[^[:alnum:\s] ]/", '', $_POST['evolvecode'][$i++]));
                    // @add to evolves into list
                    $codestring .= " return '$evolve'; ";
                    break;
            }
        }
        $drawnimal->EvolveScript($codestring);
        
        
        $arguments['SCRIPT_ERROR'] = $drawnimal->_save();
        if (strlen($arguments['SCRIPT_ERROR']) === 0) {
            $arguments['SCRIPT_OUTPUT'] = 'Drawnimal Data Saved Successfully!';
        }
    }
    $arguments['SPECIES'] = $drawnimal->Species();
    $arguments['FORM'] = $drawnimal->Form();
    $arguments['EVOLVECODERAW'] = $drawnimal->EvolveScriptRaw();
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
    
    include_once 'include/TWIG-Var.php';
    $twigfile = str_replace('/var/www/html/content/', '', str_replace('.php', '.twig', __FILE__));
    echo TWIG()->render($twigfile, $arguments);
