<?php

$GLOBALS['PLYRPARTY_LOADED'] = array();

function PLYRPARTYOBJ($uid) {
    if (!isset($GLOBALS['PLYRPARTY_LOADED'][$uid])) {
        $GLOBALS['PLYRPARTY_LOADED'][$uid] = new PLYRPARTYCLASS($uid);
    }
    return $GLOBALS['PLYRPARTY_LOADED'][$uid];
}

class PLYRPARTYCLASS {

    var $teamdata;
    var $parent;

    function __construct($uid) {
        $this->parent = PLYROBJ($uid);
        $result = SQL()->query('SELECT id FROM user_drawnimals WHERE uid=' . $this->parent->id . ' AND st_partypos > 0 ORDER BY st_partypos LIMIT 6');
        $this->teamdata = array();
        while ($row = $result->fetch_row()) {
            $this->teamdata[] = PKMNOBJ($row[0]);
        }
        return $this;
    }

    function Alive() {
        $alive = 0;
        $i = 0;
        while (isset($this->teamdata[$i])) {
            if ($this->teamdata[$i]->Hp() > 0) {
                $alive++;
            }
            $i++;
        }
        return $alive;
    }

    function Size() {
        return count($this->teamdata);
    }

    function Contains($id) {
        $i = 0;
        while (isset($this->teamdata[$i])) {
            if ($this->teamdata[$i]->id === $id) {
                return $this->teamdata[$i];
            }
            if (strcasecmp($this->teamdata[$i]->Nickname(), $id) === 0) {
                return $this->teamdata[$i];
            }
            $i++;
        }
        return false;
    }

    function Species($species) {
        $i = 0;
        while (isset($this->teamdata[$i])) {
            if (strcasecmp($this->teamdata[$i]->Species(), $species) === 0) {
                return $this->teamdata[$i];
            }
            $i++;
        }
        return false;
    }

    function BattleActive($value = null) {
        return $this->Contains($this->parent->_var('settings', 'drawnimal_battle', $value));
    }

    function Following($value = null) {
        return $this->Contains($this->parent->_var('settings', 'drawnimal_following', $value));
    }

    function Pos($pos) {
        if (isset($this->teamdata[$pos])) {
            return $this->teamdata[$pos];
        } else {
            return false;
        }
    }

    function Add($id) {
        if ($this->Size() > 5) {
            return false;
        }

        if (is_numeric($id)) {
            $pkmn = PKMNOBJ($id);
        } else {
            $pkmn = $id;
        }

        $this->teamdata[] = $pkmn;
        $pkmn->PartyPos($this->Size());
    }

    function Remove($id) {
        if ($this->Size() < 2) {
            return false;
        }

        if (is_numeric($id)) {
            $pkmn = PKMNOBJ($id);
        } else {
            $pkmn = $id;
        }

        $i = 0;
        while (isset($this->teamdata[$i])) {
            if ($this->teamdata[$i]->id == $pkmn->id) {
                array_slice($this->teamdata, $i);
            }
        }
        $pkmn->PartyPos(-1);
    }

    function Create($species, $level) {
        $pet = PKMNOBJ(-1, $this->parent->id, $species, $level);
        $this->Add($pet);
        $this->parent->Species()->Caught($species,true);
        return $pet;
    }

    function Others($offset=0,$limit=10) {
        $STMT = SQL()->prepare('SELECT id FROM user_drawnimals WHERE uid=' . $this->parent->id . ' AND st_partypos <= 0 ORDER BY species LIMIT ?,?');
        $STMT->bind_param('ii', $offset, $limit);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            return array();
        }
        $array = array();
        while ($row = $result->fetch_row()) {
            $array[] = PKMNOBJ($row[0]);
        }
        $STMT->close();
        return $array;
    }
}