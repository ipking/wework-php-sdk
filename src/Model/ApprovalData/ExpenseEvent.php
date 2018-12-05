<?php
namespace WeWork\Model\ApprovalData;

use WeWork\Util\Utils;


class ExpenseEvent {
	public $expense_type = null; // int
	public $reason = null; // string
	public $item = null; // ExpenseItem array
	
	static public function ParseFromArray($arr)
	{
		$info = new self();
		
		$info->expense_type = Utils::arrayGet($arr, "expense_type");
		$info->reason = Utils::arrayGet($arr, "reason");
		foreach($arr["item"] as $item) {
			$info->item[] = ExpenseItem::ParseFromArray($item);
		}
		
		return $info;
	}
}