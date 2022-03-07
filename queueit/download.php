<?php

/**
Script per a descarregar l'arxiu "integrationconfig.json" que emmagatzema el codi configuracio de la cua de IOC
*/

require_once('../ioc/lib/config.php');

$token = isset($_GET['token']) ? $_GET['token'] : false;
if ($token == '6be5336db2c119736cf48f475e051bfe') { 
	$content = shell_exec('curl --request GET https://ctti.queue-it.net/status/integrationconfig/secure/ctti --header "api-key: 96508ccf-067d-4fb5-9a54-d3789d86529a" --header "Host: queue-it.net"');

	if ($content) {
		file_put_contents($CONF->data_dir.'/integrationconfig.json', $content);
	}
} else {
    echo '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /></head><body>';
    echo "Acc&eacute;s incorrecte.<br />";
    echo '</body></html>';
}