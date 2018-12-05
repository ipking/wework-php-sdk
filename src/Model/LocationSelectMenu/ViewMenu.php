<?php
namespace WeWork\Model\Menu;

use WeWork\Util\Utils;

class ViewMenu {
	public $type = "view";
	public $name = null; // string
	public $url = null; // string
	
	public function __construct($name=null, $url= null)
	{
		$this->name = $name;
		$this->url = $url;
	}
	
	public static function Array2Menu($arr)
	{
		$menu = new self();
		
		$menu->type = "view";
		$menu->name = Utils::arrayGet($arr, "name");
		$menu->url = Utils::arrayGet($arr, "url");
		
		return $menu;
	}
}
