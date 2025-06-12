<?php

    include_once 'html/_php/class-type.php';
    include_once 'html/_php/class-render.php';
    include_once 'html/_php/class-monsters-learnset.php';
    class CREATEMONSTERCLASS extends TABLETEMPLATE {
        function __construct () { parent::__construct('create', 'monsters'); }
        
        // Constructors
        function byNew ( $uid,$name ) { return parent::_new('CREATEMONSTERCLASS',['species','uid'],[$name,$uid]); }
        
        function byAll ($addon='')  { return parent::_all('CREATEMONSTERCLASS',$addon);}
        function bySpecies ( $name ) { return parent::_get('CREATEMONSTERCLASS',['species'], [$name]);}
        function byId ($id) { return parent::_get('CREATEMONSTERCLASS',['id'], [$id],' ')[0];}
        function byUserId ($id) { return parent::_get('CREATEMONSTERCLASS',['uid'], [$id]);}
        function byRegion ($id,$addon="") { return parent::_get('CREATEMONSTERCLASS',['appearance_region'], [$id],$addon);}
        function byRegionByStarter ($region) { return parent::_get('CREATEMONSTERCLASS',['appearance_region','appearance_starter'], [$region,1]);}
        function byTypePrimary ( $type ) {return parent::_get('CREATEMONSTERCLASS',['type_0'], $type);}
        function byTypeSecondary ( $type ) {return parent::_get('CREATEMONSTERCLASS',['type_1'], $type);}
        
        function countAll ()  {  return parent::_count('CREATEMONSTERCLASS',['id'],[0],'',['!=']); }
        function countUserId($value) { return parent::_count('CREATEMONSTERCLASS',['uid'],[$value]); }
        function countRegion($value) { return parent::_count('CREATEMONSTERCLASS',['appearance_region'],[$value]); }
        
        // Class Functions
        function Id () {return $this->_var('id');}
        function Index ( $value = null ) { return $this->_var('number', $value);}
        function Form ( $value = null ) { return $this->_var('form', $value);}
        function UserId ( $value = null ) {return $this->_var('uid', $value);}
        function Name ( $value = null ) { return $this->_var('species', $value); }
        function Description ( $value = null ) {return $this->_var('description', $value);}
        function GenusFamily ( $value = null ) {return $this->_var('genus_family', $value);}
        function GenusClass ( $value = null ) {return $this->_var('genus_class', $value);}
        function GenusOrder ( $value = null ) {return $this->_var('genus_order', $value);}
        function TypePrimary ( $value = null ) {return TYPECLASS::byId($this->_var('type_0', $value));}
        function TypeSecondary ( $value = null ) {return TYPECLASS::byId($this->_var('type_1', $value));}
        function Ev ( $type, $value = null ) {return $this->_var('ev_' . $type, $value);}
        function Hp ( $value = null ) {return $this->_var('bs_hp', $value);}
        function Atk ( $value = null ) {return $this->_var('bs_atk', $value);}
        function Def ( $value = null ) {return $this->_var('bs_def', $value);}
        function SpAtk ( $value = null ) {return $this->_var('bs_spatk', $value);}
        function SpDef ( $value = null ) {return $this->_var('bs_spdef', $value);}
        function Speed ( $value = null ) {return $this->_var('bs_speed', $value);}
        function Experiance ( $value = null ) {return $this->_var('bs_exp', $value);}
        function Hunger ( $value = null ) {return $this->_var('bs_hunger', $value);}
        function Energy ( $value = null ) {return $this->_var('bs_energy', $value);}
        function Friendship ( $value = null ) {return $this->_var('bs_friendship', $value);}
        function CatchRate ( $value = null ) {return $this->_var('rate_catch', $value);}
        function GenderRate ( $value = null ) {return $this->_var('rate_gender', $value);}
        function HatchRate ( $value = null ) {return $this->_var('rate_hatch', $value);}
        function EvolveScript ( $value = null ) {return $this->_var('script_evolve', $value);}
        function EvolveScriptRaw ( $value = null ) {return $this->_var('script_evolve_raw', $value);}
        function AppearanceScript ( $value = null ) {return $this->_var('appearance_script', $value);}
        function AppearanceScriptRaw ( $value = null ) {return $this->_var('appearance_script_raw', $value);}
        function AppearanceRegion ( $value = null ) {return $this->_var('appearance_region', $value);}
        function AppearanceStarter ( $value = null ) {return $this->_var('appearance_starter', $value)==1;}
        function AppearanceEnvironment ( $value = null ) {return $this->_var('appearance_environment', $value);}
        
        function UnLike() { 
            if (empty(PLAYERCLASS::byMe()->Id())) return false;
            return LIKECLASS::byRemove(PLAYERCLASS::byMe()->Id(),'CREATEMONSTERCLASS',$this->Id());
            
        }
        function Like() { 
            if (empty(PLAYERCLASS::byMe()->Id())) return false;
            return LIKECLASS::byNew(PLAYERCLASS::byMe()->Id(),'CREATEMONSTERCLASS',$this->Id());
            
        }
        function Liked() { 
            if (empty(PLAYERCLASS::byMe()->Id())) return false;
            return LIKECLASS::byUserByCatagoryByItem(PLAYERCLASS::byMe()->Id(),'CREATEMONSTERCLASS',$this->Id()) === 1;
        }
        function Likes() { return LIKECLASS::byCatagoryByItem('CREATEMONSTERCLASS',$this->Id()); }
        
        function Abilities ( $value = null ) {
            $list = array();
            $ailments = explode('|', $this->_var('abilities', $value));
            $i=0;
            while (count($ailments) > 1) {
                $list[] = CREATEABILITYCLASS::byName($ailments[$i], $ailments[$i+1]);
                $i++;$i++;
                if ($i >= count($ailments)-1) {
                    break;
                }
            }

            return $list;
        }
        function Items ( $value = null ) {
            $list = array();
            $ailments = explode('|', $this->_var('items', $value));
            $i=0;
            while (count($ailments) > 1) {
                $item = CREATEITEMCLASS::byName($ailments[$i]);
                $item->percentage = $ailments[$i+1];
                $list[] = $item;
                
                $i++;$i++;
                if ($i >= count($ailments)-1) {
                    break;
                }
            }

            return $list;
        }
        function Render() { return new RENDERCREATEMONSTERCLASS($this);}
    }
    class MONSTERCLASS extends TABLETEMPLATE {
        function __construct () {
            parent::__construct('nicapa', 'monsters');
        }
        
// Constructors
        function byNew ( PLAYERCLASS $owner, CREATEMONSTERCLASS $species, $level ) {
            $experiance = pow($level, 3);
            $atkiv = mt_rand(0, 31);
            $defiv = mt_rand(0, 31);
            $spatkiv = mt_rand(0, 31);
            $spdefiv = mt_rand(0, 31);
            $speediv = mt_rand(0, 31);
            $hpiv = mt_rand(0, 31);

            $abilities_default = '';
            $abilities = $species->Abilities();
            foreach ( $abilities as $ability ) {
                if ( mt_rand(0, 200) < $ability->Percentage() ) {
                    $abilities_default .= $ability->Name() . '?0|';
                }
            }

            $item_held = 0;
            $items = $species->Items();
            foreach ( $items as $item ) {
                if ( mt_rand(0, 200) < $item[1] ) {
                    $item_held = createItem($item[0])->Id();
                    break;
                }
            }
            
            $moves = MONSTERLEARNSETCLASS::byMonsterByBelowLevel($species->Id(),$level);
            $movename = array();
            $movepp = array();
            for($i=0;$i<4;$i++) {
                if (isset($moves[$i])) {
                    $movename[] = CREATEMOVECLASS::byId($moves[$i]->Move())->Id();
                    $movepp[] = CREATEMOVECLASS::byId($moves[$i]->Move())->PP();
                } else {
                    $movepp[] = 0;
                    $movename[] = '';
                }
            }
            $newDrawnimal = MONSTERCLASS::_new('MONSTERCLASS',
                    ['uid','sid', 'origin_species', 'ailments_default', 'ailments',
                    'st_level', 'st_exp', 'iv_hp', 'iv_atk', 'iv_def', 'iv_spatk', 'iv_spdef', 'iv_speed',
                    'move_0','move_pp_0','move_1','move_pp_1','move_2','move_pp_2','move_3','move_pp_3',
                    'info_datetime_created', 'st_mood_datetime', 'origin_owner'],
                    [$owner->Id(), $species->Id(), $species->Name(), $abilities_default, $abilities_default, 
                    $level, $experiance, $hpiv, $atkiv, $defiv, $spatkiv, $spdefiv, $speediv, 
                    $movename[0],$movepp[0],$movename[1],$movepp[1],$movename[2],$movepp[2],$movename[3],$movepp[3],
                    time(), time(), $owner->Id()]);
            if ($level !== 1) {
                $owner->Caught($species->Id(),1);
            }

            $newDrawnimal->_var('st_friendship', ($species->Friendship() / 255) * 64000);
            $newDrawnimal->ObtainedDate(true);
            $newDrawnimal->ObtainedSpecies(true);
            $newDrawnimal->Nickname(ucwords($species->Name()));
            $newDrawnimal->Hp(100000);
            $partypos = count($owner->Monster()->byTeam());
            if ($partypos > 5) {$partypos = 0;}
            else {$partypos+=1;}
            $newDrawnimal->PartyPos($partypos);
            if ( $species->GenderRate() === 255 ) {
                $newDrawnimal->Gender(2);
            } else {
                $newDrawnimal->Gender((rand(0, 255) < $species->GenderRate() ? 1 : 0));
            }
            if ( mt_rand(1, 8192) === 1 ) {
                $newDrawnimal->Paint('shiny');
            }
            $newDrawnimal->_save();
            return $newDrawnimal;
        }
        function byNewEgg ( MONSTERCLASS $mama, MONSTERCLASS $papa ) {
            $species = $mama->Species();
            $owner = $mama->Owner();

            $experiance = 0;
            $level = 0;

            $hpiv = floor(($mama->Iv('HP', 'IV') + mt_rand(0, 31)) / 2);
            $atkiv = floor(($papa->Iv('ATK', 'IV') + mt_rand(0, 31)) / 2);
            $spatkiv = floor(($papa->Iv('SP_ATK', 'IV') + mt_rand(0, 31)) / 2);
            $defiv = floor(($mama->Iv('DEF', 'IV') + mt_rand(0, 31)) / 2);
            $spdefiv = floor(($mama->Iv('SP_DEF', 'IV') + mt_rand(0, 31)) / 2);
            $speediv = floor(($papa->Iv('SPEED', 'IV') + $mama->Iv('SPEED', 'IV') + mt_rand(0, 31)) / 3);

            $origin_mama = $mama->Id();
            $origin_papa = $papa->Id();

            // @todo Inherit ailments
            $abilities_default = '';
            $abilities = $species->Abilities();
            foreach ( $abilities as $ability ) {
                if ( mt_rand(0, 200) < $ability->Percentage() ) {
                    $abilities_default .= $ability->Name() . '?0|';
                }
            }

            $item_held = 0;
            $items = $species->Items();
            foreach ( $items as $item ) {
                if ( mt_rand(0, 200) < $item[1] ) {
                    $item_held = createItem($item[0])->Id();
                    break;
                }
            }
            $info = new self();
            SQL()->select_db($info->databasePrefix . $info->database);
            $STMT = SQL()->prepare('INSERT INTO ' . $info->table . '(uid, species, origin_species, form, ailments_default, ailments, '
                    . 'st_level, st_exp, iv_hp, iv_atk, iv_def, iv_spatk, iv_spdef, iv_speed, '
                    . 'info_datetime_created, st_mood_datetime, origin_mama, origin_papa, origin_owner,origin_location) '
                    . 'VALUES(?,?,?,?,?,?,'
                    . '?,?,?,?,?,?,?,?,'
                    . '?,?,?,?,?,1)');
            
            $STMT->bind_param('ississiiiiiiiiiiiii', $owner->Id(), $species->Name(), $species->Name(), $species->Form(), $abilities_default, $abilities_default, 
                    $level, $experiance, $hpiv, $atkiv, $defiv, $spatkiv, $spdefiv, $speediv, 
                    time(), time(), $origin_mama->Id(), $origin_papa->Id(), $owner->Id());
            $STMT->execute();
            $STMT->close();

            $newDrawnimal = MONSTERCLASS::byId(SQL()->insert_id);
            $newDrawnimal->_var('st_friendship', ($species->Friendship() / 255) * 64000);
            
            $teamsize = count(MONSTERCLASS::byOwnerByTeam($owner));
            if ($teamsize < 6) {
                $newDrawnimal->PartyPos($teamsize+1);
            }
            $newDrawnimal->ObtainedDate(true);
            $newDrawnimal->ObtainedSpecies(true);
            $newDrawnimal->Nickname(ucwords($species->Name()));
            if ( $species->GenderRate() === 255 ) {
                $newDrawnimal->Gender(3);
            } else {
                $newDrawnimal->Gender((rand(0, 255) < $species->GenderRate() ? 2 : 1));
            }
            if ( mt_rand(1, 8192) === 1 ) {
                $newDrawnimal->Paint('shiny');
            }
            $newDrawnimal->_save();
            return $newDrawnimal;
        }
        function byId ( $id ) {
            $instance = new self();
            $instance->searchKey = 'id';
            $instance->searchValue = $id;
            return $instance;
        }
        function byOwner ( PLAYERCLASS $player ) {
            return parent::_loadArray('uid=?', $player->Id(), 'MONSTERCLASS');
        }
        function byOwnerById ( PLAYERCLASS $player, $id ) {
            $instance = new self();
            $instance->searchKey = 'uid='.$player->Id().' AND id';
            $instance->searchValue = $id;
            return $instance;
        }
        function byOwnerByTeam ( PLAYERCLASS $player ) {
            return parent::_loadArray('uid=? AND st_partypos > 0', $player->Id(), 'MONSTERCLASS',' ORDER BY st_partypos ASC ');
        }
        function byOwnerByTeamByAlive ( PLAYERCLASS $player ) {
            $team = [];
            foreach(parent::_get('MONSTERCLASS',['uid','st_hp'],[$player->Id(),0],'ORDER BY st_partypos ASC',['=','>']) as $i) {
                if ($i->Egg()) continue;
                $team[] = $i;
            }
            return $team;
        }
        function byOwnerByTeamByLeader ( PLAYERCLASS $player ) {
            $team = parent::_get('MONSTERCLASS', ['uid','st_partypos'], [$player->Id(),0], 'ORDER BY st_battlepos DESC, st_partypos ASC LIMIT 6', ['=','>']);
            foreach($team as $i) {
                if ($i->BattleLeader() != 0) {
                    return $i;
                }
                if (!$i->Egg() && $i->Hp() > 0) {
                    $i->BattleLeader(true);
                    return $i;
                    
                }
            }
            return MONSTERCLASS::byId(-1);
        }
        function byOwnerByTeamById ( PLAYERCLASS $player, $id ) {
            return parent::_loadArray('uid=? AND st_partypos > 0 AND id=' . $id, $player->Id(), 'MONSTERCLASS');
        }
        function byOwnerByTeamBySpecies ( PLAYERCLASS $player, CREATEMONSTERCLASS $species ) {
            return parent::_loadArray('uid=? AND st_partypos > 0 AND species="' . $species->Name() . '"', $player->Id(), 'MONSTERCLASS');
        }
        function byOwnerByBox ( PLAYERCLASS $player ) {
            return parent::_loadArray('uid=? AND st_partypos=0', $player->Id(), 'MONSTERCLASS');
        }
        function byOwnerByBoxBySpecies ( PLAYERCLASS $player, CREATEMONSTERCLASS $species ) {
            return parent::_loadArray('uid=? AND st_partypos = 0 AND species="' . $species->Name() . '"', $player->Id(), 'MONSTERCLASS');
        }
        function byOwnerBySpecies ( PLAYERCLASS $player, CREATEMONSTERCLASS $species ) {
            return parent::_loadArray('uid=? AND species="' . $species->Name() . '"', $player->Id(), 'MONSTERCLASS');
        }
        function byOwnerByTrading () {
            return parent::_loadArray('uid=? AND info_trading!=""', $player->Id(), 'MONSTERCLASS');
        }
        function bySpecies ( CREATEMONSTERCLASS $species ) {
            return parent::_loadArray('species', $species->Name(), 'MONSTERCLASS');
        }
        function byMama ( MONSTERCLASS $mama ) {
            return parent::_loadArray('origin_mama', $mama->Id(), 'MONSTERCLASS');
        }
        function byPapa ( MONSTERCLASS $papa ) {
            return parent::_loadArray('origin_papa', $papa->Id(), 'MONSTERCLASS');
        }
        function bySearchTrading ( $searchKey ) {
            return parent::_loadArray('info_trading LIKE "%' . $searchKey . '%" AND 1', '1', 'MONSTERCLASS');
        }
        function bySearchTradingBySpecies ( CREATEMONSTERCLASS $species ) {
            return parent::_loadArray('info_trading != "" AND species', $species->Name(), 'MONSTERCLASS');
        }
        // Class Functions
        function Id () {
            return $this->_var('id');
        }
        /**
         * @param int $value New uid
         * @return playerclass
         */
        function Owner ( PLAYERCLASS $value = null ) {
            if ( $value !== null ) {
                $this->ObtainedSpecies(true);
                $this->ObtainedDate(true);
                $this->_var('st_friendship', ($this->Species()->Friendship() / 255) * 64000);
                $value = $value->Id();
            }
            return PLAYERCLASS::byId($this->_var('uid', $value));
        }
        /**
         * Returns or Sets the Nickname of this Drawnimal
         * @param string $NewNickname
         * @return string
         */
        function Nickname ( $NewNickname = null ) {
            if ($this->Egg()) return 'Egg';
            return $this->_var('name', $NewNickname);
        }
        /**
         * Returns or Sets the species of this Drawnimal
         * @param string $newSpecies
         * @return catalogdrawnimal
         */
        function Species ( $newSpecies = null ) {
            return CREATEMONSTERCLASS::byId($this->_var('sid', $newSpecies));
        }
        /**
         * Returns or Sets the Paint Color of this Drawnimal
         * @param type $newPaint
         * @return type
         */
        function Paint ( $newPaint = null ) {
            return $this->_var('paint', $newPaint);
        }
        /**
         * Returns CATALOGMOVECLASS that has been located in this Drawnimal by searching for $moveIdentifier (name or slot position)
         * @param string $moveIdentifier
         * @param string $newMoveId
         * @return boolean
         */
        function Move ( $moveIdentifier, $newMoveId = null ) {
            // Search for a name rather then number
            if ( ! is_numeric($moveIdentifier) ) {
                for ( $i = 4; $i > -2; $i --  ) {
                    if ( strcasecmp($this->_var('move_' . $i), $moveIdentifier) === 0 ) {
                        break;
                    }
                }
                $moveIdentifier = $i;
            }
            // Safeguard stuff
            $moveIdentifier = intval($moveIdentifier);
            if ( $moveIdentifier < 0 || $moveIdentifier > 4 ) {
                return false;
            }
            $move = CREATEMOVECLASS::byId($this->_var('move_' . $moveIdentifier, $newMoveId));

            if ( $newMoveId !== null && $moveIdentifier !== 4 ) {
                $this->_var('move_pp_' . $moveIdentifier, $move->PP());
            }
            return $move;
        }
        /**
         * Returns PP of move and can also +/- Move PP
         * @param int $moveIdentifier
         * @param int $relativeChange
         */
        function MovePP ( $moveIdentifier, $relativeChange ) {
            /// SEARCH FOR MOVE
            if ( ! is_numeric($moveIdentifier) ) {
                for ( $i = 4; $i > -2; $i --  ) {
                    if ( strcasecmp($this->_var('move_' . $i), $moveIdentifier) == 0 ) {
                        break;
                    }
                }
                $moveIdentifier = $i;
            }
            /// @todo Prevent going over max pp
            $this->_var('move_pp_' . $moveIdentifier, $relativeChange, true);
        }
        /**
         * Returns or Changes Experiance Points, $amount is relative change
         * @param int $amount
         * @return int
         */
        function Exp ( $amount = null ) {
            if ($amount !== null && pow(intval($this->Level()+1), 3) < $this->_var('st_exp')+$amount) {
                $this->Level($this->Level()+1);
                
                $move = MONSTERLEARNSETCLASS::byMonsterByLevel($this->Species()->Id(),$this->Level());
                if ( !empty($move->Id()) ) {
                    $this->Move(4, CREATEMOVECLASS::byId($move->Move())->Id());
                }
                $this->Evolving(true);
                $this->_var('st_exp', $amount, true);
                $this->_save();
                return true;
            }
            
            return $this->_var('st_exp', $amount, true);
        }
        /**
         * Returns the amount of EXP needed until next level is achieved
         * @return int
         */
        function ExpNext () {
            return pow($this->Level() + 1, 3);
        }
        /**
         * Returns the Level of the Drawnimal, Level can be set as well but it is not recommended as of yet.
         * @param type $newLevel
         * @return type
         */
        function Level ( $newLevel = null ) {
            return $this->_var('st_level',$newLevel);
        }
        /**
         * Returns or Sets the HP for this Drawnimal, $amount is relative change.
         * @param type $amount
         * @return type
         */
        function Hp ( $amount = null ) {
            if ( $amount !== null ) {
                $amount += $this->Hp();
                $amount = max(min($amount, $this->Stat('HP')), 0);
            }
            return $this->_var('st_hp', $amount);
        }
        function HpPercent ( ) {
            return ($this->_var('st_hp')/$this->Stat('HP'))*100;
        }
        /**
         * Returns the ITEMCLASS object, $item must be a ITEMCLASS object.
         * NOTE: When a new item is assigned the old item is returned to the current owners invnetory
         * @param ITEMCLASS $newItem
         * @return ITEMCLASS
         */
        function Item ( $newItem = null ) {
            if ( $newItem !== null ) {
                $item = $this->Item();
                $item->Owner($this->Owner()->Id());
                $item->_save();
                if ( is_a($newItem, 'ITEMCLASS') && $newItem->Owner()->Id() === $this->Owner()->Id() ) {
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
        /**
         * Returns or Sets the position in the party that this Drawnimal occupies.
         * If this value is 0 then the Drawnimal is not in the Players party.
         * @param int $newPosition
         * @return int
         */
        function PartyPos ( $newPosition = null ) {
            return $this->_var('st_partypos', $newPosition);
        }
        function BattleLeader ( $makeLead = null ) {
            if ($makeLead!==null) {
                SQL()->select_db($this->databasePrefix . $this->database);
                SQL()->query('UPDATE  ' . $this->table . ' SET st_battlepos=0 WHERE uid='.$this->Owner()->Id().' AND st_partypos > 0 ') or die(SQL()->error);
                if ($makeLead) SQL()->query('UPDATE  ' . $this->table . ' SET st_battlepos=1 WHERE id='.$this->Id()) or die(SQL()->error);
            }
            return intval($this->_var('st_battlepos'));
        }
        /**
         * Returns the Original Owner of this Drawnimal, This Value cannot be changed.
         * @return PLAYERCLASS
         */
        function OriginalOwner () {
            return PLAYERCLASS::byId($this->_var('origin_owner'));
        }
        /**
         * Returns the CATALOGLOCATIONCLASS object of the area where this Drawnimal was caught.
         * @return CATALOGLOCATIONCLASS
         */
        function OriginalLocation () {
            return $this->_var('origin_location');
        }
        /**
         * Returns the CREATEMONSTERCLASS object of the original species that this Drawnimal was when it was first caught or hatched
         * THIS VALUE CANNOT BE CHANGED
         * @return CREATEMONSTERCLASS
         */
        function OriginalSpecies () {
            return CREATEMONSTERCLASS::bySpeciesByForm($this->_var('origin_species'));
        }
        /**
         * Returns the MONSTERCLASS of the Mother of this Drawnimal, This Value Cannot Be Changed
         * @return MONSTERCLASS
         */
        function Mama () {
            return MONSTERCLASS::byId($this->_var('origin_mama'));
        }
        /**
         * Returns the MONSTERCLASS of the Father of this Drawnimal, This Value Cannot Be Changed
         * @return MONSTERCLASS
         */
        function Papa () {
            return MONSTERCLASS::byId($this->_var('origin_papa'));
        }
        /**
         * Returns an Array of MONSTERCLASS objects that are Offspring of this Drawnimal, This Value Cannot Be Changed
         * @return MONSTERCLASS
         */
        function Children () {
            $result = SQL()->query("SELECT id FROM user_drawnimals WHERE origin_mama=$this->id OR origin_papa=$this->id");
            if ( $result === false || $result->num_rows === 0 ) {
                return false;
            }
            $list = array();
            while ( $row = $result->fetch_row() ) {
                $list[] = MONSTERCLASS::byId($row[0]);
            }
            return $list;
        }
        /**
         * Returns an Array of MONSTERCLASS objects that have produced children with this Drawnimal, This Value Cannot Be Changed
         * @return MONSTERCLASS
         */
        function Mates () {
            // TODO: update this
            if ( $this->Gender() === 1 ) {
                $result = SQL()->query("SELECT origin_mama FROM user_drawnimals WHERE origin_papa=$this->id");
            } elseif ( $this->Gender() === 2 ) {
                $result = SQL()->query("SELECT origin_papa FROM user_drawnimals WHERE origin_mama=$this->id");
            } else {
                return false;
            }

            if ( $result === false || $result->num_rows === 0 ) {
                return false;
            }
            $list = array();
            while ( $row = $result->fetch_row() ) {
                $list[] = MONSTERCLASS::byId($row[0]);
            }
            return $list;
        }
        /**
         * Returns an Array of MONSTERCLASS objects that are Siblings of this Drawnimal, This Value Cannot Be Changed
         * @return MONSTERCLASS
         */
        function Siblings () {
            //TODO: UPDATE THIS
            $papa = $this->Papa();
            $mama = $this->Mama();
            $result = SQL()->query("SELECT id FROM user_drawnimals WHERE origin_papa=$papa->id OR origin_mama=$mama->id");

            if ( $result === false || $result->num_rows === 0 ) {
                return false;
            }
            $list = array();
            while ( $row = $result->fetch_row() ) {
                $list[] = MONSTERCLASS::byId($row[0]);
            }
            return $list;
        }
        /**
         * Returns the Gender of this Drawnimal
         * - 1 = Male
         * - 2 = Female
         * - 3 = unknown
         * @param int $newGender
         * @return int
         */
        function Gender ( $newGender = null ) {
            return $this->_var('info_gender', $newGender);
        }
        /**
         * Returns the age of the Drawnimal in Seconds
         * @return int
         */
        function Age () {
            return $this->_var('info_datetime_created');
        }
        /**
         * Returns the original timestamp from when the Drawnimal was obtained by its current owner
         * @param boolean $newOwner
         * @return int
         */
        function ObtainedDate ( $newOwner = null ) {
            if ( $newOwner !== null ) {
                $newOwner = time();
            }
            return $this->_var('info_obtained_datetime', $newOwner);
        }
        function OriginalDate ( ) {
            return $this->_var('info_datetime_created');
        }
        /**
         * Returns the CREATEMONSTERCLASS object of the original species that this Drawnimal was when it was first obtained by the current owner
         * Set $newOwner to true when the Drawnimal changes owners.
         * @param boolean $newOwner
         * @return CREATEMONSTERCLASS
         */
        function ObtainedSpecies ( $newOwner = null ) {
            if ( $newOwner !== null ) {
                $newOwner = $this->Species()->Name();
            }
            return CREATEMONSTERCLASS::bySpecies($this->_var('info_obtained_species', $newOwner));
        }
        /**
         * Returns or Sets the aboutMe section of the profile page.
         * @param string $newAboutMe
         * @return string
         */
        function AboutMe ( $newAboutMe = null ) {
            return $this->_var('info_aboutme', $newAboutMe);
        }
        /**
         * Returns or Sets the Trading Request Dialog, If this is set to '' the Drawnimal will be allowed in the users party.
         * @param string $requestText
         * @return string
         */
        function Trading ( $requestText = null ) {
            return $this->_var('info_trading', $requestText);
        }
        /**
         * Returns or Sets Relative the ev points assotiated with type (HP,ATK,DEF,SPATK,SPDEF,SPEED)
         * Setting the value to false will reset the EV points to 0
         * @param string $type
         * @param int $amount
         * @return int
         */
        function Ev ( $type, $amount = null ) {
            if ( $amount === false ) {
                return $this->_var('ev_' . $type, $amount);
            }
            return $this->_var('ev_' . $type, $amount, true);
        }
        /**
         * Returns the IV points assotiated with type (HP,ATK,DEF,SPATK,SPDEF,SPEED)
         * These values cannot be changed
         * @param string $type
         * @return int
         */
        function Iv ( $type ) {
            return $this->_var('iv_' . $type);
        }
        /**
         * Returns or Sets Relative the Modifier value of type (HP,ATK,DEF,SPATK,SPDEF,SPEED,EVV,ACC)
         * Setting the value to false will reset the modifier to 0
         * @param string $type
         * @param type $amount
         * @return int|boolean (Either value or If value is maxed out)
         */
        function Md ( $type, $amount = null ) {
            if ( $amount != null ) {
                if ( $amount === false ) {
                    $amount = 0;
                } else {
                    $amount += $this->Md($type);
                    $amount = max(min($type, 7), -7);
                }
            }
            $md = $this->_var('md_' . $type, $amount);
            if ( $md === false ) {
                return 1;
            }
            return ($md <= 0 ? 2 / (abs($md) + 2) : (abs($md) + 2) / 2 );
        }
        /**
         * Returns the stat associated with the type (HP,ATK,DEF,SPATK,SPDEF,SPEED,ACC,EVV)
         * Can Set Relative the Modifier value of the type, Setting the value to false will reset the modifier to 0
         * @param string $type Get current stat value for HP,ATK,DEF,SPATK,SPDEF,SPEED,ACC,EVV.
         * @param integer $type (if integer) Set mod value +/- for HP,ATK,DEF,SPATK,SPDEF,SPEED,ACC,EVV.
         */
        function Stat ( $type, $amount = null ) {
            $type = strtolower($type);

            // Accuracy
            if ( strcmp($type, 'acc') === 0 ) {
                return $this->Md($type);
            }
            if ( strcmp($type, 'evv') === 0 ) {
                return $this->Md($type);
            }
            // The Rest
            $iv = $this->Iv($type);
            $ev = $this->Ev($type);
            $md = $this->Md($type, $amount);
            $bs = $this->Species()->_var('bs_' . $type);
            $level = $this->level();
            if ( strcmp($type, 'hp') === 0 ) {
                return ~~(($iv + 2 * $bs + ($ev / 4) ) * ($level / 100) ) + 10 + $level;
            }
            return ((($iv + 2 * $bs + ($ev / 4) ) * ($level / 100) ) + 5) * $md;
        }
        /**
         * Returns or Sets the Status of the evolving state of the Drawnimal.
         * @param type $setEvolving (on true) Set the value of evolve to EVOLVE SCRIPT (if false) Set evolving to ''
         * @param string $itemused Name of item being used for evolving.
         * @param boolean $traded (true) if trading, (false) if not trading
         * @return boolean Whether evolving or not.
         */
        function Evolving ( $setEvolving = null, $itemused = null, $traded = null ) {
            $itemused = $itemused;
            $traded = $traded;
            if ( $setEvolving == null ) {
                return $this->_var('info_evolving');
            } elseif ( $setEvolving == false ) {
                return $this->_var('info_evolving', '');
            } else {
                return $this->_var('info_evolving', eval($this->Species()->EvolveScript()));
            }
        }
        /**
         * Changes the Species of the Drawnimal to whatever it is evolving into.
         * WARNING: Evolving(true, ..., ...) Must be ran beforehand to setup the evolution or else this function will not do anything
         */
        function Evolve () {
            $newspecies = $this->Evolving();
            if ( $newspecies === '' ) {
                return false;
            }

            // Set the Nickname to match the species.
            if ( strcasecmp($this->Nickname(), $this->Species()->Name()) == 0 ) {
                $this->Nickname(ucfirst($newspecies));
            }

            // $oldspecies = $this->Species();
            // @todo add timeline for evolving.
            $this->Species($newspecies);

            $move = $this->Species()->LearnSet($this->Level());
            if ( $move != null ) {
                $this->Move(4, $move);
            }

            $this->Owner()->Species()->Caught($newspecies, true);
            $this->Evolving(false);
        }
        function ResetStats() {
            $this->Ailments()->Reset();
            $this->Hp(1000000);
            $this->Md('atk',false);
            $this->Md('def',false);
            $this->Md('spatk',false);
            $this->Md('spdef',false);
            $this->Md('speed',false);
            $this->_save();
        }
        
        function Egg() { return ($this->Exp()<$this->Species()->HatchRate()); }
        function EggWarm($apply=false) {
            // Returns if ready to hatch
            if ($this->Egg()) {
                if ($this->Exp()+1 < $this->Species()->HatchRate()) {
                    if ($apply) {
                        $this->Exp(1);
                    }
                    return false;
                }
                return true;
            }
        }
        function EggHatch() {
            if ($this->Owner()->Id() === PLAYERCLASS::byMe()->Id()) {
                if ($this->Exp()+1 === $this->Species()->HatchRate()) {
                    $this->Exp(1);
                    return true;
                }
            }
            return false;
        }
        
        // Classes
        function Ailments () {
            return new MONSTERCLASSABILITIES($this);
        }
        function Mood () {
            return new DRAWNIMALMOODCLASS($this);
        }
        function Render () {
            return new RENDERMONSTERCLASS($this);
        }


    }
class MONSTERCLASSABILITIES {
        private $parent;
        /**
         * DO NOT USE - CALL ONLY FROM DRAWNIMALCLASS
         * @param type $parent
         */
        function __construct ( $parent ) {
            $this->parent = $parent;
        }
        /**
         * Sets the value for the ailment($name) to number value($value) not relative
         * @param string $ailmentName
         * @param int/boolean $ailmentValue Set to false to remove ailment
         * @return void
         */
        function Set ( $ailmentName, $ailmentValue, $relative=false ) {
            if ($relative) {
                $ailmentValue += $this->GetPower($ailmentName);
            }
            $ailmentValue = max(0,$ailmentValue);
            $newAilmentString = '';
            $found = false;
            $ailments = explode('|', $this->parent->_var('ailments'));
            foreach ( $ailments as $ailment ) {
                $ailment = explode('?', $ailment);
                if ( strcmp($ailment[0],'') === 0) continue;
                if ( strpos($ailment[0], $ailmentName) !== false ) {
                    $ailment[1] = $ailmentValue;
                    $found = true;
                }
                $newAilmentString .= '|' . $ailment[0] . '?' . $ailment[1];
            }
            if ( $found === false ) {
                $newAilmentString .= '|' . $ailmentName . '?' . $ailmentValue;
            }
            $this->parent->_var('ailments', $newAilmentString);
        }
        /**
         * Returns the value for the ailment($name), if false then ailment isnt set
         * @param type $ailmentName
         * @return int/false
         */
        function Get ( $ailmentName ) {
            $ailments = explode('|', $this->parent->_var('ailments'));
            foreach ( $ailments as $ailment ) {
                $ailment = explode('?', $ailment);
                if ( strpos($ailment[0], $ailmentName) !== false ) {
                    return CREATEABILITYCLASS::byName($ailment[0], $ailment[1]);
                }
            }
            return false;
        }
        function GetPower ( $ailmentName ) {
            $ailmentName = strtolower($ailmentName);
            $ailments = explode('|', $this->parent->_var('ailments'));
            foreach ( $ailments as $ailment ) {
                $ailment = explode('?', $ailment);
                if ( strpos(strtolower($ailment[0]), $ailmentName) !== false ) {
                    return $ailment[1];
                }
            }
            return 0;
        }
        /**
         * Resets the ailments of the Drawnimal to the birthright ailments
         */
        function Reset () {
            $this->parent->_var('ailments', $this->parent->_var('ailments_default'));
        }
        /**
         * Executes an event amongst the ailments, Each ailments script is evaluated.
         * Any string can be an event but ailments will only react to certain events.<br/><br/>
         * Example Events: <br/>
         * BTTLRoundBegin, BTTLRoundEnd,
         * BTTLMoveBegin, BTTLMoveEnd, BTTLAttackBegin, BTTLAttackEnd, BTTLDefendBegin, BTTLDefendEnd, BTTLItemBegin,
         * BTTLItemEnd, BTTLSwitchBegin, BTTLSwitchEnd, BTTLItemUsed, BTTLOpponentMoveBegin, BTTLOpponentMoveEnd, BTTLOpponentAttackBegin,
         * BTTLOpponentAttackEnd, BTTLOpponentDefendBegin, BTTLOpponentDefendEnd, BTTLOpponentItemBegin, BTTLOpponentItemEnd, BTTLOpponentSwitchBegin,
         * BTTLOpponentSwitchEnd, BTTLAllyMoveBegin, BTTLAllyMoveEnd, BTTLAllyAttackBegin, BTTLAllyAttackEnd, BTTLAllyDefendBegin, BTTLAllyDefendEnd,
         * BTTLAllyItemBegin, BTTLAllyItemEnd, BTTLAllySwitchBegin, BTTLAllySwitchEnd, RPGItemUsed, StatusAdded%statusname%, StatusRemoved%statusname%,
         * RPGWalkingCycle, RPGTimerCycle...
         * @param type $event
         * @return type
         * @todo Add more events
         */
        function Execute ( $event, $battleaction=null ) {
            $target = $this->parent;
            // Execute each ailment attached to the Drawnimal
            $ailments = explode('|', $this->parent->_var('ailments'));
            foreach ( $ailments as $ailment ) {
                if (strcmp($ailment,'')===0) continue;
                $ailment = explode('?', $ailment);
                $ailmentObject = CREATEABILITYCLASS::byName($ailment[0], $ailment[1]);
                if (!empty($ailmentObject->Id())) {
                    $arguments = array();
                    $arguments['MONSTER'] = ucwords($this->parent->Nickname());
                    eval(TWIGSTRING()->render($ailmentObject->Script(), $arguments));
                }
            }
            // Execute item based ailments
            $ailmentObject = CREATEABILITYCLASS::byName(preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($this->parent->Item()->Name())));
            if ( empty($ailmentObject->Id()) ) {
                
                return;
            }
            eval($ailmentObject->Script());
        }
        /**
         * Returns a array containing all the attached ailments.
         * @return array
         */
        function All () {
            $list = array();
            // Add all ailments attached to the parent
            $ailments = explode('|', $this->parent->_var('ailments'));
            foreach ( $ailments as $ailment ) {
                $ailment = explode('?', $ailment);
                if ( ($ailmentObject = CREATEABILITYCLASS::byName($ailment[0], $ailment[1])) === false ) {
                    continue;
                }
                if ( $ailmentObject !== false ) {
                    $list[] = $ailmentObject;
                }
            }
            // Add the item based ailment attached
            $ailmentObject = CREATEABILITYCLASS::byName(preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($this->parent->Item()->Name())));
            if ( $ailmentObject !== false ) {
                $list[] = $ailmentObject;
            }
            return $list;
        }
        function Defaults () {
            $list = array();
            // Add all ailments attached to the parent
            $ailments = explode('|', $this->parent->_var('ailments_default'));
            foreach ( $ailments as $ailment ) {
                $ailment = explode('?', $ailment);
                if ( ($ailmentObject = CREATEABILITYCLASS::byName($ailment[0], $ailment[1])) === false ) {
                    continue;
                }
                if ( $ailmentObject !== false ) {
                    $list[] = $ailmentObject;
                }
            }
            return $list;
        }


    }

    /*
 PKMNGetVar%variablename%
      return the new calculated value.
 GETForm
      When getting the form of this pokemon
      (example a muk who is poisoned could be a different form)
      to do this return the alternate form number.
 GETSpecies
      When getting the species of this pokemon (example ditto could return bulbasaur instead of ditto)
      to do this return the string of the species.
      @todo gotta figure out how to prevent learnset from being effected.
 GETGender
      When getting gender.
 */
