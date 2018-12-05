<?php
namespace WeWork\Model\ServiceProvider;

use WeWork\Util\Utils;

class PartyInfo
{
	public $id = null; // uint
	public $writable = null; // bool
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->id= Utils::arrayGet($arr, "id");
		$info->writable = Utils::arrayGet($arr, "writable");
		
		return $info;
	}
}
