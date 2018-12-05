<?php

use WeWork\Core\CorpAPI;

$config = require('./config.php');
include_once ('../autoload.php');
// 
$agentId = $config['APP_ID'];


try {
	$api = new CorpAPI($config['CORP_ID'], $config['APP_SECRET']);
    $UserInfoByCode = $api->GetUserInfoByCode("IPzWnFmIQVf2wJFlJrln9-P-wqu7jeQsKyUKol1TWeU"); 
    var_dump($UserInfoByCode);

    $userDetailByUserTicket = $api->GetUserDetailByUserTicket($UserInfoByCode->user_ticket); 
    var_dump($userDetailByUserTicket);

} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}
