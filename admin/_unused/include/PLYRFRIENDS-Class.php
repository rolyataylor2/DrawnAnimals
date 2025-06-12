<?php

$GLOBALS['PLYRFRIENDS_LOADED'] = array();

function PLYRFRIENDSOBJ($uid) {
    if (!isset($GLOBALS['PLYRFRIENDS_LOADED'][$uid])) {
        $GLOBALS['PLYRFRIENDS_LOADED'][$uid] = new PLYRFRIENDSCLASS($uid);
    }
    return $GLOBALS['PLYRFRIENDS_LOADED'][$uid];
}

class PLYRFRIENDSCLASS {

    function __construct($uid) {
        $this->parent = PLYROBJ($uid);
    }

    function Request($uid) {
        $player = PLYROBJ($uid);
        $status = $player->Friends()->Find($this->parent->id);
        if ($status === -1) {
            $STMT = SQL()->prepare('INSERT INTO user_friends (uid,uuid,status) VALUES (?,?,0)');
            $STMT->bind_param('ii', $player->id, $this->parent->id);
            $STMT->execute();
            $STMT->close();
        } elseif ($status === 0) {
            $STMT = SQL()->prepare('DELETE FROM user_friends WHERE uid=? AND uuid=?');
            $STMT->bind_param('ii', $player->id, $this->parent->id);
            $STMT->execute();
            $STMT->close();
        }
    }

    function Approve($uid) {
        $player = PLYROBJ($uid);
        $status = $this->Find($player->id);

        if ($status === 0) {
            $STMT = SQL()->prepare('INSERT IGNORE INTO user_friends(uid,uuid,status) VALUES(?,?,1)');
            $STMT->bind_param('ii', $player->id, $this->parent->id);
            $STMT->execute();
            $STMT->close();

            $STMT = SQL()->prepare('UPDATE user_friends SET status=1 WHERE (uid=? AND uuid=?) OR (uuid=? AND uid=?)');
            $STMT->bind_param('iiii', $player->id, $this->parent->id, $player->id, $this->parent->id);
            $STMT->execute();
            $STMT->close();
        }
    }

    function Remove($uid) {
        $player = PLYROBJ($uid);
        $STMT = SQL()->prepare('DELETE FROM user_friends WHERE (uid=? AND uuid=?) OR (uuid=? AND uid=? AND status!=2)');
        $STMT->bind_param('iiii', $player->id, $this->parent->id, $player->id, $this->parent->id);
        $STMT->execute();
        $STMT->close();
    }

    function Find($uid) {
        $STMT = SQL()->prepare('SELECT status FROM user_friends WHERE uid=? AND uuid=?');
        $STMT->bind_param('ii', $this->parent->id, $uid);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return -1;
        }
        $STMT->close();
        $row = $result->fetch_row();
        return $row[0];
    }

    function Block($uid) {
        $STMT = SQL()->prepare('DELETE FROM user_friends WHERE (uid=? AND uuid=?) OR (uuid=? AND uid=?)');
        $STMT->bind_param('iiii', $this->parent->id, $uid, $this->parent->id, $uid);
        $STMT->execute();
        $STMT->close();

        $STMT = SQL()->prepare('INSERT INTO user_friends (uid,uuid,status) VALUES (?,?,2)');
        $STMT->bind_param('ii', $this->parent->id, $uid);
        $STMT->execute();
        $STMT->close();
    }

    function Tag($uid, $tag) {
        $STMT = SQL()->prepare('UPDATE user_friends SET tag=? WHERE uid=? AND uuid=?');
        $STMT->bind_param('sii', $tag, $this->parent->id, $uid);
        $STMT->execute();
        $STMT->close();
    }

    function All() {
        $STMT = SQL()->prepare('SELECT uuid FROM user_friends WHERE uid=? AND status=1');
        $STMT->bind_param('i', $this->parent->id);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            return false;
        }
        $STMT->close();
        $items = array();
        while ($item = $result->fetch_row()) {
            $items[] = PLYROBJ($item[0]);
        }
        return $items;
    }

    function AllRequests() {
        $STMT = SQL()->prepare('SELECT uuid FROM user_friends WHERE uid=? AND status=0');
        $STMT->bind_param('i', $this->parent->id);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $items = array();
        while ($item = $result->fetch_row()) {
            $items[] = PLYROBJ($item[0]);
        }
        return $items;
    }

    function AllRequestsTotal() {
        $STMT = SQL()->prepare('SELECT COUNT(uuid) FROM user_friends WHERE uid=? AND status=0');
        $STMT->bind_param('i', $this->parent->id);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $item = $result->fetch_row();
        return $item[0];
    }

    function AllBlocked() {
        $STMT = SQL()->prepare('SELECT uuid FROM user_friends WHERE uid=? AND status=2');
        $STMT->bind_param('i', $this->parent->id);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $items = array();
        while ($item = $result->fetch_row()) {
            $items[] = PLYROBJ($item[0]);
        }
        return $items;
    }

    function AllTag($tag) {
        $STMT = SQL()->prepare('SELECT uuid FROM user_friends WHERE uid=? AND status=1 AND tag=?');
        $STMT->bind_param('is', $this->parent->id, $tag);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $items = array();
        while ($item = $result->fetch_row()) {
            $items[] = PLYROBJ($item[0]);
        }
        return $items;
    }

}
