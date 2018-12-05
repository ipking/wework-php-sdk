<?php

use WeWork\Core\CorpAPI;

$config = require('./config.php');
include_once ('../autoload.php');
// 需启用 "管理工具" -> "通讯录同步", 并使用该处的secret, 才能通过API管理通讯录

try {
	
	$api = new CorpAPI($config['CORP_ID'], $config['APP_SECRET']);
	
	$invalidUserIdList = null;
    $invalidPartyIdList = null;
    $invalidTagIdList = null;
    $api->BatchInvite(
        array('ZhuShengBen', 'abelzhu', 'userid_for_invite_test'), array(1, 2, 111), array(1, 222), 
        $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
    var_dump($invalidUserIdList);
    var_dump($invalidPartyIdList);
    var_dump($invalidTagIdList);
} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}
