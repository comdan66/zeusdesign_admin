<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Schedule extends OaModel {

  static $table_name = 'schedules';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('tag', 'class_name' => 'ScheduleTag'),
    array ('user', 'class_name' => 'User'),
  );

  const NO_FINISHED = 0;
  const IS_FINISHED = 1;

  static $finishNames = array(
    self::NO_FINISHED => '未完成',
    self::IS_FINISHED => '已完成',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function columns_val () {
    return array (
      'id'              => isset ($this->id) ? $this->id : '',
      'user_id'         => isset ($this->user_id) ? $this->user_id : '',
      'schedule_tag_id' => isset ($this->schedule_tag_id) ? $this->schedule_tag_id : '',
      'task_id'         => isset ($this->task_id) ? $this->task_id : '',
      'title'           => isset ($this->title) ? $this->title : '',
      'description'     => isset ($this->description) ? $this->description : '',
      'finish'          => isset ($this->finish) ? $this->finish : '',
      'year'            => isset ($this->year) ? $this->year : '',
      'month'           => isset ($this->month) ? $this->month : '',
      'day'             => isset ($this->day) ? $this->day : '',
      'sort'            => isset ($this->sort) ? $this->sort : '',
      'updated_at'      => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'      => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'user' => $this->user->to_array (),
        'title' => $this->title,
        'description' => $this->description,
        'finish' => $this->finish,
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day,
        'sort' => $this->sort,
        'tag' => $this->tag ? $this->tag->to_array () : array ()
      );
  }
  public function mini_title ($length = 20) {
    if (!isset ($this->title)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->title), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->title);
  }
  public function mini_description ($length = 100) {
    if (!isset ($this->description)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->description), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->description);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;
    return $this->delete ();
  }
}