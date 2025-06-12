<?php

$GLOBALS['PLYRBATTLES_LOADED'] = array();

function PLYRBATTLESOBJ($uid) {
    if (!isset($GLOBALS['PLYRBATTLES_LOADED'][$uid])) {
        $GLOBALS['PLYRBATTLES_LOADED'][$uid] = new PLYRBATTLESCLASS($uid);
    }
    return $GLOBALS['PLYRBATTLES_LOADED'][$uid];
}

class PLYRBATTLESCLASS {

    function __construct($uid) {
        $this->parent = PLYROBJ($uid);
    }

    function CreateWild($species) {
        
    }

    function CreateTrainer($trainerid) {
        
    }

    function CreatePVP($opponent1, $opponent2 = null, $opponent3 = null) {
        
    }

    function Current($value = null) {
        return $this->parent->_var('settings', 'battle_id', $value);
    }

    function RequestsGet() {
        
    }

    function RequestsGetTotal() {
        
    }

    function RequestMake($userid) {
        
    }

    function Wins() {
        
    }

    function Losses() {
        
    }

    function Total() {
        
    }

    function RankBadge() {
        
    }

    function Leaderboard() {
        
    }

}
