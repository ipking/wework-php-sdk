<?php
namespace WeWork\Model\ServiceProvider;

use WeWork\Util\Utils;

class LoginCorpInfo
{
	public $corpid = null; // string
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->corpid = Utils::arrayGet($arr, "corpid");
		
		return $info;
	}
}
