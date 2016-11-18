<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class CustomerCompany extends OaModel {

  static $table_name = 'customer_companies';

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
        'address' => $this->address,
        'telephone' => $this->telephone,
        'memo' => $this->memo,
        'customers' => array_map (function ($customer) {
          return $customer->to_array ();
        }, $this->customers),
      );
  }
  public function destroy () {
    if ($this->customers)
      foreach ($this->customers as $customer)
        if (!($customer->customer_company_id = 0) && $customer->save ())
          return false;

    return $this->delete ();
  }
}