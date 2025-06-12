<?php
include_once 'html/_php/class-render.php';
include_once 'html/_php/class-items.php';
include_once 'html/_php/class-monsters.php';

class BATTLEQUEUECLASS extends TABLETEMPLATE {

    function __construct() {
        parent::__construct('dr_battles', 'queue');
    }

    function byNew(BATTLECLASS $battle, $round) {
        return parent::_new('BATTLEQUEUECLASS',['bid','round'],[$battle->Id(),$round]);
    }

    function byBattleByRound($battleid, $round = 1) {
        return parent::_loadArray('bid=' . $battleid . ' AND round=?', intval($round), 'BATTLEQUEUECLASS')[0];
    }

    function Id() {
        return $this->_var('id');
    }

    function Battle() {
        return BATTLECLASS::byId($this->_var('bid'));
    }

    function Round() {
        return $this->_var('round');
    }

    function QueueCode($append = null) {
        if ($append !== null) {
            $append = $this->_var('data') . ' ' . $append;
        }
        return $this->_var('data', $append);
    }

    function _event() {
        
    }

    function StartBattle($battle) {
        $this->SetupRound($battle);
        $players = $battle->AllPlayers();
        $this->PlaySoundBgm('http://PokeWorlds.com/sfx/m/wildBattle.mp3');
        $ii = 0;
        foreach ($players as $i) {
            if ($i->isWild()) {
                $this->Dialog('Wild ' . ucwords($i->ActiveMonster()->Nickname()) . ' Appeared!');
                $this->ShowDrawnimal($i->DivLabel());
                $this->PlaySound($i->ActiveMonster()->Species()->Render()->soundUrl());
            } elseif (!$i->isPlayer()) {
                $trainer = $i->TrainerClass();
                $this->Dialog(ucwords($trainer->TrainerClass()->Name()) . ' ' . ucwords($trainer->Name()) . ' Wants To Battle!');
                $this->Dialog(ucwords($trainer->TrainerClass()->Name()) . ' ' . ucwords($trainer->Name()) . ' Sent Out ' . ucwords($i->ActiveMonster()->Nickname()) . '!');
                $this->ShowDrawnimal($i->DivLabel());
                $this->PlaySound($i->ActiveMonster()->Species()->Render()->soundUrl());
            } else {
                $this->Dialog($i->Username() . ' is ready for battle!');
                $this->Dialog($i->Username() . ': Go ' . $i->ActiveMonster()->Nickname() . '!');
                $this->ShowDrawnimal($i->DivLabel());
                $this->PlaySound($i->ActiveMonster()->Species()->Render()->soundUrl());
            }
            $ii++;
        }
        $this->_save();
    }

    function SetupRound($battle) {
        // Set all the pokemon images, set whether they are hidden?
        foreach ($battle->AllPlayers() as $i) {
            $this->ChangeDrawnimalImage($i->DivLabel(), $i->ActiveMonster()->Render()->imageUrl());
            $this->ChangeDrawnimalName($i->DivLabel(), $i->ActiveMonster()->Nickname());
            $this->ChangeDrawnimalHp($i->DivLabel(), $i->ActiveMonster()->Hp(), $i->ActiveMonster()->Stat('hp'));
            $this->ChangeDrawnimalLevel($i->DivLabel(), $i->ActiveMonster()->Level());
        }
    }
    function Dialog($string, $userid = '') {
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',['$string','$userid']);");
    }
    function Wait($amount) {
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',['$amount']);");
    }
    
    function PlaySound($url,$playbackspeed=1,$volume=1) {
        if (strcmp($url,'')===0) {
            $this->Dialog("SOUND DOESNT EXIT");
        }
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',['$url',$playbackspeed,$volume]);");
    }
    
    function StopSoundBgm() {
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "');");
    }
    
    function PlaySoundBgm($url) {
        if (strcmp($url,'')===0) {
            $this->Dialog("SOUND DOESNT EXIT");
        }
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',['$url']);");
    }

    function AnimateAttack($from, $too, $attack) {
        
    }

    function HideDrawnimal($divLabel) {
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',['$divLabel']);");
    }

    function ShowDrawnimal($divLabel) {
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',['$divLabel']);");
    }
    
    function FaintDrawnimal($divLabel) {
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',['$divLabel']);");
    }

    function ChangeDrawnimalImage($divlabel, $url) {
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',['$divlabel','$url']);");
    }

    function ChangeDrawnimalHp($userid, $hp, $hptotal) {
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',['$userid','$hp','$hptotal']);");
    }

    function ChangeDrawnimalOwner($userid, $nickname) {
        
    }

    function ChangeDrawnimalLeft($userid, $total, $left) {
        
    }

    function ChangeDrawnimalName($divLabel, $name) {
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',['$divLabel','$name']);");
    }

    function ChangeDrawnimalLevel($divLabel, $level) {
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',['$divLabel','$level']);");
    }

    function ChangeDrawnimalType($userid, $type0, $type1) {
        
    }

    function GotoGame() {
        $this->QueueCode("BTTL.enQueue('event" . __FUNCTION__ . "',[]);");
    }

}

class BATTLETRAINERCLASS {

    private $is_player = false;
    private $is_wild = false;
    private $battle = null;

    function byBattleById($battle, $string) {
        $instance = new self();
        if (empty($string)) {
            return false;
        }
        if (is_numeric($string)) {
            $instance->is_wild = false;
            $instance->is_player = true;
            $instance->id = $instance->trainerId = intval($string);
            $instance->battle = $battle;
            return $instance;
        }
        $args = explode('|', $string);
        $instance->is_wild = false;
        $instance->is_player = false;
        $instance->id = explode('|', $string)[1];
        $instance->trainerId = $string;
        $instance->battle = $battle;
        if ($args[0] === 'wild') {
            $instance->is_wild = true;
        }
        return $instance;
    }

    function Id() {
        return $this->id;
    }

    function TrainerId() {
        return $this->trainerId;
    }

    function DivLabel() {
        return '' . $this->Username() . '-' . ( $this->isPlayer() ? '' : 'AI' ) . $this->Id();
    }

    function TrainerClass() {
        return CREATETRAINERCLASS::byId($this->Id());
    }

