<?php
    include_once '_php/mysqli.php';
    include_once '_php/class-player.php';
    include_once '_php/class-updates.php';
    include_once '_php/class-following.php';
    include_once '_php/class-type.php';
    include_once '_php/class-monsters.php';
    include_once '_php/TWIG-Var.php';
    
    $arguments = array();
    
    
    include_once 'html/_templates/_plugin/notifications.php';
    
    $user = (intval($_GET['id'])===0?PLAYERCLASS::byUsername($_GET['id']):PLAYERCLASS::byId($_GET['id']));
    $arguments['USER'] = array();
    $user->_load();
    $arguments['USER'] = $user->data;
    $arguments['USER']['me'] = (PLAYERCLASS::byMe()->Id()===$user->Id()?true:false);
    $arguments['USER']['edit'] = ($arguments['USER']['me']?'edit':'');
    $arguments['USER']['following'] = FOLLOWINGCLASS::byFollowing(PLAYERCLASS::byMe()->Id(),$user->Id())->UserId();
    
    $arguments['USER']['id'] = $user->Id();
    $arguments['USER']['username'] = $user->Username();
    $arguments['USER']['lastseen'] = $user->LastSeen();
    $arguments['USER']['avatar'] = $user->Avatar();
    $arguments['USER']['time'] = str_replace('AM','AM <img src="img/nighttime.png"/>',str_replace('PM','PM <img src="img/daytime.png"/>',$user->CurrentTime()));
    
    $arguments['TEAM'] = array();
    foreach($user->Monster()->byTeam() as $i) {
        $arguments['TEAM'][] = $i->Render()->badgeHorizontal('image|level','href="http://PokeWorlds.com/mon.php?id='.$i->Id().'"');
    }
        
    if (isset($_GET['about'])) {
        $arguments['ABOUTPAGE'] = 'selected';
        $arguments['USER']['aboutme'] = $user->AboutMe();
        $arguments['USER']['country'] = $user->Country();
        $arguments['USER']['gender'] = ($user->Gender()===0?'Male':($user->Gender()===1?'Female':'Unknown'));
        $arguments['USER']['timezone'] = $user->Timezone();
        $arguments['USER']['created'] = $user->Created();
        $arguments['USER']['lastseen'] = $user->LastSeen();
        $arguments['USER']['birthday'] = $user->BirthDay();
        $arguments['USERBODY'] = TWIG()->render('/html/_templates/user.about.twig', $arguments);
    } elseif (isset($_GET['friends'])) {
        $arguments['FRIENDSPAGE'] = 'selected';
        $users = FOLLOWINGCLASS::byUserId($user->Id());
        $arguments['FOLLOWING'] = array();
        foreach($users as $ii) {
            if ($ii->FollowingId() === $user->Id()) continue;
            $i = PLAYERCLASS::byId($ii->FollowingId());
            if (!empty($i->Id())) {
                $arguments['FOLLOWING'][] = array('username'=>$i->Username(),
                                                  'avatar'=>$i->Avatar());
            }
        }
        
        $users = FOLLOWINGCLASS::byFollowingId($user->Id());
        $arguments['FOLLOWERS'] = array();
        foreach($users as $ii) {
            if ($ii->UserId() === $user->Id()) continue;
            $i = PLAYERCLASS::byId($ii->UserId());
            if (!empty($i->Id())) {
                $arguments['FOLLOWERS'][] = array('username'=>$i->Username(),
                                                  'avatar'=>$i->Avatar());
            }
        }
        
        $users = FOLLOWINGCLASS::byFriend($user->Id());
        $arguments['FRIENDS'] = array();
        foreach($users as $ii) {
            $i = PLAYERCLASS::byId($ii->FollowingId());
            if (!empty($i->Id())) {
                $arguments['FRIENDS'][] = array('username'=>$i->Username(),
                                                  'avatar'=>$i->Avatar());
            }
        }
        
        $arguments['USERBODY'] = TWIG()->render('/html/_templates/user.friends.twig', $arguments);
    } elseif (isset($_GET['stats'])) {
        $arguments['STATSPAGE'] = 'selected';
        $arguments['USERBODY'] = TWIG()->render('/html/_templates/user.stats.twig', $arguments);
    } elseif (isset($_GET['rival'])) {
        $arguments['RIVALPAGE'] = 'selected';
        $arguments['USERBODY'] = TWIG()->render('/html/_templates/user.rival.twig', $arguments);
    } elseif (isset($_GET['awards'])) {
        $arguments['AWARDSPAGE'] = 'selected';
        $arguments['USERBODY'] = TWIG()->render('/html/_templates/user.awards.twig', $arguments);
    } elseif (isset($_GET['journel'])) {
        $arguments['JOURNELPAGE'] = 'selected';
        $arguments['UPDATES'] = array();
        foreach(UPDATECLASS::byUser($user->Id()) as $i) {
            $arguments['UPDATES'][] = $i->Render();
        }
        
        $arguments['USERBODY'] = TWIG()->render('/html/_templates/user.updates.twig', $arguments);
    } else {
        $arguments['SUMMERYPAGE'] = 'selected';
        $arguments['EXPERIENCE'] = $user->Experience()->TypeArray();
        $arguments['EXPERIENCELABELS'] = TYPECLASS::$typearray;
        $arguments['EXPERIENCECOLORS'] = TYPECLASS::$colorarray;
        
        
        $arguments['USERBODY'] = TWIG()->render('/html/_templates/user.summery.twig', $arguments);
    }
    
    $arguments['ADMIN'] = PLAYERCLASS::byMe()->isAdmin('userAdmin');
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    $arguments['TOOLBAR'] = include '_templates/_plugin/toolbar.php';
    $arguments['BODY'] = TWIG()->render('/html/_templates/user.twig', $arguments);
    
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);