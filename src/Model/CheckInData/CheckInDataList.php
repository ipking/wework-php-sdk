<?php
namespace WeWork\Model\CheckInData;


class CheckInDataList
{
	public $checkindata = null; // CheckinData array
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		foreach($arr["checkindata"] as $item) {
			$info->checkindata[] = CheckInData::ParseFromArray($item);
		}
		
		return $info;
	}
}
