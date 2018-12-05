<?php
namespace WeWork\Model\CheckIn;

use WeWork\Util\Utils;


class SpeWorkDays {
	public $timestamp = null; // uint
	public $notes = null; // string
	public $checkintime = null; // CheckinTime array
	
	public static function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->timestamp = Utils::arrayGet($arr, "timestamp");
		$info->notes = Utils::arrayGet($arr, "notes");
		
		foreach($arr["checkintime"] as $item) {
			$info->checkintime[] = CheckInTime::ParseFromArray($item);
		}
		
		return $info;
	}
}
