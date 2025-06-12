<?php
if (!isset($_GET['goto']))  $_GET['goto'] = '';
if (!isset($_GET['p']))  $_GET['p'] = '';
$_GET['p'] = str_replace('.','',$_GET['p']);
$_GET['goto'] = str_replace('.','',$_GET['goto']);
if (file_exists('/var/www/html/_templates/_games/' . $_SESSION['currentGame'] . '/'.$_GET['goto'].'.php')) {
    include '/var/www/html/_templates/_games/' . $_SESSION['currentGame'] . '/'.$_GET['goto'].'.php';
    return TWIG()->render('/html/_templates/_games/' . $_SESSION['currentGame'] . '/_t/'.$_GET['goto'].'.twig', $arguments);
} elseif (file_exists('/var/www/html/_templates/_games/' . $_SESSION['currentGame'] . '/'.$_GET['p'].'.php')) {
    include '/var/www/html/_templates/_games/' . $_SESSION['currentGame'] . '/'.$_GET['p'].'.php';
    return TWIG()->render('/html/_templates/_games/' . $_SESSION['currentGame'] . '/_t/'.$_GET['p'].'.twig', $arguments);
} elseif (file_exists('/var/www/html/_templates/_games/' . $_SESSION['currentGame'] . '/index.php')) {
    include '/var/www/html/_templates/_games/' . $_SESSION['currentGame'] . '/index.php';
    return TWIG()->render('/html/_templates/_games/' . $_SESSION['currentGame'] . '/_t/index.twig', $arguments);
} else  {
    return 'Invalid Page';
}

