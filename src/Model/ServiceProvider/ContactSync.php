<?php
namespace WeWork\Model\ServiceProvider;

use WeWork\Util\Utils;


class ContactSync
{
	public $access_token = null; // string
	public $expires_in = null; // uint
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->access_token = Utils::arrayGet($arr, "access_token");
		$info->expires_in = Utils::arrayGet($arr, "expires_in");
		
		return $info;
	}
}
