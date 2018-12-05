<?php

use WeWork\Core\CorpAPI;
use WeWork\Model\Department;

$config = require('./config.php');
include_once ('../autoload.php');

// 需启用 "管理工具" -> "通讯录同步", 并使用该处的secret, 才能通过API管理通讯录

 
try {
	$api = new CorpAPI($config['CORP_ID'], $config['CONTACT_SYNC_SECRET']);
    $department = new Department();
    {
        $department->name = "department_1";
        $department->parentid = 1;
        $department->id = 9;
    }
    $departmentId = $api->DepartmentCreate($department);
    echo $departmentId . "\n";

    //
    $department->name = "department_2";
    $api->DepartmentUpdate($department);

    //
    $departmentList = $api->DepartmentList();
    var_dump($departmentList);

    //
    $api->DepartmentDelete($departmentId);
} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
    $api->DepartmentDelete($departmentId);
}
