<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class CompanyPmItem extends OaModel {

  static $table_name = 'company_pm_items';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const TYPE_1 = 1;
  const TYPE_2 = 2;

  static $typeNames = array (
    self::TYPE_1 => 'E-Mail',
    self::TYPE_2 => '手機',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function backup ($has = false) {
    $var = array (
      'id'            => $this->id,
      'company_pm_id' => $this->company_pm_id,
      'type'          => $this->type,
      'content'       => $this->content,
      'updated_at'    => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'    => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
        '_' => $var
      ) : $var;
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    return $this->delete ();
  }
}