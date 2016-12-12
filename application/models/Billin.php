<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Billin extends OaModel {

  static $table_name = 'billins';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );

  const NO_FINISHED = 0;
  const IS_FINISHED = 1;

  static $finishNames = array(
    self::NO_FINISHED => '未入帳',
    self::IS_FINISHED => '已入帳',
  );
  const NO_PAY = 0;
  const IS_PAY = 1;

  static $payNames = array(
    self::NO_PAY => '未支付',
    self::IS_PAY => '已支付',
  );
  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public function destroy () {
    if (!isset ($this->id)) return false;
    return $this->delete ();
  }
  public function mini_name ($length = 50) {
    if (!isset ($this->name)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->name), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'          => $this->id,
      'user_id'     => $this->user_id,
      'name'        => $this->name,
      'money'       => $this->money,
      'rate_name'   => $this->rate_name,
      'rate'        => $this->rate,
      'zeus_money'  => $this->zeus_money,
      'memo'        => $this->memo,
      'is_finished' => $this->is_finished,
      'is_pay'      => $this->is_pay,
      'date_at'     => $this->date_at ? $this->date_at->format ('Y-m-d') : '',
      'updated_at'  => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'user' => $this->user->to_array (),
        'name' => $this->name,
        'money' => $this->money,
        'rate_name' => $this->rate_name,
        'rate' => $this->rate,
        'zeus_money' => $this->zeus_money,
        'memo' => $this->memo,
        'is_finished' => $this->is_finished,
        'is_pay' => $this->is_pay,
        'date_at' => $this->date_at->format ('Y-m-d'),
      );
  }
}