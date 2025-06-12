<?php

class BTTLQCLASS {

    /** @todo Save to battle file */
    var $file;

    function __construct() {
        $file = fopen(".battles/" . BTTL()->$id . "/");
    }

    function PleaseWait() {
        
    }

    function ErrorSubmittingAction($details) {
        die('//dialog error');
    }

    function Dialog($text) {
        
    }

}

class BTTLROUNDACTIONCLASS {

    private $user;
    private $userid;
    private $action;
    private $arguments;
    public $critical;
    public $effective;
    public $hit;

    /** BTTLROUNDACTIONCLASS() - Construct a Action to be used in the round of battle.
     * @param int $userid User id that submitted the action
     * @param string $action Action Keyword "Attack,Item,Switch,Run,Dialog,Nothing"
     * @param string $arguments Action Arguments seperated by '|'
     * @return boolean False if arguments are invalid.
     */
    function __construct($userid, $action, $arguments) {
        $this->user = PLYROBJ($userid);
        $this->userid = $userid;
        $this->action = $action;
        $this->arguments = $arguments;
        $argument = explode('|', $arguments);
        switch ($action) {
            case 'Switch':
                $this->switchpkmn = BTTL()->Player($userid)->Party()->Contains($argument[0]);
                if ($this->switchpkmn === false) {
                    BTTL()->Queue()->ErrorSubmittingAction('Invalid switching drawnimal');
                }
                $this->speed = 9;
                break;
            case 'Item':
                $this->item = $this->User()->Inventory()->GetBag(intval($argument[0]));
                if (count($this->item) === 0) {
                    BTTL()->Queue()->ErrorSubmittingAction('You do not have a ' . $argument[0]);
                }
                /// THIS DEPENDS ON THE ITEM, Should probably be $this->target = PKMNOBJ($argument[1]);
                $this->target = $this->User()->Party()->Contains($argument[1]);
                if ($this->target === false) {
                    BTTL()->Queue()->ErrorSubmittingAction('Invalid target drawnimal');
                }
                $this->speed = 8;
                break;
            case 'Attack':
                $this->move = $this->User()->Party()->BattleActive()->Move($argument[0]);
                if ($this->move === false) {
                    BTTL()->Queue()->ErrorSubmittingAction('Invalid move selection');
                }
                $this->attacker = BTTL()->Player()->Find($userid);
                if ($this->attacker === false) {
                    BTTL()->Queue()->ErrorSubmittingAction('Invalid attacker');
                }
                $this->defender = BTTL()->Player()->Find($argument[1]);
                if ($this->defender === false) {
                    BTTL()->Queue()->ErrorSubmittingAction('Invalid defender');
                }
                $this->speed = $this->Move()->Speed();
                $this->atkspeed = $this->Attacker()->Stat('SPEED');
                break;
            case 'Run';
                $this->speed = 10;
                break;
            case 'Dialog':
                $this->dialog = $argument[0];
                $this->speed = -8;
                break;
            case 'Nothing':
                $this->speed = -9;
                break;
        }
    }

    /** Action() - Gets the type of this action
     * @return string Either "Attack,Item,Switch,Run,Dialog,Nothing".
     */
    function Action() {
        return $this->action;
    }

    /** User() - Gets the player assotiated with this action
     * @return PLYRCLASS
     */
    function User() {
        return $this->user;
    }

    /** Attacker() - Gets the Attacker in a "Attack" type action.
     * @return PKMNCLASS
     */
    function Attacker() {
        return $this->attacker->Party()->BattleActive();
    }

    /** Defender() - Gets the Defender in a "Attack" type action
     * @return PKMNCLASS
     */
    function Defender() {
        return $this->defender->Party()->BattleActive();
    }

    /** Move() - Gets the move being used by the Attacker() in a "Attack" type action
     * @return MOVECLASS
     */
    function Move() {
        return $this->move;
    }

    /** Item() - Gets the item being used in a "Item" type action
     * @return ITEMCLASS
     */
    function Item() {
        return $this->item;
    }

    /** SwitchPKMN() - Gets the drawnimal that is being brought out in a "Switch" type action
     * @return PKMNCLASS
     */
    function SwitchPKMN() {
        return $this->switchpkmn;
    }

