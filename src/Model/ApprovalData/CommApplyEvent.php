<?php
namespace WeWork\Model\ApprovalData;


use WeWork\Util\Utils;

class CommApplyEvent {
	public $apply_data = null; // string TODO, 文档太烂，看不懂, 无法解析！！待相关人员更新
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->apply_data = Utils::arrayGet($arr, "apply_data");
		
		return $info;
	}
}