    function Username() {
        if ($this->isWild()) {
            return 'Wild';
        }
        if ($this->isPlayer()) {
            return PLAYERCLASS::byId($this->Id())->Username();
        }
        return $this->TrainerClass()->Name();
    }

    function Monsters() {
        if ($this->isPlayer()) {
            return PLAYERCLASS::byId($this->Id())->Monster()->byTeam();
        }
        return BATTLEDRAWNIMALCLASS::byBattleByTrainer($this->Battle(), $this->TrainerId());
    }

    function ActiveMonster() {
        if ($this->isPlayer() == true) {
            return PLAYERCLASS::byId($this->Id())->Monster()->byTeamByLeader();
        } else {
            return BATTLEDRAWNIMALCLASS::byBattleByTrainerByLeader($this->Battle(), $this->TrainerId());
        }
    }

    function Battle() {
        return $this->battle;
    }

    function isPlayer() {
        return $this->is_player;
    }

    function isMe() {
        if ($this->isPlayer() && $this->Id() === PLAYERCLASS::byMe()->Id()) {
            return true;
        }
        return false;
    }

    function isWild() {
        return $this->is_wild;
    }
    
    function isDead() {
        foreach($this->Monsters() as $i) {
            if (intval($i->Hp()) > 0 && $i->Egg()===false) {
                return false;
            }
        }
        return true;
    }

}

class BATTLEDRAWNIMALCLASS extends TABLETEMPLATE {

    public $parentbattle = 0;

    function __construct() {
        parent::__construct('dr_battles', 'drawnimals');
    }

    function byNew($battle, $trainerid, $species, $level, $difficulty = 0) {
        $experiance = pow($level, 3);
        $atkiv = mt_rand(0, 31);
        $defiv = mt_rand(0, 31);
        $spatkiv = mt_rand(0, 31);
        $spdefiv = mt_rand(0, 31);
        $speediv = mt_rand(0, 31);
        $hpiv = mt_rand(0, 31);

        $atkev = mt_rand(0, $difficulty);
        $defev = mt_rand(0, $difficulty);
        $spatkev = mt_rand(0, $difficulty);
        $spdefev = mt_rand(0, $difficulty);
        $speedev = mt_rand(0, $difficulty);
        $hpev = mt_rand(0, $difficulty);

        $abilities_default = '';
        $abilities = $species->Abilities();
        foreach ($abilities as $ability) {
            if (isset($ability))
            if (mt_rand(0, 200) < $ability->Percentage()) {
                $abilities_default .= $ability->Name() . '?0|';
            }
        }

        $item_held = 0;
        $items = $species->Items();
        foreach ($items as $item) {
            if (mt_rand(0, 200) < $item[1]) {
                $item_held = createItem($item[0])->Id();
                break;
            }
        }

        $moves = MONSTERLEARNSETCLASS::byMonsterByBelowLevel($species->Id(), $level);
        $movename = array();
        $movepp = array();
        for ($i = 0; $i < 4; $i++) {
            if (isset($moves[$i])) {
                $movename[] = CREATEMOVECLASS::byId($moves[$i]->Move())->Name();
                $movepp[] = CREATEMOVECLASS::byId($moves[$i]->Move())->PP();
            } else {
                $movepp[] = 0;
                $movename[] = '';
            }
        }

        $happiness = 64000;
        
        $newDrawnimal =  parent::_new('BATTLEDRAWNIMALCLASS',['battleid', 'uid', 'name', 'sid', 'ailments_default', 'ailments', 
                                            'st_level', 'st_exp', 'iv_hp', 'iv_atk', 'iv_def', 'iv_spatk', 'iv_spdef', 'iv_speed', 
                                            'ev_hp', 'ev_atk', 'ev_def', 'ev_spatk', 'ev_spdef', 'ev_speed', 
                                            'move_0','move_pp_0','move_1','move_pp_1','move_2','move_pp_2','move_3','move_pp_3',
                                            'st_mood_datetime', 'st_friendship'],
                                            [$battle->Id(), $trainerid, ucwords($species->Name()), $species->Id(), $abilities_default, $abilities_default, 
                                             $level, $experiance, $hpiv, $atkiv, $defiv, $spatkiv, $spdefiv, $speediv, 
                                             $hpev, $atkev, $defev, $spatkev, $spdefev, $speedev, 
                                             $movename[0], $movepp[0], $movename[1], $movepp[1], $movename[2], $movepp[2], $movename[3], $movepp[3], 
                                             time(), $happiness]);


        if ($species->GenderRate() === 255) {
            $newDrawnimal->Gender(3);
        } else {
            $newDrawnimal->Gender((rand(0, 255) < $species->GenderRate() ? 2 : 1));
        }
        if (mt_rand(1, 8192) === 1) {
            $newDrawnimal->Paint('shiny');
        }

        $newDrawnimal->_save();
        $newDrawnimal->data = array();
        return $newDrawnimal;
    }

    function byId($id) {
        $instance = new self();
        $instance->searchKey = 'id';
        $instance->searchValue = $id;
        return $instance;
    }

    function byBattle($battle) {
        return parent::_loadArray('battleid=?', $battle->Id(), 'BATTLEDRAWNIMALCLASS');
    }

    function byBattleByTrainer($battle, $trainerid) {
        return parent::_loadArray('battleid=' . $battle->Id() . ' AND uid=?', $trainerid, 'BATTLEDRAWNIMALCLASS');
    }

    function byBattleByTrainerByLeader($battle, $trainerid) {
        // @todo this isnt right
        return parent::_get('BATTLEDRAWNIMALCLASS',['battleid','uid'],[$battle->Id(),$trainerid])[0];
    }

    function Id() {
        return $this->_var('id');
    }

    function Owner() {
        return BATTLETRAINERCLASS::byBattleById($this->Battle(), $this->_var('uid'));
    }

    function Nickname($NewNickname = null) {
        return ucwords($this->_var('name', $NewNickname));
    }

    function Species($newSpecies = null) {
        return CREATEMONSTERCLASS::byId($this->_var('sid', $newSpecies));
    }

    function Paint($newPaint = null) {
        return $this->_var('paint', $newPaint);
    }

