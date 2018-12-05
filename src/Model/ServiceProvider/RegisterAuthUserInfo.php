<?php
namespace WeWork\Model\ServiceProvider;

use WeWork\Util\Utils;

class RegisterAuthUserInfo
{
	public $email = null; // string
	public $mobile = null; // string
	public $userid = null; // string
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->email = Utils::arrayGet($arr, "email");
		$info->mobile = Utils::arrayGet($arr, "mobile");
		$info->userid = Utils::arrayGet($arr, "userid");
		
		return $info;
	}
}
