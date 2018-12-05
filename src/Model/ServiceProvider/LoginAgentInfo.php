<?php
namespace WeWork\Model\ServiceProvider;

use WeWork\Util\Utils;


class LoginAgentInfo
{
	public $agentid = null; // uint
	public $auth_type = null; // uint
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->agentid = Utils::arrayGet($arr, "agentid");
		$info->auth_type = Utils::arrayGet($arr, "auth_type");
		
		return $info;
	}
}