    function Move($moveIdentifier, $newMoveName = null) {
        // Search for a name rather then number
        if (!is_numeric($moveIdentifier)) {
            for ($i = 4; $i > -2; $i --) {
                if (strcasecmp($this->_var('move_' . $i), $moveIdentifier) === 0) {
                    break;
                }
            }
            $moveIdentifier = $i;
        }
        // Safeguard stuff
        $moveIdentifier = intval($moveIdentifier);
        if ($moveIdentifier < 0 || $moveIdentifier > 4) {
            return false;
        }
        $move = CREATEMOVECLASS::byName($this->_var('move_' . $moveIdentifier, $newMoveName));

        if ($newMoveName !== null) {
            $this->_var('move_pp_' . $moveIdentifier, $move->PPmax());
        }
        return $move;
    }

    function MovePP($moveIdentifier, $relativeChange) {
        /// SEARCH FOR MOVE
        if (!is_numeric($moveIdentifier)) {
            for ($i = 4; $i > -2; $i --) {
                if (strcasecmp($this->_var('move_' . $i), $moveIdentifier) == 0) {
                    break;
                }
            }
            $moveIdentifier = $i;
        }
        /// @todo Prevent going over max pp
        $this->_var('move_pp_' . $moveIdentifier, $relativeChange, true);
    }

    function Exp($amount = null) {
        return $this->_var('st_exp', $amount, true);
    }

    function ExpNext() {
        return pow($this->Level() + 1, 3);
    }

    function Level($newLevel = null) {
        if ($newLevel != null) {
            $this->_var('st_exp', pow(intval($newLevel), 3));
            $this->_var('st_level', intval($newLevel));

            $move = $this->Species()->LearnSet($newLevel);
            if ($move != null) {
                $this->Move(4, $move);
            }
        }
        return $this->_var('st_level');
    }

    function Hp($amount = null) {
        if ($amount !== null) {
            $amount += $this->Hp();
            $amount = max(min($amount, $this->Stat('HP')), 0);
        }
        return $this->_var('st_hp', $amount);
    }

    function HpPercent() {
        return $this->_var('st_hp') / $this->Stat('HP');
    }

    function Item($newItem = null) {
        if ($newItem !== null) {
            $item = $this->Item();
            $item->Owner($this->Owner()->Id());
            $item->_save();
            if (is_a($newItem, 'ITEMCLASS') && $newItem->Owner()->Id() === $this->Owner()->Id()) {
                $this->_var('st_helditem', $newItem->Id());
                $newItem->Owner(-1);
                $newItem->_save();
            } else {
                $this->_var('st_helditem', 0);
            }
        }
        $this->_save();
        return ITEMCLASS::byId($this->_var('st_helditem'));
    }

    function PartyPos($newPosition = null) {
        return $this->_var('st_partypos', $newPosition);
    }

    function BattleLeader($makeLead = null) {
        if ($makeLead !== null) {
            SQL()->select_db($this->databasePrefix . $this->database);
            SQL()->query('UPDATE  ' . $this->table . ' SET st_battlepos=0 WHERE uid="' . $this->Owner()->TrainerId() . '" AND battleid=' . $this->Battle()->Id()) or die(SQL()->error);
            $makeLead = 1;
            $this->_var('st_battlepos', $makeLead);
            $this->_save();
        }
        return $this->_var('st_battlepos', $makeLead);
    }

    function OriginalOwner() {
        return BATTLETRAINERCLASS::byBattleById($this->Battle(),$this->_var('uid'));
    }

    function OriginalLocation() {
        return CREATELOCATIONCLASS::byId(0);
    }

    function OriginalSpecies() {
        return $this->Species();
    }

    function Mama() {
        return DRAWNIMALCLASS::byId(0);
    }

    function Papa() {
        return DRAWNIMALCLASS::byId(0);
    }

    function Children() {
        return array();
    }

    function Mates() {
        return array();
    }

    function Siblings() {
        return array();
    }

    function Gender($newGender = null) {
        return $this->_var('info_gender', $newGender);
    }

    function Age() {
        return time();
    }

    function ObtainedDate($nothing = null) {
        return time();
    }

    function OriginalDate() {
        return time();
    }

    function ObtainedSpecies($newOwner = null) {
        return $this->Species();
    }

    function AboutMe($newAboutMe = null) {
        return '';
    }

    function Trading($requestText = null) {
        return '';
    }

    function Ev($type, $amount = null) {
        if ($amount === false) {
            return $this->_var('ev_' . $type, $amount);
        }
        return $this->_var('ev_' . $type, $amount, true);
    }

    function Iv($type) {
        return $this->_var('iv_' . $type);
    }

    function Md($type, $amount = null) {
        if ($amount != null) {
            if ($amount === false) {
                $amount = 0;
            } else {
                $amount += $this->Md($type);
                $amount = max(min($type, 7), -7);
            }
        }
        $md = $this->_var('md_' . $type, $amount);
        if ($md === false) {
            return 1;
        }
        return ($md <= 0 ? 2 / (abs($md) + 2) : (abs($md) + 2) / 2 );
    }

    function Stat($type, $amount = null) {
        $type = strtolower($type);

        // Accuracy
        if (strcmp($type, 'acc') === 0) {
            return $this->Md($type);
        }
        if (strcmp($type, 'evv') === 0) {
            return $this->Md($type);
        }
        // The Rest
        $iv = $this->Iv($type);
        $ev = $this->Ev($type);
        $md = $this->Md($type, $amount);
        $bs = $this->Species()->_var('bs_' . $type);
        $level = $this->level();
        if (strcmp($type, 'hp') === 0) {
            return ~~(($iv + 2 * $bs + ($ev / 4) ) * ($level / 100) ) + 10 + $level;
        }
        return ((($iv + 2 * $bs + ($ev / 4) ) * ($level / 100) ) + 5) * $md;
    }

    function Evolving($setEvolving = null, $itemused = null, $traded = null) {
        return '';
    }

    function Evolve() {
        return '';
    }

    function Egg() { return false; }
    // Classes
    function Ailments() {
        return new MONSTERCLASSABILITIES($this);
    }

