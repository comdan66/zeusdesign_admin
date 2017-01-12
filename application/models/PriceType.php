<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class PriceType extends OaModel {

  static $table_name = 'price_types';

  static $has_one = array (
  );

  static $has_many = array (
    array ('prices', 'class_name' => 'Price'),
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'          => $this->id,
      'name'        => $this->name,
      'updated_at'  => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
      'this' => $var,
      'prices' => array_map (function ($price) {
        return $price->columns_val ();
      }, Price::find ('all', array ('conditions' => array ('price_type_id = ?', $this->id))))) : $var;
  }
  public function destroy () {
    if ($this->prices)
      foreach ($this->prices as $price)
        if (!$price->destroy ())
          return false;

    return $this->delete ();
  }
}