    /** Target() - Gets the drawnimal that is the target of a item in a "Item" type action
     * @return PKMNCLASS
     */
    function Target() {
        return $this->target;
    }

    /** Dialog() - Gets the dialog to be displayed in a "Dialog" type action
     * @return string
     */
    function Dialog() {
        return $this->dialog;
    }

    /** Speed() - Gets the speed of this action
     * @return int
     */
    function Speed() {
        return ($this->speed * 1000) + (isset($this->atkspeed) ? $this->atkspeed : 0);
    }

    /** Action Sequences - These functions make up the basic sequence of this action. */
    function __execute() {
        $this->MoveBegin();
        switch ($this->Action()) {
            case 'Switch':
                $this->ExecuteSwitch();
                break;
            case 'Item':
                if ($this->User()->Party()->BattleActive() == false) {
                    break;
                }
                $this->ExecuteItem();
                break;
            case 'Attack':
                if ($this->Attacker()->Party()->BattleActive() == false) {
                    break;
                }
                if ($this->Defender()->Party()->BattleActive() == false) {
                    break;
                }
                $this->ExecuteAttack();
                break;
            case 'Run':
                if ($this->User()->Party()->BattleActive() == false) {
                    break;
                }
                $this->ExecuteRun();
                break;
            case 'Dialog':
                BTTL()->Queue()->Dialog($this->dialog);
                break;
            case 'Nothing':
                break;
        }
        $this->MoveEnd();
    }

    private function MoveBegin() {
        if ($this->User()->Party()->BattleActive() != false) {
            $this->User()->Party()->BattleActive()->Ailments()->Execute('BTTLMoveBegin');
        }
    }