    function Mood() {
        return new MONSTERCLASSMOOD($this);
    }

    function Render() {
        return new RENDERMONSTERCLASS($this);
    }

    ///SPECIAL FUNCTIONS
    function Battle() {
        return BATTLECLASS::byId($this->_var('battleid'));
    }

    function Trainer() {
        return BATTLETRAINERCLASS::byBattleById($this->Battle(), $this->_var('uid'));
    }

    function Wild() {
        return (strpos($this->_var('uid'), 'wild') !== -1);
    }

}

class BATTLEACTIONCLASS extends TABLETEMPLATE {

    public $damage = 0;
    public $missed = 0;
    public $effectivness = 1;
    public $critical = 1;
    public $criticalratio = 1;
    public $exprecievemod = 1;
    public $expawardmod = 1;
    public $damagemod = 1;
    
    function __construct() {
        parent::__construct('dr_battles', 'actions');
    }

    function byNew($battle, $playerid, $action, $args) {
        $instance = new self();
        $instance->connection->select_db($instance->databasePrefix . $instance->database);
        $STMT = $instance->connection->prepare('INSERT INTO ' . $instance->table . '(bid,uid,action,args) VALUES(?,?,?,?)') or die(SQL()->error);
        $STMT->bind_param('isis', $battle->Id(), $playerid, $action, $args) or die(SQL()->error);
        $STMT->execute();
        $STMT->close();

        $instance->searchKey = 'id';
        $instance->searchValue = $instance->connection->insert_id;
        return $instance;
    }

    function byFake($battleid, $trainer, $action, $args) {
        // Create a filler set values of this->data
        $instance = new self();
        $instance->data['id'] = -1;
        $instance->data['uid'] = $trainer;
        $instance->data['action'] = $action;
        $instance->data['args'] = $args;
        $instance->data['bid'] = $battleid;
        return $instance;
    }

    function byId($id) {
        return parent::_loadArray('id=?', intval($id), 'BATTLEACTIONCLASS')[0];
    }

    function byBattle($battle) {
        return parent::_loadArray('bid=?', intval($battle->Id()), 'BATTLEACTIONCLASS', '');
    }

    function byBattleByUserid($battle, $playerid) {
        $instance = new self();
        $instance->searchKey = 'uid=' . $playerid . ' AND bid';
        $instance->searchValue = $battle->Id();
        return $instance;
    }

    //Var
    function Id() {
        return $this->_var('id');
    }

    function Action() {
        return $this->_var('action');
    }

    function Battle() {
        return BATTLECLASS::byId($this->_var('bid'));
    }

    function Player() {
        return BATTLETRAINERCLASS::byBattleById($this->Battle(), $this->_var('uid'));
    }

    function TargetPlayer() {
        $argument = $this->Args();
        if (!isset($argument->target)) {
            foreach ($this->Battle()->AllPlayers() as $i) {
                if (strcmp($i->TrainerId(), $this->Player()->TrainerId()) !== 0) {
                    $argument->target = $i->TrainerId();
                    break;
                }
            }
            $this->_var('args', json_encode($argument));
        }
        return BATTLETRAINERCLASS::byBattleById($this->Battle(), $argument->target);
    }

    function Target() {
        if ($this->TargetPlayer() === false) return false;
        return $this->TargetPlayer()->ActiveMonster();
    }

    function Monster() {
        return $this->Player()->ActiveMonster();
    }

    function Attacker() {
        return $this->Player()->ActiveMonster();
    }

    function Defender() {
        if ($this->TargetPlayer() === false) return false;
        return $this->TargetPlayer()->ActiveMonster();
    }

    function MoveTally() {
        if (!isset($this->Args()->moveTally)) {
            return 1;
        }
        return intval($this->Args()->moveTally);
    }

    function SwitchMonster() {
        $team = $this->Player()->Monsters();
        $id = intval($this->Args()->id);
        foreach ($team as $i) {
            if ($i->Id() === $id) {
                return $i;
            }
        }
        return null;
    }

    function Args() {

        return json_decode($this->_var('args'));
    }

    function Item() {
        return ITEMCLASS::byId(intval($this->Args()->id));
    }

    function Move() {
        return $this->Monster()->Move(intval($this->Args()->id));
    }

    function Queue() {
        return $this->Battle()->Queue();
    }

    function Process() {
        $this->Player()->ActiveMonster()->Ailments()->Execute('BattleMoveBegin',$this);
        switch ($this->Action()) {
            case 1:
                $this->ProcessAttackDamageCalculate();
                $this->Attacker()->Ailments()->Execute('BTTLAttackBegin',$this);
                $this->Defender()->Ailments()->Execute('BTTLDefendBegin',$this);
                $this->ProcessAttack();
                $this->Attacker()->Ailments()->Execute('BTTLAttackEnd',$this);
                $this->Defender()->Ailments()->Execute('BTTLDefendEnd',$this);
                break;
            case 2:
                $this->Attacker()->Ailments()->Execute('BTTLSwitchBegin',$this);
                $this->ProcessSwitch();
                $this->Attacker()->Ailments()->Execute('BTTLSwitchEnd',$this);
                break;

            case 3:
                $this->Attacker()->Ailments()->Execute('BTTLItemBegin',$this);
                $this->ProcessItem();
                $this->Attacker()->Ailments()->Execute('BTTLItemEnd',$this);
                break;
            case 4:
//                $this->Player()->Monster()->byTeamByLeader()->Ailments()->Execute('BattleBeginRun');
//                $this->Target()->Monster()->byTeamByLeader()->Ailments()->Execute('BattleBeginOpponentRun');
                $this->ProcessRun();
//                $this->Player()->Monster()->byTeamByLeader()->Ailments()->Execute('BattleEndRun');
//                $this->Target()->Monster()->byTeamByLeader()->Ailments()->Execute('BattleEndOpponentRun');
                break;
            default:
                $this->ProcessNone();
                break;
        }
        $this->Battle()->Queue()->_save();
        $this->Player()->ActiveMonster()->Ailments()->Execute('BattleMoveEnd',$this);
    }

