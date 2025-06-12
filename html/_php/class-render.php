<?php
 class RENDERCREATEMONSTERCLASS {
//put your code here
        function __construct ( CREATEMONSTERCLASS $parent ) {
            $this->parent = $parent;
        }
        function imageUrl ( $gender = '3', $paint = '' ) {
            $gender = '.g_' . $gender;
            $paint = '.c_' . $paint;
            if ( file_exists('/var/www/html/img/mon/' . $this->parent->Id() . $gender . $paint . '.png') ) {
                return 'http://PokeWorlds.com/img/mon/' . $this->parent->Id() . $gender . $paint . '.png';
            }
            return 'http://PokeWorlds.com/img/mon/blank.png';
        }
        function imageUploadUrl( $gender = '3', $paint = '' ) {
            $gender = '.g_' . $gender;
            $paint = '.c_' . $paint;
            if ( file_exists('/var/www/html/img/uploads/mon/' . $this->parent->Id() . $gender . $paint . '.png') ) {
                return 'http://PokeWorlds.com/img/uploads/mon/' . $this->parent->Id() . $gender . $paint . '.png';
            }
            return 'http://PokeWorlds.com/img/mon/blank.png';
        }
        function imageUrls () {
            
            $list = array();
            $files = glob('/var/www/html/img/uploads/mon/' . $this->parent->Id() . '.*');
            foreach ( $files as $filename ) {
                if (strlen(basename($filename)) < 4) continue;
                $approved = (file_exists(str_replace('/var/www/html/img/uploads/mon/','/var/www/html/img/mon/',$filename)) &&
                            file_exists(str_replace('/var/www/html/img/uploads/mon/ow/','/var/www/html/img/mon/ow/',$filename)));
                $attr = explode('.', $filename);
                $list[] = array('approved' => $approved,
                    'gender' => str_replace('g_', '', $attr[1]),
                    'paint' => str_replace('c_', '', $attr[2]),
                    'filename' => $this->parent->Id() . '.' . $attr[1] . '.' . $attr[2] . '.png');
            }
            return $list;
        }
        function soundUrl() {
            if (file_exists('/var/www/images/sfx/d/'.$this->parent->Id().'.mp3')) {
                return 'http://PokeWorlds.com/sfx/d/'.$this->parent->Id().'.mp3';
            } else {
                return '';
            }
        }
        function soundUpload($newfile) {
            if ($newfile === null) {
                if (file_exists('/var/www/html/sfx/d/u/'.$this->parent->Id().'.mp3')) {
                    return 'http://PokeWorlds.com/sfx/d/u/'.$this->parent->Id().'.mp3';
                } else {
                    return '';
                }
            } else {
                // COPY UPLOADED FILE To snd/u/
            }
        }
        function soundApprove($approve=false) {
            if ($approve === true) {
                copy('/var/www/images/sfx/d/u/'.$this->parent->Id().'.mp3','/var/www/images/sfx/d/'.$this->parent->Id().'.mp3');
            } else {
                unlink('/var/www/images/sfx/d/'.$this->parent->Id().'.mp3');
            }
        }

    }

    class RENDERMONSTERCLASS {
        private $parent;
        function __construct ( $parent ) {
            $this->parent = $parent;
        }
        function badgeHorizontal ( $labels='', $href='' ) {
            $arguments = array();
            
            $items = explode('|', $labels);
            foreach ( $items as $item ) {
                switch ( $item ) {
                    case 'onlyalive': 
                        if ($this->parent->Hp() === 0) {$href='';}
                        break;
                    case 'onlynonleader':
                        if ($this->parent->BattleLeader() > 0) {$href='';}
                        break;
                    case 'classes':
                        if ($this->parent->Egg()) continue;
                        if ($this->parent->Hp() === 0) {
                            $arguments['CLASS'] = 'fainted';
                        } elseif($this->parent->BattleLeader() > 0) {
                           $arguments['CLASS'] = 'leader';
                        }
                        break;
                    case 'hp': 
                        if ($this->parent->Egg()) continue;
                        $arguments['HPGUAGE'] = $this->guageHp();
                        break;
                    case 'hpguage':
                        if ($this->parent->Egg()) continue;
                        $arguments['HPGUAGE'] = $this->guageHpNoNumbers();
                        break;
                    case 'exp':
                        if ($this->parent->Egg()) continue;
                        $arguments['EXPGUAGE'] = $this->guageExp();
                        break;
                    case 'item':
                        if ( $this->parent->Item() === false ) {
                            continue;
                        }
                        $arguments['ITEM'] = 'ITEM: ' . $this->parent->Item()->Name();
                        break;
                    case 'itemicon':
                        if ( !empty($this->parent->Item()->Id()) ) {
                            $arguments['ITEMICON'] = true;
                        }
                        break;
                    case 'image': $arguments['IMAGEURL'] = $this->imageURL();
                        break;
                    case 'ball':
                        if ($this->parent->Egg()) continue;
                        $arguments['BALL'] = $this->parent->Ball();
                        break;
                    case 'caught': 
                        if ($this->parent->Egg()) continue;
                        $arguments['CAUGHT'] = PLAYERCLASS::byMe()->LogDrawnimal()->byDrawnimal($this->parent->Species())->Caught();
                        break;
                    case 'gender':
                        if ($this->parent->Egg()) $arguments['GENDER'] = 2;
                        $arguments['GENDER'] = $this->parent->Gender();
                        break;
                    case 'level': 
                        if ($this->parent->Egg()) continue;
                        $arguments['LEVEL'] = $this->parent->Level();
                        break;
                }
            }
            $arguments['ONCLICK'] = $href;
            $arguments['NAME'] = $this->parent->Nickname();
            
            $arguments['PAINT'] = $this->parent->Paint();
            if ($this->parent->Egg()) {
                $arguments['NAME'] = 'Egg';
                $arguments['PAINT'] = '';
            }
            return TWIG()->render('html/_templates/_plugin/render/monster/' . __FUNCTION__ . '.twig', $arguments);
        }
        function imageUrl () {
            if ($this->parent->Egg()) return 'http://PokeWorlds.com/img/mon/egg.png';
            $base = '/var/www/html/img/mon/';
            $possible = array();
            // FILENAME = 0.g_0.c_shiny.png
            $possible[] = $this->parent->Species()->Id() . '.g_' . $this->parent->Gender() . '.c_' . $this->parent->Paint() . '.png';
            // FILENAME = 0.g_3.c_shiny.png
            $possible[] = $this->parent->Species()->Id() . '.g_3.c_' . $this->parent->Paint() . '.png';
            // FILENAME = 0.g_0.c_.png
            $possible[] = $this->parent->Species()->Id() . '.g_' . $this->parent->Gender() . '.c_.png';
            // FILENAME = 0.png
            $possible[] = $this->parent->Species()->Id() . '.g_3.c_.png';
            foreach ( $possible as $image ) {
                if ( file_exists($base . $image) ) {
                    return 'http://PokeWorlds.com/img/mon/' . $image;
                }
            }
            return 'http://PokeWorlds.com/mon/NA.png';
        }
        function owUrl() {
            if ($this->parent->Egg()) return 'http://PokeWorlds.com/img/mon/ow/egg.png';
            $base = '/var/www/html/img/mon/ow/';
            $possible = array();
            // FILENAME = 0.g_0.c_shiny.png
            $possible[] = $this->parent->Species()->Id() . '.g_' . $this->parent->Gender() . '.c_' . $this->parent->Paint() . '.png';
            // FILENAME = 0.c_shiny.png
            $possible[] = $this->parent->Species()->Id() . '.c_' . $this->parent->Paint() . '.png';
            // FILENAME = 0.g_0.png
            $possible[] = $this->parent->Species()->Id() . '.g_' . $this->parent->Gender() . '.png';
            // FILENAME = 0.png
            $possible[] = $this->parent->Species()->Id() . '.g_3.c_.png';

            foreach ( $possible as $image ) {
                if ( file_exists($base . $image) ) {
                    return 'http://PokeWorlds.com/img/mon/ow/' . $image;
                }
            }
            return 0;
        }
        function iconUrl () {
            $possible = array();
            $possible[] = $this->parent->Species()->Name() . '.' . $this->parent->Form() . '.' . $this->parent->Gender() . '.' . $this->parent->Paint() . '.icon.gif';
            $possible[] = $this->parent->Species()->Name() . '.' . $this->parent->Form() . '.' . $this->parent->Paint() . '.icon.gif';
            $possible[] = $this->parent->Species()->Name() . '.' . $this->parent->Gender() . '.' . $this->parent->Paint() . '.icon.gif';
            $possible[] = $this->parent->Species()->Name() . '.' . $this->parent->Paint() . '.icon.gif';
            $possible[] = $this->parent->Species()->Name() . '.icon.gif';
            foreach ( $possible as $image ) {
                if ( file_exists('/var/www/html/img/mon/icon/' . $image) ) {
                    return 'http://PokeWorlds.com/img/mon/icon/' . $image;
                }
            }
            return 'http://PokeWorlds.com/img/mon/NA.png';
        }
        function statTable () {
            $variables = array();
            $variables['ATK'] = floor($this->parent->Stat('atk'));
            $variables['DEF'] = floor($this->parent->Stat('def'));
            $variables['SPATK'] = floor($this->parent->Stat('spatk'));
            $variables['SPDEF'] = floor($this->parent->Stat('spdef'));
            $variables['SPEED'] = floor($this->parent->Stat('speed'));

            return TWIG()->render('html/_templates/_plugin/render/monster/' . __FUNCTION__ . '.twig', $variables);
        }
        function statsChart () {
            $variables['ID'] = $this->parent->Id();
            $variables['ATK'] = 200 * ($this->parent->Stat('atk') / 300);
            $variables['DEF'] = 200 * ($this->parent->Stat('def') / 300);
            $variables['SPATK'] = 200 * ($this->parent->Stat('spatk') / 300);
            $variables['SPDEF'] = 200 * ($this->parent->Stat('spdef') / 300);
            $variables['SPEED'] = 200 * ($this->parent->Stat('speed') / 300);

            $variables['ATKBASE'] = 200 * ($this->parent->Species()->Atk() / 170);
            $variables['DEFBASE'] = 200 * ($this->parent->Species()->Def() / 170);
            $variables['SPATKBASE'] = 200 * ($this->parent->Species()->SpAtk() / 170);
            $variables['SPDEFBASE'] = 200 * ($this->parent->Species()->SpDef() / 170);
            $variables['SPEEDBASE'] = 200 * ($this->parent->Species()->Speed() / 170);

            return TWIG()->render('html/_templates/_plugin/render/monster/' . __FUNCTION__ . '.twig', $variables);
        }
        function ailmentTable () {
            $ailments = $this->parent->Ailments()->All();
            $content = '';
            foreach ( $ailments as $ailment ) {
                if ( file_exists('/var/www/html/images/icons/ailment-' . $ailment->Name() . '.png') ) {
                    $content.='<a class="icon-ailment" href="#" onclick="MENU.Help.Ailment(\'id=' . $ailment->Id() . '\');"><img src="http://PokeWorlds.com/img/icons/ailment-' . $ailment->Name() . '.png" title="' . $ailment->Description() . '"/> ' . $ailment->PowerLevel() . '</a>';
                } else {
                    $content.='<a class="icon-ailment" href="#" onclick="MENU.Help.Ailment(\'id=' . $ailment->Id() . '\');" title="' . $ailment->Description() . '">' . $ailment->Name() . '</a>';
                }
            }
            return $content;
        }
        function ailmentDefaultsTable () {
            $ailments = $this->parent->Ailments()->All();
            $content = '';
            foreach ( $ailments as $ailment ) {
                if ( file_exists('/var/www/html/images/icons/ailment-' . $ailment->Name() . '.png') ) {
                    $content.='<a class="icon-ailment-default" href="#" onclick="MENU.Help.Ailment(\'id=' . $ailment->Id() . '\');"><img src="http://PokeWorlds.com/img/icons/ailment-' . $ailment->Name() . '.png" title="' . $ailment->Description() . '"/></a>';
                } else {
                    $content.='<a class="icon-ailment-default" href="#" onclick="MENU.Help.Ailment(\'id=' . $ailment->Id() . '\');" title="' . $ailment->Description() . '">' . $ailment->Name() . '</a>';
                }
            }
            return $content;
        }
        function guageExp () {
            return TWIG()->render('html/_templates/_plugin/render/monster/' . __FUNCTION__ . '.twig', array('EXP' => $this->parent->Exp(),
                                                                                        'EXPLAST' => pow(intval($this->parent->Level()), 3),
                                                                                        'EXPNEXT' => $this->parent->ExpNext()));
        }
        function guageHp () {
            $hp = $this->parent->Hp();
            $hpmax = $this->parent->Stat('HP');
            $hppercent = round(($hp / $hpmax) * 100);
            return TWIG()->render('html/_templates/_plugin/render/monster/' . __FUNCTION__ . '.twig', array('HP' => $hp,
                        'HPMAX' => $hpmax,
                        'HPPERCENT' => $hppercent));
        }
        function guageHpNoNumbers () {
            $hppercent = round(($this->parent->Hp() / $this->parent->Stat('HP')) * 100);
            return TWIG()->render('html/_templates/_plugin/render/monster/' . __FUNCTION__ . '.twig', array('HPPERCENT' => $hppercent));
        }
        

    }