    private function ExecuteAttack() {
        // Exit if any pokemon are fainted
        if ($this->Attacker()->Hp() === 0 || $this->Defender()->Hp() === 0) {
            return;
        }
        // Precalculate defaults for "critical,hit,effective"
        $this->critical = $this->Move()->Critical();
        $this->hit = $this->Move()->Acc() * ($this->Attacker()->Stat('ACC') / $this->Defender()->Stat('EVV'));
        $this->effective = GLSS()->TypeEffective($this->Move()->Type(), $this->Defender()->Base()->TypePrimary());
        $this->effective *= GLSS()->TypeEffective($this->Move()->Type(), $this->Defender()->Base()->TypeSecondary());

        // Calculate Damage
        $atk = ($this->Move()->DmgType() == 0 ? $this->Attacker()->Stat('ATK') : $this->Attacker()->Stat('SPATK') );
        $def = ($this->Move()->DmgType() == 0 ? $this->Attacker()->Stat('DEF') : $this->Attacker()->Stat('SPDEF') );
        $this->dmg = floor(floor(floor(2 * $this->Attacker()->Level() / 5 + 2) * $atk * $this->Move()->Power() / $def) / 50) + 2;

        // Remove 1 PP from move 
        $this->Attacker()->MovePP($this->Move()->Movename(), -1);

        // Trigger abilities (BTTLAttackBegin,BTTLDefendBegin)
        $this->Attacker()->Ailments()->Execute('BTTLAttackBegin');
        $this->Defender()->Ailments()->Execute('BTTLDefendBegin');
        /** @Hint These values can be changed by these Abilities and By the Move Script
         *  @Hint Variables that can be modified 
         *        BTTL()->Round()->Action()->dmg;
         *        $this->dmg; $this->critical; $this->hit; 
         *        $this->stop; $this->effective;
         *  @Hint Also feel free to modify these (they will not be saved)
         *        $this->Attacker/Defender()->data; 
         *        $this->Move()->data;
         *        $this->Attacker/Defender()->Base()->data;
         *        @Warning unset(variable) to reset for the rest of the round.
         */
        // Record What Happened
        BTTL()->Queue()->Dialog($this->Attacker()->Nickname() . ' used ' . $this->Move()->Movename());
        if ($this->hit == false) {
            BTTL()->Queue()->Dialog($this->Attacker()->Nickname() . ' Missed!');
            $this->Attacker()->Ailments()->Execute('BTTLAttackMissed');
            $this->Defender()->Ailments()->Execute('BTTLDefendMissed');
            return;
        }
        BTTL()->Queue()->Animation($this->Move()->Movename());

        // Calculate critical
        if ($this->Move()->DmgType() != 2) {
            switch ($this->critical) {
                case 0: $this->critical = (mt_rand(1, 16) = 1 ? true : false);
                    break;
                case 1: $this->critical = (mt_rand(1, 8) = 1 ? true : false);
                    break;
                case 2: $this->critical = (mt_rand(1, 4) = 1 ? true : false);
                    break;
                case 3: $this->critical = (mt_rand(1, 3) = 1 ? true : false);
                    break;
                case 4: $this->critical = (mt_rand(1, 2) = 1 ? true : false);
                    break;
                default:
                    if ($this->critical > 4) {
                        $this->critical = (mt_rand(1, 2) = 1 ? true : false);
                    } else {
                        $this->critical = false;
                    }
                    break;
            }
        }
        /** @Hint Criticals Can be announced in move execution (for high critical chance moves)
         *        Be sure to check if ($this->critical === false) so it doesnt announce twice.
         *  @Hint Abilities and Items Held can set $this->critical to -1 - 4 for modification.
         */
        // More Damage Calc
        if ($this->critical) {
            $this->dmg *= 2; //CRITICAL
        }
        $this->dmg = ($this->dmg * (100 - mt_rand(0, 15))) / 100; //RAND FACTOR
        $this->dmg *= ($this->Attacker()->Base()->TypePrimary() == $this->Move()->Type() ||
                $this->Attacker()->Base()->TypeSecondary() == $this->Move()->Type() ? 1.5 : 1 ); //STAB
        $this->dmg *= $this->effective; //Effectivness
        $this->dmg = ($this->dmg < 1 ? 1 : $this->dmg); //At Least 1
        
        // Execute Move Code
        if (!empty($this->Move()->ScriptExecute())) {
            eval($this->Move()->ScriptExecute());
        } else {
            BTTL()->Queue()->Dialog('Move ' . $this->Move()->Movename() . ' Not Implemented');
            $this->stop = true;
        }
        /** 
         * @Hint Set $this->stop = true; to disable Effectiveness announcement and also stop damage from being applied
         * @Hint Apply $this->dmg in move execution or else it will not happen if $this->stop is set. 
         * @Hint To do Hi-Critical Moves change $this->critical = true, Be sure to apply damage and dont set $this->stop.
         */
        if (!isset($this->stop)) {
            // Announce Effectivness
            if ($this->Move()->DmgType() != 2) {
                if ($this->effective > 1.9) {
                    BTTL()->Queue()->Dialog('It was super effective!');
                } elseif ($this->effective > 1) {
                    BTTL()->Queue()->Dialog('It was very effective!');
                } elseif ($this->effective < 0.1) {
                    BTTL()->Queue()->Dialog('It was not effective!');
                } elseif ($this->effective < 1) {
                    BTTL()->Queue()->Dialog('It was not very effective!');
                }
            }
            // Apply Damage
            $this->Defender()->Hp(-$this->dmg);
            
            // Announce Critical
            if ($this->critical) { BTTL()->Queue()->Dialog('Critical Hit!'); }
        }

        // Trigger abilities (BTTLAttackEnd,BTTLDefendEnd)
        $this->Attacker()->Ailments()->Execute('BTTLAttackEnd');
        $this->Defender()->Ailments()->Execute('BTTLDefendEnd');

        // Exit if any pokemon are fainted
        if ($this->Attacker()->Hp() === 0) {
            BTTL()->Queue()->Dialog($this->Attacker()->Nickname() . ' Fainted!');
        }
        if ($this->Defender()->Hp() === 0) {
            BTTL()->Queue()->Dialog($this->Defender()->Nickname() . ' Fainted!');
        }
        /** @todo Make Queue()->dialog() personalized to each user. */
    }