    function ProcessAttack() {
        if ($this->Target() === false || $this->Attacker()->Hp() === 0 || $this->Defender()->Hp() === 0) {
            return;
        }
        if ($this->missed) {
            $this->Battle()->Queue()->Dialog(ucwords($this->Attacker()->Nickname()) . "\\'s Attack Missed!");
            return;
        }
        $this->target = $this->TargetPlayer();
        if ($this->Player()->isPlayer()) {
            $playerexp = PLAYERCLASS::byId($this->Player()->Id())->Experience();
            $species = $this->Monster()->Species();
            $playerexp->Type($species->TypePrimary()->Id(),2);
            $playerexp->Type($species->TypeSecondary()->Id(),1);
        }
        
        $script = $this->Move()->Script();
        $arguments = array();
        $arguments['ATTACKER'] = ucwords($this->Attacker()->Nickname());
        $arguments['MOVENAME'] = ucwords($this->Move()->Name());
        $arguments['DEFENDER'] = ucwords($this->Defender()->Nickname());
        if (strcmp($script, '') === 0) {
            eval(TWIGSTRING()->render(CREATEMOVECLASS::byId(33)->Script(), $arguments));
        } else {
            eval(TWIGSTRING()->render($script, $arguments));
        }
        
        // Remove 1 pp
        //$this->Attacker()->MovePP($this->Move()->Movename(), -1);
        
        //Check for deaths this turn
        if ($this->Attacker()->Hp() <= 0) {
            $this->Battle()->Queue()->Dialog(ucwords($this->Attacker()->Nickname()) . " Fainted!");
            $this->Battle()->Queue()->PlaySound($this->Attacker()->Species()->Render()->soundUrl(),0.5);
            $this->Battle()->Queue()->FaintDrawnimal($this->Player()->DivLabel());
            if ($this->Defender()->Hp() > 0 && $this->TargetPlayer()->isPlayer()) {
                $exp = $this->ProcessAttackExperianceCalculate($this->TargetPlayer(), $this->Player());
                $this->Battle()->Queue()->Dialog(ucwords($this->Defender()->Nickname()) . " Got $exp Exp!");
                if ($this->Defender()->Exp($exp) === true) {
                    $this->Battle()->Queue()->Dialog(ucwords($this->Defender()->Nickname()) . " Leveled Up!");
                }
            }
            
            if (!$this->Player()->isDead()) {
                foreach($this->Battle()->AllPlayers() as $i) {
                    if ($i === false ) { continue; }
                    if (strcmp(''.$this->Player()->TrainerId(),''.$i->TrainerId()) !== 0) {
                        $this->Battle()->SubmitDummyAction($i);
                    }
                }
            }
        }
        if ($this->Defender()->Hp() <= 0) {
            $this->Battle()->Queue()->Dialog(ucwords($this->Defender()->Nickname()) . " Fainted!");
            $this->Battle()->Queue()->PlaySound($this->Defender()->Species()->Render()->soundUrl(),0.5);
            $this->Battle()->Queue()->FaintDrawnimal($this->TargetPlayer()->DivLabel());
            if ($this->Attacker()->Hp() > 0 && $this->Player()->isPlayer()) {
                // Give player experiance
                $playerexp = PLAYERCLASS::byId($this->Player()->Id())->Experience();
                $species = $this->Monster()->Species();
                $playerexp->Type($species->TypePrimary()->Id(),2);
                $playerexp->Type($species->TypeSecondary()->Id(),1);
                
                // Give Pokemon Experiance
                $exp = $this->ProcessAttackExperianceCalculate($this->Player(), $this->TargetPlayer());
                $this->Battle()->Queue()->Dialog(ucwords($this->Attacker()->Nickname()) . " Got $exp Exp!");
                if ($this->Attacker()->Exp($exp) === true) {
                    $this->Battle()->Queue()->Dialog(ucwords($this->Attacker()->Nickname()) . " Leveled up!");
                }
            }
            if (!$this->TargetPlayer()->isDead()) {
                foreach($this->Battle()->AllPlayers() as $i) {
                    if ($i === false ) { continue; }
                    if (strcmp(''.$this->TargetPlayer()->TrainerId(),''.$i->TrainerId()) !== 0) {
                        $this->Battle()->SubmitDummyAction($i);
                    }
                }
            }
        }
        if ($this->Attacker()->Hp() <= 0 || $this->Defender()->Hp() <= 0) {
            while (($action = array_pop($this->Battle()->actions)) !== null) {
                $action->_delete();
            }
        }
        $this->Attacker()->_save();
        $this->Defender()->_save();
        $this->ProcessBattleEnd();
        return;
    }
    
    function ProcessAttackDamageCalculate() {
        $this->missed = (($this->Move()->Acc() / 100) * $this->Defender()->Stat('ACC') * (1-$this->Defender()->Stat('EVV')) > rand(0,100));
        $this->effectivness = $this->Move()->Type()->Attacking($this->Defender()->Species()->TypePrimary());
        $this->effectivness *= $this->Move()->Type()->Attacking($this->Defender()->Species()->TypeSecondary());


        // Calculate Damage
        $atk = ($this->Move()->DamageType() == 0 ? $this->Attacker()->Stat('ATK') : $this->Attacker()->Stat('SPATK') );
        $def = ($this->Move()->DamageType() == 0 ? $this->Defender()->Stat('DEF') : $this->Defender()->Stat('SPDEF') );
        $this->damage = (2 * $this->Attacker()->Level() + 10);
        $this->damage /= 250;
        $this->damage *= ($atk / $def);
        $this->damage *= $this->Move()->Power();
        $this->damage += 2;
    }
    
    function ProcessAttackExperianceCalculate($atk,$def) {
        $atkd = $atk->ActiveMonster();
        $defd = $def->ActiveMonster();
        
        $exp = ($def->isWild() ? 1 : 1.5)*$defd->Species()->Experiance()*$defd->Level();
        $exp /= 5;
        
        $exp *= (pow($defd->Level()+$defd->Level()+10,2.5)/pow($defd->Level()+$atkd->Level()+10,2.5));
        $exp *= ($atkd->Owner()->Id() !== $atkd->OriginalOwner()->Id() ? 1.5 : 1 );
        if ($atk->Id() === $this->Player()->Id()) {
            $exp *= $this->exprecievemod;
        } else {
            $exp *= $this->expawardmod;
        }
        return floor($exp);
    }
    
