<?php
if (!LoggedIn()) {
    die('<script>window.location.href="http://PokeWorlds.com/register.php";</script>');
}
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-items.php';
include_once 'html/_php/class-regions.php';
include_once 'html/_php/class-monsters.php';
include_once 'html/_php/class-monsters-learnset.php';
include_once 'html/_php/class-type.php';
include 'html/_php/class-html-code-table.php';

function SaveMonsterData($monster) {
    if ($monster->UserId() === PLAYERCLASS::byMe()->Id()) {
        $moveset = HTMLLISTABLETABLECLASS::byNew('MoveSetTable');
        $moveset->addLabel('Level Learned');
        $moveset->addLabel('Move Name');
        $itemset = HTMLLISTABLETABLECLASS::byNew('ItemSetTable');
        $itemset->addLabel('Item Name');
        $itemset->addLabel('% chance of dropping');
        $abilityset = HTMLLISTABLETABLECLASS::byNew('AbilitySetTable');
        $abilityset->addLabel('Ability Name');
        $abilityset->addLabel('% chance of obtaining');
        $evolveTable = HTMLCODETABLECLASS::byNew('EvolveCodeTable');
        $appearTable = HTMLCODETABLECLASS::byNew('AppearCodeTable');
    
        $monster->Form($_POST['form']);
        $monster->Index($_POST['index']);
        $monster->Name($_POST['species']);
        $monster->Description($_POST['description']);
        $monster->GenusFamily($_POST['genus_family']);
        $monster->GenusClass($_POST['genus_class']);
        $monster->GenusOrder($_POST['genus_order']);
        $monster->TypePrimary($_POST['type_0']);
        $monster->TypeSecondary($_POST['type_1']);
        $monster->Ev('hp',$_POST['ev_hp']);
        $monster->Ev('atk',$_POST['ev_atk']);
        $monster->Ev('def',$_POST['ev_def']);
        $monster->Ev('spatk',$_POST['ev_spatk']);
        $monster->Ev('spdef',$_POST['ev_spdef']);
        $monster->Ev('speed',$_POST['ev_speed']);
        $monster->Ev('exp',$_POST['ev_exp']);
        $monster->Hp($_POST['bs_hp']);
        $monster->Atk($_POST['bs_atk']);
        $monster->Def($_POST['bs_def']);
        $monster->SpAtk($_POST['bs_spatk']);
        $monster->SpDef($_POST['bs_spdef']);
        $monster->Speed($_POST['bs_speed']);
        $monster->Experiance($_POST['bs_exp']);
        $monster->Hunger($_POST['bs_hunger']);
        $monster->Energy($_POST['bs_energy']);
        $monster->Friendship($_POST['bs_friendship']);
        $monster->CatchRate($_POST['rate_catch']);
        $monster->GenderRate($_POST['rate_gender']);
        $monster->HatchRate($_POST['rate_hatch']);
        $monster->AppearanceRegion($_POST['appearance_region']);
        $monster->AppearanceStarter((isset($_POST['appearance_starter'])?1:0));
        /**
         * Save Images
         */
        global $arguments;
        $currentImages = $monster->Render()->imageUrls();
        foreach($_POST['imageGender'] as $key=>$gender) {
            $paint = $_POST['imagePaint'][$key];
            $gender = intval($gender);
            foreach($currentImages as $pos=>$i) {
                if ($i['gender'] === $gender && strcmp($i['paint'],$paint) === 0) {
                    array_splice($currentImages,$pos,1);
                }
            }
            
            $paint = preg_replace("/[\W_]*/", '', $paint);
            $paint = str_replace('/','',$paint);
            if (!empty($_FILES['image_g_'.$gender.'_p_'.$paint]['name'])) {
                $filepath = $_FILES['image_g_'.$gender.'_p_'.$paint]["tmp_name"];
                $savepath = '/var/www/html/img/uploads/mon/' . $monster->Id() . '.g_' . $gender . '.c_' . $paint . '.png';
                move_uploaded_file($filepath,$savepath);
                trim_png($savepath);
                $contents = compress_png($savepath);
                file_put_contents($savepath, $contents);
                if (file_exists('/var/www/html/img/mon/'.$monster->Id().'.g_'.$gender.'.c_'.$paint.'.png')) {
                        unlink('/var/www/html/img/mon/'.$monster->Id().'.g_'.$gender.'.c_'.$paint.'.png');
                }
            }
            
            if (!empty($_FILES['ow_g_'.$gender.'_p_'.$paint]['name'])) {
                $filepath = $_FILES['ow_g_'.$gender.'_p_'.$paint]["tmp_name"];
                $savepath = '/var/www/html/img/uploads/mon/ow/' . $monster->Id() . '.g_' . $gender . '.c_' . $paint . '.png';
                move_uploaded_file($filepath,$savepath);
                $contents = compress_png($savepath);
                file_put_contents($savepath, $contents);
                if (file_exists('/var/www/html/img/mon/ow/'.$monster->Id().'.g_'.$gender.'.c_'.$paint.'.png')) {
                        unlink('/var/www/html/img/mon/ow/'.$monster->Id().'.g_'.$gender.'.c_'.$paint.'.png');
                }
            }
            
            if (isset($_POST['approve_g_'.$gender.'_p_'.$paint])) {
                if (file_exists('/var/www/html/img/uploads/mon/'.$monster->Id().'.g_'.$gender.'.c_'.$paint.'.png')) {
                    copy('/var/www/html/img/uploads/mon/'.$monster->Id().'.g_'.$gender.'.c_'.$paint.'.png',
                            '/var/www/html/img/mon/'.$monster->Id().'.g_'.$gender.'.c_'.$paint.'.png');
                }
                if (file_exists('/var/www/html/img/uploads/mon/ow/'.$monster->Id().'.g_'.$gender.'.c_'.$paint.'.png')) {
                    copy('/var/www/html/img/uploads/mon/ow/'.$monster->Id().'.g_'.$gender.'.c_'.$paint.'.png',
                            '/var/www/html/img/mon/ow/'.$monster->Id().'.g_'.$gender.'.c_'.$paint.'.png');
                }
                
            }
        }
//        foreach($arguments['DRAWNIMAL']['IMAGES'] as $i) {
//            $drawnimal->ImageDelete($i['paint'],$i['gender']);
//        }
        /**
         * Save MoveSet
         */
        $oldmoves = MONSTERLEARNSETCLASS::byMonster($monster->Id());
        foreach($moveset->renderArray() as $i) {
            
            $add = true;
            foreach($oldmoves as $key=>$ii) {
                $move = CREATEMOVECLASS::byId($ii->Move());
                if (strcmp($move->Name(),$i['Move Name'])==0) {
                    if ($ii->Level() == $i['Level Learned']) {
                        array_splice($oldmoves,$key,1);
                        $add = false;
                    }
                }      
            }
            if ($add) {
                $move = CREATEMOVECLASS::byName($i['Move Name']);
                if (!empty($move->Id()))
                    MONSTERLEARNSETCLASS::byNew($monster->Id(),$move->Id(),$i['Level Learned']);
            }
        }
        foreach($oldmoves as $key=>$ii) { $ii->_delete(); }
        
        $monster->_save();
    } else {
        die('You do not have permission to edit this item');
    }
}

