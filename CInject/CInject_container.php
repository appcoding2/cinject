<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CInject_container {

	private $search_paths = array();
	private $registry = array();
	private $last_bound_class;
	private $resolver;
	
	/**
     * Setter for the container resolver.
     *
     *
     * @param object $resolver the resolver to be used
     */	
	public function set_resolver($resolver)
	{
		$this->resolver = $resolver;
		$this->resolver->set_container($this);
	}
	
	/**
     * Adder for the container search paths.
     *
     *
     * @param mixed $paths the array of paths to search undefined objects, or a single path to add to search paths
     */		
	public function add_search_path($paths)
	{
		if (is_array($paths))
		{
			foreach ($paths as $path)
			{
				$this->search_paths[] = $path;
			}
		}
		else
		{
			$this->search_paths[] = $paths;
		}
	}
	
	/**
     * Getter for the search paths
     *
     *
     * @return array the search paths
     */		
	public function get_search_path()
	{
		return $this->search_paths;
	}
	
	/**
     * Clears the search paths
     *
     *
     */		
	public function clear_search_path()
	{
		$this->search_paths = array();
	}		
	
	/**
     * Begins registration process of new abstract type.
     *
     *
     * @param string $type the name of the interface to register (e.g. IFoo)
     * @return object the container instance
     */
	public function bind($type)
	{
		$this->registry[$type] = new CInject_registered_item($type);
		$this->last_bound_class = $type;
		
		return $this;
	}

	/**
     * Completes registration process by associating concrete type
	 * with the registered abstract type.
     *
     *
     * @param mixed $concrete the name of the concrete class (e.g. Foo), or the instance of the concrete type
     * @return object the container instance
     */	
	public function to($concrete)
	{
		$this->registry[$this->last_bound_class]->concrete = $concrete;
		return $this;
	}

	/**
     * Sets the latest registration to be a singleton.
     *
     *
     * @return object the container instance
     */		
	public function as_singleton()
	{
		$this->registry[$this->last_bound_class]->singleton = TRUE;
		return $this;
	}

	/**
     * Resolves the abstract type requested by returning the associated concrete type.
     *
     *
	 * @param string $type the abstract type to resolve
     * @return object the requested type
     */			
	public function resolve($type)
	{
		if ( ! class_exists($type) && ! interface_exists($type))
		{
			if ( ! $this->load_file_for_type($type))
			{
				throw new Exception("CInject cannot resolve requested type [$type]. The type is undefined.");
			}
		}
		
		$reflected_class = new ReflectionClass($type);
		
		if ( ! $this->is_bound($type))
		{
			return $this->resolve_unbound_type($type, $reflected_class);
		}
		else
		{
			return $this->resolve_bound_type($type, $reflected_class);
		}
	}
	
	/**
     * Resolves the bound type requested
     *
     *
	 * @param string $type the type to resolve
	 * @param ReflectionClass $reflected_class the reflection class of the requested type
     * @return object the requested type
     */
	private function resolve_bound_type($type, $reflected_class)
	{
		$object = NULL;
		
		// If the type already has an instance associated with, just return that instance
		if (is_object($this->registry[$type]->concrete))
		{
			$object = $this->registry[$type]->concrete;
		}
		else
		{
			if ($reflected_class->isInterface())
			{
				$object = $this->resolver->resolve($this->registry[$type]->concrete);
			}
			else
			{
				$object = $this->resolver->resolve($type);
			}
		}
		
		// If the type is registered as a singleton and is not already an instance, set concrete type to new instance
		if ($this->registry[$type]->singleton && ! is_object($this->registry[$type]->concrete))
		{
			$this->registry[$type]->concrete =& $object;
		}
		
		return $object;
	}
	
	/**
     * Resolves the unbound type requested
     *
     *
	 * @param string $type the type to resolve
	 * @param ReflectionClass $reflected_class the reflection class of the requested type
     * @return object the requested type
     */	
	private function resolve_unbound_type($type, $reflected_class)
	{
		if ($reflected_class->isInstantiable())
		{
			return $this->resolver->resolve($type);
		}
		else
		{
			throw new Exception("Requested type [$type] could not be resolved. Type is not instantiable. Make sure it is defined and bound in the container.");
		}	
	}
	
	/**
     * Attempts to load the class/interface file for the given type
     *
     *
	 * @param string $type the type that needs its file loaded
     * @return bool if the file was loaded successfully
     */		
	private function load_file_for_type($type)
	{
		// Iterate through search paths looking for file that matches the type
		foreach ($this->search_paths as $path)
		{
			$possible_path = APPPATH.$path.'/'.$type.EXT;
			if (file_exists($possible_path))
			{
				require($possible_path);
				
				// Make sure the located file actually contains the definition for the type
				if ( ! class_exists($type) && ! interface_exists($type))
				{
					return FALSE;
				}
				else
				{
					return TRUE;
				}
			}
		}
		
		return FALSE;
	}

	/**
     * Check if the abstract type is registered in the container.
     *
     *
	 * @param string $type the abstract type you're curious about
     * @return bool if the type is registered or not
     */		
	public function is_bound($type)
	{
		return array_key_exists($type, $this->registry);
	}

	/**
     * Check if the abstract type is registered in the container as a singleton.
     *
     *
	 * @param string $type the abstract type you're curious about	 
     * @return bool if the type is registered as a singleton or not
     */		
	public function is_singleton($type)
	{
		if ( ! array_key_exists($type, $this->registry))
		{
			return FALSE;
		}
		
		return $this->registry[$type]->singleton;
	}

	/**
     * Removes a registered type from the container.
     *
     *
	 * @param string $type the abstract type you don't love anymore	 
     */			
	public function forget($type)
	{
		unset($this->registry[$type]);
	}
	
	/**
     * Empties the container.
     *
     *
     */			
	public function clear()
	{
		$this->registry = array();
	}
	
}