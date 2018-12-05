<?php

use WeWork\Core\CorpAPI;

$config = require('./config.php');
include_once ('../autoload.php');


try {
	$api = new CorpAPI($config['CORP_ID'], $config['APP_SECRET']);
    $mediaId = $api->MediaUpload("TestConfig.php", "file");
    echo $mediaId."\n";

    //
    $data = $api->MediaGet($mediaId);
    var_dump($data);
} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}
