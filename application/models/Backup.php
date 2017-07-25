<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Backup extends OaModel {

  static $table_name = 'backups';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const STATUS_1 = 1;
  const STATUS_2 = 2;
  const STATUS_3 = 3;

  static $statusNames = array (
    self::STATUS_1 => '失敗',
    self::STATUS_2 => '成功',
    self::STATUS_3 => '已讀',
  );
  
  const TYPE_1 = 1;
  const TYPE_2 = 2;

  static $typeNames = array (
    self::TYPE_1 => '資料庫',
    self::TYPE_2 => 'Query',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmFileUploader::bind ('file', 'BackupFileFileUploader');
  }
}