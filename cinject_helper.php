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
CInject::container()->add_search_path('models/logic');
CInject::container()->add_search_path('models/repositories');

/*
 * Register your dependencies.
 */
 
CInject::container()->bind('IHomeLogic')->to('HomeLogic')->as_singleton();
CInject::container()->bind('IUserRepository')->to('UserRepository')->as_singleton();