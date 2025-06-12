<?php

$GLOBALS['PLYRNODE_LOADED'] = array();

function PLYRNODEOBJ($uid) {
    if (!isset($GLOBALS['PLYRNODE_LOADED'][$uid])) {
        $GLOBALS['PLYRNODE_LOADED'][$uid] = new PLYRNODECLASS($uid);
    }
    return $GLOBALS['PLYRNODE_LOADED'][$uid];
}

class PLYRNODECLASS {

    var $sendarguments;
    var $parent;
    var $node;

    function __construct($uid) {
        $this->parent = PLYROBJ($uid);
        $this->node = NODE();
        $this->sendarguments = array();
        $this->sendarguments['username'] = $this->parent->Username();

        $this->sendable = 'username, avatar, avatar_forums, '
                . 'coins, cash, type, location, location_x, '
                . 'location_y, country, battle_id, sessionid';
    }

    function __destruct() {
        $this->node->_send('PlayerUpdateInformation', $this->sendarguments);
    }

    function Variable($name, $value) {
        if (strpos($this->sendable, $name) !== -1) {
            $this->sendarguments[$name] = $value;
        }
    }
    
    function PartyVariable($id,$name,$value) {
        if (!isset($this->sendarguments['party'])) {
            $this->sendarguments['party'] = array();
        }
        $i=6;
        while($i--) {
            if (($pet = $this->parent->Party()->Pos($i)) === false) {continue;}
            if ($pet->id !== $id) {continue;}
            if (!isset($this->sendarguments['party'][$i])) {
                $this->sendarguments['party'][$i] = array();
            }
            $this->sendarguments['party'][$i][$name] = $value;
        }
    }
    
    function Following() {
        $this->sendarguments['following'] = array(
            'id' => $this->parent->Party()->Following()->id,
            'species' => $this->parent->Party()->Following()->Species(),
            'form' => $this->parent->Party()->Following()->Form(),
            'name' => $this->parent->Party()->Following()->Nickname()
        );
    }
    
    function NewMessages() {
        $this->sendarguments['newmessages'] = $this->parent->Messages()->AllUnseenTotal();
        return $this;
    }

    function NewBattleRequests() {
        $this->sendarguments['newbattles'] = $this->parent->Battles()->RequestsGetTotal();
        return $this;
    }

    function NewFriendRequests() {
        $this->sendarguments['newfriends'] = $this->parent->Friends()->AllRequestsTotal();
        return $this;
    }

    function NewTradeRequests() {
        $this->sendarguments['newtrades'] = 0;
        return $this;
    }

    function ExecuteJavascript($javascript) {
        $this->sendarguments['JS'] = $javascript;
    }

    function ExecuteQueue() {
        
    }

}
