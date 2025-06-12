<?php
    $token = $_SESSION['token'] = uniqid();
?>
<a href="javascript:" onclick="BTTL.sendCommand('a=4&v={}&token=<?php echo $token; ?>');">Yes Run</a>
<a href='javascript:' onclick="BTTL.sendCommand();">Cancel</a>