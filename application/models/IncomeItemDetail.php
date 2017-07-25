<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class IncomeItemDetail extends OaModel {

  static $table_name = 'income_item_details';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('user',  'class_name' => 'User'),
    array ('item',  'class_name' => 'IncomeItem'),
    array ('zb',    'class_name' => 'Zb')
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = $this->getBackup ();
    return $has ? array (
        '_' => $var
      ) : $var;
  }
}