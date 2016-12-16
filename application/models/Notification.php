<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Notification extends OaModel {

  static $table_name = 'notifications';

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
  public function mini_description ($length = 100) {
    if (!isset ($this->description)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->description), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->description);
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'          => $this->id,
      'user_id'     => $this->user_id,
      'description' => $this->description,
      'link'        => $this->link,
      'is_read'     => $this->is_read,
      'updated_at'  => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public static function send ($users = array (), $description = '', $link = '') {
    if (!$user_ids = array_values (array_filter (array_map (function ($user) { if (is_object ($user) && ($user instanceof User) && isset ($user->id)) return $user->id; if (is_array ($user) && isset ($user['id'])) return $user['id']; return ''; }, $users))))
      return false;

    if (!(is_string ($description) && ($description = trim ($description))))
      return false;

    $link = is_string ($link) && ($link = trim ($link)) ? $link : '';

    $posts = array (
        'description' => $description,
        'link' => $link,
        'is_read' => Notification::READ_NO,
      );

    foreach ($user_ids as $user_id)
      Notification::transaction (function () use (&$obj, $posts, $user_id) { return verifyCreateOrm ($obj = Notification::create (array_intersect_key (array_merge ($posts, array ('user_id' => $user_id)), Notification::table ()->columns))); });

    return true;
  }
}