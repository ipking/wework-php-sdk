<?php

use WeWork\Core\CorpAPI;

$config = require('./config.php');
include_once ('../autoload.php');

 
try {
	$api = new CorpAPI($config['CORP_ID'], $config['APP_SECRET']);
    $ticket = $api->TicketGet();
    echo $ticket . "\n";

    //
    $ticket = $api->JsApiTicketGet();
    echo $ticket . "\n";
} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}

