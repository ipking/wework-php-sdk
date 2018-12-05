<?php
namespace WeWork\Model\ServiceCorp;

use WeWork\Util\Utils;

class AppAdmin
{
	public $userid = null; // string
	public $auth_type = null; // uint, 该管理员对应用的权限：0=发消息权限，1=管理权限
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->userid = Utils::arrayGet($arr, "userid");
		$info->auth_type = Utils::arrayGet($arr, "auth_type");
		
		return $info;
	}
}

