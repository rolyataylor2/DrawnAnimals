<?php
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-battle.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/class-monsters.php';
    include_once 'html/_php/TWIG-Var.php';
    
    /// Initialize Vars if they are not.
    session_start();
    $battleid = $_SESSION['battleid'];
    $_SESSION['battle_timeout'] = ( isset($_SESSION['battle_timeout']) ? $_SESSION['battle_timeout'] : 0 );
    $_SESSION['battle_round'] = ( isset($_SESSION['battle_round']) ? $_SESSION['battle_round'] : 1 );
    $_SESSION['battle_round'] = ( isset($_GET['r']) ? $_GET['r'] : $_SESSION['battle_round']);
   
    /// Get Animation Queue
    $queue = BATTLEQUEUECLASS::byBattleByRound($battleid,$_SESSION['battle_round']);
    if ($queue->Id() !== null) {
        $_SESSION['battle_round'] += 1;
        die('<script>'.$queue->QueueCode().'</script>');
    }
    
    $player = PLAYERCLASS::byMe();
    /// If participating continue or else exit with >Waiting for battle to continue;
    if ($player->BattleId() != $battleid || $battleid == 0) {
        die('<script>BTTL.enQueue("eventDialog",["Waiting For Battle To Continue..."]);</script>');
    }
    
    /// Dead.... PROBLEM WITH PVP BATTLES, IT EJECTS THE PERSON BEFORE PLAYING BACK THE FINAL ROUND
    //    $teamalive = $player->Monster()->byTeamByAlive();
    //    if (count($teamalive) === 0 || empty($teamalive[0]->Id())) {
    //        $player->Reset();
    //        $_SESSION['battleid'] = 0;
    //        die('You are out of usable pokemon, You have been ejected out of battle...');
    //    }
    
    /// Learn move if needed
    if (!empty($player->Monster()->byTeamByLeader()->Move(4)->Id())) {
        die('Your Pokemon is trying to learn a move...<a href="javascript:BTTL.sendCommand();">Continue</a><script>inlinePopup("monsterLearnMove",'.$player->Monster()->byTeamByLeader()->Id().');</script>');
    }
    /// Evolve if needed!
//    if (!empty($player->Monster()->byTeamByLeader()->Move(4)->Id())) {
//        die('Your Pokemon is trying to learn a move!<a href="">Click Here!</a>');
//    }
    
    /// Waiting for Other Players!
    $battle = BATTLECLASS::byId($battleid);
    if ($battle->ActionByPlayer($player->Id())->Id() !== null) {
        $_SESSION['battle_timeout'] += 1;
        if ($_SESSION['battle_timeout'] < 10) {
            die('<script>BTTL.enQueue("eventDialog",["Waiting For Others..."]);</script>');
        } else {
            if (isset($_GET['w'])) {
                $_SESSION['battle_timeout'] = 0;
                die('<script>BTTL.enQueue("eventDialog",["Waiting For Others..."]);</script>');
            }
            if (isset($_GET['e'])) {
                $_SESSION['battle_timeout'] = 0;
                $player->Battle(-1);
                $player->_save();
                die('<script>window.location.reload(true);</script>');
            }
            die('<center>The other player Is taking a while</center><a href="javascript:BTTL.sendCommand(\'w\');">Wait For Them</a><a href="javascript:BTTL.sendCommand(\'e\');">End Battle</a>');
        }
    }
    $_SESSION['battle_timeout'] = 0;
    $_SESSION['battleid'] = $player->BattleId();
    // Do the battle stuff
    if ($player->Monster()->byTeamByLeader()->Hp() === 0) {
        $_GET['a'] = 2;
    }
    if (isset($_GET['a'])) {
        if (isset($_GET['token']) && $_GET['token'] === $_SESSION['token']) {
            if ($player->Battle()->SubmitAction($_GET['a'], $_GET['v'])) {
                $queue = BATTLEQUEUECLASS::byBattleByRound($battleid,$_SESSION['battle_round']);
                $_SESSION['battle_round'] += 1;
                die('<script>'.$queue->QueueCode().'</script>');
            }
            die('<script>BTTL.enQueue("eventDialog",["Waiting For Others..."]);</script>');
        }
        switch($_GET['a']) {
            default: case 1: // Choose Attack
                include_once 'battleCommandAttack.php';
                break;
            case 2: // Choose Switch
                include_once 'battleCommandSwitch.php';
                break;
            case 3: // Choose Item
                include_once 'battleCommandItem.php';
                break;
            case 4: // Choose Run
                include_once 'battleCommandRun.php';
                break;
        }

    } else {
        include_once 'battleCommandMove.php';
    }