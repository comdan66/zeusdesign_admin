<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Invoice extends OaModel {

  static $table_name = 'invoices';

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('tag', 'class_name' => 'InvoiceTag'),
    array ('contact', 'class_name' => 'InvoiceContact'),
    array ('user', 'class_name' => 'User'),
  );

  const NO_FINISHED = 0;
  const IS_FINISHED = 1;

  static $finishNames = array(
    self::NO_FINISHED => '未請款',
    self::IS_FINISHED => '已請款',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public function to_array ($opt = array ()) {
    return array (
        'id' => $this->id,
        'user' => $this->user->to_array (),
        'tag' => $this->tag ? $this->tag->to_array () : array (),
        'contact' => $this->contact ? $this->contact->to_array () : '',
        'name' => $this->name,
        'quantity' => $this->quantity,
        'single_money' => $this->single_money,
        'all_money' => $this->all_money,
        'memo' => $this->memo,
        'is_finished' => $this->is_finished,
        'closing_at' => $this->closing_at->format ('Y-m-d'),
      );
  }
  public function mini_name ($length = 50) {
    if (!isset ($this->name)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->name), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function destroy () {
    if (!isset ($this->id))
      return false;

      return $this->delete ();
  }
}