<?php
if (!LoggedIn()) {
    die('<script>window.location.href="http://PokeWorlds.com/register.php";</script>');
}
include_once 'html/_php/class-monsters-learnset.php';
include_once 'html/_php/class-html-code-table.php';
function SaveAbilityData($ability) {
    if ($ability->UserId() !== PLAYERCLASS::byMe()->Id()) die('You do not have permissions to edit this ability.');
    global $table;
    $ability->Name($_POST['name']);
    $ability->Script($table->renderPHP());
    $ability->ScriptRaw($table->renderRawCode());
    $ability->Description($_POST['description']);
    $ability->_save();
}
if (isset($_GET['id'])) {
    if ($_GET['id'] == -1) {
        if (VerifyPostToken()) {
            $item = CREATEABILITYCLASS::byNew(PLAYERCLASS::byMe()->Id(),$_POST['name']);
            SaveAbilityData($item);
            die('<script>history.go(-2);</script>');
        }
    }
    $table = HTMLCODETABLECLASS::byNew('evolutionCode');
    $table->createFunction('Activate Event')
            ->addText('Activate Event')
            ->addSelect(array('none'), '')
            ->addCodeContainer()
            ->addEndIf()
            ->addCodeTranslation(function($argument) {
                $eventName = htmlEntities(htmlspecialchars($argument[0], ENT_COMPAT,'ISO-8859-1', true), ENT_QUOTES);
                return "if (strcmp(\$event,'$eventName')===0):";
            });
    $table->createGroup('Conditional'); 
    $table->createFunction('Active Attack?')
            ->addText('If Active Attack: ')
            ->addSelect(array('Name', 'Power', 'Type', 'Damage', 'Missed', 'Critical', 'Effectivness', 'PP Left', 'PP Max'))
            ->addSelect(array('<=', '==', '>='))
            ->addInput('0')
            ->addCodeContainer()
            ->addEndIf()
            ->addCodeTranslation(function($argument) {
                $find = array('Name', 'Power', 'Type', 'PP Left', 'PP Max', 'Damage', 'Missed', 'Critical', 'Effectivness');
                $replace = array('$battleaction->Move()->Name()','$battleaction->Move()->Power()','$battleaction->Move()->Type()->Id()',
                                   '$battleaction->Move()->PP()','$battleaction->Move()->PPMax()','$battleaction->damage',
                                    '$battleaction->missed','$battleaction->critical','$battleaction->effectivness');
                $function = $replace[array_search($argument[0],$find)];
                
                $find = array('<=', '==', '>=');
                $operator = $find[array_search($argument[1],$find)];
                
                $value = $argument[2];
                if (is_numeric($value)) {
                    $value = intval($argument[2]);
                    return 'if ('.$function.' '.$operator.' '.$value.'):';
                } else {
                    return 'if (strcmp('.$function.',"'.$value.'") '.$operator.' 0):';
                }
            });
    $table->createFunction('Active Item?')
            ->addText('If Active Item: ')
            ->addSelect(array('Name', 'Catagory', 'Description'))
            ->addText(' Contains The Text:')
            ->addInput('Potion')
            ->addCodeContainer()
            ->addEndIf()
            ->addCodeTranslation(function($argument) {
                $value = preg_replace('/\PL/u', '', $argument[1]);
                switch ( $argument[0] ) {
                    case 'Name':
                        return "if (strpos(BTTL()->Round()->Item()->Name(),'$value')===false):";
                    case 'Catagory':
                        return "if (strpos(BTTL()->Round()->Item()->Catagory(),'$value')===false):";
                    case 'Description':
                        return "if (strpos(BTTL()->Round()->Item()->Description(),'$value')===false):";
                    default:
                        return 'if (true):';
                }
            });

    $table->createFunction('Else')
            ->addText('Else')
            ->addCodeTranslation(function($argument) {
                return ' else: ';
            });

    //---------------------------------------------------------------
    $table->createGroup('Target');
    $table->createFunction('Change Target')
            ->addText('Target: ')
            ->addSelect(array('Myself', 'Attacker', 'Defender', 'Target', 'Current Drawnimal', 'Upcoming Drawnimal'), '')
            ->addCodeTranslation(function($argument) {
                switch ( $argument[0] ) {
                    case 'Myself':
                        return "\$target=\$this->parent; ";
                    case 'Attacker':
                        return "\$target=\$battleaction->Attacker(); ";
                    case 'Defender':
                        return "\$target=\$battleaction->Defender(); ";
                    case 'Target':
                        return "\$target=\$battleaction->Target(); ";
                    case 'Current Drawnimal':
                        return "\$target=\$battleaction->Monster(); ";
                    case 'Upcoming Drawnimal':
                        return "\$target=\$battleaction->SwitchMonster(); ";
                    default:
                        return "\$target=\$this->parent; ";
                }
            });
    $table->createFunction('Has Ailment?')
            ->addText('If Target Has Ailment ')
            ->addInput('Poison')
            ->addSelect(array('<=', '==', '>='))
            ->addInput(0)
            ->addCodeContainer()
            ->addEndIf()
            ->addCodeTranslation(function($argument) {
                $name = $argument[0];
                $operator = $argument[1];
                $value = intval($argument[2]);
                return "if (\$target->Ailments()->GetPower('$name') $operator $value):";
            });
    $table->createFunction('Target Stat is...')
            ->addText('If Target\'s')
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
            ->addSelect(array('=','<','>','<=','>='))
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
                
                $find = array('male','female','boy','girl','unknown','none');
                $replace = array('0','1','0','1','2','2');
                $value = $replace[array_search($argument[2],$find)];
                    
                $value = $argument[2];
                if (is_numeric($value)) {
                    return 'if ($target'.$function.' '.$operator.' '.intval($value).'):';
                } else {
                    $value = strtolower(htmlEntities(htmlspecialchars($value, ENT_COMPAT,'ISO-8859-1', true), ENT_QUOTES));
                    return 'if (strcmp(strtolower($target'.$function.'),"'.$value.'") '.$operator.' 0):';
                }
            });
    $table->createFunction('Has Modifier?')
            ->addText('If Modifier For ')
            ->addSelect(array('Attack', 'Defense', 'Sp. Attack', 'Sp. Defense', 'Speed'))
            ->addSelect(array('<=', '==', '>='))
            ->addSelect(array('-5', '-4', '-3', '-2', '-1', '0', '1', '2', '3', '4', '5'), '0')
            ->addCodeContainer()
            ->addEndIf()
            ->addCodeTranslation(function($argument) {
                $find = array('Attack', 'Defense', 'Sp. Attack', 'Sp. Defense', 'Speed');
                $replace = array('atk', 'def', 'spatk', 'spdef', 'speed');
                $function = $replace[array_search($argument[0],$find)];
                
                $find = array('<=', '==', '>=');
                $operator = $find[array_search($argument[1],$find)];
                
                return 'if ($target->_var("md_'.$function.'") '.$operator.' '.intval($argument[2]).'):';
            });
    $table->createFunction('Ailment +/-')
            ->addText('Set Targets Ailment ')
            ->addInput('Poison')
            ->addSelect(array('==', '+='))
            ->addInput(0)
            ->addCodeTranslation(function($argument) {
                $name = strtolower(htmlEntities(htmlspecialchars($argument[0], ENT_COMPAT,'ISO-8859-1', true), ENT_QUOTES));
                $operator = ( strcmp('+=', $argument[1]) === 0 ? 'true' : 'false' );
                $value = intval($argument[2]);
                return '$target->Ailments()->Set("'.$name.'",'.$value.','.$operator.');';
            });
    $table->createFunction('Stat  +/-')
            ->addText('Set Stat ')
            ->addSelect(array('Hp (Percent)', 'Hp (Value)', 'Attack Modifier', 'Defense Modifier', 'Sp. Attack Modifier', 'Sp. Defense Modifier', 'Speed Modifier', 'Hunger', 'Energy', 'Friendship'))
            ->addSelect(array('+=', '==', '-=', '*='))
            ->addInput(0)
            ->addCodeTranslation(function($argument) {

            });


    //---------------------------------------------------------------
    $table->createGroup('Effects');
    $table->createFunction('Active Attack +/-')
            ->addText('Change Active Attack: ')
            ->addSelect(array('Power', 'Type', 'Damage', 'Missed', 'Critical', 'Effectivness', 'PP Left', 'Stop Attack'))
            ->addSelect(array('+=', '=', '-=', '*='))
            ->addInput(1);
    $table->createFunction('Weather  +/-')
            ->addText('Weather: ')
            ->addSelect(array('Add', 'Remove'))
            ->addSelect(array('Rain', 'Snow', 'Hail', 'SandStorm'));
    $table->createFunction('Stop Action')
            ->addText('Current Action Will Stop');
    $table->createFunction('Escape Rate  +/-')
            ->addText('Escape is now ')
            ->addSelect(array('Easier', 'Normal', 'Difficult', 'Imposible'));
    $table->createFunction('Encounter Rate  +/-')
            ->addText('Encounter Rate is now')
            ->addSelect(array('More Frequent', 'Normal', 'Less Frequent', 'No Encounter'));

    //---------------------------------------------------------------
    $table->createGroup('Visual');
    $table->createFunction('Dialog')
            ->addText('Display Dialog')
            ->addTextarea('Nothing Happened...')
            ->addCodeTranslation(function($argument) {
                $dialog = htmlEntities(htmlspecialchars($argument[0], ENT_COMPAT,'ISO-8859-1', true), ENT_QUOTES);
                return '$battleaction->Queue()->Dialog("'.$dialog.'");';
            });
    
    $ability = CREATEABILITYCLASS::byId($_GET['id']);
    $ability->_load();
    if (VerifyPostToken()) {
        SaveAbilityData($ability);
        die('<script>history.go(-2);</script>');
    }
    $arguments['ABILITY'] = $ability->data;
    $arguments['CODETABLE'] = $table->renderHTML($ability->ScriptRaw());
    $arguments['EDIT'] = ($ability->UserId() === PLAYERCLASS::byMe()->Id());
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
}