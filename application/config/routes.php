<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

Route::root ('main');

// $route['admin'] = "admin/main";
Route::get ('admin', 'admin/main@index');

Route::get ('/login', 'platform@login');
Route::get ('/logout', 'platform@logout');
Route::get ('/platform/index', 'platform@login');
Route::get ('/platform', 'platform@login');












Route::group ('admin', function () {
  Route::get ('/users/(:id)/show', 'users@show($1)');
  Route::get ('/users/(:id)/show/(:any)', 'users@show($1, $2)');
  Route::get ('/users/(:id)/show/(:any)/(:num)', 'users@show($1, $2, $3)');

  Route::get ('/all_calendar', 'main@all_calendar');
  Route::get ('/calendar', 'main@calendar');
  Route::get ('/my', 'main@index');
  Route::get ('/my/(:any)', 'main@index($1)');
  Route::get ('/my/(:any)/(:num)', 'main@index($1, $2)');
  Route::resourcePagination (array ('my_salaries'), 'my_salaries');

  Route::resourcePagination (array ('schedule_tags'), 'schedule_tags');
  Route::resourcePagination (array ('users'), 'users');
  Route::resourcePagination (array ('contacts'), 'contacts');
  Route::resourcePagination (array ('deploys'), 'deploys');

  Route::resourcePagination (array ('billins'), 'billins');
  Route::resourcePagination (array ('billous'), 'billous');
  Route::resourcePagination (array ('bills'), 'bills');

  Route::resourcePagination (array ('banners'), 'banners');
  Route::resourcePagination (array ('promos'), 'promos');

  Route::resourcePagination (array ('company', 'customers'), 'company_customers');
  Route::resourcePagination (array ('customer_companies'), 'customer_companies');
  Route::resourcePagination (array ('customers'), 'customers');
  
  Route::resourcePagination (array ('article_tags'), 'article_tags');
  Route::resourcePagination (array ('articles'), 'articles');

  Route::resourcePagination (array ('work_tags'), 'work_tags');
  Route::resourcePagination (array ('tag', 'work_tags'), 'tag_work_tags');
  Route::resourcePagination (array ('works'), 'works');
  
  Route::resourcePagination (array ('invoice_tags'), 'invoice_tags');
  Route::resourcePagination (array ('invoices'), 'invoices');
  
  Route::resourcePagination (array ('ftps'), 'ftps');
  Route::resourcePagination (array ('salaries'), 'salaries');
});

Route::group ('api', function () {
  Route::get ('/pv/(:any)/(:id)', 'pv@index($1, $2)');
  Route::post ('/contacts', 'contacts@create');
  Route::post ('/users/token', 'users@token');
  Route::post ('/users/notification', 'users@notification');

  Route::resource (array ('schedules'), 'schedules');
  Route::resource (array ('schedule_tags'), 'schedule_tags');
  
  Route::resource (array ('spends'), 'spends');
  Route::resource (array ('spend_items'), 'spend_items');
});
// echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
// var_dump (Route::getRoute ());
// exit ();

// $route['main/index/(:num)/(:num)'] = "main/aaa/$1/$2";
// Route::get ('main/index/(:num)/(:num)', 'main@aaa($1, $2)');
// Route::post ('main/index/(:num)/(:num)', 'main@aaa($1, $2)');
// Route::put ('main/index/(:num)/(:num)', 'main@aaa($1, $2)');
// Route::delete ('main/index/(:num)/(:num)', 'main@aaa($1, $2)');
// Route::controller ('main', 'main');
  // whit get、post、put、delete prefix

/* End of file routes.php */
/* Location: ./application/config/routes.php */