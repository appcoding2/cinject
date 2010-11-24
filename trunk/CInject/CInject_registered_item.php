<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CInject_registered_item {

	public $type;
	public $concrete;
	public $singleton;
	

	public function __construct($type)
	{
		$this->type = $type;
		$this->singleton = FALSE;
	}

}