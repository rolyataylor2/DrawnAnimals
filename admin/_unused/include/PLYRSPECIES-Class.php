<?php

$GLOBALS['PLYRSPECIES_LOADED'] = array();

function PLYRSPECIESOBJ($uid) {
    if (!isset($GLOBALS['PLYRSPECIES_LOADED'][$uid])) {
        $GLOBALS['PLYRSPECIES_LOADED'][$uid] = new PLYRSPECIESCLASS($uid);
    }
    return $GLOBALS['PLYRSPECIES_LOADED'][$uid];
}

class PLYRSPECIESCLASS {

    function __construct($uid) {
        $this->parent = PLYROBJ($uid);
    }

    private function _load() {
        if (!isset($this->data)) {
            $result = SQL()->query("SELECT system_drawnimals.species, user_species.caught
                                    FROM user_species 
                                    LEFT JOIN system_drawnimals
                                    ON user_species.speciesid = system_drawnimals.id
                                    WHERE user_species.uid=" . $this->parent->id) or die(SQL()->error);
            $this->data = array();
            $this->caughtdata = array();
            if ($result === false) {
                return;
            }
            while ($row = $result->fetch_row()) {
                if ($row[1] == 1) {
                    $this->caughtdata[] = strtolower($row[0]);
                } else {
                    $this->data[] = strtolower($row[0]);
                }
            }
        }
    }

    function Seen($name = null, $set = null) {
        $this->_load();
        if ($name == null) {
            return $this->data;
        }
        $name = strtolower($name);
        if ($set === null) {
            return array_search($name, $this->data);
        } else {
            if ($this->Seen($name) === false) {
                $STMT = SQL()->prepare("INSERT INTO user_species (uid,speciesid,caught,datetime) VALUES (?,(SELECT id FROM system_drawnimals WHERE species=?),0,?)");
                $STMT->bind_param('isi', $this->parent->id, $name, time());
                $STMT->execute();
                $STMT->close();
                unset($this->data);
                unset($this->caughtdata);
            }
        }
    }

    function Caught($name = null, $set = null) {
        $this->_load();
        if ($name == null) {
            return $this->caughtdata;
        }
        $name = strtolower($name);
        if ($set === null) {
            return array_search($name, $this->caughtdata);
        } else {
            if ($this->Seen($name) === false) {
                $STMT = SQL()->prepare('INSERT INTO user_species (uid,speciesid,caught,datetime) VALUES (?,(SELECT id FROM system_drawnimals WHERE species=?),1,?)');
                $STMT->bind_param('isi', $this->parent->id, $name, time());
                $STMT->execute() or die(SQL()->error);
                $STMT->close();
            } else {
                $STMT = SQL()->prepare('UPDATE user_species SET caught=1 WHERE uid=? AND speciesid=(SELECT id FROM system_drawnimals WHERE species=?)');
                $STMT->bind_param('is', $this->parent->id, $name);
                $STMT->execute();
                $STMT->close();
            }
            unset($this->data);
            unset($this->caughtdata);
        }
    }

    function NumberSeen() {
        return count($this->Seen());
    }

    function NumberCaught() {
        return count($this->Caught());
    }

}

