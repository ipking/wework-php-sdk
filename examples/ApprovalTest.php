<?php

use WeWork\Core\CorpAPI;

$config = require('./config.php');
include_once ('../autoload.php');

try {
	$api = new CorpAPI($config['CORP_ID'], $config['APPROVAL_APP_SECRET']);
    $ApprovalDataList = $api->ApprovalDataGet(1513649733, 1513770113);
    var_dump($ApprovalDataList);
} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}

