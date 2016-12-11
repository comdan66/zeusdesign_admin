<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class WorkImage extends OaModel {

  static $table_name = 'work_images';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('name', 'WorkImageNameImageUploader');
  }
  public function columns_val () {
    return array (
      'id'         => isset ($this->id) ? $this->id : '',
      'work_id'    => isset ($this->work_id) ? $this->work_id : '',
      'name'       => isset ($this->name) ? $this->name : '',
      'updated_at' => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'ori' => $this->name->url (),
        'w800' => $this->name->url ('800w'),
      );

  }
  public function destroy () {
    if (!(isset ($this->name) && isset ($this->id)))
      return false;

    return $this->delete ();
  }
}