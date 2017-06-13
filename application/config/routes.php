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
});

Route::group ('api', function () {
  Route::get ('/pv/(:any)/(:id)', 'pv@index($1, $2)');
  Route::post ('/contacts', 'contacts@create');
});