<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class PLYRTRAINERCLASS {
    function __construct($uid) {
        $this->parent = PLYROBJ($uid);
    }
    function Get($offset=0,$limit=10) {
        $STMT = SQL()->prepare('SELECT user_trainers.tid, system_trainers.name, system_trainers.difficulty, 
                                       system_trainers.image, user_trainers.defeated,
                                       system_maps.name, COUNT(system_trainer_drawnimals.pid), 
                                       AVG(system_trainer_drawnimals.level)
                                FROM user_trainers 
                                LEFT JOIN system_trainers 
                                ON user_trainers.tid = system_trainers.id
                                LEFT JOIN system_trainer_drawnimals
                                ON user_trainers.tid = system_trainer_drawnimals.tid
                                LEFT JOIN system_maps
                                ON system_trainers.locationid = system_maps.id
                                WHERE user_trainers.uid = '.$this->parent->id.'
                                GROUP BY user_trainers.tid
                                ORDER BY system_trainers.name
                                LIMIT ?,?') or die(SQL()->error);
        $STMT->bind_param('ii',$offset,$limit);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            return array();
        }
        $list = array();
        while ($row = $result->fetch_row()) {
            $list[] = array('id'=>$row[0],
                            'name'=>$row[1],
                            'difficulty'=>floor($row[2]),
                            'image'=>$row[3],
                            'defeated'=>$row[4],
                            'location'=>$row[5],
                            'total'=>$row[6],
                            'level'=>floor($row[7]));
        }
        return $list;
    }
    function GetDefeated($offset=0,$limit=10) {
        $STMT = SQL()->prepare('SELECT user_trainers.tid, system_trainers.name, system_trainers.difficulty, 
                                       system_trainers.image, user_trainers.defeated,
                                       system_maps.name, COUNT(system_trainer_drawnimals.pid), 
                                       AVG(system_trainer_drawnimals.level)
                                FROM user_trainers 
                                LEFT JOIN system_trainers 
                                ON user_trainers.tid = system_trainers.id
                                LEFT JOIN system_trainer_drawnimals
                                ON user_trainers.tid = system_trainer_drawnimals.tid
                                LEFT JOIN system_maps
                                ON system_trainers.locationid = system_maps.id
                                WHERE user_trainers.uid = '.$this->parent->id.'
                                GROUP BY user_trainers.tid
                                ORDER BY system_trainers.name
                                LIMIT ?,?') or die(SQL()->error);
        $STMT->bind_param('ii',$offset,$limit);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            return array();
        }
        $list = array();
        while ($row = $result->fetch_row()) {
            $list[] = array('id'=>$row[0],
                            'name'=>$row[1],
                            'difficulty'=>floor($row[2]),
                            'image'=>$row[3],
                            'defeated'=>$row[4],
                            'location'=>$row[5],
                            'total'=>$row[6],
                            'level'=>floor($row[7]));
        }
        return $list;
    }
    function GetSingle($tid) {
        $STMT = SQL()->prepare('SELECT user_trainers.tid, system_trainers.name, system_trainers.difficulty, 
                                       system_trainers.image, user_trainers.defeated,
                                       system_maps.name, COUNT(system_trainer_drawnimals.pid), 
                                       AVG(system_trainer_drawnimals.level)
                                FROM user_trainers 
                                LEFT JOIN system_trainers 
                                ON user_trainers.tid = system_trainers.id
                                LEFT JOIN system_trainer_drawnimals
                                ON user_trainers.tid = system_trainer_drawnimals.tid
                                LEFT JOIN system_maps
                                ON system_trainers.locationid = system_maps.id
                                WHERE user_trainers.uid = '.$this->parent->id.' AND user_trainers.tid = ?
                                GROUP BY user_trainers.tid
                                ORDER BY system_trainers.name
                                LIMIT 1') or die(SQL()->error);
        $STMT->bind_param('i',$tid);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            return array();
        }
        $row = $result->fetch_row();
        return array('id'=>$row[0],
                    'name'=>$row[1],
                    'difficulty'=>floor($row[2]),
                    'image'=>$row[3],
                    'defeated'=>$row[4],
                    'location'=>$row[5],
                    'total'=>$row[6],
                    'level'=>floor($row[7]));
    }
    function GetSearchName($search, $offset=0, $limit=10) {
        $STMT = SQL()->prepare('SELECT user_trainers.tid, system_trainers.name, system_trainers.difficulty, 
                                       system_trainers.image, user_trainers.defeated,
                                       system_maps.name, COUNT(system_trainer_drawnimals.pid), 
                                       AVG(system_trainer_drawnimals.level)
                                FROM user_trainers 
                                LEFT JOIN system_trainers 
                                ON user_trainers.tid = system_trainers.id
                                LEFT JOIN system_trainer_drawnimals
                                ON user_trainers.tid = system_trainer_drawnimals.tid
                                LEFT JOIN system_maps
                                ON system_trainers.locationid = system_maps.id
                                WHERE user_trainers.uid = '.$this->parent->id.'  '
                . '                 AND CONVERT( system_trainers.name USING utf8) LIKE "%?%"
                                GROUP BY user_trainers.tid
                                ORDER BY system_trainers.name
                                LIMIT ?,?') or die(SQL()->error);
        $STMT->bind_param('sii',$search,$offset,$limit);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            return array();
        }
        $list = array();
        while ($row = $result->fetch_row()) {
            $list[] = array('id'=>$row[0],
                            'name'=>$row[1],
                            'difficulty'=>floor($row[2]),
                            'image'=>$row[3],
                            'defeated'=>$row[4],
                            'location'=>$row[5],
                            'total'=>$row[6],
                            'level'=>floor($row[7]));
        }
        return $list;
    }
    function Set($tid,$defeated) {
        $STMT = SQL()->prepare('UPDATE user_trainers SET defeated=? WHERE uid=? AND tid=?');
        $STMT->bind_param('iii',$defeated,$this->parent->id,$tid);
        $STMT->execute();
        $STMT->close();
    }
    function Battle($tid) {
        
    }
}