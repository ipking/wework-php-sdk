<?php
namespace WeWork\Model\CheckIn;

use WeWork\Util\Utils;

class CheckInTime {
	public $work_sec = null; // int
	public $off_work_sec = null; // int
	public $remind_work_sec = null; // int
	public $remind_off_work_sec = null; // int
	
	public static function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->work_sec = Utils::arrayGet($arr, "work_sec");
		$info->off_work_sec = Utils::arrayGet($arr, "off_work_sec");
		$info->remind_work_sec = Utils::arrayGet($arr, "remind_work_sec");
		$info->remind_off_work_sec = Utils::arrayGet($arr, "remind_off_work_sec");
		
		return $info;
	}
}