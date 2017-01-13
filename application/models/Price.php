<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Price extends OaModel {

  static $table_name = 'prices';

  static $has_one = array (
  );

  static $has_many = array (
    array ('sources', 'class_name' => 'PriceSource'),
  );

  static $belongs_to = array (
    array ('type', 'class_name' => 'PriceType'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'          => $this->id,
      'name'        => $this->name,
      'money'       => $this->money,
      'desc'        => $this->desc,
      'memo'        => $this->memo,
      'updated_at'  => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
      'this' => $var,
      'sources' => array_map (function ($source) {
        return $source->columns_val ();
      }, PriceSource::find ('all', array ('conditions' => array ('price_id = ?', $this->id))))) : $var;
  }
  public function mini_desc ($length = 50) {
    if (!isset ($this->desc)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->desc), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function destroy () {
    return $this->delete ();
  }
}