    function ProcessAttackDamage($silent = false) {
        //Modifiers
        switch ($this->criticalratio) {
            case -1: $this->critical = 1;
                break;
            case 0: $this->critical = (rand(1, 16) === 1 ? 2 : 1);
                break;
            case 1: $this->critical = (rand(1, 8) === 1 ? 2 : 1);
                break;
            case 2: $this->critical = (rand(1, 4) === 1 ? 2 : 1);
                break;
            case 3: $this->critical = (rand(1, 3) === 1 ? 2 : 1);
                break;
            case 4: $this->critical = (rand(1, 2) === 1 ? 2 : 1);
                break;
        }
        $this->criticalratio = 999;
        $this->damage *= ($this->effectivness * $this->critical * (rand(85, 100) / 100) * $this->damagemod);
        $this->damage = floor($this->damage)+1;
        
        if ($this->critical === 2 && $silent== false && $this->damagemod > 0) {
            $this->Battle()->Queue()->Dialog('Critical Hit!');
        }
        
        $defender = $this->target->ActiveMonster();
        $defender->Hp(-$this->damage);
        $defender->_save();
        if ($silent== false) {
            if ($this->effectivness < 1) {
                $this->Battle()->Queue()->PlaySound('http://PokeWorlds.com/sfx/attackHitWeak.wav');
            } else {
                $this->Battle()->Queue()->PlaySound('http://PokeWorlds.com/sfx/attackHit.wav');
            }
        }
        $this->Battle()->Queue()->ChangeDrawnimalHp($this->target->DivLabel(), $defender->Hp(), $defender->Stat('hp'));
        if ($silent== false  && $this->damagemod > 0) {
            switch ($this->effectivness) {
                case 0: $this->Battle()->Queue()->Dialog('Not Effective!');
                    break;
                case 0.5: $this->Battle()->Queue()->Dialog('Not Very Effective!');
                    break;
                case 1.5: $this->Battle()->Queue()->Dialog('Very Effective!');
                    break;
                case 2: $this->Battle()->Queue()->Dialog('Super Effective!');
                    break;
            }
        }
    }

    function ProcessAttackAddTurn() {
        $arguments = $this->Args();
        $arguments->moveTally = $this->MoveTally() + 1;
        BATTLEACTIONCLASS::byNew($this->Battle(), $this->Player()->TrainerId(), $this->Action(), json_encode($arguments));
    }

    function ProcessSwitch() {
        $drawnimal = $this->SwitchMonster();
        if ($drawnimal === null) {
            $this->Battle()->Queue()->Dialog('Tried to switch to invalid Drawnimal!');
        } else {
            $divLabel = $this->Player()->DivLabel();
            $player = $this->Player();
            if ($this->Monster()->Hp() > 0) {
                $this->Battle()->Queue()->Dialog($player->Username() . ' is withdrawing ' . $player->ActiveMonster()->Nickname());
                $this->Battle()->Queue()->HideDrawnimal($divLabel);
            }
            $this->Battle()->Queue()->Dialog('Go ' . $drawnimal->Nickname() . '!');
            $this->Battle()->Queue()->ChangeDrawnimalImage($divLabel, $drawnimal->Render()->imageUrl());
            $this->Battle()->Queue()->ChangeDrawnimalName($divLabel, $drawnimal->Nickname());
            $this->Battle()->Queue()->ChangeDrawnimalHp($divLabel, $drawnimal->Hp(), $drawnimal->Stat('hp'));
            $this->Battle()->Queue()->ChangeDrawnimalLevel($divLabel, $drawnimal->Level());
            $this->Battle()->Queue()->ShowDrawnimal($divLabel);
            $this->Battle()->Queue()->PlaySound($drawnimal->Species()->Render()->soundUrl());
            $drawnimal->BattleLeader(true);
        }
    }

    function ProcessItem() {
        $this->Battle()->Queue()->Dialog('Used an item!');
        $this->ProcessCatch();
    }

    function ProcessCatch($ballbonus=1) {
        $catch = 3*$this->Target()->Stat('hp');
        $catch -= 2*$this->Target()->Hp();
        $catch *= $this->Target()->Species()->CatchRate()*$ballbonus;
        $catch /= (3*$this->Target()->Stat('hp'));
        
        for($i=0;$i<3;$i++) {
            $this->Queue()->Dialog('::Wiggle Wiggle::');
            if (mt_rand(1,255) < $catch) {
                //Shinyness animation and sfx
                $this->Queue()->StopSoundBgm();
                $this->Queue()->PlaySound('http://PokeWorlds.com/sfx/caughtDrawnimal.mp3');
                $this->Queue()->Dialog($this->Target()->Nickname().' was Caught!');
                $this->Queue()->Wait(2000);
                $this->Queue()->PlaySoundBgm('http://PokeWorlds.com/sfx/m/victoryBattle.mp3');
                $this->Queue()->Dialog('Do you want to give a nickname to '.$this->Target()->Nickname().'?');
                $this->Queue()->Dialog($this->Target()->Nickname().'\\\'s Information is being added to your Sketchbook!');
                
                for($i=0;$i<4;$i++) {
                    $player = $this->Battle()->Player($i);
                    if ($player === false) { continue; }
                    if ($player->isPlayer()) {
                        $player = PLAYERCLASS::byId($player->Id());
                        $player->Battle(-1);
                        $player->_save();
                    } else {
                        foreach($player->Monsters() as $d) {
                            $d->_delete();
                        }
                    }
                }
                $this->Queue()->GotoGame();
                $this->Queue()->_save();
                $this->Battle()->actions = array();
                $this->Battle()->deleteMe = true;
                
                return;
            }
        }
        $this->Queue()->Dialog('Broke Free!');
        return;
    }
    
    function ProcessRun() {
        $this->Battle()->Queue()->Dialog('Ran!');
        $this->Battle()->Queue()->GotoGame();
        for($i=0;$i<4;$i++) {
            $player = $this->Battle()->Player($i);
            if ($player === false) continue;
            if ($player->Id() === $this->Player()->Id()) {
                $this->Battle()->Player($i,'');
                
            }
        }
        $player = PLAYERCLASS::byId($this->Player()->Id());
        $player->Battle(-1);
        $player->_save();
        $this->ProcessBattleEnd();
        
    }

