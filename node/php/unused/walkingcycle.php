<?php
    session_id($argv[1]);
    include 'PLYR-Class.php';
    $i = 6;
    while($i--) {
        $pet = PLYR()->Party()->Pos($pos);
        if ($pet === false) {continue;}
        
        $exp = floor(($pet->Base()->HatchRate()/255)*15);
        $pet->Exp($exp);
        
        $hap = floor(($pet->Base()->Friendship()/255)*350);
        $pet->Mood()->Friendship($hap);
    }
