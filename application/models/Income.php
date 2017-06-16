<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Income extends OaModel {

  static $table_name = 'incomes';

  static $has_one = array (
  );

  static $has_many = array (
    array ('items',  'class_name' => 'IncomeItem')
  );

  static $belongs_to = array (
  );
  
  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '未入帳',
    self::STATUS_2 => '已入帳',
  );

  const TYPE_1 = 1;
  const TYPE_2 = 2;

  static $typeNames = array (
    self::TYPE_1 => '有發票',
    self::TYPE_2 => '沒發票',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;
    
    if ($this->items)
      foreach ($this->items as $item)
        if (!(!($item->income_id = 0) && $item->save ()))
          return false;

    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = array (
      'id'           => $this->id,
      'invoice_date' => $this->invoice_date ? $this->invoice_date->format ('Y-m-d') : '',
      'status'       => $this->status,
      'type'         => $this->type,
      'memo'         => $this->memo,
      'updated_at'   => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'   => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
        '_' => $var,
        'items' => $this->subBackup ('IncomeItem', $has),
      ) : $var;
  }
}