<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Schedule extends OaModel {

  static $table_name = 'schedules';

  static $has_one = array (
  );

  static $has_many = array (
    array ('items', 'class_name' => 'ScheduleItem'),
    array ('shares', 'class_name' => 'ScheduleShare'),
    array ('users', 'class_name' => 'User', 'through' => 'schedule_share'),
  );

  static $belongs_to = array (
    array ('tag', 'class_name' => 'ScheduleTag'),
    array ('user', 'class_name' => 'User'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;
    
    if ($this->items)
      foreach ($this->items as $item)
        if (!$item->destroy ())
          return false;
    
    if ($this->shares)
      foreach ($this->shares as $share)
        if (!$share->destroy ())
          return false;

    return $this->delete ();
  }

  public function backup ($has = false) {
    $var = $this->getBackup ();
    return $has ? array (
        '_' => $var,
        'items' => $this->subBackup ('ScheduleItem', $has),
        'shares' => $this->subBackup ('ScheduleShare', $has),
      ) : $var;
  }
}