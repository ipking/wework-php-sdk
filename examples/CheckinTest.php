<?php

use WeWork\Core\CorpAPI;

$config = require('./config.php');
include_once ('../autoload.php');
// 

try {
	$api = new CorpAPI($config['CORP_ID'], $config['CHECKIN_APP_SECRET']);
    //
    $checkinOption = $api->CheckinOptionGet(1513760113, array("ZhuShengBen"));
    var_dump($checkinOption);

    //
    $checkinDataList = $api->CheckinDataGet(1, 1513649733, 1513770113, array("ZhuShengBen"));
    var_dump($checkinDataList);
} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}

