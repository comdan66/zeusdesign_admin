<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Company extends OaModel {

  static $table_name = 'companies';

  static $has_one = array (
  );

  static $has_many = array (
    array ('pms', 'class_name' => 'CompanyPm', 'order' => 'id DESC'),
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function mini_address ($length = 100) {
    if (!isset ($this->address)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->address), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->address);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    if ($this->pms)
      foreach ($this->pms as $pm)
        if (!$pm->destroy ())
          return false;

    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = array (
      'id'          => $this->id,
      'name'        => $this->name,
      'tax_no'      => $this->tax_no,
      'address'     => $this->address,
      'phone'       => $this->phone,
      'memo'        => $this->memo,
      'updated_at'  => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
        '_' => $var,
        'pms' => $this->subBackup ('CompanyPm', $has),
      ) : $var;
  }
}