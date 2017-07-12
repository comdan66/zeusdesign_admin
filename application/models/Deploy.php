<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Deploy extends OaModel {

  static $table_name = 'deploys';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );

  const TYPE_1 = 1;
  const TYPE_2 = 2;

  static $typeNames = array (
    self::TYPE_1 => '編譯',
    self::TYPE_2 => '編譯 & 上傳',
  );
  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '失敗',
    self::STATUS_2 => '成功',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function res ($key = '') {
    if (!(isset ($this->res) && $this->res)) return '';
    if ($t = json_decode ($this->res, true)) return $key && isset ($t[$key]) ? $t[$key] : '';
    return '';
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    return $this->delete ();
  }

  public function backup ($has = false) {
    $var = array (
      'id'         => $this->id,
      'user_id'    => $this->user_id,
      'type'       => $this->type,
      'res'        => $this->res,
      'status'     => $this->status,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );

    return $has ? array (
        '_' => $var,
      ) : $var;
  }
}