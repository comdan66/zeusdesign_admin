<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Task extends OaModel {

  static $table_name = 'tasks';

  static $has_one = array (
  );

  static $has_many = array (
    array ('user_mappings', 'class_name' => 'TaskUserMapping'),
    array ('commits', 'class_name' => 'TaskCommit'),
    array ('users', 'class_name' => 'User', 'through' => 'task_mappings'),
    array ('schedules', 'class_name' => 'Schedule'),
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );

  const NO_FINISHED = 0;
  const IS_FINISHED = 1;

  static $finishNames = array(
    self::NO_FINISHED => '尚未完成',
    self::IS_FINISHED => '已經完成',
  );
  const LEVEL_1 = 1;
  const LEVEL_2 = 2;
  const LEVEL_3 = 3;
  const LEVEL_4 = 4;
  const LEVEL_5 = 5;

  static $levelNames = array(
    self::LEVEL_1 => '有空再處理',
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

  public function mini_description ($length = 100) {
    if (!isset ($this->description)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->description), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->description);
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'          => $this->id,
      'user_id'     => $this->user_id,
      'title'       => $this->title,
      'description' => $this->description,
      'finish'      => $this->finish,
      'date_at'     => $this->date_at ? $this->date_at->format ('Y-m-d') : '',
      'updated_at'  => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
      'this' => $var,
      'mappings' => array_map (function ($mapping) {
        return $mapping->columns_val ();
      }, TaskUserMapping::find ('all', array ('conditions' => array ('task_id = ?', $this->id)))),
      'commits' => array_map (function ($commit) {
        return $commit->columns_val ();
      }, TaskCommit::find ('all', array ('conditions' => array ('task_id = ?', $this->id)))),
      'schedules' => array_map (function ($schedule) {
        return $schedule->columns_val ();
      }, Schedule::find ('all', array ('conditions' => array ('task_id = ?', $this->id))))) : $var;
  }
  public function destroy () {
    if ($this->user_mappings)
      foreach ($this->user_mappings as $user_mapping)
        if (!$user_mapping->destroy ())
          return false;
    
    if ($this->commits)
      foreach ($this->commits as $commit)
        if (!$commit->destroy ())
          return false;

    if ($this->schedules)
      foreach ($this->schedules as $schedule)
        if (!$schedule->destroy ())
          return false;

    return $this->delete ();
  }
  // public function update_schedules () {
  //   foreach ($this->schedules as $schedule)
  //     if (!$schedule->update_from_task ())
  //       return false;
  //   return true;
  // }
}