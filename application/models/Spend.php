<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Spend extends OaModel {

  static $table_name = 'spends';

  static $has_one = array (
  );

  static $has_many = array (
    array ('items', 'class_name' => 'SpendItem'),
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
   
    OrmImageUploader::bind ('cover', 'SpendCoverImageUploader');
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'         => $this->id,
      'user_id'    => $this->user_id,
      'number'     => $this->number,
      'address'    => $this->address,
      'lat'        => $this->lat,
      'lng'        => $this->lng,
      'memo'       => $this->memo,
      'cover'      => (string)$this->cover ? (string)$this->cover : '',
      'timed_at'   => $this->timed_at ? $this->timed_at->format ('Y-m-d H:i:s') : '',
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public function destroy () {
    if ($this->items)
      foreach ($this->items as $item)
        if (!$item->destroy ())
          return false;

    return $this->delete ();
  }
}