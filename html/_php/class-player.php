<?php
class PLAYERCLASS extends TABLETEMPLATE {
    function __construct () {
        parent::__construct('nicapa', 'users');
        $this->className = 'PLAYERCLASS';
    }
    // Constructors
    function byNew($username,$password,$email) {
        $instance = new self();
        $instance->connection->select_db($instance->databasePrefix . $instance->database);
        $STMT = $instance->connection->prepare('INSERT INTO ' . $instance->table . '(username,password,email,status,datecreated,datelastseen) VALUES(?,?,?,"'.uniqid().'",' . time() . ',' . time() . ')') or die(SQL()->error);
        $STMT->bind_param('sss', $username, $instance->_saltme($password,$username),$email);
        $STMT->execute();
        $STMT->close();
        $instance->searchKey = 'id';
        $instance->searchValue = $instance->connection->insert_id;
        return $instance;
    }
    function byAll () {
        return parent::_loadArray('id!=?',0,'PLAYERCLASS');
    }
    function countAll () {
        return parent::_count('PLAYERCLASS',['id'],[0],'',['!=']);
    }
    function byId ($id) {
        return parent::_loadArray('id=?',intval($id),'PLAYERCLASS','')[0];
    }
    function byMe () {
        if ( ! isset($_SESSION)) {
            session_start();
        }
        if ( ! isset($_SESSION['MyID']) ) {
            return PLAYERCLASS::byid(-1);
        }

        return PLAYERCLASS::byid($_SESSION['MyID']);
    }
    function byUsername ($username) {
        return parent::_loadArray('username=?',$username,'PLAYERCLASS','')[0];
    }
    function byUsernameByPassword ($username,$password) {
        return parent::_loadArray("username='$username' AND password=?",PLAYERCLASS::_saltme($password,$username),'PLAYERCLASS')[0];
    }
    function byEmail ($email) {
        $instance = new self();
        $instance->searchKey = 'email';
        $instance->searchValue = $email;
        return $instance;
    }
    function byCountry ($country) {
        return parent::_loadArray('country=?', $country, 'PLAYERCLASS');
    }
    function byTimezone ($timezone) {
        return parent::_loadArray('timezone=?', $timezone, 'PLAYERCLASS');
    }
    function byStatus ($status) {
        return parent::_loadArray('status=?', $status, 'PLAYERCLASS');
    }
    function bySearch () {

    }

// Class Functions
    function Id () {
        return intval($this->_var('id'));
    }
    function Username ($newUsername = null) {
        return ucwords($this->_var('username', $newUsername));
    }

