<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class UserLog extends OaModel {

  static $table_name = 'user_logs';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const STATUS_0 = 0;
  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_0 => '未知',
    self::STATUS_1 => '讀',
    self::STATUS_2 => '寫',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public static function logRead ($icon, $title, $desc = '', $backup = array ()) {
    return UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $icon,
      'title' => $title,
      'desc' => $desc,
      'status' => UserLog::STATUS_1,
      'backup'  => json_encode ($backup)));
  }
  public static function logWrite ($icon, $title, $desc = '', $backup = array ()) {
    return UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $icon,
      'title' => $title,
      'desc' => $desc,
      'status' => UserLog::STATUS_2,
      'backup'  => json_encode ($backup)));
  }
  public function backup ($has = false) {
    $var = array (
      'id'         => $this->id,
      'user_id'    => $this->user_id,
      'title'      => $this->title,
      'desc'       => $this->desc,
      'backup'     => $this->backup,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('_' => $var) : $var;
  }
}