if (isset($_GET['id'])) {
    if ($_GET['id'] == -1) {
        if (VerifyPostToken()) {
            $monster = CREATEMONSTERCLASS::byNew(PLAYERCLASS::byMe()->Id(),$_POST['species']);
            SaveMonsterData($monster);
            die('<script>history.go(-2);</script>');
        }
    }
    
    $monster = CREATEMONSTERCLASS::byId($_GET['id']);
    $monster->_load();
    if (VerifyPostToken()) {
        SaveMonsterData($monster);
        die('<script>history.go(-2);</script>');
    }
    
    /**
     * Pull Users Regions
     */
    $regions = CREATEREGIONCLASS::byUserId(PLAYERCLASS::byMe()->Id());
    $arguments['REGIONS'] = array();
    foreach($regions as $i) {
        $arguments['REGIONS'][] = array('name'=>$i->Name(),'id'=>$i->Id());
    }
    /**
     *  PULL MOVESET
     */
    $moveset = HTMLLISTABLETABLECLASS::byNew('MoveSetTable');
    $moves = array();
    foreach(CREATEMOVECLASS::byAll() as $i) {
        $moves[] = array('value'=>'<b>'.$i->Name().'</b>'.$i->Type()->Name(),'data'=>$i->Name());
    }
    
    $moveset->addLabel('Level Learned');
    $moveset->addLabel('Move Name',$moves);
    foreach(MONSTERLEARNSETCLASS::byMonster($monster->Id()) as $i ) {
        if (!empty($i->Id()))
            $moveset->addRow(array($i->Level(),  CREATEMOVECLASS::byId($i->Move())->Name()), true);
    }
    $arguments['MOVESETTABLE'] = $moveset->renderHTML();

    /**
     * PULL ITEMS THAT MAY DROP
     */
    $itemset = HTMLLISTABLETABLECLASS::byNew('ItemSetTable');
    $items = array();
    $items[] = array('value'=>'<b>Tackle-NORMAL</b>','data'=>'tackle');
    $itemset->addLabel('Item Name',$items);
    $itemset->addLabel('% chance of dropping');
    foreach(MONSTERITEMCLASS::byMonster($monster->Id()) as $i ) {
        if (!empty($i->Id()))
            $itemset->addRow(array(CREATEITEMCLASS::byId($i->Item())->Name(),$i->Chance()), true);
    }
    $arguments['ITEMSETTABLE'] = $itemset->renderHTML();
    
    /**
     * PULL ABILITIES THAT MAY BE BORN WITH
     */
    $abilityset = HTMLLISTABLETABLECLASS::byNew('AbilitySetTable');
    $abilitys = array();
    $abilitys[] = array('value'=>'<b>Tackle-NORMAL</b>','data'=>'tackle');
    $abilityset->addLabel('Ability Name',$abilitys);
    $abilityset->addLabel('% chance of obtaining');
    foreach(MONSTERABILITYCLASS::byMonster($monster->Id()) as $i ) {
        if (!empty($i->Id()))
            $abilityset->addRow(array(CREATEABILITYCLASS::byId($i->Ability())->Name(),$i->Chance()), true);
    }
    $arguments['ABILITYSETTABLE'] = $abilityset->renderHTML();
    /**
    * Evolve Table
    */
    $evolveTable = HTMLCODETABLECLASS::byNew('EvolveCodeTable');
    $evolveTable->createFunction('Evolve Into')->addTitle('Evolve Into');
    $evolveTable->createFunction('Split Into')->addTitle('Split Into');
    $evolveTable->createGroup('About Me');
    $evolveTable->createFunction('Level')->addText('is')
            ->addSelect(['equal to','greater than','less than'])
            ->addInput('10')
            ->addCodeContainer()
            ->addCodeTranslation(function($arguments) { return '';});
    $evolveTable->createFunction('Gender')->addTitle('If Gender');
    $evolveTable->createFunction('Stat vs Stat')->addTitle('If Stats');
    $evolveTable->createFunction('Stat is')->addTitle('If Stat');
    $evolveTable->createFunction('Knows Move')->addTitle('If Knows Move');
    $evolveTable->createFunction('Holding a Item')->addTitle('If Holding A');
    
    $evolveTable->createGroup('Method');
    $evolveTable->createFunction('Level is')->addTitle('If Leveled Up');
    $evolveTable->createFunction('Item Used')->addTitle('If Using The Item');
    $evolveTable->createFunction('Traded')->addTitle('If Traded');
    
    $evolveTable->createGroup('Environment');
    $evolveTable->createFunction('Hour')->addTitle('If The Current Hour Is');
    $evolveTable->createFunction('Location')->addTitle('If The Current Location Is');
    $evolveTable->createFunction('Team Size')->addTitle('If Team Contains');
    $evolveTable->createFunction('Team Contains')->addTitle('If Team Member Is');
    $evolveTable->createFunction('Trainer Stat')->addTitle('If Trainer Has');
    
    $evolveTable->createGroup('Effect');
    $evolveTable->createFunction('Remove Item')->addTitle('Remove Held item');
    $evolveTable->createFunction('Add Item')->addTitle('Add Item To Inventory');
    $evolveTable->createFunction('Delete Party Member')->addTitle('Delete Party Member');
    

    /**
    * Appearence Table
    */
    $appearTable = HTMLCODETABLECLASS::byNew('AppearCodeTable');
    $appearTable->createGroup('If\'s');
    $appearTable->createFunction('If level')->addTitle('Nothing')->addText('this function does nothing...')->addCodeContainer();

    /**
    * Images
    */
    $arguments['DRAWNIMAL']['IMAGES'] = $monster->Render()->imageUrls();
    
    $arguments['PAINT'] = ['shiny','pixel'];
    $arguments['MONSTER'] = (isset($monster->data)?$monster->data:array());
    $arguments['EDIT'] = ($monster->UserId() === PLAYERCLASS::byMe()->Id());
    $arguments['EVOLVECODETABLE'] = $evolveTable->renderHTML();
    $arguments['APPEARCODETABLE'] = $appearTable->renderHTML();

    $arguments['TYPES'] = TYPECLASS::$typearray;
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
}
