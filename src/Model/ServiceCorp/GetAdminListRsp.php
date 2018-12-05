<?php
namespace WeWork\Model\ServiceCorp;


class GetAdminListRsp
{
	public $admin = null; // AppAdmin array
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		foreach($arr["admin"] as $item) {
			$info->admin[] = AppAdmin::ParseFromArray($item);
		}
		
		return $info;
	}
}

