<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Demo extends OaModel {

  static $table_name = 'demos';

  static $has_one = array (
  );

  static $has_many = array (
    array ('images', 'class_name' => 'DemoImage', 'order' => 'sort ASC'),
  );

  static $belongs_to = array (
  );
  const ENABLE_NO  = 0;
  const ENABLE_YES = 1;

  static $enableNames = array(
    self::ENABLE_NO  => '不公開',
    self::ENABLE_YES => '公開',
  );
  const MOBILE_NOT = 0;
  const MOBILE_YES = 1;

  static $mobileNames = array(
    self::MOBILE_NOT => '不是手機版',
    self::MOBILE_YES => '是手機版',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function demo_url () {
    if (!isset ($this->uid)) return '';

    return "http://" . (ENVIRONMENT != 'production' ? 'dev.' : '') . "demo.zeusdesign.com.tw/#" . $this->uid;
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'          => $this->id,
      'name'        => $this->name,
      'password'    => $this->password,
      'memo'        => $this->memo,
      'is_enabled'  => $this->is_enabled,
      'updated_at'  => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
      'this' => $var,
      'images' => array_map (function ($image) {
        return $image->columns_val ();
      }, DemoImage::find ('all', array ('conditions' => array ('demo_id = ?', $this->id))))) : $var;
  }
  public function destroy () {
    if ($this->images)
      foreach ($this->images as $image)
        if (!$image->destroy ())
          return false;

    return $this->delete ();
  }
}