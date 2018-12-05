<?php
namespace WeWork\Model\ApprovalData;

use WeWork\Util\Utils;

class LeaveEvent {
	public $timeunit = null; // int
	public $leave_type = null; // int
	public $start_time = null; // int
	public $end_time = null; // int
	public $duration = null; // int
	public $reason = null; // string
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->timeunit = Utils::arrayGet($arr, "timeunit");
		$info->leave_type = Utils::arrayGet($arr, "leave_type");
		$info->start_time = Utils::arrayGet($arr, "leave_type");
		$info->end_time = Utils::arrayGet($arr, "end_time");
		$info->duration = Utils::arrayGet($arr, "duration");
		$info->reason = Utils::arrayGet($arr, "reason");
		
		return $info;
	}
}
