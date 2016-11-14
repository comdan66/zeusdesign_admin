<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Contact extends OaModel {

  static $table_name = 'contacts';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const READ_NO  = 0;
  const READ_YES = 1;

  static $readNames = array(
    self::READ_NO  => '未讀',
    self::READ_YES => '已讀',
  );
  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function destroy () {
    return $this->delete ();
  }
  public function to_array ($opt = array ()) {
    return array (
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'message' => $this->message,
        'ip' => $this->ip,
        'is_readed' => $this->is_readed,
      );
  }
  public function mini_message ($length = 100) {
    if (!isset ($this->message)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->message), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->message);
  }
}