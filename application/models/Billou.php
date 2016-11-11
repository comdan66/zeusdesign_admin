<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Billou extends OaModel {

  static $table_name = 'billous';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );

  const INVOICE_NO  = 0;
  const INVOICE_YES = 1;

  static $invoiceNames = array(
    self::INVOICE_NO  => '沒有發票',
    self::INVOICE_YES => '有開發票',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  
  public function destroy () {
    if (!isset ($this->id)) return false;
    return $this->delete ();
  }
}