    function Email ($newEmail = null) {
        return $this->_var('email', $newEmail);
    }
    function _saltme($password,$username) {
        $username = strtolower($username);
        $password .= substr($username, -2);
        $password .= substr($username, -3);
        $password .= substr($username, 0, 2);
        $password .= substr($username, 1, 2);

        return md5($password);
    }
    function Password ($password,$newpassword = null) {
        if ($newpassword !== null) {
            $newpassword = $this->_saltme($newpassword,$this->Username());
        }
        return ($this->_var('password',$newpassword) === $this->_saltme($password,$this->Username()));
    }
    function Country ($newCountry = null) {
        return $this->_var('country', $newCountry);
    }
    function Gender ($newgender = null) {
        return $this->_var('gender', $newgender);
    }
    function Timezone ($newTimezone = null) {
        return $this->_var('timezone', $newTimezone);
    }
    function Created ($date = 'F, jS Y') {
        return date($date,$this->_var('datecreated'));
    }
    function LastSeen ($update='F, jS Y') {
        $value = null;
        if ($update === true) {
            $value = time();
            $update = 'F, jS Y';
        }
        return date($update,$this->_var('datelastseen',$value));
    }
    function CurrentTime ($format='g:i A') {
        if (strcmp($this->Timezone(),'') !== 0) {
            date_default_timezone_set($this->Timezone());
            $date = date($format);
            if (strcmp(PLAYERCLASS::byMe()->Timezone(),'') !== 0) {
                date_default_timezone_set(PLAYERCLASS::byMe()->Timezone());
            }
            return $date;
        } else {
            return '--:--';
        }

    }
    function Birthday ($newBirthday = null) {
        return date('F, jS Y',$this->_var('datebirth', $newBirthday));
    }
    function Ip ($newIp = null) {
        if ( $newIp !== null ) {
            $newIp = $_SERVER['REMOTE_ADDR'];
        }
        return $this->_var('ipaddress', $newIp);
    }
    function SessionId ($newSession = null) {
        if ( $newSession !== null ) {
            $newSession = session_id();
        }
        return $this->_var('sessionid', $newSession);
    }
    function BattleId ($battleId = null) {
        if ($battleId === -1) {
            $this->Monster()->byTeamByLeader()->BattleLeader(false);
        }
        return $this->_var('battleid', $battleId);
    }
    function Status ($newStatus = null) {
        return $this->_var('status', $newStatus);
    }
    function Type ($newType = null) {
        return $this->_var('type', $newType);
    }
    function Avatar ($newAvatar = null) {
        return $this->_var('avatar', $newAvatar);
    }
    function AvatarOw ($newAvatar = null) {
        return $this->_var('avatar_ow', $newAvatar);
    }
    function SiteTheme ($newType = null) {
        return $this->_var('sitetheme', $newType);
    }
    function AboutMe ($newText = null) {
        return $this->_var('aboutme', $newText);
    }
    function Region ($region = null) {
        return $this->_var('current_region', $region);
    }
    function Color ($color = null) {
        return $this->_var('color', $color);
    }
    function Follower ($newmonster = null) {
        return $this->_var('drawnimal_following', $newmonster);
    }
    function isAdmin($ofwhat='') {
        return (strpos($this->Type(),$ofwhat) !== false);
    }
    // Helper Classes
    function Reset() {
        foreach($this->Monster()->byTeam() as $i) {
            $i->ResetStats();
        }
        $this->BattleId(-1);
        $this->_save();
    }
    function Monster() {
        return new PLAYERCLASSMONSTER($this);
    }
    function Battle($value=null) {
        return BATTLECLASS::byId($this->BattleId($value));
    }
    function Experience() { return PLAYERCLASSEXPERIENCE::byUserId($this->Id()); }
    function Caught($sid, $set=null) {
        if ($set !== null) {
            return PLAYERCLASSCATALOG::byNew($this->Id(),$sid,1)->Caught()==1;
        }
        return PLAYERCLASSCATALOG::byUserIdBySpecies($this->Id(),$sid)->Caught()===1;
    }
    function Seen($sid, $set=null) {
        if ($set !== null) {
            return PLAYERCLASSCATALOG::byNew($this->Id(),$sid,0)->Caught()==0;
        }
        return PLAYERCLASSCATALOG::byUserIdBySpecies($this->Id(),$sid)->Caught()===0;
    }
    function Variable($name,$set=null) {
        if ($set !== null) {
            return PLAYERCLASSVARIABLE::byNew($this->Id(),$name,$set);
        }
        return PLAYERCLASSVARIABLE::byUserIdByName($this->Id(),$name);
    }
    function VariableTemp($name,$set=null) {
        if ($set !== null) {
            $_SESSION['variables'][$name] = $set;
        }
        return $_SESSION['variables'][$name];
    }
    function Update($type,$arguments) {return UPDATECLASS::byNew($this->Id(),$arguments,$type);}
}
class PLAYERCLASSMONSTER {
    function __construct (PLAYERCLASS $player) {
        $this->parent = $player;
    }
    function byId ($id) {
        return MONSTERCLASS::byOwnerById($this->parent, $id);
    }
    function byFollowing($newone = null) {
        return MONSTERCLASS::byOwnerById($this->parent,$this->parent->_var('drawnimal_following',$newone));
    }
    function byTeam () {
        return MONSTERCLASS::byOwnerByTeam($this->parent);
    }
    function byTeamByAlive () {
        return MONSTERCLASS::byOwnerByTeamByAlive($this->parent);
    }
    function byTeamByLeader () {
        return MONSTERCLASS::byOwnerByTeamByLeader($this->parent);
    }
    function byTeamById ($id) {
        return MONSTERCLASS::byOwnerByTeamById($this->parent, $id);
    }
    function byTeamBySpecies (CATALOGMONSTERCLASS $drawnimal) {
        return MONSTERCLASS::byOwnerByTeamBySpecies($this->parent, $drawnimal);
    }
    function byBox () {
        return MONSTERCLASS::byOwnerByBox($this->parent);
    }
    function byBoxBySpecies (CATALOGMONSTERCLASS $drawnimal) {
        return MONSTERCLASS::byOwnerByBoxBySpecies($this->parent, $drawnimal);
    }
    function bySpecies (CATALOGMONSTERCLASS $drawnimal) {
        return MONSTERCLASS::byOwnerBySpecies($this->parent, $drawnimal);
    }
    function byTrading () {
        return MONSTERCLASS::byOwnerByTrading($this->parent);
    }
}
class PLAYERCLASSEXPERIENCE extends TABLETEMPLATE {
    function __construct () { parent::__construct('nicapa', 'experience'); }

