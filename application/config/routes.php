<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
//$route['news'] = 'news';

$route['api/v2/(.*)'] = 'api/v2/index/$1';

$route['admin/api/(.*)'] = 'api/admin_ajax/$1';
$route['admin/api'] = 'api/admin_ajax';

$route['api/(.*)'] = 'api/ajax/$1';
$route['api'] = 'api/ajax';


//$route['admin/([a-zA-Z]+)/view/(:num)'] = 'admin/posts/view/$1/$2';
$route['admin/([a-zA-Z]+)/draft'] = 'admin/posts/viewlist/$1/draft';
$route['admin/([a-zA-Z]+)/primary'] = 'admin/posts/viewlist/$1/primary';
$route['admin/([a-zA-Z]+)/recyclebin'] = 'admin/posts/viewlist/$1/recyclebin';
$route['admin/([a-zA-Z]+)/test'] = 'admin/posts/viewlist/$1/test';//Alireza Balvardi

$route['admin/([a-zA-Z]+)/edit/(:num)'] = 'admin/posts/addedit/$1/edit/$2';
$route['admin/([a-zA-Z]+)/add'] = 'admin/posts/addedit/$1';

$route['admin/([a-zA-Z]+)/category/edit/(:num)'] = 'admin/posts/category_page/$1/edit/$2';
$route['admin/([a-zA-Z]+)/category'] = 'admin/posts/category_page/$1';


$route['category/(:num)/(.*)/page/(:num)'] = 'category/index/$1/$2';
$route['category/(:num)/(.*)'] = 'category/index/$1/1';

$route['tags/(:any)'] = 'blog/tags/$1/1';
$route['tags/(:any)/page/(:num)'] = 'blog/tags/$1/$2';



$route['proficient/page/(:num)'] = 'proficient/index/$1';
$route['proficient/page/(:num)/(.*)'] = 'proficient/index/$1/$2';

$route['tools/page/(:num)']      = 'tools/index/$1';
$route['tools/page/(:num)/(.*)'] = 'tools/index/$1/$2';

$route['user/(:any)'] = 'user/index/$1';

//$route['page/(:num)'] = 'home/index/$1';

$route['search/page/(:num)'] = 'search/index/$1';
$route['(:num)/(:any)'] = 'home/view/$1';

$route['admin/([a-zA-Z]+)/levels'] = 'admin/users/levels/$1';//Alireza Balvardi Add
$route['admin/users/adduser'] = 'admin/users/adduser';//Alireza Balvardi Add

//$route['admin/(:any)'] = 'admin/home/$1';
//$route['admin'] = 'admin';
//$route['admin/(:any)/(:any)'] = 'admin/home/$1/$2';

global $POST_TYPES;

foreach ($POST_TYPES as $k=>$type)
{
    if( isset($type['seo_url']) )
    {
        $route[$type['seo_url']] = "blog/index/{$k}";
        $route[$type['seo_url'] ."/page/(:num)"] = "blog/index/{$k}/$1";
    }
}

$route['default_controller'] = 'home';
$route['404_override'] = 'notfound';
$route['translate_uri_dashes'] = TRUE;