    private function ExecuteSwitch() {
        if ($this->User()->Party()->BattleActive() != false) {
            BTTL()->Queue()->Dialog($this->User()->Party()->BattleActive()->Nickname() . ' Return!');
            $this->User()->Party()->BattleActive()->Ailments()->Execute('BTTLSwitchBegin');
            $this->User()->Party()->BattleActive()->BattlePos(-1);
        }
        $this->SwitchPKMN()->BattlePos(1);
        BTTL()->Queue()->Dialog('Go ' . $this->User()->Party()->BattleActive()->Nickname() . '!');
        $this->User()->Party()->BattleActive()->Ailments()->Execute('BTTLSwitchEnd');
    }

    private function ExecuteItem() {
        $this->User()->Party()->BattleActive()->Ailments()->Execute('BTTLItemBegin');
        ///Item Script here
        if (!empty($this->Item()->ScriptBattleExecute())) {
            eval($this->Item()->ScriptBattleExecute());
            $this->Target->Ailments()->Execute('BTTLItemUsed');
        } else
            BTTL()->Queue()->Dialog('Item ' . $this->Item()->Name() . ' Not Implemented');
        $this->User()->Party()->BattleActive()->Ailments()->Execute('BTTLItemEnd');
    }

    private function ExecuteRun() {
        $this->User()->Party()->BattleActive()->Ailments()->Execute('BTTLRunBegin');
        //Run code
        $this->User()->Party()->BattleActive()->Ailments()->Execute('BTTLRunEnd');
    }

    private function MoveEnd() {
        if ($this->User()->Party()->BattleActive() != false)
            $this->User()->Party()->BattleActive()->Ailments()->Execute('BTTLMoveEnd');
    }

}

class BTTLROUNDCLASS {

    var $actiondata;

    function __construct() {
        $this->actiondata = array();
    }

