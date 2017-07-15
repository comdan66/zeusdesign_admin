<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
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

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;
    
    if ($this->schedules)
      foreach ($this->schedules as $schedule)
        if (!(!($schedule->schedule_tag_id = 0) && $schedule->save ()))
          return false;

    return $this->delete ();
  }

  public function backup ($has = false) {
    $var = $this->getBackup ();
    return $has ? array (
        '_' => $var,
        'schedules' => $this->subBackup ('Schedule', $has),
      ) : $var;
  }
}