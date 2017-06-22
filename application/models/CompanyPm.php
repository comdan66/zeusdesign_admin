<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class CompanyPm extends OaModel {

  static $table_name = 'company_pms';

  static $has_one = array (
  );

  static $has_many = array (
    array ('items', 'class_name' => 'CompanyPmItem'),
    array ('emails', 'class_name' => 'CompanyPmItem', 'conditions' => array ('type = ?', CompanyPmItem::TYPE_1)),
    array ('phones', 'class_name' => 'CompanyPmItem', 'conditions' => array ('type = ?', CompanyPmItem::TYPE_2)),
  );

  static $belongs_to = array (
    array ('company', 'class_name' => 'Company'),
  );

  private $typeItems = array ();

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function typeItems ($type) {
    if (isset ($typeItems[$type])) return $typeItems[$type];
    return $typeItems[$type] = array_values (array_filter ($this->items, function ($t) use ($type) {
      return $t->type == $type;
    }));
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    if ($this->items)
      foreach ($this->items as $item)
        if (!$item->destroy ())
          return false;

    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = array (
      'id'          => $this->id,
      'company_id'  => $this->company_id,
      'name'        => $this->name,
      'extension'   => $this->extension,
      'experience'  => $this->experience,
      'memo'        => $this->memo,
      'updated_at'  => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
        '_' => $var,
        'items' => $this->subBackup ('CompanyPmItem', $has),
      ) : $var;
  }
}