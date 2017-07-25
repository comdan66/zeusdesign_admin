<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Notice extends OaModel {

  static $table_name = 'notices';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
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
  public static function send ($user_ids, $content, $uri = '') {
    if (!is_array ($user_ids)) $user_ids = array ($user_ids);
    if (!$user_ids = array_map (function ($u) { return $u && is_object ($u) && ($u instanceof User) && isset ($u->id) ? $u->id : is_numeric ($u); }, $user_ids)) return false;

    foreach (array_unique ($user_ids) as $user_id)
      if (!verifyCreateOrm (Notice::create (array ('user_id' => $user_id, 'content' => $content, 'uri' => $uri, 'status' => Notice::STATUS_1))))
        return false;

    return true;
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = $this->getBackup ();
    return $has ? array (
        '_' => $var,
      ) : $var;
  }
}