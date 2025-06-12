<?php

$GLOBALS['PLYRMESSAGES_LOADED'] = array();

function PLYRMESSAGESOBJ($uid) {
    if (!isset($GLOBALS['PLYRMESSAGES_LOADED'][$uid])) {
        $GLOBALS['PLYRMESSAGES_LOADED'][$uid] = new PLYRMESSAGESCLASS($uid);
    }
    return $GLOBALS['PLYRMESSAGES_LOADED'][$uid];
}

class PLYRMESSAGESCLASS {

    function __construct($uid) {
        $this->parent = PLYROBJ($uid);
    }

    function Add($sender, $subject, $body, $theme, $item=0, $delay = 0) {
        $delay = time() + $delay;
        
        $STMT = SQL()->prepare('INSERT INTO user_messages(uid,sender,subject,body,item,theme,unread,datetime) VALUES(?,?,?,?,?,?,0,?)');
        $subject = htmlspecialchars($subject);
        $theme = htmlspecialchars($theme);
        $body = ProcessBBcode($body);
        $STMT->bind_param('iissisi', $this->parent->id, $sender, $subject, $body, $item, $theme, $delay);
        $STMT->execute();
        $STMT->close();
    }

    function Get($id) {
        $STMT = SQL()->prepare('SELECT * FROM user_messages WHERE uid=? AND id=? AND datetime<=?');
        $STMT->bind_param('iii', $this->parent->id, $id, time());
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        return $result->fetch_assoc();
    }

    function RemoveItem($id) {
        $STMT = SQL()->prepare('UPDATE user_messages SET item=0 WHERE uid=? AND id=? AND datetime<=?');
        $STMT->bind_param('iii', $this->parent->id, $id, time());
        $STMT->execute();
        $STMT->close();
        return true;
    }
    
    function Search($search) {
        $search = "%$search%";
        $STMT = SQL()->prepare('SELECT id,sender,subject,theme,unread,datetime FROM user_messages WHERE uid=? AND datetime<=? AND (subject LIKE ? OR body LIKE ?)');
        $STMT->bind_param('iiss', $this->parent->id, time(), $search, $search);
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

    function MarkRead($array) {
        if (!is_array($array)) {
            $array = array($array);
        }
        $item = 0;
        $STMT = SQL()->prepare('UPDATE user_messages SET unread=2 WHERE uid=? AND id=? AND datetime<=?');
        $STMT->bind_param('isi', $this->parent->id, $item, time());
        foreach ($array as $item) {
            $STMT->execute();
        }
        $STMT->close();
    }

    function MarkUnRead($array) {
        if (!is_array($array)) {
            $array = array($array);
        }
        $item = 0;
        $STMT = SQL()->prepare('UPDATE user_messages SET unread=1 WHERE uid=? AND id=? AND datetime<=?');
        $STMT->bind_param('isi', $this->parent->id, $item, time());
        foreach ($array as $item) {
            $STMT->execute();
        }
        $STMT->close();
    }

    function MarkSeen() {
        $STMT = SQL()->prepare('UPDATE user_messages SET unread=1 WHERE uid=? AND datetime<=?');
        $STMT->bind_param('ii', $this->parent->id, time());
        $STMT->execute();
        $STMT->close();
    }

    function Delete($array) {
        if (!is_array($array)) {
            $array = array($array);
        }
        $item = 0;
        $STMT = SQL()->prepare('DELETE FROM user_messages WHERE uid=? AND id=? AND datetime<=?');
        $STMT->bind_param('isi', $this->parent->id, $item, time());
        foreach ($array as $item) {
            $STMT->execute();
        }
        $STMT->close();
    }

    function All($limit = 10, $offset = 0) {
        $STMT = SQL()->prepare('SELECT id,sender,subject,theme,unread,datetime,item FROM user_messages WHERE uid=? AND datetime<=? ORDER BY datetime DESC, unread ASC LIMIT ?,?');
        $STMT->bind_param('iiii', $this->parent->id, time(), $offset, $limit);
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

    function AllSent($limit = 10, $offset = 0) {
        $STMT = SQL()->prepare('SELECT id,uid,subject,theme,unread,datetime,item FROM user_messages WHERE sender=? AND datetime<=? ORDER BY datetime DESC, unread ASC LIMIT ?,?');
        $STMT->bind_param('iiii', $this->parent->id, time(), $offset, $limit);
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

    function AllRead($limit = 10, $offset = 0) {
        $STMT = SQL()->prepare('SELECT id,sender,subject,theme,unread,datetime FROM user_messages WHERE uid=? AND unread=2 AND datetime<=? ORDER BY datetime LIMIT ?,?');
        $STMT->bind_param('iiii', $this->parent->id, time(), $offset, $limit);
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

    function AllUnread($limit = 10, $offset = 0) {
        $STMT = SQL()->prepare('SELECT id,sender,subject,theme,unread,datetime FROM user_messages WHERE uid=? AND unread=1 AND datetime<=? ORDER BY datetime LIMIT ?,?');
        $STMT->bind_param('iiii', $this->parent->id, time(), $offset, $limit);
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

    function AllUnseenTotal() {
        $STMT = SQL()->prepare('SELECT COUNT(id) FROM user_messages WHERE uid=? AND unread=0 AND datetime<=?');
        $STMT->bind_param('ii', $this->parent->id, time());
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $items = $result->fetch_row();
        return $items[0];
    }
    
    function Printer($array,$items) {
        $results = array();
        while($letter = array_shift($array)) {
            $arguments = array();
            $arguments['ID'] = $letter['id'];
            
            $itemslist = explode('|', $items);
            foreach ($itemslist as $item) {
                switch ($item) {
                    case 'sender':
                        $sender = PLYROBJ($letter['sender']);
                        $arguments['SENDERID'] = $letter['sender'];
                        $arguments['SENDER'] = $sender->Username();
                        break;
                    case 'senderavatar':
                        $sender = PLYROBJ($letter['sender']);
                        $arguments['SENDERAVATAR'] = '<img class="senderavatar" src="http://img.drawnimals.com/avatars/'.$sender->AvatarForum().'.png"/>';
                        break;
                    case 'recipient':
                       $recipient = PLYROBJ($letter['uid']);
                       $arguments['REC_USERNAME'] = $recipient->Username();
                       break;
                    case 'recipientavatar':
                       $recipient = PLYROBJ($letter['uid']);
                       $arguments['REC_AVATAR'] = '<img class="recipientavatar" src="http://img.drawnimals.com/avatars/'.$recipient->AvatarForum().'.png"/>';
                       break;
                    case 'theme':
                        $arguments['THEME'] = $letter['theme'];
                        break;
                    case 'icon':
                        $arguments['ICON'] = '<img class="icon" src="http://img.drawnimals.com/icons/letter-'.$letter['unread'].'.png"/>';
                        break;
                    case 'item':
                        $arguments['ITEM'] = $letter['item'];
                        break;
                    case 'timestamp':
                        $arguments['TIMESTAMP'] = date('g:iA - D jS - F Y',$letter['datetime']);
                        break;
                    case 'subject':
                        $arguments['SUBJECT'] = $letter['subject'];
                        break;
                    
                }
            }
            $results[] = TWIG()->render('/_plugins/msg/Summery.twig', $arguments);
            
        }
        return $results;
    }

}
