<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class PLYRQUESTCLASS {
    function __construct($uid) {
        $this->parent = PLYROBJ($uid);
    }
    function Set($name,$stage=0) {
        
    }
    function Get($name=null) {
        $list = array();
        if ($name===null) {
            $result = SQL()->Query("SELECT system_quests.name, system_quests.image, system_quests.description, system_quests.region,
                                            system_quest_stages.description, user_quests.stageid, system_quests.stagecount
                                     FROM user_quests
                                     LEFT JOIN system_quests
                                     ON system_quests.id = user_quests.questid
                                     LEFT JOIN system_quest_stages
                                     ON system_quest_stages.questid=user_quests.questid AND system_quest_stages.stageid=user_quests.stageid
                                     WHERE user_quests.uid=".$this->parent->id) or die(SQL()->error);
            if ($result === false) {
                return false;
            }
            while ($row = $result->fetch_row()) {
                $list[] = array('name'=>$row[0],
                              'image'=>$row[1],
                              'description'=>$row[2],
                              'region'=>$row[3],
                              'task'=>$row[4],
                              'stage'=>$row[5],
                              'stagecount'=>$row[6]);
            }
            return $list;
        }
        return $list;
    }
    
}