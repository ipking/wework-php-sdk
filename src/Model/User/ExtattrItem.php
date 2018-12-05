<?php
namespace WeWork\Model\User;


class ExtattrItem
{
	public $name = null;
	public $value = null;
	
	public function __construct($name = null, $value = null)
	{
		$this->name = $name;
		$this->value = $value;
	}
}
