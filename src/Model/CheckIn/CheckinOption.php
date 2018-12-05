<?php

namespace WeWork\Model\CheckIn;


class CheckInOption
{
	public $info = null; // CheckinInfo array
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		foreach($arr["info"] as $item) {
			$info->info[] = CheckInInfo::ParseFromArray($item);
		}
		
		return $info;
	}
}