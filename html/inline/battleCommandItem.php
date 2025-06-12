<?php

    $token = $_SESSION['token'] = uniqid();
?>
No Items To Display
<a href="javascript:" onclick="BTTL.sendCommand('a=3&v={\'id\':0}&token=<?php echo $token;?>');">FAKE POKEBALL</a>
<a href='javascript:' style="background-color:pink;" onclick="BTTL.sendCommand();">Back</a>
