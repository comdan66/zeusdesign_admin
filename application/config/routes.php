<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Route::root ('main');

Route::get ('admin', 'admin/main@index');

Route::get ('/login', 'platform@login');
Route::get ('/logout', 'platform@logout');
Route::get ('/platform/index', 'platform@login');
Route::get ('/platform', 'platform@login');

Route::group ('api', function () {
  Route::resource (array ('schedules'), 'schedules');
  Route::resource (array ('schedule_tags'), 'schedule_tags');
});

Route::resourcePagination (array ('schedule_tags'), 'schedule_tags');
Route::resourcePagination (array ('banners'), 'banners');
Route::resourcePagination (array ('promos'), 'promos');
Route::resourcePagination (array ('users'), 'users');
Route::resourcePagination (array ('article_tags'), 'article_tags');

Route::get ('/users/(:id)/show', 'users@show($1)');
Route::get ('/users/(:id)/show/(:any)', 'users@show($1, $2)');
Route::get ('/users/(:id)/show/(:any)/(:num)', 'users@show($1, $2, $3)');

Route::get ('/calendar', 'index@calendar');
Route::get ('/my', 'index@index');

Route::get ('/my/(:any)', 'index@index($1)');
Route::get ('/my/(:any)/(:num)', 'index@index($1, $2)');

// echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
// var_dump (Route::getRoute ());
// exit ();
