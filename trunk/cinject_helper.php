<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Require CInject
 *
 */

require('CInject/CInject.php');

/*
 * Set your search paths.
 *
 * When asking CInject to resolve a type whose associated file has not beed loaded, CInject will
 * look in the defined search paths to find and load the file automatically.
 *
 */

CInject::container()->add_search_path('models');

/*
 * Register your dependencies.
 */
 
// Example: CInject::container()->bind('IUserRepository')->to('UserRepository');