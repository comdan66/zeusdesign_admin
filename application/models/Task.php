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
      'schedules' => array_map (function ($schedule) {
        return $schedule->columns_val ();
      }, Schedule::find ('all', array ('conditions' => array ('task_id = ?', $this->id))))) : $var;
  }
  public function destroy () {
    if ($this->user_mappings)
      foreach ($this->user_mappings as $user_mapping)
        if (!$user_mapping->destroy ())
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