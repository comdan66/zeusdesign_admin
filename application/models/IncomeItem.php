<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class IncomeItem extends OaModel {

  static $table_name = 'income_items';

  static $has_one = array (
  );

  static $has_many = array (
    array ('images',  'class_name' => 'IncomeItemImage'),
    array ('details',  'class_name' => 'IncomeItemDetail'),
    array ('users',  'class_name' => 'User', 'through' => 'zbs')
  );

  static $belongs_to = array (
    array ('user',  'class_name' => 'User'),
    array ('income',  'class_name' => 'Income')
  );

  private $money = null;

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function money () {
    if ($this->money !== null) return $this->money;
    return $this->money = array_sum (column_array ($this->details, 'all_money'));
  }
  public function destroy () {
    if (!isset ($this->id)) return false;
    
    if ($this->images)
      foreach ($this->images as $image)
        if (!$image->destroy ())
          return false;
    
    if ($this->details)
      foreach ($this->details as $detail)
        if (!$detail->destroy ())
          return false;

    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = array (
      'id'            => $this->id,
      'income_id'     => $this->income_id,
      'user_id'       => $this->user_id,
      'company_pm_id' => $this->company_pm_id,
      'title'         => $this->title,
      'close_date'    => $this->close_date ? $this->close_date->format ('Y-m-d') : '',
      'memo'          => $this->memo,
      'updated_at'    => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'    => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
        '_' => $var,
        'images' => $this->subBackup ('IncomeItemImage', $has),
        'details' => $this->subBackup ('IncomeItemDetail', $has),
      ) : $var;
  }
}