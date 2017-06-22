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
    array ('items',  'class_name' => 'IncomeItem'),
    array ('zbs',  'class_name' => 'Zb')
  );

  static $belongs_to = array (
  );
  
  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '未入帳',
    self::STATUS_2 => '已入帳',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public function has_tax () {
    return $this->invoice_date ? true : false;
  }
  public function tax_rate () {
    return $this->has_tax () ? 1.05 : 1;
  }
  public function zeus_rate () {
    return $this->has_tax () ? 0.2 : 0.1;
  }

  public function tax_money () {
    return round ($this->money * $this->tax_rate ());
  }

  public function zeus_money () {
    return $this->tax_money () - array_sum (array_map (function ($zb) {
          return $zb->pay ();
        }, $this->zbs));
  }
  public function use_money () {
    return $this->tax_money () - $this->zeus_money ();
  }
  public function progress () {
    $a = round (($this->zbs ? count (array_filter ($this->zbs, function ($zb) {
              return $zb->status == Zb::STATUS_2;
            })) / count ($this->zbs) : 1) * 100);

    return $a < 100 ? $a > 0 ? $a : 0 : 100;
  }

  public function destroy () {
    if (!isset ($this->id)) return false;
    
    if ($this->items)
      foreach ($this->items as $item)
        if (!(!($item->income_id = 0) && $item->save ()))
          return false;

    if ($this->zbs)
      foreach ($this->zbs as $zb)
        if (!$zb->destroy ())
          return false;

    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = array (
      'id'           => $this->id,
      'invoice_date' => $this->invoice_date ? $this->invoice_date->format ('Y-m-d') : '',
      'status'       => $this->status,
      'money'        => $this->money,
      'memo'         => $this->memo,
      'updated_at'   => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'   => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
        '_' => $var,
        'items' => $this->subBackup ('IncomeItem', $has),
        'zbs' => $this->subBackup ('Zb', $has),
      ) : $var;
  }
}