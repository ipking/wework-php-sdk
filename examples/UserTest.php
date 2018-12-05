<?php

use WeWork\Core\CorpAPI;
use WeWork\Model\User\ExtattrItem;
use WeWork\Model\User\ExtattrList;
use WeWork\Model\User\User;

$config = require('./config.php');
include_once ('../autoload.php');
// 需启用 "管理工具" -> "通讯录同步", 并使用该处的secret, 才能通过API管理通讯录


try {
	$api = new CorpAPI($config['CORP_ID'], $config['CONTACT_SYNC_SECRET']);
	
	$user = new User();
    {
        $user->userid = "userid";
        $user->name = "name";
        $user->mobile = "131488888888";
        $user->email = "sbzhu@ipp.cas.cn";
        $user->department = array(1); 

        $ExtattrList = new ExtattrList();
        $ExtattrList->attrs = array(new ExtattrItem("s_a_2", "aaa"), new ExtattrItem("s_a_3", "bbb"));
        $user->extattr = $ExtattrList;
    } 
    $api->UserCreate($user);

    //
    $user = $api->UserGet("userid");
    var_dump($user);

    //
    $user->mobile = "1219887219873";
    $api->UserUpdate($user); 

    //
    $userList = $api->userSimpleList(1, 0);
    var_dump($userList);

    //
    $userList = $api->UserList(1, 0);
    var_dump($userList);

    //
    $openId = null;
    $api->UserId2OpenId("ZhuShengBen", $openId);
    echo "openid: $openId\n";

    //
    $userId = null;
    $api->openId2UserId($openId, $userId);
    echo "userid: $userId\n";

    //
    $api->UserAuthSuccess("userid");

    //
    $api->UserBatchDelete(array("userid", "aaa"));

    //
    $api->UserDelete("userid"); 
} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
    $api->UserDelete("userid"); 
}
