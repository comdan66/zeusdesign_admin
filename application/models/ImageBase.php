<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class ImageBase extends OaModel {

  static $table_name = 'image_bases';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
    array ('tag', 'class_name' => 'ImageBaseTag'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('name', 'ImageBaseNameImageUploader');
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'                => $this->id,
      'user_id'           => $this->user_id,
      'image_base_tag_id' => $this->image_base_tag_id,
      'from_url'          => $this->from_url,
      'image_url'         => $this->image_url,
      'name'              => $this->name,
      'updated_at'        => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'        => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public function destroy () {
    if (!isset ($this->id))
      return false;

      return $this->delete ();
  }
}