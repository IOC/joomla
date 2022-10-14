<?php

require_once('configuration.php');

$CONF = new Jconfig();

$token = isset($_GET['token']) ? $_GET['token'] : false;
if ($token == $CONF->tokenCron) {
    $content = shell_exec('/usr/bin/php7.4 /dades/html/educacio/cli/sessionMetadataGc.php');
    echo 'OK cron Portal';
} else {
    echo '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /></head><body>';
    echo "Acc&eacute;s incorrecte.<br />";
    echo '</body></html>';
}
