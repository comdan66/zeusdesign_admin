<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Contact extends OaModel {

  static $table_name = 'contacts';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '未讀',
    self::STATUS_2 => '已讀',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;
    
    return $this->delete ();
  }
  public function mini_message ($length = 100) {
    if (!isset ($this->message)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->message), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->message);
  }
  public function backup ($has = false) {
    $var = array (
      'id'         => $this->id,
      'name'       => $this->name,
      'email'      => $this->email,
      'message'    => $this->message,
      'ip'         => $this->ip,
      'status'     => $this->status,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('_' => $var) : $var;
  }
}