<?php
namespace WeWork\Model\Menu;

use WeWork\Util\Utils;

class ClickMenu {
	public $type = "click";
	public $name = null; // string
	public $key = null; // string
	
	public function __construct($name=null, $key=null)
	{
		$this->name = $name;
		$this->key = $key;
	}
	
	public static function Array2Menu($arr)
	{
		$menu = new self();
		
		$menu->type = Utils::arrayGet($arr, "type");
		$menu->name = Utils::arrayGet($arr, "name");
		$menu->key = Utils::arrayGet($arr, "key");
		
		return $menu;
	}
}
