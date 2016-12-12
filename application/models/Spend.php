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
  public function columns_val () {
    return array (
      'id'         => isset ($this->id) ? $this->id : '',
      'user_id'    => isset ($this->user_id) ? $this->user_id : '',
      'cover'      => isset ($this->cover) ? $this->cover : '',
      'number'     => isset ($this->number) ? $this->number : '',
      'address'    => isset ($this->address) ? $this->address : '',
      'lat'        => isset ($this->lat) ? $this->lat : '',
      'lng'        => isset ($this->lng) ? $this->lng : '',
      'memo'       => isset ($this->memo) ? $this->memo : '',
      'timed_at'   => isset ($this->timed_at) && $this->timed_at ? $this->timed_at->format ('Y-m-d H:i:s') : '',
      'updated_at' => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
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