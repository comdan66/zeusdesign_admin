<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Company extends OaModel {

  static $table_name = 'companies';

  static $has_one = array (
  );

  static $has_many = array (
    array ('customers', 'class_name' => 'Customer'),
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
      );
  }
  public function destroy () {
    if ($this->customers)
      foreach ($this->customers as $customer)
        if (!($customer->company_id = 0) && $customer->save ())
          return false;

    return $this->delete ();
  }
}