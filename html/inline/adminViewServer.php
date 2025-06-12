<?php

$logfile = file_get_contents('/var/www/node/logFile.log');
?>
<h1>Server Log:</h1>
<div id="ServerLog">
    <?php echo $logfile; ?>
</div>
<a href="javascript:">Restart Server</a>
<script>
    setTimeout(function() {$('#ServerLog').scrollTop($('#ServerLog')[0].scrollHeight);},100);
</script>