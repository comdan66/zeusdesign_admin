<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Siteconf extends OaModel {

  static $table_name = 'siteconfs';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public static function toArray () {
    $ss = Siteconf::find ('all', array ('select' => '`key`, val'));
    return array_combine (column_array ($ss, 'key'), column_array ($ss, 'val'));
  }
  public static function getVal ($key) {
    return ($s = Siteconf::find ('one', array ('conditions' => array ('`key` = ?', $key)))) ? $s->val : '';
  }
  public function destroy () {
    return $this->delete ();
  }
}