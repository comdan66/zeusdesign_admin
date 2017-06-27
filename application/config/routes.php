<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

Route::root ('main');

Route::get ('/login', 'platform@login');
Route::get ('/logout', 'platform@logout');

Route::get ('admin', 'admin/main@index');

Route::group ('admin', function () {
  Route::resourcePagination (array ('banners'), 'banners');
  Route::resourcePagination (array ('promos'), 'promos');
  Route::resourcePagination (array ('contacts'), 'contacts');
  Route::resourcePagination (array ('article_tags'), 'article_tags');
  Route::resourcePagination (array ('articles'), 'articles');
  Route::resourcePagination (array ('work_tags'), 'work_tags');
  Route::resourcePagination (array ('tag', 'work_tags'), 'tag_work_tags');
  Route::resourcePagination (array ('works'), 'works');
  Route::resourcePagination (array ('companies'), 'companies');
  Route::resourcePagination (array ('company', 'pms'), 'company_pms');
  
  Route::resourcePagination (array ('income_items'), 'income_items');
  Route::resourcePagination (array ('incomes'), 'incomes');
  Route::resourcePagination (array ('my_zbs'), 'my_zbs');
  Route::resourcePagination (array ('my_calendar'), 'my_calendar');
  Route::resourcePagination (array ('my_schedule_tags'), 'my_schedule_tags');
  
  // Route::post ('/income_items/ajax/', 'income_items@ajax(0)');
  // Route::post ('/income_items/ajax/(:id)', 'income_items@ajax($1)');
});

Route::group ('api', function () {
  Route::get ('/pv/(:any)/(:id)', 'pv@index($1, $2)');
  Route::post ('/contacts', 'contacts@create');
});

// echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
// print_r (Route::getRoute ());
// exit ();