    function ProcessNone() {
        //$this->Battle()->Queue()->Dialog('Something Went Wrong...');
    }

    function ProcessBattleEnd() {
        //Check if any players need to be ejected
        
        $total = 0;
        for($i=0;$i<4;$i++) {
            $player = $this->Battle()->Player($i);
            if ($player !== false && !$player->isDead()) {
                $total+=1;
            }
        }
        if ($total === 1) {
            $this->Queue()->PlaySoundBgm('http://PokeWorlds.com/sfx/m/victoryBattle.mp3');
        }
        
        $total = 0;
        for($i=0;$i<4;$i++) {
            $player = $this->Battle()->Player($i);
            if ($player === false) { continue; }
            if ($player->isDead()) {
                if ($player->isWild()) {
                    $this->Queue()->Dialog('Wild '.ucwords($player->ActiveMonster()->Nickname()).' has been defeated!');
                    $this->Queue()->Wait(500);
                    foreach($player->Monsters() as $d) {
                        $d->_delete();
                    }
                } elseif (!$player->isPlayer()) {
                    $this->Queue()->Dialog($player->Username().' has been defeated!');
                    $stat = $this->Player()->Stat();
                    $stat->Coins(1);
                    $stat->_save();
                    $this->Queue()->Dialog($this->Player()->Username().' Got 1$!');
                    $this->Queue()->Wait(500);
                    foreach($player->Monsters() as $d) {
                        $d->_delete();
                    }
                } else {
                    //Eject out of battle, Also add to loses
                    $playerclass = PLAYERCLASS::byId($player->Id());
                    $playerclass->Battle(-1);
                    $playerclass->_save();
                    foreach($player->Monsters() as $d) {
                        $d->ResetStats();
                        $d->_save();
                    }
                    //$this->Queue()->Dialog($player->Username().' is out of usable Drawnimals!',$player->DivLabel());
                    //$this->Queue()->Dialog($player->Username().' whited out!',$player->DivLabel());
                    //$this->Queue()->EndBattle()
                }
                $this->Battle()->Player($i,'');
            } else {
                $total +=1;
            }
        }
        
        if ($total <=1) {
            for($i=0;$i<4;$i++) {
                $player = $this->Battle()->Player($i);
                if ($player === false) { continue; }
                if ($player->isPlayer()) {
                    $player = PLAYERCLASS::byId($player->Id());
                    $player->Battle(-1);
                    $player->_save();
                } else {
                    foreach($player->Monsters() as $d) {
                        $d->_delete();
                    }
                }
            }
            $this->Queue()->GotoGame();
            $this->Queue()->_save();
            $this->Battle()->actions = array();
            $this->Battle()->deleteMe = true;
        }
    }
}

class BATTLECLASS extends TABLETEMPLATE {
    public $deleteMe = false;
    public $totalTurns = 0;
    function __construct() {
        parent::__construct('dr_battles', 'info');
    }

    //PULL
    function byNew() { return parent::_new('BATTLECLASS',['time_start'],[time()]);}
    function byId($id) {
        return parent::_get('BATTLECLASS',['id'], [intval($id)])[0];
    }

    //VARS
    function Id() { return $this->_var('id'); }
    function Type($value = null) { return $this->_var('type', $value); }
    function Environment($value = null) { return $this->_var('environment', $value); }
    function ActiveRound($newround = null) {
        if ($newround !== null) {
            $newround = $this->ActiveRound() + 1;
        }
        return $this->_var('active_round', $newround);
    }
    
    function TotalParticipents() {
        for ($i = 0; $i < 4; $i++) {
            if (strcmp($this->_var('player' . $i), '') === 0) {
                return $i;
            }
        }
        return 4;
    }
    function TotalPlayers($add = null) {
        if ($add !== null) {
            $add = $this->TotalPlayers() + 1;
        }
        return $this->_var('player_total', $add);
    }
    function TotalAiPlayers($add = null) {
        if ($add !== null) {
            $add = $this->TotalAiPlayers() + 1;
        }
        return $this->_var('player_ai_total', $add);
    }
    
    function AllPlayers() {
        $list = array();
        $total = $this->TotalParticipents();
        for ($i = 0; $i < $total; $i++) {
            $list[] = $this->Player($i);
        }
        return $list;
    }
    function AllAiPlayers() {
        $list = array();
        $total = $this->TotalParticipents();
        for ($i = 0; $i < $total; $i++) {
            $player = $this->Player($i);
            if (!$player->isPlayer()) {
                $list[] = $player;
            }
        }
        return $list;
    }
    function Player($slot, $setId = null) {
        return BATTLETRAINERCLASS::byBattleById($this, $this->_var('player' . $slot, $setId));
    }
    
    function AllActions() {
        return BATTLEACTIONCLASS::byBattle($this);
    }
    function CountPlayerActions() {
        $count = 0;
        foreach (BATTLEACTIONCLASS::byBattle($this) as $i) {
            if (is_numeric($i->_var('uid'))) {
                $count += 1;
            }
        }
        return $count;
    }
    
    function ActionByPlayer($playerId) {
        return BATTLEACTIONCLASS::byBattleByUserid($this, $playerId);
    }

