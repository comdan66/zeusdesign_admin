<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
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
        'date_at' => $this->date_at->format ('Y-m-d'),
      );
  }
}