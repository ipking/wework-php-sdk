<?php
namespace WeWork\Model\CheckIn;

use WeWork\Util\Utils;


class WifiMacInfo {
	public $wifiname = null; // string
	public $wifimac = null; // string
	
	public static function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->wifiname = Utils::arrayGet($arr, "wifiname");
		$info->wifimac = Utils::arrayGet($arr, "wifimac");
		
		return $info;
	}
}