<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class WorkBlock extends OaModel {

  static $table_name = 'work_blocks';

  static $has_one = array (
  );

  static $has_many = array (
    array ('items', 'class_name' => 'WorkBlockItem'),
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'         => $this->id,
      'work_id'    => $this->work_id,
      'title'      => $this->title,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
      'this' => $var,
      'items' => array_map (function ($block) {
        return $block->columns_val ();
      }, WorkBlockItem::find ('all', array ('conditions' => array ('work_block_id = ?', $this->id))))) : $var;
  }
  public function to_array (array $opt = array ()) {
    return array (
      'id' => $this->id,
      'title' => $this->title,
      'items' => array_map (function ($item) {
        return $item->to_array ();
      }, $this->items),
    );
  }
  public function destroy () {
    if ($this->items)
      foreach ($this->items as $item)
        if (!$item->destroy ())
          return false;

    return $this->delete ();
  }
}