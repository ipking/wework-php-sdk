<?php
namespace WeWork\Model\CheckIn;

use WeWork\Util\Utils;

class CheckInInfo
{
	public $userid = null; // string
	public $group = null; // CheckinGroup
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->userid = Utils::arrayGet($arr, "userid");
		$info->group = CheckInGroup::ParseFromArray($arr["group"]);
		
		return $info;
	}
}