<?php
namespace WeWork\Model\ServiceCorp;


class AuthInfo
{
	public $agent = null; // AgentBriefEx array
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		foreach($arr["agent"] as $item) {
			$info->agent[] = AgentBriefEx::ParseFromArray($item);
		}
		
		return $info;
	}
}
