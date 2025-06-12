<?php

include_once 'html/_php/class-monsters.php';
include_once 'html/_php/class-monsters-learnset.php';
include_once 'html/_php/class-like.php';
include_once 'html/_php/class-items.php';
include_once 'html/_php/class-regions.php';

if (isset($_GET['id'])) {
    $monster = CREATEMONSTERCLASS::byId($_GET['id']);
    $monster->_load();
    
    $arguments['MONSTER'] = $monster->data;
    if (PLAYERCLASS::byMe()->Id() !== 1 && 
        PLAYERCLASS::byMe()->Id() !== $monster->UserId() && 
        PLAYERCLASS::byMe()->Caught($monster->Id()) === false ) {
            $arguments['MONSTER']['visible'] = PLAYERCLASS::byMe()->Seen($monster->Id());
            $arguments['MONSTER']['genus_class'] = '???';
            $arguments['MONSTER']['genus_order'] = '???';
            $arguments['MONSTER']['genus_family'] = '???';
            $arguments['MONSTER']['species'] = '???';
            $arguments['MONSTER']['number'] = '???';
            $arguments['MONSTER']['bs_hp'] = '???';
            $arguments['MONSTER']['bs_atk'] = '???';
            $arguments['MONSTER']['bs_def'] = '???';
            $arguments['MONSTER']['bs_spatk'] = '???';
            $arguments['MONSTER']['bs_spdef'] = '???';
            $arguments['MONSTER']['bs_speed'] = '???';
            $arguments['MONSTER']['bs_exp'] = '???';
            $arguments['MONSTER']['ev_hp'] = '?';
            $arguments['MONSTER']['ev_atk'] = '?';
            $arguments['MONSTER']['ev_def'] = '?';
            $arguments['MONSTER']['ev_spatk'] = '?';
            $arguments['MONSTER']['ev_spdef'] = '?';
            $arguments['MONSTER']['ev_speed'] = '?';
            $arguments['MONSTER']['ev_exp'] = '?';
            $arguments['MONSTER']['bs_hunger'] = 128;
            $arguments['MONSTER']['bs_energy'] = 128;
            $arguments['MONSTER']['bs_friendship'] = 128;
            $arguments['MONSTER']['hidden'] = true;
            $arguments['MONSTER']['description'] = "This Pokemon is not in your pokedex!";
        }
    /**
     * Region Name
     */
    $region = CREATEREGIONCLASS::byId($monster->AppearanceRegion());
    $arguments['REGION'] = $region->Name();
    /**
     * Pull learnset
     */
    $arguments['MOVES'] = array();
    foreach(MONSTERLEARNSETCLASS::byMonster($monster->Id()) as $i ) {
        $move = CREATEMOVECLASS::byId($i->Move());
        $move->_load();
        $i->data['MOVE'] = $move->data;
        if (!empty($move->Id())) $arguments['MOVES'][] = $i->data;
    }
    /**
    * PULL ITEMS THAT MAY DROP
    */
    $arguments['ITEMS'] = array();
    foreach(MONSTERITEMCLASS::byMonster($monster->Id()) as $i ) {
       $item = CREATEITEMCLASS::byId($i->Item());
       if (!empty($item->Id())) $arguments['ITEMS'][] = array('name'=>$item->Name());
    }

    /**
    * PULL ABILITIES THAT MAY BE BORN WITH
    */
    $arguments['ABILITIES'] = array();
    foreach(MONSTERABILITYCLASS::byMonster($monster->Id()) as $i ) {
       $ability = CREATEABILITYCLASS::byId($i->Ability());
       if (!empty($ability->Id())) $arguments['ABILITIES'][] = array('name'=>$ability->Name());
    }

    /**
    * Images
    */
    $arguments['MONSTER']['imageUrl'] = $monster->Render()->imageUrl();
    $arguments['DRAWNIMAL']['IMAGES'] = $monster->Render()->imageUrls();
    $arguments['MONSTER']['liked'] = $monster->Liked();
    $arguments['MONSTER']['likes'] = $monster->Likes();
    

    $arguments['EDIT'] = ($monster->UserId() === PLAYERCLASS::byMe()->Id());
}