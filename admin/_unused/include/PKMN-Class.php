<?php
    include_once 'include/MOVE-Class.php';

    class GAMEREGIONCLASS {
        function __construct () {

        }
        function IsAdmin ( $value ) {
            return true;
        }


    }

    /**
     * PKMNAILMENTCLASS($parent_object)
     * (Never call outside of PKMNCLASS or BTTLPKMNCLASS)
     */
    class PKMNAILMENTCLASS {
        var $parent;
        function __construct ( $parent ) {
            $this->parent = $parent;
        }
        function _wrapper ( $name, $set ) {
            if ( $set != null ) {
                if ( is_numeric($set) ) {
                    $this->_set($name, $set);
                    return $this->_get($name);
                } else {
                    return null;
                }
            } else {
                return $this->_get($name);
            }
        }
        function _set ( $name, $value ) {
            $abilities = explode('|', $this->parent->_var('', 'ailments'));
            foreach ( $abilities as $key => $ability ) {
                if ( strpos($ability, $name) !== false ) {
                    if ( $value === 0 ) {
                        array_slice($abilities, $key, 1);
                        $this->Execute("StatusRemoved$name");
                    } else {
                        $ability = $name . $value;
                    }
                    $this->parent->_var('', 'ailments', implode('|', $abilities));
                    return;
                }
            }
        }
        function _get ( $name ) {
            $abilities = explode('|', $this->parent->_var('', 'ailments'));
            foreach ( $abilities as $ability ) {
                if ( strpos($ability, $name) !== false ) {
                    $ability = str_replace($name, '', $ability);
                    return intval($ability);
                }
            }
            return 0;
        }
        function Reset () {
            $this->parent->_var('', 'ailments', $this->parent->_var('', 'ailments_default'));
        }
        function Execute ( $type ) {
            $abilities = explode('|', $this->parent->_var('', 'ailments'));
            foreach ( $abilities as $ability ) {
                eval('$this->' . $ability . '("' . $type . '");');
            }
            $item = preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($this->parent->Item()));
            if ( method_exists($this, $item) ) {
                eval('$this->' . $item . '("' . $type . '");');
            }

            // ("BTTLRoundBegin"),
            // ("BTTLRoundEnd"),
            // ("BTTLMoveBegin"),
            // ("BTTLMoveEnd"),
            // ("BTTLAttackBegin"),
            // ("BTTLAttackEnd"),
            // ("BTTLDefendBegin"),
            // ("BTTLDefendEnd"),
            // ("BTTLItemBegin"),
            // ("BTTLItemEnd"),
            // ("BTTLSwitchBegin"),
            // ("BTTLSwitchEnd"),
            // ("BTTLItemUsed"),
            // ("BTTLOpponentMoveBegin"),
            // ("BTTLOpponentMoveEnd"),
            // ("BTTLOpponentAttackBegin"),
            // ("BTTLOpponentAttackEnd"),
            // ("BTTLOpponentDefendBegin"),
            // ("BTTLOpponentDefendEnd"),
            // ("BTTLOpponentItemBegin"),
            // ("BTTLOpponentItemEnd"),
            // ("BTTLOpponentSwitchBegin"),
            // ("BTTLOpponentSwitchEnd"),
            // ("BTTLAllyMoveBegin"),
            // ("BTTLAllyMoveEnd"),
            // ("BTTLAllyAttackBegin"),
            // ("BTTLAllyAttackEnd"),
            // ("BTTLAllyDefendBegin"),
            // ("BTTLAllyDefendEnd"),
            // ("BTTLAllyItemBegin"),
            // ("BTTLAllyItemEnd"),
            // ("BTTLAllySwitchBegin"),
            // ("BTTLAllySwitchEnd"
            //
        // ItemUsed
            // StatusAdded
            // StatusRemoved
            //
        //  NODEWalkingCycle
            //  NODETimerCycle
            //
        // PKMNGetVar%variablename%
            //      return the new calculated value.
            // GETForm
            //      When getting the form of this pokemon
            //      (example a muk who is poisoned could be a different form)
            //      to do this return the alternate form number.
            // GETSpecies
            //      When getting the species of this pokemon (example ditto could return bulbasaur instead of ditto)
            //      to do this return the string of the species.
            //      @todo gotta figure out how to prevent learnset from being effected.
            // GETGender
            //      When getting gender.
        }
        function All () {
            return explode('|', $this->parent->_var('', 'ailments'));
        }
//  Ailments
        function burn ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    $this->parent->Hp(-100);
                    BTTL()->QAnimation('AilmentBurn');
                    BTTL()->QDialog('Was hurt by its burn');
                    BTTL()->QHp(-100, $this->parent->id);
                    break;
            }
        }
        function freeze ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function paralysis ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function poison ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function badpoison ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'StatusAddedBadPoison':
                    if ( BTTL() != null ) {
                        BTTL()->Queue($this->parent->Nickname() . ' Became Badly Poisoned!');
                    }
                    break;
            }
        }
        function sleep ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'StatusAddedSleep':
                    if ( BTTL() != null ) {
                        BTTL()->Queue($this->parent->Nickname() . ' Fell Asleep!');
                    }
                    break;
            }
        }
//  Volatile
        function confusion ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function curse ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function embargo ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function encore ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function flinch ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function healblock ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function identification ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function infatuation ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function nightmare ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function partiallytrapped ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function perishsong ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function seeding ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function taunt ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function telekineticlevitation ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function torment ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function trapped ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
//  Items
        function aquaring ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function bracing ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function centerofattention ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function defensecurl ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function focusenergy ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function glowing ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function rooting ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function magiccoat ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function magneticlevitation ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function minimize ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function protection ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function recharging ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function semiinvulnerable ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function substitute ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function takingaim ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function takinginsunlight ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function withdrawing ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
        function whippingupawhirlwind ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BattleEndTurn':
                    break;
            }
        }
