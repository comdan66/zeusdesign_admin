<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Ftp extends OaModel {

  static $table_name = 'ftps';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'name' => $this->name,
        'url' => $this->url,
        'host' => $this->host,
        'account' => $this->account,
        'password' => $this->password,
        'admin_url' => $this->admin_url,
        'memo' => $this->memo,
      );
  }
  public function destroy () {
    return $this->delete ();
  }
}