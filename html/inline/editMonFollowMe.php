<?php
    /**
     *  Headers
     */
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/class-monsters.php';
    /**
     * Variables
     */
    if (isset($_POST['id'])) {
        if (VerifyPostToken()) {
            $player = PLAYERCLASS::byMe();
            switch(intval($_POST['action'])) {
                case 0:
                    $monster = MONSTERCLASS::byId($_POST['id']);
                    if ($monster->Owner()->Id() === $player->Id()) {
                        $player->Follower($monster->Id());
                        $player->_save();
                        die('<h1>Setting Saved!</h1><sub>This monster is now following you!</sub>');
                    } else {
                        die('<h1>Setting Failed!</h1><sub>You are not the owner of this monster!</sub>');
                    }
                    break;
                case 1:
                    $player->Monster()->byFollowing(-1);
                    $player->_save();
                    die('<h1>Let This Monster Out</h1><sub>Nobody is following you now!</sub>');
                    break;
            }
            
        }
    }
    $monster = MONSTERCLASS::byId($_GET['arguments'][0]);
    if ($monster->Egg()) {
        $nickname = 'Egg';
    } else $nickname = $monster->Nickname();
    $token = $_SESSION['token'] = uniqid();
    ?>
<h1>Let <?php echo ucwords($nickname); ?> Follow you?  </h1>
<sub>Letting a monster out will allow it to follow you on the map. This can also increase it's happiness.</sub>
<a href='javascript:' onclick='inlinePopupSubmit({"id":<?php echo $_GET['arguments'][0]; ?>,"action":0,"token":"<?php echo $token; ?>"},"editMonFollowMe");'>Follow me!</a>
<a href='javascript:inlinePopupClose();' >Nevermind...</a>
<a href='javascript:' onclick='inlinePopupSubmit({"id":<?php echo $_GET['arguments'][0]; ?>,"action":1,"token":"<?php echo $token; ?>"},"editMonFollowMe");'>Nobody Follow Me :\</a>