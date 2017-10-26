<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

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
    self::STATUS_1 => '讀取資料',
    self::STATUS_2 => '寫入資料',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
   
    OrmFileUploader::bind ('json', 'UserLogJsonFileUploader');
  }
  public static function logRead ($icon, $title, $content = '', $backup = array ()) {
    return true;
    return UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $icon,
      'title' => $title,
      'content' => $content,
      'status' => UserLog::STATUS_1,
      'json' => '',
      'backup'  => json_encode ($backup)));
  }
  public static function logWrite ($icon, $title, $content = '', $backup = array ()) {
    return UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $icon,
      'title' => $title,
      'content' => $content,
      'status' => UserLog::STATUS_2,
      'json' => '',
      'backup'  => json_encode ($backup)));
  }
  public function backup ($has = false) {
    $var = $this->getBackup ();
    return $has ? array (
        '_' => $var
      ) : $var;
  }
}