<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class ScheduleTag extends OaModel {

  static $table_name = 'schedule_tags';

  static $has_one = array (
  );

  static $has_many = array (
    array ('schedules', 'class_name' => 'Schedule'),
  );

  static $belongs_to = array (
  );

  const DEFAULT_COLOR = '#000000';


  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public function columns_val ($has = false) {
    $var = array (
      'id'         => $this->id,
      'user_id'    => $this->user_id,
      'name'       => $this->name,
      'color'      => $this->color,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
      'this' => $var,
      'schedules' => array_map (function ($schedule) {
        return $schedule->columns_val ();
      }, Schedule::find ('all', array ('conditions' => array ('schedule_tag_id = ?', $this->id))))) : $var;
  }
  public function color () {
    return $this->color ? '#' . $this->color : ScheduleTag::DEFAULT_COLOR;
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    if ($this->schedules)
      foreach ($this->schedules as $schedule)
        if (!(!($schedule->schedule_tag_id = 0) && $schedule->save ()))
          return false;

    return $this->delete ();
  }
}