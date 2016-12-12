<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class WorkBlockItem extends OaModel {

  static $table_name = 'work_block_items';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'            => isset ($this->id) ? $this->id : '',
      'work_block_id' => isset ($this->work_block_id) ? $this->work_block_id : '',
      'title'         => isset ($this->title) ? $this->title : '',
      'link'          => isset ($this->link) ? $this->link : '',
      'updated_at'    => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'    => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'title' => $this->title,
        'link' => $this->link,
      );
  }
  public function destroy () {
    return $this->delete ();
  }
}