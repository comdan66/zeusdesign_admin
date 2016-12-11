<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

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
  public function columns_val ($has = false) {
    $var = array (
      'id'          => isset ($this->id) ? $this->id : '',
      'name'        => isset ($this->name) ? $this->name : '',
      'business_no' => isset ($this->business_no) ? $this->business_no : '',
      'telephone'   => isset ($this->telephone) ? $this->telephone : '',
      'address'     => isset ($this->address) ? $this->address : '',
      'memo'        => isset ($this->memo) ? $this->memo : '',
      'updated_at'  => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var, 'customers' => array_map (function ($customer) {
      return $customer->columns_val ();
    }, Customer::find ('all', array ('conditions' => array ('customer_company_id = ?', $this->id))))) : $var;
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
        if (!(!($customer->customer_company_id = 0) && $customer->save ()))
          return false;

    return $this->delete ();
  }
}