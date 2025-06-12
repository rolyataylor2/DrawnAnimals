<?php

$GLOBALS['PLYRINVENTORY_LOADED'] = array();

function PLYRINVENTORYOBJ($uid) {
    if (!isset($GLOBALS['PLYRINVENTORY_LOADED'][$uid])) {
        $GLOBALS['PLYRINVENTORY_LOADED'][$uid] = new PLYRINVENTORYCLASS($uid);
    }
    return $GLOBALS['PLYRINVENTORY_LOADED'][$uid];
}

class PLYRINVENTORYCLASS {

    var $parent;

    function __construct($uid) {
        $this->parent = PLYROBJ($uid);
    }

    function GetBag($name = null, $limit = 100, $offset = 0) {
        if ($name !== null) {
            $STMT = SQL()->prepare("SELECT id,type,count(id) FROM user_items WHERE uid=? AND type=? AND selling=0 AND storage=0 LIMIT 1");
            $STMT->bind_param('is', $this->parent->id, strtolower($name));
        } else {
            $STMT = SQL()->prepare("SELECT id,type,count(id) FROM user_items WHERE uid=? AND selling=0 AND storage=0 GROUP BY type LIMIT ?,? ");
            $STMT->bind_param('iii', $this->parent->id, $offset, $limit);
        }
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return array();
        }
        $STMT->close();
        $items = array();
        while ($item = $result->fetch_row()) {
            $itemobj = new ITEMCLASS($item[0]);
            $items[] = array('id' => $item[0],
                'name' => $itemobj->Base()->Name(),
                'catagory' => $itemobj->Base()->Catagory(),
                'quantity' => $item[2],
                'cash' => $itemobj->Base()->Cash());
        }
        return $items;
    }


    function GetStorage($name = null) {
        if ($name != null) {
            $STMT = SQL()->prepare("SELECT * FROM user_items WHERE uid=? AND type=? selling=0 AND storage=1");
            $STMT->bind_param('is', $this->parent->id, strtolower($name));
        } else {
            $STMT = SQL()->prepare("SELECT * FROM user_items WHERE uid=? AND selling=0 AND storage=1");
            $STMT->bind_param('i', $this->parent->id);
        }
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $items = array();
        while ($item = $result->fetch_assoc()) {
            $items[] = $item;
        }
        return $items;
    }

    function GetStore($name = null) {
        if ($name != null) {
            $STMT = SQL()->prepare("SELECT * FROM user_items WHERE uid=? AND type=? selling>0 AND storage=0");
            $STMT->bind_param('is', $this->parent->id, strtolower($name));
        } else {
            $STMT = SQL()->prepare("SELECT * FROM user_items WHERE uid=? AND selling>0 AND storage=0");
            $STMT->bind_param('i', $this->parent->id);
        }
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $items = array();
        while ($item = $result->fetch_assoc()) {
            $items[] = $item;
        }
        return $items;
    }

    function GetCashCount($name = null) {
        if ($name != null) {
            $STMT = SQL()->prepare("SELECT COUNT(id) FROM user_items WHERE uid=? AND type=? AND selling=0 AND storage=0 AND cash=1");
            $STMT->bind_param('is', $this->parent->id, strtolower($name));
        } else {
            $STMT = SQL()->prepare("SELECT COUNT(id) FROM user_items WHERE uid=? AND selling=0 AND storage=0 AND cash=1");
            $STMT->bind_param('i', $this->parent->id);
        }
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $item = $result->fetch_row();
        return $item[0];
    }

    function GetBagCount($name = null) {
        if ($name != null) {
            $STMT = SQL()->prepare("SELECT COUNT(id) FROM user_items WHERE uid=? AND type=? AND selling=0 AND storage=0");
            $STMT->bind_param('is', $this->parent->id, $name);
        } else {
            $STMT = SQL()->prepare("SELECT COUNT(id) FROM user_items WHERE uid=? AND selling=0 AND storage=0");
            $STMT->bind_param('i', $this->parent->id);
        }
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $item = $result->fetch_row();
        return $item[0];
    }

    function GetStorageCount($name = null) {
        if ($name != null) {
            $STMT = SQL()->prepare("SELECT COUNT(id) FROM user_items WHERE uid=? AND type=? AND selling=0 AND storage=1");
            $STMT->bind_param('is', $this->parent->id, strtolower($name));
        } else {
            $STMT = SQL()->prepare("SELECT COUNT(id) FROM user_items WHERE uid=? AND selling=0 AND storage=1");
            $STMT->bind_param('i', $this->parent->id);
        }
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $item = $result->fetch_row();
        return $item[0];
    }

    function GetStoreCount($name = null) {
        if ($name != null) {
            $STMT = SQL()->prepare("SELECT COUNT(id) FROM user_items WHERE uid=? AND type=? AND selling>0 AND storage=0");
            $STMT->bind_param('is', $this->parent->id, strtolower($name));
        } else {
            $STMT = SQL()->prepare("SELECT COUNT(id) FROM user_items WHERE uid=? AND selling>0 AND storage=0");
            $STMT->bind_param('i', $this->parent->id);
        }
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $item = $result->fetch_row();
        return $item[0];
    }

    function Add($name) {
        $name = strtolower($name);
        if ($this->GetBagCount() > 50) {
            $storage = 1;
        } else {
            $storage = 0;
        }
        $item = new ITEMCLASS(true);
        $item->Type($name);
        $item->Owner($this->parent->id);
        $item->Storage($storage);
        return $storage;
    }

    function Discard($name, $total = 1) {
        if ($this->GetBagCount($name) < $total) {
            return false;
        }
        $STMT = SQL()->prepare("DELETE FROM user_items WHERE uid=? AND type=? AND storage=0 AND selling=0 LIMIT ?");
        $STMT->bind_param('iii', $this->parent->id, $name, $total);
        $STMT->execute();
        $STMT->close();
        return true;
    }

    function DiscardStorage($name, $total = 1) {
        $name = strtolower($name);
        if ($this->GetStorageCount($name) < $total) {
            return false;
        }
        $STMT = SQL()->prepare("DELETE user_items WHERE uid=? AND type=? AND storage=1 AND selling=0 LIMIT ?");
        $STMT->bind_param('isi', $this->parent->id, $name, $total);
        $STMT->execute();
        $STMT->close();
        return true;
    }

    function DiscardStore($name, $total = 1) {
        $name = strtolower($name);
        if ($this->GetStorageCount($name) < $total) {
            return false;
        }
        $STMT = SQL()->prepare("DELETE user_items WHERE uid=? AND type=? AND storage=0 AND selling>0 LIMIT ?");
        $STMT->bind_param('isi', $this->parent->id, $name, $total);
        $STMT->execute();
        $STMT->close();
        return true;
    }

    function MoveToBag($name, $total = 1) {
        $name = strtolower($name);
        if ($this->GetStorageCount($name) < $total) {
            return false;
        }
        $STMT = SQL()->prepare("UPDATE user_items SET storage=0, selling=0 WHERE uid=? AND type=? AND storage=1 AND selling=0 LIMIT ?");
        $STMT->bind_param('isi', $this->parent->id, $name, $total);
        $STMT->execute();
        $STMT->close();
        return true;
    }

    function MoveToStorage($name, $total = 1) {
        $name = strtolower($name);
        if ($this->GetBagCount($name) < $total) {
            return false;
        }
        $STMT = SQL()->prepare("UPDATE user_items SET storage=1, selling=0 WHERE uid=? AND type=? AND storage=0 AND selling=0 LIMIT ?");
        $STMT->bind_param('isi', $this->parent->id, $name, $total);
        $STMT->execute();
        echo $STMT->affected_rows;
        $STMT->close();
        return true;
    }

    function SetPrice($name, $price, $total = 1) {
        $name = strtolower($name);
        $storage = 0;
        if ($price === 0) {
            if ($this->GetBagCount($name) > 50) {
                $storage = 1;
            }
        } else {
            if ($this->GetBagCount($name) + $this->GetStorageCount($name) < $total) {
                return false;
            }
        }

        $STMT = SQL()->prepare("UPDATE user_items SET storage=$storage, selling=? WHERE uid=? AND type=? ORDER BY selling DESC, storage ASC LIMIT ?");
        $STMT->bind_param('iisi', $price, $this->parent->id, $name, $total);
        $STMT->execute();
        $STMT->close();
        return true;
    }

}
