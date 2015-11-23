<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "welcome";

$route['contact'] = 'pages/contact';
$route['mail'] = 'pages/sendEmail';
$route['terms'] = 'pages/terms';
$route['privacy'] = 'pages/privacy';
$route['unsubscribe'] = 'pages/unsubscribe';
$route['forgot_password'] = 'pages/forgotPassword';
$route['trending'] = 'pages/trending';
$route['popular'] = 'pages/trending';
$route['trend'] = 'pages/trending';
$route['trends'] = 'pages/trending';
$route['what_is_readbo'] = 'pages/whatIsReadbo';
$route['shut_down'] = 'pages/shutDown';

$route['wrong_password'] = 'welcome/login/error/incorrect';
$route['l/(:any)'] = 'link/index/id/$1';
$route['shares/(:any)'] = 'shares/index/id/$1';
$route['reset_password/(:any)'] = 'pages/resetPassword/code/$1';
$route['signup'] = 'welcome/index/signup/signup';
$route['profile/(:any)'] = 'welcome/index/profile/$1';
$route['error_importing'] = 'welcome/index/error/importing';

$route['export'] = 'import_export/export';


/* End of file routes.php */
/* Location: ./application/config/routes.php */