<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class ImageBaseTag extends OaModel {

  static $table_name = 'image_base_tags';

  static $has_one = array (
  );

  static $has_many = array (
    array ('image_bases', 'class_name' => 'ImageBase'),
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'             => $this->id,
      'user_id'        => $this->user_id,
      'name'           => $this->name,
      'updated_at'     => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'     => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
      'this' => $var,
      'image_bases' => array_map (function ($image_base) {
        return $image_base->columns_val ();
      }, ImageBase::find ('all', array ('conditions' => array ('image_base_tag_id = ?', $this->id))))) : $var;
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    if ($this->image_bases)
      foreach ($this->image_bases as $image_base)
        if (!(!($image_base->image_base_tag_id = 0) && $image_base->save ()))
          return false;

    return $this->delete ();
  }
}