    //Setup
    function SetupSetEnvironment($setEnvironment) {
        $this->Environment($setEnvironment);
        return $this;
    }
    function SetupAddPlayer(PLAYERCLASS $player) {
        $this->Player($this->TotalParticipents(), $player->Id());
        $player->BattleId($this->Id());
        $player->_save();
        $this->TotalPlayers(1);
        return $this;
    }
    function SetupAddTrainer(CREATETRAINERCLASS $trainer) {
        $leader = false;
        $this->Player($this->TotalParticipents(), 'trainer|' . $trainer->Id());
        foreach ($trainer->Team() as $i) {
            $d = BATTLEDRAWNIMALCLASS::byNew($this, 'trainer|' . $trainer->Id(), $i->Species(), $i->Level(), $i->Difficulty());
            if ($leader === false) {
                $d->BattleLeader(true);
                $leader = true;
            }
            $d->Hp(99999);
            $d->_save();
        }
        $this->TotalAiPlayers(1);
        return $this;
    }
    function SetupAddWild(CREATEMONSTERCLASS $species, $level = 5) {
        if (empty($species->Id())) die('Unknown Monster Id');
        $trainerId = 'wild|' . uniqid();
        $this->Player($this->TotalParticipents(), $trainerId);
        $wild = BATTLEDRAWNIMALCLASS::byNew($this, $trainerId, $species, $level, 0);
        $wild->BattleLeader(true);
        $wild->Hp(99999);
        $wild->_save();
        $this->TotalAiPlayers(1);
        return $this;
    }
    function SetupFinalize() {
        $this->_save();
        $this->_queue = BATTLEQUEUECLASS::byNew($this, 1);
        $this->_queue->StartBattle($this);
        $this->ActiveRound(true);
        $this->_save();
    }

    //Process
    function SubmitDummyAction($player) {
        BATTLEACTIONCLASS::byNew($this, $player->TrainerId(), 5, '{id:0}');
    }
    function SubmitAction($action, $args) {
        $player = PLAYERCLASS::byMe();
        $battleAction = $this->ActionByPlayer($player->Id());
        if ($battleAction->Id() === null) {
            if ($action == 1) {
                if ($player->Monster()->byTeamByLeader()->Hp() === 0) {
                    die("<script>BTTL.enQueue('eventDialog',['You cannot attack, Your drawnimal is Fainted!','']);</script>");
                }
                $argument = json_decode($args);
                if ($argument->id < 0 || $argument->id > 3) {
                    die("<script>BTTL.enQueue('eventDialog',['Invalid Move Selection!','']);</script>");
                }
            }
            if ($action == 2) {
                $argument = json_decode($args);
                $drawnimal = MONSTERCLASS::byId($argument->id);
                if ($drawnimal->Hp() === 0) {
                    die("<script>BTTL.enQueue('eventDialog',['You cannot Switch to this Drawnimal, It is Fainted!','']);</script>");
                }
                if ($drawnimal->Egg() === true) {
                    die("<script>BTTL.enQueue('eventDialog',['Eggs Cannot Battle!','']);</script>");
                }
                if ($drawnimal->Owner()->Id() !== $player->Id()) {
                    die("<script>BTTL.enQueue('eventDialog',['You cannot Switch to this Drawnimal, You are not its Trainer!','']);</script>");
                }
                if ($drawnimal->PartyPos() <= 0) {
                    die("<script>BTTL.enQueue('eventDialog',['You cannot Switch to this Drawnimal, It is in the Daycare!','']);</script>");
                }
                if ($drawnimal->BattleLeader() === 1) {
                    die("<script>BTTL.enQueue('eventDialog',['You cannot Switch to this Drawnimal, It is already in battle!','']);</script>");
                }
            }
            $battleAction = BATTLEACTIONCLASS::byNew($this, $player->Id(), $action, $args);
        }
        if ($this->CountPlayerActions() >= $this->TotalPlayers()) {
            $this->ProcessActions();
            return true;
        }
        return false;
    }

    function GenerateActions() {
        $AIPlayers = $this->AllAiPlayers();
        while (($player = array_pop($AIPlayers)) !== null) {
            /// See if Ai move has been processed already?
            $skipit = false;
            foreach ($this->actions as $i) {
                if (strcmp($i->Player()->TrainerId(), $player->TrainerId()) === 0) {
                    $skipit = true;
                }
            }
            if ($skipit) continue;
            /// Generate a move for the AI
            $drawnimal = $player->ActiveMonster();
            while ($movenumber = rand(0, 3)) {
                $move = $drawnimal->Move($movenumber);
                if (!empty($move->Name())) {
                    break;
                }
            }
            $this->actions[] = BATTLEACTIONCLASS::byFake($this->Id(), $player->TrainerId(), 1, '' . '{ "id":' . $movenumber . '}');
        }
    }

    function SortActions() {
        usort($this->actions, function($a,$b) {
            if($a->Action() == $b->Action()){ return 0 ; }
            return ($a->Action() < $b->Action()) ? -1 : 1;
        });
        usort($this->actions, function($a,$b) {
            if($a->Action() !== 1){ return 0 ; }
            if($b->Action() !== 1){ return 0 ; }
            $aspeed = $a->Attacker()->Stat('speed');
            $bspeed = $b->Attacker()->Stat('speed');
            if($aspeed == $bspeed){ return 0 ; }
            return ($aspeed > $bspeed) ? -1 : 1;
        });
        usort($this->actions, function($a,$b) {
            if($a->Action() !== 1){ return 0 ; }
            if($b->Action() !== 1){ return 0 ; }
            $aspeed = $a->Move()->Speed();
            $bspeed = $b->Move()->Speed();
            if($aspeed == $bspeed){ return 0 ; }
            return ($aspeed < $bspeed) ? -1 : 1;
        });
        
    }
    function ProcessActions() {
        $this->_queue = BATTLEQUEUECLASS::byNew($this, $this->ActiveRound());
        $this->actions = $this->AllActions();
        $this->GenerateActions();
        $this->SortActions();
        while (($action = array_pop($this->actions)) !== null) {
            $action->Process();
            $action->_delete();
            if ($this->deleteMe) {
                $this->_delete();
                return true;
            }
        }
        foreach($this->AllPlayers() as $i) {
            $i->ActiveMonster()->Ailments()->Execute('BTTLRoundEnd',$this);
        }
        $this->Queue()->_save();
        $this->ActiveRound(true);
        $this->_save();
        // Check just in case 1 player and move is a double turn
        if ($this->CountPlayerActions() >= $this->TotalPlayers() && $this->totalTurns < 10) {
            $this->totalTurns+=1;
            unset($this->_queue);
            $this->ProcessActions();
            return true;
        }
    }

    function Queue() {
        if (isset($this->_queue)) {
            return $this->_queue;
        }
        $this->_queue = BATTLEQUEUECLASS::byBattleByRound($this->Id(), $this->ActiveRound());
        return $this->_queue;
    }

}
