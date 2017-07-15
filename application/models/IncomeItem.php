<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class IncomeItem extends OaModel {

  static $table_name = 'income_items';

  static $has_one = array (
    array ('image',  'class_name' => 'IncomeItemImage'),
  );

  static $has_many = array (
    array ('images',  'class_name' => 'IncomeItemImage'),
    array ('details',  'class_name' => 'IncomeItemDetail'),
    array ('users',  'class_name' => 'User', 'through' => 'zbs')
  );

  static $belongs_to = array (
    array ('user',  'class_name' => 'User'),
    array ('income',  'class_name' => 'Income'),
    array ('pm',  'class_name' => 'CompanyPm')
  );

  private $money = null;

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function mini_title ($length = 50) {
    if (!isset ($this->title)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->title), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function hasIncome () {
    return $this->income_id && $this->income;
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
    $var = $this->getBackup ();
    return $has ? array (
        '_' => $var,
        'images' => $this->subBackup ('IncomeItemImage', $has),
        'details' => $this->subBackup ('IncomeItemDetail', $has),
      ) : $var;
  }
}