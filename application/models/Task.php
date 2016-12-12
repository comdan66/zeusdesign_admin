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
    array ('task_mappings', 'class_name' => 'TaskUserMapping'),
    array ('users', 'class_name' => 'User', 'through' => 'task_mappings'),
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
  public function columns_val () {
    return array (
      'id'          => isset ($this->id) ? $this->id : '',
      'user_id'     => isset ($this->user_id) ? $this->user_id : '',
      'title'       => isset ($this->title) ? $this->title : '',
      'description' => isset ($this->description) ? $this->description : '',
      'finish'      => isset ($this->finish) ? $this->finish : '',
      'date_at'     => isset ($this->date_at) && $this->date_at ? $this->date_at->format ('Y-m-d') : '',
      'updated_at'  => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
  }
}