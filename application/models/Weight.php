<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Weight extends OaModel {

  static $table_name = 'weights';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('cover', 'WeightCoverImageUploader');
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'         => $this->id,
      'user_id'    => $this->user_id,
      'weight'     => $this->weight,
      'rate'       => $this->rate,
      'calorie'    => $this->calorie,
      'memo'       => $this->memo,
      'cover'      => (string)$this->cover ? (string)$this->cover : '',
      'date_at'    => $this->date_at ? $this->date_at->format ('Y-m-d') : '',
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public function destroy () {
    if (!(isset ($this->cover) && isset ($this->id)))
      return false;

    return $this->delete ();
  }
}