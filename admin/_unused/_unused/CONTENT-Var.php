<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CONTENTSPECIESCLASS {
    var $data;
    function __construct($id, $uid) {
        $this->destructdb = SQL();
        $STMT = SQL()->prepare('SELECT * FROM system_drawnimals WHERE id=? AND cuid=?');
        $STMT->bind_param('ii',$id,$uid);
        $STMT->execute();
        $result = $STMT->get_result();
        $this->data = $result->fetch_assoc();
        $this->database = 'system_drawnimals';
    }

    function __destruct() {
        //save contents of submission
    }
    function _var($name,$value) {
        $this->submission['datastring'][$name] = $value;
    }
    function Approve() {
        $this->_var('capproved',1);
        // @todo copy image from raw_imagepath to /img/drawnimals
    }
    function Image($value=null) {
        // @todo copy from tmp_path to submission folder
        return $this->_var('cimgraw',$value);
    }
    
    function Name($value=null) {
        return $this->_var('species',$value);
    }
    function Form($value=null) {
        return $this->_var('form',$value);
    }
    function Type($value=null) {
        return $this->_var('type_0',$value);
    }
    function TypeSecondary($value=null) {
        return $this->_var('type_1',$value);
    }
    function Hp($value=null) {
        return $this->_var('bs_hp',$value);
    }
    function Attack($value=null) {
        return $this->_var('bs_atk',$value);
    }
    function Defense($value=null) {
        return $this->_var('bs_def',$value);
    }
    function SpAttack($value=null) {
        return $this->_var('bs_spatk',$value);
    }
    function SpDefense($value=null) {
        return $this->_var('bs_spdef',$value);
    }
    function Speed($value=null) {
        return $this->_var('bs_speed',$value);
    }
    function Hunger($value=null) {
        return $this->_var('bs_hunger',$value);
    }
    function Energy($value=null) {
        return $this->_var('bs_energy',$value);
    }
    function Friendship($value=null) {
        return $this->_var('bs_friendship',$value);
    }
    function CatchRate($value=null) {
        return $this->_var('rate_catch',$value);
    }
    function GenderRate($value=null) {
        return $this->_var('rate_gender',$value);
    }
    function LevelRate($value=null) {
        return $this->_var('rate_level',$value);
    }
    function HatchRate($value=null) {
        return $this->_var('rate_hatch',$value);
    }
    function EvolveCode($value=null) {
        return $this->_var('script_evolve',$value);
    }
    function AppearanceCode($value=null) {
        return $this->_var('script_appearance',$value);
    }
    function Locations($value=null) {
        return $this->_var('script_locations',$value);
    }
    function Abilities($value=null) {
        return $this->_var('abilities',$value);
    }

    function Learnset($value=null) {
        return $this->_var('learnset',$value);
    }
    function Items($value=null) {
        return $this->_var('items',$value);
    }
    function Description($value=null) {
        return $this->_var('description',$value);
    }
}

class CONTENTCLASS {
    function Species($id) {
        $this->SpeciesClass = new CONTENTSEGMENTCLASS(0);
        $this->SpeciesClass->_ApprovedFolder('/var/www/html/img/drawnimals');
        $this->SpeciesClass->_UnApprovedFolder('/var/www/submissions/drawnimals');
        
        
    }
    function Items() {}
    function Move() {}
    function Map() {}
    function Objects() {}
    function Overworld() {}
    function Music() {}
}

$GLOBALS['CONTENTCLASSOBJ'] = new CONTENTCLASS();
function CONTENT() {
    return $GLOBALS['CONTENTCLASSOBJ'];
}