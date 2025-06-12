<?php
if (!LoggedIn()) {
    die('<script>window.location.href="http://PokeWorlds.com/register.php";</script>');
}
include_once 'html/_php/class-monsters-learnset.php';
include_once 'html/_php/class-type.php';
include_once 'html/_php/class-html-code-table.php';

function SaveMoveData($move) {
    if ($move->UserId() !== PLAYERCLASS::byMe()->Id()) die('You do not have permissions to edit this move.');
    global $scripttable;
    $move->Name($_POST['name']);
    $move->Description($_POST['description']);
    $move->Power($_POST['power']);
    $move->PP($_POST['pp']);
    $move->Speed($_POST['speed']);
    $move->Acc($_POST['accuracy']);
    $move->Target($_POST['target']);
    $move->DamageType($_POST['damagetype']);
    $move->Script($scripttable->renderPHP());
    $move->ScriptRaw($scripttable->renderRawCode());
    $move->_save();
}

if (isset($_GET['id'])) {
    if ($_GET['id'] == -1) {
        if (VerifyPostToken()) {
            $move = CREATEMOVECLASS::byNew(PLAYERCLASS::byMe()->Id(),$_POST['name']);
            SaveMoveData($move);
            die('<script>history.go(-2);</script>');
        }
    }
    
    $scripttable = HTMLCODETABLECLASS::byNew('scripttable');
    $scripttable->createGroup('Conditional');
 
    $scripttable->createFunction('Target Stat is...')
            ->addTitle('If Target\'s')
            ->addSelect(array('Hp %','Hp','Hp (EV)','HpMax',
                              'Attack','Attack (EV)','Attack (MOD)',
                              'Defense','Defense (EV)','Defense (MOD)',
                              'SpAttack','SpAttack (EV)','SpAttack (MOD)',
                              'SpDefense','SpDefense (EV)','SpDefense (MOD)',
                              'Speed','Speed (EV)','Speed (MOD)',
                              'Accuracy (MOD)','Evasivness (MOD)',
                              'Class','Order','Family','Species','Paint','Form',
                              'Level','Primary Type','Secondary Type',
                              'Gender','Hunger','Friendship','Energy','Held Item'))
            ->addSelect(array('=','<','>','<=','>=','!='))
            ->addInput('10')
            ->addCodeContainer()
            ->addEndIf()
            ->addCodeTranslation(function($argument) {
                $find = array('Hp %','Hp','Hp (EV)','HpMax',
                              'Attack','Attack (EV)','Attack (MOD)',
                              'Defense','Defense (EV)','Defense (MOD)',
                              'SpAttack','SpAttack (EV)','SpAttack (MOD)',
                              'SpDefense','SpDefense (EV)','SpDefense (MOD)',
                              'Speed','Speed (EV)','Speed (MOD)',
                              'Accuracy (MOD)','Evasivness (MOD)',
                              'Class','Order','Family','Species','Paint','Form',
                              'Level','Primary Type','Secondary Type',
                              'Gender','Hunger','Friendship','Energy','Held Item');
                $replace = array('->Hp()','->Hp()','->Ev("hp")','->Stat("hp")',
                              '->Stat("atk")','->Ev("atk")','->_var("md_atk")',
                              '->Stat("def")','->Ev("def")','->_var("md_def")',
                              '->Stat("spatk")','->Ev("spatk")','->_var("md_spatk")',
                              '->Stat("spdef")','->Ev("spdef")','->_var("md_spdef")',
                              '->Stat("speed")','->Ev("speed")','->_var("md_speed")',
                              '->_var("md_acc")','->_var("md_evv")',
                              '->Species()->GenusClass()','->Species()->GenusOrder()','->Species()->GenusFamily()',
                              '->Species()->Name()','->Paint()','->Form()',
                              '->Level()','->Species()->TypePrimary()->Name()','->Species()->TypeSecondary()->Name()',
                              '->Gender()','->Hunger()','->Friendship()','->Energy()','->Item()->Name()');
                $function = $replace[array_search($argument[0],$find)];
                
                $find = array('=','<','>','<=','>=','!=');
                $replace = array('=','<','>','<=','>=','!=');
                $operator = $replace[array_search($argument[1],$find)];
                
                $find = array('male','female','boy','girl','unknown','genderless','none');
                $replace = array('0','1','0','1','2','2','');
                $value = $replace[array_search($argument[2],$find)];
                    
                $value = $argument[2];
                if (is_numeric($value)) {
                    return 'if ($this->target->ActiveDrawnimal()'.$function.' '.$operator.' '.intval($value).'):';
                } else {
                    $value = strtolower(htmlEntities(htmlspecialchars($value, ENT_COMPAT,'ISO-8859-1', true), ENT_QUOTES));
                    return 'if (strcmp(strtolower($this->target->ActiveDrawnimal()'.$function.'),"'.$value.'") '.$operator.' 0):';
                }
            });
    $scripttable->createFunction('Weather is...')
            ->addTitle('If Weather')
            ->addInput('snowing')
            ->addCodeContainer()
            ->addEndIf()
            ->addCodeTranslation(function($argument) {
                // @TODO work on
                return 'if (getWeather() === 0):';
            });
    $scripttable->createFunction('Move Tally...')
            ->addTitle('If Move Tally')
            ->addText('Round Number')
            ->addInput('1')
            ->addText('(Starts at 1)')
            ->addCodeContainer()
            ->addEndIf()
            ->addCodeTranslation(function($argument) {
                return 'if ($this->MoveTally() === '.intval($argument[0]).'):';
            });
            
    $scripttable->createFunction('Random Number')
            ->addTitle('If Random Number')
            ->addInput('100')
            ->addText('in 255 chance')
            ->addCodeContainer()
            ->addEndIf()
            ->addCodeTranslation(function($argument) {
                return 'if (mt_rand(1,255) < '.intval($argument[0]).'):';
            });
    $scripttable->createFunction('Else')
            ->addTitle('<center>==== Else ====</center>')
            ->addElse();
    
    ////////////////////////////////////////////////////////////////////////
    $scripttable->createGroup('Visual');
    $scripttable->createFunction('Display Text')
            ->addTitle('Show Dialog')
            ->addTextarea('{{ ATTACKER }} Used {{ MOVENAME }}!')
            ->addCodeTranslation(function($argument) {
                $text = htmlEntities(htmlspecialchars($argument[0], ENT_COMPAT,'ISO-8859-1', true), ENT_QUOTES);
                return '$this->Queue()->Dialog("'.$text.'");';
            });
    $scripttable->createFunction('Play Sound Effect')
            ->addTitle('Sound Effect')
            ->addInputDialog('sfx')
            ->addCodeTranslation(function($argument) {
                $text = htmlEntities(htmlspecialchars($argument[0], ENT_COMPAT,'ISO-8859-1', true), ENT_QUOTES);
                return '$this->Queue()->PlaySound("http://PokeWorlds.com/sfx/battle/'.$text.'");';
            });   
    $scripttable->createFunction('Animation')
            ->addTitle('Animation')
            ->addText('Image to use')
            ->addInputDialog('battleimages')
            ->addText('<br/>Move From')
            ->addSelect(array('Attacker','Defender','Top','Left','Right','Bottom','Middle'))
            ->addText('to')
            ->addSelect(array('Attacker','Defender','Top','Left','Right','Bottom','Middle'))
            ->addText('effect')
            ->addSelect(array('Hit','Shower','Twinkle','Fly Fast','Float Over','Fade','Meander'));
            
            
    $scripttable->createFunction('Change Background')
            ->addTitle('Change Background')
            ->addText('Image:')
            ->addSelect(array('Blue','Red','Green','Yellow',
                              'Stat Up','Stat Down','Heal','focus',
                              'Snow','Leafs','Ghost','Poison','Heat',
                              'Water'))
            ->addText(' Effect:')
            ->addSelect(array('Scroll Up','Scroll Down','Scroll Left','Scroll Right',
                              'Expand Slow','Expand Fast','Spin','Shake'));

    
    ////////////////////////////////////////////////////////////
    $scripttable->createGroup('Modifier');
    $scripttable->createFunction('Critical...')
            ->addTitle('Change Move')
            ->addSelect(array('Increase','Decrease'))
            ->addText('Critical Attack Chance')
            ->addCodeTranslation(function($argument) {
                if (strcmp($argument[0],'Increase') === 0) {
                    return '$this->criticalratio += 1;';
                } else {
                    return '$this->criticalratio -= 1;';
                }
            });
    $scripttable->createFunction('Effectivness')
            ->addTitle('Change Move')
            ->addText('Effectivness to')
            ->addSelect(array('0x','0.5x','1x','1.5x','2x'));
    $scripttable->createFunction('Power')
            ->addTitle('Change Move')
            ->addText('Power To')
            ->addInput('50');
    $scripttable->createFunction('Accuracy')
            ->addTitle('Change Move')
            ->addText('Accuracy To')
            ->addInput('50');
    $scripttable->createFunction('Type')
            ->addTitle('Change Move')
            ->addText('Type To')
            ->addSelect(TYPECLASS::$typearray);
    $scripttable->createFunction('Weather')
            ->addTitle('Change Weather')
            ->addInput('Snowing')
            ->addText('For')
            ->addInput('2')
            ->addText('Turns');
    $scripttable->createFunction('Add Move Tally')
            ->addTitle('Move Tally + 1. Move will continue next turn!')
            ->addCodeTranslation(function() { 
                return '$this->ProcessAttackAddTurn(); return;';
            });
    $scripttable->createFunction('Repeat Move')
            ->addTitle('==== Repeat this attack ====')
            ->addCodeTranslation(function() { 
                return 'eval(TWIGSTRING()->render($script, $arguments)); return;';
            });
    $scripttable->createFunction('Stop Here')
            ->addTitle('==== End Attack Here ====')
            ->addCodeTranslation(function() { 
                return 'return;';
            });
    
    ////TARGET
    $scripttable->createGroup('Target');
    $scripttable->createFunction('Increase...')
            ->addTitle('Increase The Target\'s')
            ->addSelect(array('Attack', 'Defense', 'SpAttack', 'SpDefense', 'Speed', 'Accuracy', 'Evasivness','Random Base (dialogs)'))
            ->addText('by')
            ->addInput(1)
            ->addText('stage')
            ->addCodeTranslation(function($argument) {
                $search = array('Attack', 'Defense', 'SpAttack', 'SpDefense', 'Speed', 'Accuracy', 'Evasivness','Random Base (dialogs)');
                $replace = array('atk', 'def', 'spatk', 'spdef', 'speed', 'acc', 'evv',0);
                $stat = $replace[array_search($argument[0],$search)];
                if ($stat === 0) {
                    return '$stat = array_rand(array("atk", "def", "spatk", "spdef", "speed")); '
                            . '$this->target->Md($stat,'.min(max(intval($argument[1]),0),2).');'
                            . '$this->Queue()->Dialog("{{ TARGET }}&#039;s ".$stat." Rose!");';
                } else {
                    return '$this->target->Md("'.$stat.'",'.min(max(intval($argument[1]),0),2).');';
                }
            });
    $scripttable->createFunction('Decrease...')
            ->addTitle('Decrease The Target\'s')
            ->addSelect(array('Attack', 'Defense', 'SpAttack', 'SpDefense', 'Speed', 'Accuracy', 'Evasivness','Random Base (dialogs)'))
            ->addText('by')
            ->addInput(1)
            ->addText('stage')
            ->addCodeTranslation(function($argument) {
                $search = array('Attack', 'Defense', 'SpAttack', 'SpDefense', 'Speed', 'Accuracy', 'Evasivness','Random Base (dialogs)');
                $replace = array('atk', 'def', 'spatk', 'spdef', 'speed', 'acc', 'evv',0);
                $stat = $replace[array_search($argument[0],$search)];
                if ($stat === 0) {
                    return '$stat = array_rand(array("atk", "def", "spatk", "spdef", "speed")); '
                            . '$this->target->Md($stat,'.min(max(intval($argument[1]),0),2).');'
                            . '$this->Queue()->Dialog("{{ TARGET }}&#039;s ".$stat." Fell!");';
                } else {
                    return '$this->target->Md("'.$stat.'",-'.min(max(intval($argument[1]),0),2).');';
                }
                
            });
    $scripttable->createFunction('Damage Target')
            ->addTitle('Damage Target')
            ->addInput('1')
            ->addText('x Damage to Target..')
            ->addText(' Dialogs?')
            ->addSelect(array('Default','Silent'))
            ->addCodeTranslation(function($argument) {
                $code = '$this->damagemod='.floatval($argument[0]).'; ';
                
                if (strcmp($argument[1],'Default')===0) {
                    return $code.'$this->ProcessAttackDamage();';
                } else {
                    return $code.'$this->ProcessAttackDamage(true);';
                }
                
            });

    $scripttable->createFunction('Change Hp')
            ->addTitle('Target\'s HP')
            ->addSelect(array('+','-','+ %','- %'))
            ->addInput('10');
    $scripttable->createFunction('Set Ailment...')
            ->addTitle('Target\'s Ailment')
            ->addInputDialog('ailments')
            ->addText('=')
            ->addInput('1')
            ->addText('(0 = Remove)');
    $scripttable->createFunction('Held Item...')
            ->addTitle('Target\'s Held Item')
            ->addSelect(array('Loses','Puts Away','Steals','Swaps'))
            ->addText('its held item');
    $scripttable->createFunction('Change Target...')
            ->addTitle('Change Target')
            ->addSelect(array('Attacker','Defender'))
            ->addCodeTranslation(function($argument) {
                switch($argument[0]) {
                    case 'Attacker':
                        $argument[0] = '$this->Player()';
                        break;
                    case 'Defender':
                        $argument[0] = '$this->TargetPlayer()';
                        break;
                    default:
                        $argument[0] = '$this->TargetPlayer()';
                        break;
                }
                return '$this->target = '.$argument[0].';';
            });
    
    
    
    $move = CREATEMOVECLASS::byId($_GET['id']);
    $move->_load();
    if (VerifyPostToken()) {
        SaveMoveData($move);
        die('<script>history.go(-2);</script>');
    }
    $arguments['MOVE'] = (isset($move->data)?$move->data:array());
    $arguments['CODETABLE'] = $scripttable->renderHTML($move->ScriptRaw());
    $arguments['EDIT'] = ($move->UserId() === PLAYERCLASS::byMe()->Id());
    $arguments['TYPES'] = TYPECLASS::$typearray;
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
}