//  Abilities
        function angerpoint ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BTTLDefendEnd':
                    if ( BTTL()->Round()->Action()->critical ) {
                        BTTL()->Queue()->Dialog($this->parent->Nickname() . ' is getting angry!');
                        $this->parent->Stat('ATK', 1);
                    }
                    break;
            }
        }
        function nursesaid ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BTTLItemEnd':
                    if ( BTTL()->Round()->Action()->Item()->Catagory() == 'Healing' ) {
                        BTTL()->Queue()->Dialog($this->parent->Nickname() . ' helped nurse wounds!');
                        BTTL()->Round()->Action()->target->HP(15);
                    }
                    break;
            }
        }
        function undead ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BTTLItemUsed':
                    if ( strpos(BTTL()->Round()->Action()->Item()->Name(), 'Potion') !== false ) {
                        BTTL()->Queue()->Dialog($this->parent->Nickname() . ' is sickened by POTIONS!');
                        $this->Poison(1);
                    }
                    break;
            }
        }
        function empathy ( $set = null ) {
            $value = $this->_wrapper(__FUNCTION__, $set);
            if ( $value != null ) {
                return $value;
            }
            // Execute based on $set value
            switch ( $set ) {
                case 'BTTLDefendEnd':
                    if ( BTTL()->Round()->Action()->effective == 2 ) {
                        BTTL()->Queue()->Dialog(BTTL()->Round()->Action()->Attacker()->Nickname() . ' feels the pain of ' . $this->parent->Nickname() . '!');
                        BTTL()->Round()->Action()->Attacker()->Hp(BTTL()->Round()->Action()->dmg / 10);
                    }
                    break;
            }
        }


    }

    /**
     * PKMNMOODCLASS($parent_object)
     * (Never call outside of PKMNCLASS or BTTLPKMNCLASS)
     */
    class PKMNMOODCLASS {
        function __construct ( $parent ) {
            $this->parent = $parent;
        }
        private function _update () {
            $lastupdate = time() - $this->parent->_var('', 'st_mood_datetime');
            if ( $lastupdate > 256 ) {
                if ( $this->parent->PartyPos() === 0 ) {
                    $lastupdate = floor($lastupdate / 256) * 256;

                    $hunger = $this->parent->_var('', 'st_hunger');
                    $base_hunger = 0.5 + ($this->parent->Base()->Hunger() / 512);
                    $hunger += round($lastupdate * $base_hunger) / 2;
                    $this->parent->_var('', 'st_hunger', min(max($hunger, 0), 64000));

                    $energy = $this->parent->_var('', 'st_energy');
                    $base_energy = 0.5 + ($this->parent->Base()->Energy() / 512);
                    $energy += round($lastupdate * $base_energy) / 2;
                    $this->parent->_var('', 'st_energy', min(max($energy, 0), 64000));

                    $this->parent->_var('', 'st_mood_datetime', $lastupdate, true);
                } else {
                    $lastupdate = floor($lastupdate / 256) * 256;

                    $hunger = $this->parent->_var('', 'st_hunger');
                    $base_hunger = 0.5 + ($this->parent->Base()->Hunger() / 512);
                    $hunger -= round($lastupdate * $base_hunger) / 2;
                    $this->parent->_var('', 'st_hunger', min(max($hunger, 0), 64000));

                    $energy = $this->parent->_var('', 'st_energy');
                    $base_energy = 0.5 + ($this->parent->Base()->Energy() / 512);
                    $energy -= round($lastupdate * $base_energy) / 2;
                    $this->parent->_var('', 'st_energy', min(max($energy, 0), 64000));

                    $this->parent->_var('', 'st_mood_datetime', $lastupdate, true);
                }
            }
        }
        function Hunger ( $value = null ) {
            $this->_update();
            return round(($this->parent->_var('', 'st_hunger', $value, true) / 64000) * 100);
        }
        function Energy ( $value = null ) {
            $this->_update();
            return round(($this->parent->_var('', 'st_energy', $value, true) / 64000) * 100);
        }
        function Friendship ( $value = null ) {
            return round(($this->parent->_var('', 'st_friendship', $value, true) / 64000) * 100);
        }
        function Mood () {
            // @todo return the mood of the pet based on the 3 values above.
            return 'I am feeling okay for now...';
        }


    }

    /**
     * PKMNBASEOBJ() - Get info about species->form
     * @param string/int $species, $form
     */
    function PKMNBASEOBJ ( $species, $form = 0 ) {
        $species = strtolower($species);
        if ( ! isset($GLOBALS['PKMNBASE_LOADED'][$species]) ) {
            $GLOBALS['PKMNBASE_LOADED'][$species] = array();
            if ( ! isset($GLOBALS['PKMNBASE_LOADED'][$species][$form]) ) {
                $GLOBALS['PKMNBASE_LOADED'][$species][$form] = new PKMNBASECLASS($species, $form);
            }
        }
        return $GLOBALS['PKMNBASE_LOADED'][$species][$form];
    }
    $GLOBALS['PKMNBASE_LOADED'] = array();

    /**
     * PKMNBASECLASS($species,$form) !DONT USE!
     * (NEVER CREATE USE WRAPPER PKMNBASEOBJ($species,$form);
     */
    class PKMNBASECLASS {
        var $data;
        var $newdata;
        function __construct ( $species, $form = 0 ) {
            $this->destructdb = SQL();
            $this->data = array();
            if ( $species === -1 ) {
                $this->data['id'] = -1;
                $this->data['uid'] = PLYR()->id;
                return;
            }
            $this->data['id'] = 0;
            $this->data['species'] = $species;
            $this->data['form'] = $form;
        }
        function _save () {
            if ( ! isset($this->newdata) ) {
                return 'No New Data Variable';
            }
            if ( $this->_var('id', null) === -1 && isset($this->newdata['species']) && isset($this->newdata['form']) ) {
                $STMT = SQL()->prepare('SELECT uid, form FROM system_drawnimals WHERE species=?');
                $STMT->bind_param('s', $this->newdata['species']);
                $STMT->execute();
                $result = $STMT->get_result();
                $STMT->close();
                while ( $item = $result->fetch_assoc() ) {
                    if ( $item['uid'] !== PLYR()->id ) {
                        return 'The species name chosen has already been created by another user.';
                    }
                    if ( $item['form'] === $this->Form() ) {
                        return 'Please make up another form id, the one you have selected already exists.';
                    }
                }

                if ( $this->AppearanceRegion() !== 0 ) {
                    $region = new GAMEREGIONCLASS($this->AppearanceRegion());
                    if ( ! $region->IsAdmin(PLYR()->id) && ! PLYR()->IsAdmin('adminapprove') ) {
                        return 'You do not have permission to modify that region.';
                    }
                }

                $STMT = SQL()->prepare('INSERT INTO system_drawnimals (species,form) VALUES (?,?)');
                $STMT->bind_param('si', $this->newdata['species'], $this->newdata['form']);
                $STMT->execute();
                $STMT->close();
                $this->newdata['uid'] = PLYR()->id;
                $this->newdata['id'] = SQL()->insert_id;
                $this->data['id'] = SQL()->insert_id;
            }

            if ( $this->Creator() !== PLYR()->id && ! PLYR()->IsAdmin('adminapprove') ) {
                return 'You cannot modify a drawnimal you did not create';
            }

            $this->newdata['id'] = $this->data['id'];
            $types = '';
            $params = array();
            $query = 'UPDATE system_drawnimals SET id=id';
            foreach ( $this->newdata as $variable => $value ) {
                $query .= ', ' . $variable . '=?';
                $params[] = &$this->newdata[$variable];
                if ( is_numeric($value) ) {
                    $types .= 'i';
                } else {
                    $types .= 's';
                }
            }
            $query .= ' WHERE id=' . $this->newdata['id'];

            if ( strlen($types) === 0 ) {
                return 'Error with arguments list.';
            }
            $STMT = SQL()->prepare($query) or die($this->destructdb->error);
            call_user_func_array('mysqli_stmt_bind_param', array_merge(array($STMT, $types), $params));
            $STMT->execute();
            $STMT->close();
        }
        function _load () {
            if ( $this->data['id'] === -1 || ! isset($this->data['species']) ) {
                return;
            }
            $STMT = SQL()->prepare('SELECT * FROM system_drawnimals WHERE species=? AND form=?');
            $STMT->bind_param('si', $this->data['species'], $this->data['form']);
            $STMT->execute();
            if ( ($result = $STMT->get_result()) === false || $result->num_rows === 0 ) {
                $STMT->close();
                return false;
            }
            $this->data = $result->fetch_assoc();
            $STMT->close();
        }
        function _var ( $name, $set = null ) {
            $this->_load();
            $name = strtolower($name);

            if ( $set !== null ) {
                if ( ! isset($this->newdata) ) {
                    $this->newdata = array();
                }
                if ( is_numeric($set) ) {
                    $set = floor($set);
                }
                $this->newdata[$name] = $set;
            }
            if ( isset($this->newdata[$name]) ) {
                return $this->newdata[$name];
            }
            if ( isset($this->data[$name]) ) {
                return $this->data[$name];
            }
            return 0;
        }
        function Creator ( $value = null ) {
            return $this->_var('uid', $value);
        }
        function Approved ( $value = null ) {
            return $this->_var('approved', $value);
        }
        function Species ( $value = null ) {
            return $this->_var('species', $value);
        }
        function Form ( $value = null ) {
            return $this->_var('form', $value);
        }
        function TypePrimary ( $value = null ) {
            return $this->_var('type_0', $value);
        }
        function TypeSecondary ( $value = null ) {
            return $this->_var('type_1', $value);
        }
        function Ev ( $type, $value = null ) {
            return $this->_var('ev_' . $type, $value);
        }
        function Hp ( $value = null ) {
            return $this->_var('bs_hp', $value);
        }
        function Atk ( $value = null ) {
            return $this->_var('bs_atk', $value);
        }
        function Def ( $value = null ) {
            return $this->_var('bs_def', $value);
        }
        function SpAtk ( $value = null ) {
            return $this->_var('bs_spatk', $value);
        }
        function SpDef ( $value = null ) {
            return $this->_var('bs_spdef', $value);
        }
        function Speed ( $value = null ) {
            return $this->_var('bs_speed', $value);
        }
        function Experiance ( $value = null ) {
            return $this->_var('bs_exp', $value);
        }
        function Hunger ( $value = null ) {
            return $this->_var('bs_hunger', $value);
        }
        function Energy ( $value = null ) {
            return $this->_var('bs_energy', $value);
        }
        function Friendship ( $value = null ) {
            return $this->_var('bs_friendship', $value);
        }
        function CatchRate ( $value = null ) {
            return $this->_var('rate_catch', $value);
        }
        function GenderRate ( $value = null ) {
            return $this->_var('rate_gender', $value);
        }
        function LevelRate ( $value = null ) {
            return $this->_var('rate_level', $value);
        }
        function HatchRate ( $value = null ) {
            return $this->_var('rate_hatch', $value);
        }
        function EvolveScript ( $value = null ) {
            return $this->_var('script_evolve', $value);
        }
        function EvolveScriptRaw ( $value = null ) {
            return $this->_var('script_evolve_raw', $value);
        }
        function AppearanceScript ( $value = null ) {
            return $this->_var('appearance_script', $value);
        }
        function AppearanceScriptRaw ( $value = null ) {
            return $this->_var('appearance_script_raw', $value);
        }
        function AppearanceRegion ( $value = null ) {
            return $this->_var('appearance_region', $value);
        }
        function AppearanceEnvironment ( $value = null ) {
            return $this->_var('appearance_environment', $value);
        }
        function Abilities ( $value = null ) {
            return $this->_var('abilities', $value);
        }
        function Items ( $value = null ) {
            return $this->_var('items', $value);
        }
        function Description ( $value = null ) {
            return $this->_var('description', $value);
        }
        function DescriptionShort ( $value = null ) {
            return $this->_var('description_short', $value);
        }
        function ReleaseData ( $value = null ) {
            return $this->_var('datetime_release', $value);
        }
        function ImageRaw ( $value = null ) {
            return $this->_var('img_raw', $value);
        }
        // classes
        function Statistics () {
            return PKMNBASESTATISTICSOBJ($this->data['species']);
        }
        /** LearnSet() - Get the move Learnset for the species
         * @param integer $level (if numeric) returns the move learned at the level.
         * @param string $level (if string) returns whether the move can be learned.
         * @param integer $limit (if set) returns array of moves leading up to (and including) (int)$level.
         * @return bool/string True if can learn move $level. String if $level is-numeric.
         */
        function LearnSet ( $level = null, $limit = null ) {
            $query = "SELECT movename,level FROM system_drawnimals_learnset WHERE species=? AND form=?";
            if ( $level != null ) {
                if ( is_numeric($level) ) {
                    $query .= " AND level<=? ORDER BY level DESC";
                } else {
                    $query .= " AND movename=?";
                }
            }
            if ( $limit != null ) {
                $query .= " LIMIT " . intval($limit);
            }
            $STMT = SQL()->prepare($query);
            $STMT->bind_param('sis', $this->data['species'], $this->Form(), $level);
            $STMT->execute();
            if ( ($result = $STMT->get_result()) === false ) {
                $STMT->close();
                return false;
            }
            $rows = array();
            while ( $row = $result->fetch_assoc() ) {
                $rows[] = $row;
            }
            $STMT->close();
            return $rows;
        }


    }

    /**
     * PKMNOBJ() - Get PKMNCLASS object.
     * @param type $id
     * @param type $owner
     * @param type $species
     * @param type $level
     * @param PKMNCLASS $papa
     * @param PKMNCLASS $mama
     * @return \PKMNCLASS
     */
    function PKMNOBJ ( $id, $owner = null, $species = null, $level = null, PKMNCLASS $papa = null, PKMNCLASS $mama = null ) {
        if ( $id < 0 ) {
            return new PKMNCLASS($id, $owner, $species, $level, $papa, $mama);
        }
        if ( ! isset($GLOBALS['PKMN_LOADED'][$id]) ) {
            $GLOBALS['PKMN_LOADED'][$id] = new PKMNCLASS($id);
        }
        return $GLOBALS['PKMN_LOADED'][$id];
    }
    $GLOBALS['PKMN_LOADED'] = array();

    /**
     * PKMNCLASS !DONT USE!
     * Use the wrapper function PKMNOBJ();
     */
    class PKMNCLASS {
        /**
         * PKMNCLASS !DONT USE!
         * Use the wrapper function PKMNOBJ();
         */
        function __construct ( $id, $owner = null, $species = null, $level = null, PKMNCLASS $papa = null, PKMNCLASS $mama = null ) {
            if ( $id < 0 ) {
                $species = strtolower($species);
                if ( ($base = PKMNBASEOBJ($species)) === false ) {
                    return false;
                }
                if ( $papa != null && $mama != null ) {
                    $hpiv = floor(($mama->Stat('HP', 'IV') + mt_rand(0, 31)) / 2);
                    $atkiv = floor(($papa->Stat('ATK', 'IV') + mt_rand(0, 31)) / 2);
                    $spatkiv = floor(($papa->Stat('SP_ATK', 'IV') + mt_rand(0, 31)) / 2);
                    $defiv = floor(($mama->Stat('DEF', 'IV') + mt_rand(0, 31)) / 2);
                    $spdefiv = floor(($mama->Stat('SP_DEF', 'IV') + mt_rand(0, 31)) / 2);
                    $speediv = floor(($papa->Stat('SPEED', 'IV') + $mama->Stat('SPEED', 'IV') + mt_rand(0, 31)) / 3);
                    // @todo Inherit ailments (mainly abilities)
                    $abilities_default = '';
                    $origin_mama = $mama->id;
                    $origin_papa = $papa->id;
                } else {
                    $atkiv = mt_rand(0, 31);
                    $defiv = mt_rand(0, 31);
                    $spatkiv = mt_rand(0, 31);
                    $spdefiv = mt_rand(0, 31);
                    $speediv = mt_rand(0, 31);
                    $hpiv = mt_rand(0, 31);
                    $abilities_default = '';
                    $origin_mama = 0;
                    $origin_papa = 0;
                    // @todo Set itemheld and set likes and dislikes
                }

                // Create/Add to string of abilities from the base
                $abilities = $base->Abilities();
                if ( is_array($abilities) ) {
                    foreach ( $abilities as $key => $ability ) {
                        if ( is_numeric($ability) ) {
                            continue;
                        }
                        if ( mt_rand(0, 200) < $abilities[$key + 1] ) {
                            $abilities_default .= $ability . '1|';
                        }
                    }
                }

                // Insert new Drawnimal
                $STMT = SQL()->prepare('INSERT INTO user_drawnimals (uid, origin_owner, species, origin_species, origin_mama, origin_papa,
                                                                 st_level, st_exp, iv_hp, iv_atk, iv_def, iv_spatk, iv_spdef, iv_speed,
                                                                 ailments_default, info_datetime_created, st_mood_datetime)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)') or die(SQL()->error);
                $STMT->bind_param('iissiiiiiiiiiisii', $owner, $owner, $species, $species, $origin_mama, $origin_papa, $level, pow($level, 3), $hpiv, $atkiv, $defiv, $spatkiv, $spdefiv, $speediv, $abilities_default, time(), time());
                $STMT->execute();
                $STMT->close();
                $id = $this->id = intval(SQL()->insert_id);

                // Add Moves
                $i = 0;
                $moves = $this->Base()->LearnSet($level, 4);
                while ( isset($moves[$i]) ) {
                    $this->Move($i, $moves[$i]['movename']);
                    $i ++;
                }
                // Set some defaults
                $this->_var('', 'st_friendship', ($this->Base()->Friendship() / 255) * 64000);
                $this->ObtainedDate(true);
                $this->Nickname(ucfirst($species));
                $gender = $base->GenderRate();
                if ( $gender === 255 ) {
                    $this->Gender(3);
                } else {
                    $this->Gender((rand(0, 255) < $gender ? 2 : 1));
                }
                if ( mt_rand(1, 8192) === 1 ) {
                    $this->Paint('shiny');
                }
                $GLOBALS['PKMN_LOADED'][$this->id] = $this;
            }
            $this->id = intval($id);
            $this->destructdb = SQL();
        }
        function __destruct () {
            if ( ! isset($this->newdata) ) {
                return;
            }
            $types = '';
            $params = array();
            $query = 'UPDATE user_drawnimals SET id=id';
            foreach ( $this->newdata as $variable => $value ) {
                $query .= ', ' . $variable . '=?';
                $params[] = &$this->newdata[$variable];
                if ( is_numeric($value) ) {
                    $types .= 'i';
                } else {
                    $types .= 's';
                }
            }
            $query .= ' WHERE id=' . $this->id;
            // SAVE VALUES @todo for some reason 0 value hp doesnt save???
            if ( strlen($types) === 0 ) {
                return;
            }
            $STMT = $this->destructdb->prepare($query) or die($this->destructdb->error);
            call_user_func_array('mysqli_stmt_bind_param', array_merge(array($STMT, $types), $params));

            $STMT->execute();
            $STMT->close();
        }
        function _load () {
            if ( ! isset($this->data) ) {
                $this->data = array();
            } else {
                return;
            }

            $result = SQL()->query("SELECT * FROM user_drawnimals WHERE id=$this->id");
            $this->data = $result->fetch_assoc();
            $this->newdata = array();
        }
        function _var ( $table, $name, $set = null, $relative = null ) {
            $this->_load();
            $name = strtolower($name);
            if ( ! isset($this->data) ) {
                return false;
            }

            if ( $set !== null ) {
                if ( $relative === true ) {
                    $set+=$this->_var($table, $name);
                }
                if ( is_numeric($set) ) {
                    $set = floor($set);
                }
                $this->newdata[$name] = $set;
                if ( $this->PartyPos() > 0 ) {
                    switch ( $name ) {
                        case 'st_hp':
                            $this->Owner()->Node()->PartyVariable($this->id, 'hp', $set);
                            break;
                        case 'species': case 'form': case 'paint':
                            $this->Owner()->Node()->PartyVariable($this->id, 'image', $this->Printer()->ImageURL());
                            break;
                        case 'st_level':
                            $this->Owner()->Node()->PartyVariable($this->id, 'level', $set);
                            break;
                        case 'st_ailments':
                            $this->Owner()->Node()->PartyVariable($this->id, 'ailments', $this->Ailments()->All());
                            break;
                        case 'st_partypos':
                            $this->Owner()->Node()->PartyVariable($this->id, 'id', $this->id);
                            $this->Owner()->Node()->PartyVariable($this->id, 'name', $this->Nickname());
                            $this->Owner()->Node()->PartyVariable($this->id, 'hp', $this->Hp());
                            $this->Owner()->Node()->PartyVariable($this->id, 'hpmax', $this->Stat('hp'));
                            $this->Owner()->Node()->PartyVariable($this->id, 'image', $this->Printer()->ImageURL());
                            $this->Owner()->Node()->PartyVariable($this->id, 'level', $this->Level());
                            $this->Owner()->Node()->PartyVariable($this->id, 'ailments', $this->Ailments()->All());
                            break;
                    }
                }
            }
            if ( isset($this->newdata[$name]) ) {
                return $this->newdata[$name];
            }
            if ( isset($this->data[$name]) ) {
                return $this->data[$name];
            }
        }
        function Owner ( $value = null ) {
            if ( $value !== null ) {
                $this->ObtainedSpecies($this->Species());
                $this->ObtainedDate(true);
                $this->_var('', 'st_friendship', ($this->Base()->Friendship() / 255) * 64000);
            }
            return new PLYRCLASS($this->_var('', 'uid', $value));
        }
        function Nickname ( $value = null ) {
            return $this->_var('', 'name', $value);
        }
        function Species ( $value = null ) {
            return $this->_var('', 'species', $value);
        }
        function Paint ( $value = null ) {
            return $this->_var('', 'paint', $value);
        }
        function Form ( $value = null ) {
            return $this->_var('', 'form', $value);
        }
        function Move ( $number, $value = null ) {
            // Search for a name rather then number
            if ( ! is_numeric($number) ) {
                for ( $i = 4; $i > -2; $i --  ) {
                    if ( strcasecmp($this->_var('', 'move_' . $i), $number) === 0 ) {
                        break;
                    }
                }
                $number = $i;
            }
            // Safeguard stuff
            $number = intval($number);
            if ( $number < 0 || $number > 4 ) {
                return false;
            }
            $move = MOVEOBJ($this->_var('', 'move_' . $number, $value));

            if ( $value !== null ) {
                $this->_var('', 'move_pp_' . $number, $move->PPmax());
            }
            return $move;
        }
        function MovePP ( $number, $amount ) {
            // Search for a name rather then number
            if ( ! is_numeric($number) ) {
                for ( $i = 4; $i > -2; $i --  ) {
                    if ( strcasecmp($this->_var('', 'move_' . $i), $number) == 0 ) {
                        break;
                    }
                }
                $number = $i;
            }
            $this->_var('', 'move_pp_' . $number, $amount, true);
        }
        function Exp ( $value = null ) {
            return $this->_var('', 'st_exp', $value, true);
        }
        function ExpNext () {
            return pow($this->Level() + 1, 3);
        }
        function Level ( $value = null ) {
            if ( $value != null ) {
                $this->_var('', 'st_exp', pow(intval($value), 3));
                $this->_var('', 'st_level', intval($value));

                $move = $this->Base()->LearnSet($value);
                if ( $move != null ) {
                    $this->Move(4, $move);
                }
            }
            return $this->_var('', 'st_level');
        }
        function Hp ( $value = null ) {
            if ( $value != null ) {
                $value += $this->Hp();
                $value = max(min($value, $this->Stat('HP')), 0);
            }
            return $this->_var('', 'st_hp', $value);
        }
        function Item ( $value = null ) {
            return $this->_var('', 'st_helditem', $value);
        }
        function PartyPos ( $value = null ) {
            return $this->_var('', 'st_partypos', $value);
        }
        /** Ev() - Gets the ev points assotiated with value
         * @param string $value Can be HP,ATK,DEF,SPATK,SPDEF,SPEED
         * @param int $amount Add/Remove Ev point to HP,ATK,DEF,SPATK,SPDEF,SPEED
         * @return int Ev point value.
         */
        function Ev ( $value, $amount = null ) {
            return $this->_var('', 'ev_' . $value, $amount, true);
        }
        /** Iv() - Gets the Iv Points of this pet associated with $value.
         *
         * @param type $value Can be HP,ATK,DEF,SPATK,SPDEF,SPEED
         * @return int The iv point value for this pet
         */
        function Iv ( $value ) {
            return $this->_var('', 'iv_' . $value);
        }
        /** Md() - Gets/Sets the Modifier values associated with this pet
         * @param type $value Can be HP,ATK,DEF,SPATK,SPDEF,SPEED,EVV,ACC
         * @param type $amount Amount to change the Md value.
         * @return int|boolean Either value or If value is maxed out
         */
        function Md ( $value, $amount = null ) {
            if ( $amount != null ) {
                $amount += $this->Md($value);
                $amount = max(min($value, 7), -7);
            }
            $md = $this->_var('', 'md_' . $value, $amount);
            if ( $md === false ) {
                return 1;
            }
            return ($md <= 0 ? 2 / (abs($md) + 2) : (abs($md) + 2) / 2 );
        }
        /** Stat() - Gets the stat value associated with this pet.
         * @param string $type Get current stat value for HP,ATK,DEF,SPATK,SPDEF,SPEED,ACC,EVV.
         * @param integer $value (if integer) Set mod value +/- for HP,ATK,DEF,SPATK,SPDEF,SPEED,ACC,EVV.
         */
        function Stat ( $value, $amount = null ) {
            $type = strtolower($value);

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
            $bs = $this->Base()->_var('bs_' . $type);
            $level = $this->level();
            if ( strcmp($type, 'hp') === 0 ) {
                return ~~(($iv + 2 * $bs + ($ev / 4) ) * ($level / 100) ) + 10 + $level;
            }
            return ((($iv + 2 * $bs + ($ev / 4) ) * ($level / 100) ) + 5) * $md;
        }
        function OriginalOwner () {
            return new PLYRCLASS($this->_var('', 'origin_owner'));
        }
        function OriginalLocation () {
            return $this->_var('', 'origin_location');
        }
        function OriginalSpecies () {
            return $this->_var('', 'origin_species');
        }
        function Mama () {
            return PKMNOBJ($this->_var('', 'origin_mama'));
        }
        function Papa () {
            return PKMNOBJ($this->_var('', 'origin_papa'));
        }
        function Children () {
            $result = SQL()->query("SELECT id FROM user_drawnimals WHERE origin_mama=$this->id OR origin_papa=$this->id");
            if ( $result === false || $result->num_rows === 0 ) {
                return false;
            }
            $list = array();
            while ( $row = $result->fetch_row() ) {
                $list[] = PKMNOBJ($row[0]);
            }
            return $list;
        }
        function Mates () {
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
                $list[] = PKMNOBJ($row[0]);
            }
            return $list;
        }
        function Siblings () {
            $papa = $this->Papa();
            $mama = $this->Mama();
            $result = SQL()->query("SELECT id FROM user_drawnimals WHERE origin_papa=$papa->id OR origin_mama=$mama->id");

            if ( $result === false || $result->num_rows === 0 ) {
                return false;
            }
            $list = array();
            while ( $row = $result->fetch_row() ) {
                $list[] = PKMNOBJ($row[0]);
            }
            return $list;
        }
        function Gender ( $value = null ) {
            // @todo Get Images (2 = female, 1 = male, 3 = unknown)
            return $this->_var('', 'info_gender', $value);
        }
        function Age () {
            return $this->_var('', 'info_datetime_created');
        }
        function ObtainedDate ( $value = null ) {
            if ( $value !== null ) {
                $value = time();
            }
            return $this->_var('', 'info_obtained_datetime', $value);
        }
        function ObtainedSpecies ( $value = null ) {
            if ( $value !== null ) {
                $value = $this->Species();
            }
            return $this->_var('', 'info_obtained_species', $value);
        }
        function Awards ( $value = null ) {
            return $this->_var('', 'info_awards', $value);
        }
        function AboutMe ( $value = null ) {
            return $this->_var('', 'info_aboutme', $value);
        }
        function Trading ( $value = null ) {
            return $this->_var('', 'info_trading', $value);
        }
        function Ball ( $value = null ) {
            return $this->_var('', 'info_ball', $value);
        }
        function Likes () {
            return $this->_var('', 'info_likes');
        }
        function Dislikes () {
            return $this->_var('', 'info_dislikes');
        }
        /** Evolving() - Set/Get the evolving status of this pet
         * @param type $value (on true) Set the value of evolve to EVOLVE SCRIPT (if false) Set evolving to ''
         * @param type $itemused Name of item being used for evolving.
         * @param type $traded (true) if trading, (false) if not trading
         * @return type Whether evolving or not.
         */
        function Evolving ( $value = null, $itemused = null, $traded = null ) {
            $itemused = $itemused;
            $traded = $traded;
            if ( $value == null ) {
                return $this->_var('', 'info_evolving');
            } elseif ( $value == false ) {
                return $this->_var('', 'info_evolving', '');
            } else {
                return $this->_var('', 'info_evolving', eval($this->Base()->EvolveScript()));
            }
        }
        /** Evolve() - Evolve this Drawnimal!
         * Must run Evolving(true,...) beforehand or else nothing will happen.
         */
        function Evolve () {
            $newspecies = $this->Evolving();
            if ( $newspecies === '' ) {
                return false;
            }

            // Set the Nickname to match the species.
            if ( strcasecmp($this->Nickname(), $this->Species()) == 0 ) {
                $this->Nickname(ucfirst($newspecies));
            }

            // $oldspecies = $this->Species();
            // @todo add timeline for evolving.
            $this->Species($newspecies);

            $move = $this->Base()->LearnSet($this->Level());
            if ( $move != null ) {
                $this->Move(4, $move);
            }

            $this->Owner()->Species()->Caught($newspecies, true);
            $this->Evolving(false);
        }
        //classes
        function Ailments () {
            return PKMNAILMENTCLASS($this);
        }
        function Mood () {
            return PKMNMOODCLASS($this);
        }
        function Base () {
            return PKMNBASEOBJ($this->Species(), $this->Form());
        }
        function Printer () {
            if ( isset($this->printer) ) {
                return $this->printer;
            }
            return $this->printer = new PKMNPRINTERCLASS($this->id);
        }


    }

    class PKMNPRINTERCLASS {
        function __construct ( $id ) {
            $this->parent = PKMNOBJ($id);
        }
        function MoodSummery () {
            $arguments = array();
            $arguments['MOOD'] = $this->parent->Mood()->Mood();
            $arguments['HUNGERGUAGE'] = $this->HungerGuage();
            $arguments['ENERGYGUAGE'] = $this->EnergyGuage();
            $arguments['FRIENDGUAGE'] = $this->FriendshipGuage();
            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', $arguments);
        }
        function HungerGuage () {
            $hunger = $this->parent->Mood()->Hunger();
            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', array('HUNGER' => $hunger));
        }
        function EnergyGuage () {
            $energy = $this->parent->Mood()->Energy();
            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', array('ENERGY' => $energy));
        }
        function FriendshipGuage () {
            $freindship = $this->parent->Mood()->Friendship();
            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', array('FRIENDSHIP' => $freindship));
        }
        function HpGuage () {
            $hp = $this->parent->Hp();
            $hpmax = $this->parent->Stat('HP');
            $hppercent = round(($hp / $hpmax) * 100);
            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', array('HP' => $hp,
                        'HPMAX' => $hpmax,
                        'HPPERCENT' => $hppercent));
        }
        function HpGuageNoNumbers () {
            $hppercent = round(($this->parent->Hp() / $this->parent->Stat('HP')) * 100);
            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', array('HPPERCENT' => $hppercent));
        }
        function ExpGuage () {
            $exppercent = round(($this->parent->Exp() / $this->parent->ExpNext()) * 100);
            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', array('EXPPERCENT' => $exppercent));
        }
        function AilmentList () {
            $ailments = $this->parent->Ailments()->All();
            $content = '';
            foreach ( $ailments as $ailment ) {
                if ( file_exists('res/img/icons/ailment-' . $ailment . '.png') ) {
                    $content.='<img src="res/img/icons/ailment-' . $ailment . '.png"/>';
                }
            }
            return $content;
        }
        function Caught () {
            if ( PLYR() === false ) {
                return 0;
            }
            return PLYR()->Species()->Caught($this->parent->Species());
        }
        function Summery ( $onclick = '', $items = '' ) {
            $arguments = array();
            $arguments['ONCLICK'] = $onclick;
            $arguments['NAME'] = $this->parent->Nickname();
            $items = explode('|', $items);
            foreach ( $items as $item ) {
                switch ( $item ) {
                    case 'hp': $arguments['HPGUAGE'] = $this->HpGuage();
                        break;
                    case 'hpguage': $arguments['HPGUAGE'] = $this->HpGuageNoNumbers();
                        break;
                    case 'exp': $arguments['EXPGUAGE'] = $this->ExpGuage();
                        break;
                    case 'item':
                        if ( $this->parent->Item() == 0 ) {
                            continue;
                        }
                        $item = new ITEMCLASS($this->parent->Item());
                        $arguments['ITEM'] = 'ITEM: ' . $item->Base()->Name();
                        break;
                    case 'itemicon':
                        if ( $this->parent->Item() !== 0 ) {
                            $arguments['ITEMICON'] = true;
                        }
                        break;
                    case 'image': $arguments['IMAGEURL'] = $this->ImageURL();
                        break;
                    case 'ball': $arguments['BALL'] = $this->parent->Ball();
                        break;
                    case 'caught': $arguments['CAUGHT'] = PLYR()->Species()->Caught($this->parent->Species());
                        break;
                    case 'gender': $arguments['GENDER'] = $this->parent->Gender();
                        break;
                    case 'level': $arguments['LEVEL'] = $this->parent->Level();
                        break;
                }
            }
            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', $arguments);
        }
        function SmallSummeryWild () {
            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', array('HPGUAGE' => $this->HpGuageNoNumbers(),
                        'NAME' => $this->parent->Nickname(),
                        'GENDER' => $this->parent->Gender(),
                        'LEVEL' => $this->parent->Level(),
                        'CAUGHT' => $this->Caught()));
        }
        function SmallSummeryTrainer () {
            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', array('HPGUAGE' => $this->HpGuageNoNumbers(),
                        'NAME' => $this->parent->Nickname(),
                        'GENDER' => $this->parent->Gender(),
                        'LEVEL' => $this->parent->Level()));
        }
        function SmallSummeryMine ( $onclick = '' ) {
            // @todo pass only the arguments you want to render such as HP, ITEM HELD, and whatnot.
            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', array('HPGUAGE' => $this->HpGuage(),
                        'ONCLICK' => $onclick,
                        'EXPGUAGE' => $this->ExpGuage(),
                        'IMAGEURL' => $this->ImageURL(),
                        'AILMENTS' => $this->AilmentList(),
                        'NAME' => $this->parent->Nickname(),
                        'GENDER' => $this->parent->Gender(),
                        'LEVEL' => $this->parent->Level(),
                        'BALL' => $this->parent->Ball()));
        }
        function ImageURL ( $small = false ) {
            if ( $small === true ) {
                $small = '.sm';
            } else {
                $small = '';
            }
            $possible = array();
            $possible[] = $this->parent->Species() . '.' . $this->parent->Form() . '.' . $this->parent->Gender() . '.' . $this->parent->Paint() . $small . '.png';
            $possible[] = $this->parent->Species() . '.' . $this->parent->Form() . '.' . $this->parent->Paint() . $small . '.png';
            $possible[] = $this->parent->Species() . '.' . $this->parent->Gender() . '.' . $this->parent->Paint() . $small . '.png';
            $possible[] = $this->parent->Species() . '.' . $this->parent->Paint() . $small . '.png';
            $possible[] = $this->parent->Species() . $small . '.png';
            foreach ( $possible as $image ) {
                if ( file_exists('/var/www/html/images/drawnimals/' . $image) ) {
                    return 'http://img.drawnimals.com/drawnimals/' . $image;
                }
            }
            return 'http://img.drawnimals.com/drawnimals/' . $this->parent->Species() . $small . '.png';
        }
        function ImageFull () {
            // or bulbasaur.Form.Male.shiny.png
            // or bulbasaur.Form.shiny.png
            // or bulbasaur.Male.shiny.png
            // or bulbasaur.shiny.png
            // or bulbasaur.png

            $possible = array();
            $possible[] = 'res/img/drawnimals/' . $this->parent->Species() . '.' . $this->parent->Form() . '.' . $this->parent->Gender() . '.' . $this->parent->Paint() . '.png';
            $possible[] = 'res/img/drawnimals/' . $this->parent->Species() . '.' . $this->parent->Form() . '.' . $this->parent->Paint() . '.png';
            $possible[] = 'res/img/drawnimals/' . $this->parent->Species() . '.' . $this->parent->Gender() . '.' . $this->parent->Paint() . '.png';
            $possible[] = 'res/img/drawnimals/' . $this->parent->Species() . '.' . $this->parent->Paint() . '.png';
            $possible[] = 'res/img/drawnimals/' . $this->parent->Species() . '.png';
            foreach ( $possible as $image ) {
                if ( file_exists('/var/www/html/' . $image) ) {
                    return '<img class="PkmnImageFull" src="' . $image . '"/>';
                }
            }
            return '<img class="PkmnImageFull" src=""/>';
        }
        function ImageSmall () {
            return '<img class="PkmnImageSmall" src="res/img/drawnimals/' . $this->parent->Species() . '.' . $this->parent->Paint() . '.small.png"/>';
        }
        function ImageTiny () {
            return '<img class="PkmnImageTiny" src="res/img/drawnimals/' . $this->parent->Species() . '.' . $this->parent->Paint() . '.tiny.png"/>';
        }
        function StatsFull () {
            $variables = array();
            $variables['ATK'] = floor($this->parent->Stat('atk'));
            $variables['DEF'] = floor($this->parent->Stat('def'));
            $variables['SPATK'] = floor($this->parent->Stat('spatk'));
            $variables['SPDEF'] = floor($this->parent->Stat('spdef'));
            $variables['SPEED'] = floor($this->parent->Stat('speed'));

            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', $variables);
        }
        function StatsChart () {
            $variables['ID'] = $this->parent->id;
            $variables['ATK'] = 200 * ($this->parent->Stat('atk') / 300);
            $variables['DEF'] = 200 * ($this->parent->Stat('def') / 300);
            $variables['SPATK'] = 200 * ($this->parent->Stat('spatk') / 300);
            $variables['SPDEF'] = 200 * ($this->parent->Stat('spdef') / 300);
            $variables['SPEED'] = 200 * ($this->parent->Stat('speed') / 300);

            $variables['ATKBASE'] = 200 * ($this->parent->Base()->Atk() / 170);
            $variables['DEFBASE'] = 200 * ($this->parent->Base()->Def() / 170);
            $variables['SPATKBASE'] = 200 * ($this->parent->Base()->SpAtk() / 170);
            $variables['SPDEFBASE'] = 200 * ($this->parent->Base()->SpDef() / 170);
            $variables['SPEEDBASE'] = 200 * ($this->parent->Base()->Speed() / 170);

            return TWIG()->render('/_plugins/pkmn/' . __FUNCTION__ . '.twig', $variables);
        }
        function Move () {

        }
        function AboutMe () {

        }


    }
