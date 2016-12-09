<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
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
  public function to_array (array $opt = array ()) {
    return array (
        'user' => $this->user->to_array (),
        'cover' => array (
            'c100' => $this->cover->url ('100x100c'),
            'c500' => $this->cover->url ('500x500c'),
          ),
        'items' => array_map (function ($item) {
          return $item->to_array ();
        }, $this->items),
        'number' => $this->number,
        'address' => $this->address,
        'lat' => $this->lat,
        'lng' => $this->lng,
        'memo' => $this->memo,
        'timed_at' => $this->timed_at->format ('Y-m-d'),
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