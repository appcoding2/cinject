<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CInject_resolver {

	private $container;

	/**
     * Sets the container instance used by the resolver
     *
     *
     * @param object $container the container to use
     */		
	public function set_container(&$container)
	{
		$this->container =& $container;
	}

	/**
     * Resolves the requested type
     *
     *
     * @param string $type the type to resolve
	 * @return object the requested type
     */	
	public function resolve($type)
	{
		$reflector = new ReflectionClass($type);
		
		$constructor = $reflector->getConstructor();
		$dependencies = array();
		
		if ($constructor != NULL)
		{
			$dependencies = $constructor->getParameters();
		}

		// If the constructor has dependencies, resolve them and return resolved type
		if (count($dependencies) > 0)
		{
			return $reflector->newInstanceArgs($this->resolve_dependencies($dependencies));
		}

		// The requested type has no dependencies, simply new up the type
		return new $type;
	}
	
	/**
     * Resolves an array of dependencies
     *
     *
     * @param array $dependencies the dependencies to resolve
	 * @return array the resolved dependencies
     */		
	private function resolve_dependencies($dependencies)
	{
		$resolved_dependencies = array();
		
		foreach ($dependencies as $dependency)
		{
			// To resolve all dependencies, make a recursive call into the container to resolve each dependency
			$resolved_dependencies[] = $this->container->resolve($dependency->getClass()->getName());
		}
		
		return $resolved_dependencies;
	}

}