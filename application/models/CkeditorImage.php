<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class CkeditorImage extends OaModel {

  static $table_name = 'ckeditor_images';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('name', 'CkeditorImageNameImageUploader');
  }
  public function columns_val () {
    return array (
      'id'         => isset ($this->id) ? $this->id : '',
      'name'       => isset ($this->name) ? $this->name : '',
      'updated_at' => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
  }
}