    // Constructors
    function byNew($uid) { return parent::_new('PLAYERCLASSEXPERIENCE',['uid'],[$uid]); }
    function byUserId ( $uid ) { return parent::_get('PLAYERCLASSEXPERIENCE',['uid'],[$uid])[0]; }

    // Class Functions
    function Id() { return $this->_var('id'); }
    function UserId() { return $this->_var('uid'); }
    function Coins($offset=null) {return $this->_var('coins',$offset,true);}
    function Cash($offset=null) {return $this->_var('cash',$offset,true);}
    function Alignment($offset=null) {return $this->_var('alignment',$offset,true);}
    function Type($typeid, $offset=null) { 
        if ($typeid===0) return 0;
        if ($offset !== null) {
            $this->_var('xp'.$typeid,$offset,true);
            $this->_save();
            return $this->_var('xp'.$typeid);
        }
        return $this->_var('xp'.$typeid,$offset,true);
    }
    function TypeArray() {
        $list = array();
        for($i=0;$i<18;$i++) {
            $list[] = $this->Type($i);
        }
        return $list;
    }
}
class PLAYERCLASSCATALOG extends TABLETEMPLATE {
    function __construct () { parent::__construct('nicapa', 'users_catalog'); }

    // Constructors
    function byNew($uid,$sid,$captured=0) { 
        $current = PLAYERCLASSCATALOG::byUserIdBySpecies($uid,$sid);
        if (!empty($current->Id())) {
            if ($current->Caught() !== 1) {
                PLAYERCLASS::byId($uid)->Update(5,  CREATEMONSTERCLASS::byId($sid)->Name());
                PLAYERCLASS::byId($uid)->Experience()->Type(CREATEMONSTERCLASS::byId($sid)->TypePrimary()->Id(),10);
            }
            $current->Caught($captured);
            $current->Date(time());
            $current->_save();
            return $current;
        }
        if ($current->Caught() === $captured) return $current;
        PLAYERCLASS::byId($uid)->Update(5,  CREATEMONSTERCLASS::byId($sid)->Name());
        PLAYERCLASS::byId($uid)->Experience()->Type(CREATEMONSTERCLASS::byId($sid)->TypePrimary()->Id(),10);
        return parent::_new('PLAYERCLASSCATALOG',['uid','sid','caught'],[$uid,$sid,$captured]);
    }
    function byUserId ( $uid ) { return parent::_get('PLAYERCLASSCATALOG',['uid'],[$uid]); }
    function byUserIdByCaught ( $uid, $caught ) { return parent::_get('PLAYERCLASSCATALOG',['uid','caught'],[$uid,$caught]); }
    function byUserIdBySpecies ($uid,$sid) { return parent::_get('PLAYERCLASSCATALOG',['uid','sid'],[$uid,$sid])[0];}

