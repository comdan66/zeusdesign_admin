<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class CustomerEmail extends OaModel {

  static $table_name = 'customer_emails';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('customer', 'class_name' => 'Customer'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'email' => $this->email,
      );
  }
  public function destroy () {
    return $this->delete ();
  }
}