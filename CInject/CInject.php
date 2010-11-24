<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require('CInject_container.php');
require('CInject_resolver.php');
require('CInject_registered_item.php');

class CInject {

	private static $container;
	
	/**
     * Returns instance of the Container
     *
     *
     * @return object the container
     */			
	public static function container()
	{
		if ( ! isset(self::$container))
		{
			self::$container = new CInject_container;
			self::$container->set_resolver(new CInject_resolver);
		}
		
		return self::$container;
	}
	
	/**
     * Returns instance of requested type
     *
     *
	 * @param string $type the name of the type to make
     * @return object the requested type
     */			
	public static function make($type)
	{
		return self::container()->resolve($type);
	}
	
}