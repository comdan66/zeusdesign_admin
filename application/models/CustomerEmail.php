<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
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
  public function columns_val ($has = false) {
    $var = array (
      'id'          => $this->id,
      'customer_id' => $this->customer_id,
      'email'       => $this->email,
      'updated_at'  => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
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