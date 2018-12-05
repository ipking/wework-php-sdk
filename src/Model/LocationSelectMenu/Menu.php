<?php
namespace WeWork\Model\Menu;

class Menu {
	public $button = null; // xxxMenu array, 即各种子菜单array
	
	public function __construct($xxmenuArray=null)
	{
		$this->button = $xxmenuArray;
	}
	
	public static function CheckMenuCreateArgs($agentid,$menu) {
	
	}
	
	public static function Array2Menu($arr)
	{
		$menu = new self();
		
		foreach($arr["button"] as $item) {
			$subButton = null;
			if ( ! array_key_exists("type", $item)) {
				$subButton = SubMenu::Array2Menu($item);
			} else {
				$type = $item["type"];
				if ($type == "click") $subButton = ClickMenu::Array2Menu($item);
				if ($type == "view") $subButton = ViewMenu::Array2Menu($item);
				if ($type == "scancode_push") $subButton = ScanCodePushMenu::Array2Menu($item);
				if ($type == "scancode_waitmsg") $subButton = ScanCodeWaitMsgMenu::Array2Menu($item);
				if ($type == "pic_sysphoto") $subButton = PicSysPhotoMenu::Array2Menu($item);
				if ($type == "pic_photo_or_album") $subButton = PicPhotoOrAlbumMenu::Array2Menu($item);
				if ($type == "pic_weixin") $subButton = PicWeixinMenu::Array2Menu($item);
				if ($type == "location_select") $subButton = LocationSelectMenu::Array2Menu($item);
			}
			$menu->button[] = $subButton;
		}
		
		return $menu;
	}
	
} // class