    function __destruct() {
        /** Save ONLY PLYR()'s action to the database, so you dont overwrite the others */
        if (count($this->actiondata) == 0) {
            return;
        }
        if ($this->actiondata[0]->user !== PLYR()) {
            return;
        }
        $actionevent = $this->actiondata[0];
        $split = $actionevent->userid . '~' . $actionevent->action . '~' . $actionevent->arguments;
        $number = BTTL()->Player()->Find(PLYR()->id);
        if ($number === false) {
            PLYR()->Battle('');
            return;
        }

        /** Save my action into the database :D */
        $STMT = SQL()->prepare('UPDATE battle_info 
                                SET (player' . $number . 'action)
                                VALUES (?)
                                WHERE id = ?');
        $STMT->bind_param('ss', $split, BTTL()->id);
        $STMT->execute();
        $STMT->close();
    }

    function __loadActions() {
        // Create Actions from the database 
        for ($i = 0; isset(BTTL()->data['player' . $i]); $i++) {
            if (empty(BTTL()->data['player' . $i . 'action'])) {
                continue;
            }
            $split = explode('~', BTTL()->data['player' . $i . 'action']);
            $userid = $split[0];
            $action = $split[1];
            $arguments = $split[2];
            $actionobject = $this->actiondata[] = new BTTLROUNDACTIONCLASS($userid, $action, $arguments);
            $actionobject->battleindex = $i;
        }
    }

    /** Action() - Only works when all actions have been submitted
     * @return BTTLROUNDACTIONCLASS Current Action being executed.
     */
    function Action() {
        return $this->currentaction;
    }

    /** ActionSubmitted() - Check this player has alread submitted
     * @return boolean Whether user has submitted an action.
     */
    function ActionSubmitted() {
        $number = BTTL()->Player()->Find(PLYR()->id);
        if ($number === false) {
            PLYR()->Battle('');
            return true;
        }
        return (BTTL()->data['player_action_' . $number] != '');
    }

    /** ActionSubmit() - Submits this players action/arguments.
     * @param string $action The action type 'Attack', 'Item', 'Switch', 'Run', 'Dialog', 'Nothing';
     * @param string $arguments For Action seperated by '|'. Example: 'Tackle|30';
     */
    function ActionSubmit($action, $arguments) {
        // Checks if PLYR() has submitted, if they have Queue will die(JS) to try again later.
        if ($this->ActionSubmitted() === false) {
            $this->actiondata[] = new BTTLROUNDACTIONCLASS(PLYR()->id, $action, $arguments);
        } else {
            BTTL()->Queue()->PleaseWait();
        }

        // PLYR() submited/saved an action, If all users are done begin round.
        $this->__loadActions();
        if (count($this->actiondata) === BTTL()->Player()->PlayerTotal()) {
            $this->BeginRound();
        } else {
            BTTL()->Queue()->PleaseWait();
        }
    }

    private function BeginRound() {
        // We are done with the database, Reset the Actions. */
        SQL()->query('UPDATE battle_info 
                        SET (movecurrent,player0action,player1action,player2action,player3action)
                        VALUES (movecurrent+1,"","","","")');
        /** @todo Create database template, align-values */
        // Creates Actions in $this->actiondata[] that corrispond to the AI players */
        $aiarray = BTTL()->Player()->AllAI();
        while ($ai = array_pop($aiarray)) {
            $this->actiondata[] = new BTTLROUNDACTIONCLASS($ai->id, 'Attack', '1|99');
        }
        /** @todo Make Smarter, formulate method, choose opponent from BTTL()->Player() array. */
        // Activates the "BTTLBeginRound" abilities of the BattleActive Pets
        foreach (BTTL()->Player()->All() as $player) {
            if ($player->Party()->BattleActive() !== false) {
                $player->Party()->BattleActive()->Ailments()->Execute('BTTLRoundBegin');
            }
        }

        // Sorts $this->actiondata[] by $action->Speed()
        usort($this->actiondata, function ($a, $b) {
            if ($a->Speed() == $b->Speed()) {
                return 0;
            }
            return ($a->Speed() < $b->Speed()) ? -1 : 1;
        });

        // Executes the stack
        foreach ($this->actiondata as $value) {
            $this->currentaction = $value;
            $value->__exectute();
        }

        // Activates the "BTTLEndRound" abilities of all the BattleActive pets
        foreach (BTTL()->Player()->All() as $player) {
            if ($player->Party()->BattleActive() !== false) {
                $player->Party()->BattleActive()->Ailments()->Execute('BTTLRoundEnd');
            }
        }
    }

}

class BTTLPLYRCLASS {

    /** AllPlayer() - Gets an array of all the players in battle */
    function All() {
        $all = array();
        for ($i = 0; $i < 5; $i++) {
            if (strcmp(BTTL()->data['player_' . $i], '') !== 0) {
                if (is_numeric(BTTL()->data['player_' . $i])) {
                    $all[] = new PLYRCLASS(BTTL()->data['player_' . $i]);
                } else {
                    $all[] = new PLYRAICLASS(BTTL()->data['player_' . $i]);
                }
            }
        }
        return $all;
    }

    /** AllPlayer() - Gets an array of all the ai players in battle */
    function AllAI() {
        $all = array();
        for ($i = 0; $i < 5; $i++) {
            if (strcmp(BTTL()->data['player_' . $i], '') !== 0 && !is_numeric(BTTL()->data['player_' . $i])) {
                $all[] = new PLYRAICLASS(BTTL()->data['player_' . $i]);
            }
        }
        return $all;
    }

    /** AllPlayer() - Gets an array of all the non-ai players in battle */
    function AllPlayer() {
        $all = array();
        for ($i = 0; $i < 5; $i++) {
            if (strcmp(BTTL()->data['player_' . $i], '') !== 0 && is_numeric(BTTL()->data['player_' . $i])) {
                $all[] = new PLYRCLASS(BTTL()->data['player_' . $i]);
            }
        }
        return $all;
    }

    /** Find() - Gets the index of this player in BTTL().
     * @param type $uid Id of a player
     * @return PLYRCLASS/PLYRAICLASS/false
     */
    function Find($uid) {
        for ($i = 0; $i < 5; $i++) {
            if (strcmp(BTTL()->data['player_' . $i], $uid) === 0) {
                return $i;
            }
        }
        return false;
    }

    /** Total() - Get total players in battle */
    function Total() {
        $count = 0;
        for ($i = 0; $i < 5; $i++) {
            if (strcmp(BTTL()->data['player_' . $i], '') !== 0) {
                $count++;
            }
        }
        return $count;
    }

    /** AITotal() - Get total ai players in battle */
    function AITotal() {
        $count = 0;
        for ($i = 0; $i < 5; $i++) {
            if (strcmp(BTTL()->data['player_' . $i], '') !== 0 && !is_numeric(BTTL()->data['player_' . $i])) {
                $count++;
            }
        }
        return $count;
    }

    /** PlayerTotal() - Get Total non-ai players in battle */
    function PlayerTotal() {
        $count = 0;
        for ($i = 0; $i < 5; $i++) {
            if (strcmp(BTTL()->data['player_' . $i], '') !== 0 && is_numeric(BTTL()->data['player_' . $i])) {
                $count++;
            }
        }
        return $count;
    }

}

class BTTLCLASS {

    var $id;
    var $data;
    var $rounddata;
    var $playerdata;

    /** BTTLCLASS() - Returns Battle class or false if user is not in a battle. */
    function __construct() {
        $this->id = PLYR()->Battle();
        $this->queuedata = new BTTLQCLASS();
        $this->rounddata = new BTTLROUNDCLASS();

        $STMT = SQL()->prepare('SELECT * FROM temp_battle WHERE id = ?');
        $STMT->bind_param('s', $this->id);
        $STMT->execute();
        if (($result = $STMT->get_result())===false) {
            $STMT->close();
            return;
        }
        $this->data = $result->fetch_assoc();
        $STMT->close();

        $this->playerdata = new BTTLPLYRCLASS();
    }

    /** Round() - Returns the current battle round
     * @return type
     */
    function Round() {
        return $this->rounddata;
    }

    /** Queue() - Gets this Battles Queue.
     * @return type
     */
    function Queue() {
        return $this->queuedata;
    }

    /** Player() - Get information about players in battle */
    function Player() {
        return $this->playerdata;
    }

}
class BTTLTRAINERCLASS {
    function __construct($trainername) {
        $trainerinfo = explode('|',$trainername);
        $this->battleid = $trainerinfo[0];
        $this->player_index = $trainerinfo[1];
        $this->catalog_index = $trainerinfo[2];
        //PULL catalog maybe
    }
}
class BTTLPKMNCLASS {
    function __construct($id,$trainername=null,$species=null,$level=null) {
        if ($id === -1) {
            //INSERT INTO table
            $STMT = SQL()->prepare('INSERT INTO drawnimals(trainername, species, st_exp, st_level) VALUES(?,?,?,?)');
            $STMT->bind_param('ssii',$trainername,$species,$level,$level);
            $STMT->execute();
        }
        //PULL drawnimals WHERE id=$id
    }
    
    function Species() {}
    function Form() {}
    
    // Shared Classes With PKMNCLASS()
    function Base() {
        return PKMNBASEOBJ($this->Species(), $this->Form());
    }
    function Ailments() {
        return PKMNAILMENTOBJ($this);
    }

    
}
class BTTLCREATORCLASS {
    var $player_index=0;
    var $battleid;
    function Create() {
        SQL()->query('INSERT INTO info() VALUES()');
    }
    function AddPlayer($userid) {
        if ($this->player_index > 3) {
            return;
        }
        $STMT = SQL()->prepare('UPDATE info SET player'.$this->player_index.'=?');
        $STMT->bind_param('i',$userid);
        $STMT->execute();
        $this->player_index += 1;
    }
    function AddAI($trainerid, $drawnimal=null) {
        if ($this->player_index > 3) {
            return;
        }
        $trainername = $this->battleid.'|'.$this->player_index.'|'.$trainerid;
        SQL()->query('UPDATE info SET player'.$this->player_index.'="'.$trainername.'" WHERE id='.$this->battleid);
        $this->player_index += 1;
        
        if ($trainerid!==-1) {
            //READ FROM TRAINER_CATALOG AND INSERT INTO DRAWNIMALS WITH UID=$TRAINERNAME
            return;
        }
        //INSERT INTO DRAWNIMALS WITH UID=$TRAINERNAME AND SPECIES=$drawnimal
        
    }
}
if (PLYR()->Battle() != '') {
    $GLOBALS['BTTL'] = new BTTLCLASS(PLYR()->Battle());
} else {
    $GLOBALS['BTTL'] = new BTTLCREATORCLASS();
}

function BTTL() {
    return $GLOBALS['BTTL'];
}
