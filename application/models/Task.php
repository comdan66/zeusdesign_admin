<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Task extends OaModel {

  static $table_name = 'tasks';

  static $has_one = array (
  );

  static $has_many = array (
    array ('users', 'class_name' => 'User', 'through' => 'task_mappings'),

    array ('attachments', 'class_name' => 'TaskAttachment'),
    array ('user_mappings', 'class_name' => 'TaskUserMapping'),
    array ('commits', 'class_name' => 'TaskCommit', 'order' => 'id DESC'),
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );

  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '未完成',
    self::STATUS_2 => '已完成',
  );

  const LEVEL_1 = 1;
  const LEVEL_2 = 2;
  const LEVEL_3 = 3;
  const LEVEL_4 = 4;
  const LEVEL_5 = 5;

  static $levelNames = array (
    self::LEVEL_1 => '有空處理',
    self::LEVEL_2 => '一般事件',
    self::LEVEL_3 => '重要事件',
    self::LEVEL_4 => '非常重要',
    self::LEVEL_5 => '非常緊急',
  );
  static $levelColors = array (
    self::LEVEL_1 => 'rgba(142, 226, 236, 1.00)',
    self::LEVEL_2 => 'rgba(157, 210, 248, 1.00)',
    self::LEVEL_3 => 'rgba(176, 219, 178, 1.00)',
    self::LEVEL_4 => 'rgba(254, 227, 147, 1.00)',
    self::LEVEL_5 => 'rgba(253, 117, 74, 1.00)',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function mini_content ($length = 100) {
    if (!isset ($this->content)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->content), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;
    
    if ($this->attachments)
      foreach ($this->attachments as $attachment)
        if (!$attachment->destroy ())
          return false;

    if ($this->user_mappings)
      foreach ($this->user_mappings as $user_mapping)
        if (!$user_mapping->destroy ())
          return false;

    if ($this->commits)
      foreach ($this->commits as $commit)
        if (!$commit->destroy ())
          return false;

    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = $this->getBackup ();
    return $has ? array (
        '_' => $var,
        'attachments' => $this->subBackup ('TaskAttachment', $has),
        'user_mappings' => $this->subBackup ('TaskUserMapping', $has),
        'commits' => $this->subBackup ('TaskCommit', $has),
      ) : $var;
  }
}