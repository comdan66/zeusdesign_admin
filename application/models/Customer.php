<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Customer extends OaModel {

  static $table_name = 'customers';

  static $has_one = array (
  );

  static $has_many = array (
    array ('invoices', 'class_name' => 'Invoice'),
    array ('emails', 'class_name' => 'CustomerEmail'),
  );

  static $belongs_to = array (
    array ('company', 'class_name' => 'CustomerCompany'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'name' => $this->name,
        'extension' => $this->extension,
        'cellphone' => $this->cellphone,
        'experience' => $this->experience,
        'memo' => $this->memo,
        'emails' => array_map (function ($email) {
          return $email->to_array ();
        }, $this->emails),
      );
  }
  public function destroy () {
    if ($this->invoices)
      foreach ($this->invoices as $invoice)
        if (!($invoice->customer_id = 0) && !$invoice->save ())
          return false;
    
    if ($this->emails)
      foreach ($this->emails as $email)
        if (!$email->destroy ())
          return false;

    return $this->delete ();
  }
}