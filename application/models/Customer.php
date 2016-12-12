<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
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
  public function columns_val ($has = false) {
    $var = array (
      'id'                  => $this->id,
      'customer_company_id' => $this->customer_company_id,
      'name'                => $this->name,
      'extension'           => $this->extension,
      'cellphone'           => $this->cellphone,
      'experience'          => $this->experience,
      'memo'                => $this->memo,
      'updated_at'          => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'          => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var, 'emails' => array_map (function ($email) {
      return $email->columns_val ();
    }, CustomerEmail::find ('all', array ('conditions' => array ('customer_id = ?', $this->id)))), 'invoices' => array_map (function ($invoice) {
      return $invoice->columns_val ();
    }, Invoice::find ('all', array ('conditions' => array ('customer_id = ?', $this->id))))) : $var;
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