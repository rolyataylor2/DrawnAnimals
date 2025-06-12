<?php

class SOCIALFORUMCLASS {
    
}

class SOCIALLIKECLASS {

    function Add($onwhat, $uid = null) {
        if ($uid === null) {
            $uid = PLYR()->id;
        }
        if ($this->Get($onwhat, $uid) !== 0) {
            return false;
        }
        $STMT = SQL()->prepare('INSERT INTO social_likes (uid,onwhat) VALUES(?,?)');
        $STMT->bind_param('is', $uid, $onwhat);
        $STMT->execute();
        $STMT->close();
    }

    function Remove($onwhat, $uid = null) {
        if ($uid === null) {
            $uid = PLYR()->id;
        }
        $STMT = SQL()->prepare('DELETE FROM social_likes WHERE uid=? AND onwhat=?');
        $STMT->bind_param('is', $uid, $onwhat);
        $STMT->execute();
        $STMT->close();
    }

    function Get($onwhat, $uid = null) {
        if ($uid != null) {
            $STMT = SQL()->prepare('SELECT COUNT(uid) FROM social_likes WHERE uid=? AND onwhat=?');
            $STMT->bind_param('is', $uid, $onwhat);
        } else {
            $STMT = SQL()->prepare('SELECT COUNT(uid) FROM social_likes WHERE onwhat=?');
            $STMT->bind_param('s', $onwhat);
        }
        $STMT->execute();
        if (($result = $STMT->get_result())===false) {
            #STMT->close();
            return false;
        }
        $STMT->close();
        $number = $result->fetch_row();
        return $number[0];
    }

    //Shortcuts for the add function
//    function Comment($id) {
//        
//    }
//
//    function Drawnimal($id) {
//        
//    }
//
//    function DrawnimalDefinition($species) {
//        
//    }
//
//    function Species($species) {
//        
//    }
//
//    function TimelinePost($id) {
//        
//    }

}

class SOCIALTIMELINECLASS {
    function Add($type, $arguments, $uid=null) {
        if ($uid === null) {
            $uid = PLYR()->id;
        }
        $STMT = SQL()->prepare('INSERT INTO social_timeline (uid,type,arguments,datetime) VALUES(?,?,?,?)');
        $STMT->bind_param('issi', $uid, $type, $arguments,time());
        $STMT->execute();
        $STMT->close();
    }

    function Remove($id) {
        $STMT = SQL()->prepare('DELETE FROM social_timeline WHERE id=?');
        $STMT->bind_param('i',$id);
        $STMT->execute();
        $STMT->close();
    }

    function Search($searchterm) {
        $STMT = SQL()->prepare('SELECT * FROM social_timeline WHERE arguments LIKE "%?%" OR uid=?');
        $STMT->bind_param('si',$searchterm,intval($searchterm));
        $STMT->execute();
        if (($result = $STMT->get_result())===false) {
            #STMT->close();
            return false;
        }
        $items = array();
        while ($item = $result->fetch_assoc()) {
            $item['arguments'] = explode('|',$item['arguments']);
            $items[] = $item;
        }
        $STMT->close();
        return $items;
    }

    function Get($id) {
        $STMT = SQL()->prepare('SELECT * FROM social_timeline WHERE id=?');
        $STMT->bind_param('i',$id);
        $STMT->execute();
        if (($result = $STMT->get_result())===false) {
            #STMT->close();
            return false;
        }
        $STMT->close();
        $item = $result->fetch_assoc();
        $item['arguments'] = explode('|',$item['arguments']);
        return $item;
    }

    function PrintSegment($id) {
        $item = $this->Get($id);
        $user = new PLAYERCLASS($item['uid']);
        echo TWIG()->renderFind('soc-var/timeline/'.$item['type'].'.twig', array('DATETIME' => $item['datetime'],
                                                                                 'USERNAME' => $user->Username(),
                                                                                 'ARGUMENTS' => $item['arguments']));
    }

    //Shortcuts for the add function
//    function DrawnimalCaught($species, $id) {
//        $this->Add('newdrawnimal',implode('|',array($species,$id)));
//    }
//
//    function DrawnimalEvolved($species, $speciestoo, $id) {
//        $this->Add('evolveddrawnimal',implode('|',array($species,$speciestoo,$id)));
//    }
//
//    function HatchedEgg($species, $id) {
//        
//    }
//
//    function FriendPost($content) {
//        
//    }

}
// @todo finish the below classes
class SOCIALVIEWCLASS {

    function Add($onwhat) {
        
    }

    function Set($onwhat) {
        
    }

    function Get($onwhat) {
        
    }

    function PrintViews($onwhat) {
        
    }

    //Shortcuts for the add function
//    function Drawnimal($id) {
//        
//    }
//
//    function DrawnimalDefinition($species) {
//        
//    }
//
//    function Player($id) {
//        
//    }
//
//    function Battle($id) {
//        
//    }
//
//    function Comment($id) {
//        
//    }

}

class SOCIALCOMMENTCLASS {

    function Add($onwhat, $content, $mood) {
        
    }

    function Remove($id) {
        
    }

    function Get($onwhat, $offset, $limit) {
        
    }

    //Shortcuts for the add function
//    function Drawnimal($id) {
//        
//    }
//
//    function Player($id) {
//        
//    }
//
//    function Battle($id) {
//        
//    }
//
//    function TimelinePost($id) {
//        
//    }
//
//    function Comment($id) {
//        
//    }

}

class SOCIALCLASS {

    function __construct() {
        $this->likedata = new SOCIALLIKECLASS();
        $this->timelinedata = new SOCIALTIMELINECLASS();
        $this->viewdata = new SOCIALVIEWCLASS();
        $this->commentdata = new SOCIALCOMMENTCLASS();
    }

    function Like() {
        return $this->likedata;
    }

    function Timeline() {
        return $this->timelinedata;
    }

    function View() {
        return $this->viewdata;
    }

    function Comment() {
        return $this->commentdata;
    }

}

$GLOBALS['SOCIALCLASS'] = new SOCIALCLASS();

function SOC() {
    return $GLOBALS['SOCIALCLASS'];
}
