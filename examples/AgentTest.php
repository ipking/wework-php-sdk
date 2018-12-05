<?php

use WeWork\Core\CorpAPI;
use WeWork\Model\Agent;

$config = require('./config.php');
include_once ('../autoload.php');


// ------------------------- 应用管理 --------------------------------------
try {
	$api = new CorpAPI($config['CORP_ID'], $config['APP_SECRET']);
    //
    $agent = new Agent();
    {
        $agent->agentid = $config['APP_ID'];
        $agent->description = "I'm description";
    }
    $api->AgentSet($agent);

    //
    $agent = $api->AgentGet($config['APP_ID']);
    var_dump($agent);

    //
    $agentList = $api->AgentGetList();
    var_dump($agentList);

} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}
