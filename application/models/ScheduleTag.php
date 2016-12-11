<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
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

  public function columns_val () {
    return array (
      'id'         => isset ($this->id) ? $this->id : '',
      'user_id'    => isset ($this->user_id) ? $this->user_id : '',
      'name'       => isset ($this->name) ? $this->name : '',
      'color'      => isset ($this->color) ? $this->color : '',
      'updated_at' => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'name' => $this->name,
        'color' => $this->color (),
      );
  }
  public function color () {
    return $this->color ? '#' . $this->color : ScheduleTag::DEFAULT_COLOR;
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    if ($schedule_ids = column_array ($this->schedules, 'id'))
      Schedule::update_all (array (
          'set' => 'schedule_tag_id = NULL',
          'conditions' => array ('id IN (?)', $schedule_ids)
        ));

    return $this->delete ();
  }
}