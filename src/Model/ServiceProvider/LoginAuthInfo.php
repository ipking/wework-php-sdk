<?php
namespace WeWork\Model\ServiceProvider;


class LoginAuthInfo
{
	public $department = null; // PartyInfo Array
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		foreach($arr["department"] as $item) {
			$info->department[] = PartyInfo::ParseFromArray($item);
		}
		return $info;
	}
}
