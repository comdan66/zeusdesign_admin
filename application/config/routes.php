<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Route::root ('main');

Route::get ('admin', 'admin/main@index');

Route::get ('/login', 'platform@login');
Route::get ('/logout', 'platform@logout');
Route::get ('/platform/index', 'platform@login');
Route::get ('/platform', 'platform@login');

Route::group ('api', function () {
  Route::resource (array ('schedules'), 'schedules');
});

Route::resourcePagination (array ('schedule_tags'), 'schedule_tags');
Route::resourcePagination (array ('banners'), 'banners');

// echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
// var_dump (Route::getRoute ());
// exit ();
