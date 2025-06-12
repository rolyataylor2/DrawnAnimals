<?php
    $token = $_SESSION['token'] = uniqid();
    
    $drawnimal = PLAYERCLASS::byMe()->Monster()->byTeamByLeader();
    $arguments['MOVES'] = array();
    
    for($i=0;$i<4;$i++) {
        $move = $drawnimal->Move($i);
        $move->_load();
        ?>
        <a href="javascript:" onclick="BTTL.sendCommand('a=1&v={\'id\':<?php echo $i; ?>}&token=<?php echo $token; ?>');">
            <?php echo $move->Name(); ?>
        </a>
    <?php } ?>

<a href='javascript:' style="background-color:pink;" onclick="BTTL.sendCommand();">Back</a>

