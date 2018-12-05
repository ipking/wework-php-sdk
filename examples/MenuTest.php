<?php

use WeWork\Core\CorpAPI;
use WeWork\Model\Menu\Menu;
use WeWork\Model\Menu\PicWeixinMenu;
use WeWork\Model\Menu\ScanCodePushMenu;
use WeWork\Model\Menu\SubMenu;
use WeWork\Model\Menu\ViewMenu;

$config = require('./config.php');
include_once ('../autoload.php');
// 
$agentId = $config['APP_ID'];


try {
	$api = new CorpAPI($config['CORP_ID'], $config['APP_SECRET']);
    $subMenu = new SubMenu(
        "subMenu_1", 
        array(
            new ViewMenu("viewMenu_1", "www.qq.com"),
            new ViewMenu("viewMenu_2", "www.baidu.com")
        )
    );
    $scanCodePushMenu = new ScanCodePushMenu(
        "ScanCodePushMenu", 
        null, 
        array(
            new ViewMenu("viewMenu_3", "www.qq.com"),
            new PicWeixinMenu( "PicWeixinMenu", "keykeykey", null),
        )
    );

    $menu = new Menu(
        array(
            $subMenu,
            $scanCodePushMenu
        )
    );
    $api->MenuCreate($agentId, $menu);

    //
    $menu = $api->MenuGet($agentId);
    var_dump($menu);

    //
    $api->MenuDelete($agentId);

} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}