    // Class Functions
    function Id() { return $this->_var('id'); }
    function UserId() { return $this->_var('uid'); }
    function Species() { return CREATEMONSTERCLASS::byId($this->_var('sid')); }
    function Caught($value=null) { return $this->_var('caught',$value); }
    function Date($value=null) { return date('Ymj',$this->_var('date_time',$value)); }
}
class PLAYERCLASSTRAINERS extends TABLETEMPLATE {
    function __construct () { parent::__construct('nicapa', 'users_trainers'); }

    // Constructors
    function byNew($uid,$tid,$team,$win=0) { 
        $current = PLAYERCLASSTRAINERS::byUserIdByTrainerByTeam($uid,$tid);
        if (!empty($current->Id())) {
            $current->Win($win);
            $current->Date(time());
            $current->_save();
            return $current;
        }
        return parent::_new('PLAYERCLASSCATALOG',['uid','tid','team','win','date_time'],[$uid,$tid,$team,$win,time()]);
    }
    function byUserId ( $uid ) { return parent::_get('PLAYERCLASSTRAINERS',['uid'],[$uid]); }
    function byUserIdByWin ( $uid, $win ) { return parent::_get('PLAYERCLASSTRAINERS',['uid','win'],[$uid,$win]); }
    function byUserIdByTrainer ($uid,$sid) { return parent::_get('PLAYERCLASSTRAINERS',['uid','tid'],[$uid,$sid]);}
    function byUserIdByTrainerByTeam ($uid,$sid,$team) { return parent::_get('PLAYERCLASSTRAINERS',['uid','tid','team'],[$uid,$sid,$team])[0];}

    // Class Functions
    function Id() { return $this->_var('id'); }
    function UserId() { return $this->_var('uid'); }
    function Trainer() { return CREATETRAINERCLASS::byId($this->_var('tid')); }
    function Team() { return $this->_var('team'); }
    function Win($value=null) { return $this->_var('win',$value); }
    function Date($value=null) { return date('Ymj',$this->_var('date_time',$value)); }
}
class PLAYERCLASSVARIABLE extends TABLETEMPLATE {
    function __construct () { parent::__construct('nicapa', 'users_variables'); }

    // Constructors
    function byNew($uid,$name,$value=0) { 
        $current = PLAYERCLASSVARIABLE::byUserIdByName($uid,$name);
        if (!empty($current->Id())) {
            $current->Value($value);
            $current->Date(time());
            $current->_save();
            return $current;
        }
        return parent::_new('PLAYERCLASSVARIABLE',['uid','name','currentvalue'],[$uid,$name,$value]);
    }
    function byUserId ( $uid ) { return parent::_get('PLAYERCLASSVARIABLE',['uid'],[$uid]); }
    function byUserIdByName ( $uid, $name ) { return parent::_get('PLAYERCLASSVARIABLE',['uid','name'],[$uid,$name])[0]; }
    function byUserIdByValue ($uid,$value) { return parent::_get('PLAYERCLASSVARIABLE',['uid','value'],[$uid,$value]);}

    // Class Functions
    function Id() { return $this->_var('id'); }
    function UserId() { return $this->_var('uid'); }
    function Name() { return $this->_var('name'); }
    function Value($value=null) { 
        if ($value !== null) {
            $this->Date(time());
        }
        return $this->_var('currentvalue',$value);
    }
    function Date($value=null) { return date('Ymj',$this->_var('date_time',$value)); }
}