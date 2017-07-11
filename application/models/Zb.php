<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Zb extends OaModel {

  static $table_name = 'zbs';

  static $has_one = array (
  );

  static $has_many = array (
    array ('details',  'class_name' => 'IncomeItemDetail'),
  );

  static $belongs_to = array (
    array ('user',  'class_name' => 'User'),
    array ('income',  'class_name' => 'Income'),
  );

  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '未給付',
    self::STATUS_2 => '已給付',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public function percentage ($c = 4) {
    return floor ($this->percentage * ($t = pow (10, $c))) / $t;
  }
  public function pay ($income = null) {
    if ($income)
      $money = $income->tax_money () - round ($income->tax_money () * $income->zeus_rate ());
    else
      $money = $this->income->tax_money () - round ($this->income->tax_money () * $this->income->zeus_rate ());

    return floor ($this->percentage () * $money);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    if ($this->details)
      foreach ($this->details as $detail)
        if (!(!($detail->zb_id = 0) && $detail->save ()))
          return false;

    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = array (
      'id'           => $this->id,
      'user_id'      => $this->user_id,
      'income_id'    => $this->income_id,
      'percentage'   => $this->percentage,
      'money'        => $this->money,
      'status'       => $this->status,
      'updated_at'   => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'   => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
        '_' => $var,
      ) : $var;
  }
}