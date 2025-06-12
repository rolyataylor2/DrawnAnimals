<?php

$GLOBALS['PLYRACHIEVMENT_LOADED'] = array();

function PLYRACHIEVMENTOBJ($uid) {
    if (!isset($GLOBALS['PLYRACHIEVMENT_LOADED'][$uid])) {
        $GLOBALS['PLYRACHIEVMENT_LOADED'][$uid] = new PLYRACHIEVMENTCLASS($uid);
    }
    return $GLOBALS['PLYRACHIEVMENT_LOADED'][$uid];
}

class PLYRACHIEVMENTCLASS {

    function __construct($uid) {
        $this->parent = PLYROBJ($uid);

        if (isset($GLOBALS['PLYRACHIEVMENT_LOADED'][$this->parent->id])) {
            return $GLOBALS['PLYRACHIEVMENT_LOADED'][$this->parent->id];
        } else {
            $GLOBALS['PLYRACHIEVMENT_LOADED'][$this->parent->id] = $this;
        }
        return $this;
    }

    private function _load() {
        if (isset($this->data)) {
            return;
        }
        $this->data = array();
        $STMT = SQL()->prepare('SELECT type,title,description FROM user_achievments WHERE uid=?');
        $STMT->bind_param('i', $this->parent->id);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        while ($item = $result->fetch_assoc()) {
            $this->data[] = $item;
        }
    }

    private function _add($type, $title, $description) {
        if ($this->_find($type, $title)) {
            return;
        }
        $STMT = SQL()->prepare('INSERT INTO user_achievments(uid,type,title,description) VALUES(?,?,?,?)');
        $STMT->bind_param('iiss', $this->parent->id, $type, $title, $description);
        $STMT->execute();
        $STMT->close();
        unset($this->data);
    }

    private function _find($type, $title) {
        $this->_load();
        foreach ($this->data as $item) {
            if ($item['type'] === $type && strcasecmp($item['title'], $title) === 0) {
                return true;
            }
        }
        return false;
    }

    private function _get($type) {
        $this->_load();
        $items = array();
        foreach ($this->data as $item) {
            if ($item['type'] === $type) {
                $items[] = $item;
            }
        }
        return $items;
    }

    function AddAward($title, $description) {
        $this->_add(0, $title, $description);
    }

    function FindAward($title) {
        return $this->_find(0, $title);
    }

    function GetAwards() {
        return $this->_get(0);
    }

    function AddRibbon($title, $description) {
        $this->_add(1, $title, $description);
    }

    function FindRibbon($title) {
        return $this->_find(1, $title);
    }

    function GetRibbons() {
        return $this->_get(1);
    }

    function AddAvatar($title, $description) {
        $this->_add(2, $title, $description);
    }

    function FindAvatar($title) {
        return $this->_find(2, $title);
    }

    function GetAvatars() {
        return $this->_get(2);
    }

    function AddTheme($title, $description) {
        $this->_add(3, $title, $description);
    }

    function FindTheme($title) {
        return $this->_find(3, $title);
    }

    function GetThemes() {
        return $this->_get(3);
    }

}
