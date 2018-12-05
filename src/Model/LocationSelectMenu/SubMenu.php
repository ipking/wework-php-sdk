<?php
namespace WeWork\Model\Menu;

use WeWork\Util\Utils;

class SubMenu {
	public $name = null; // string
	public $sub_button = null; // xxxMenu array
	
	public function __construct($name=null, $xxmenuArray=null)
	{
		$this->name = $name;
		$this->sub_button = $xxmenuArray;
	}
	
	public static function Array2Menu($arr)
	{
		$menu = new self();
		
		$menu->name = Utils::arrayGet($arr, "name");
		foreach($arr["sub_button"] as $item) {
			
			$subButton = null;
			if ( ! array_key_exists("type", $item)) {
				$subButton = self::Array2Menu($item);
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
			$menu->sub_button[] = $subButton;
		}
		
		return $menu;
	}
}
