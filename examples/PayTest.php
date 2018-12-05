<?php

use WeWork\Core\CorpAPI;
use WeWork\Model\Pay\SendWorkWxRedpackReq;

$config = require('./config.php');
include_once ('../autoload.php');
// 
$agentId = $config['APP_ID'];

 
try {
	$api = new CorpAPI($config['CORP_ID'], $config['APP_SECRET']);
    $SendWorkWxRedpackReq = new SendWorkWxRedpackReq();
    {
        $SendWorkWxRedpackReq->nonce_str = "nonce_str";
    }
    $api->SendWorkWxRedpack($SendWorkWxRedpackReq);
} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}
