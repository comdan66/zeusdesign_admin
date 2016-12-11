<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Billou extends OaModel {

  static $table_name = 'billous';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );

  const INVOICE_NO  = 0;
  const INVOICE_YES = 1;

  static $invoiceNames = array(
    self::INVOICE_NO  => '沒有發票',
    self::INVOICE_YES => '有開發票',
  );
  const NO_FINISHED = 0;
  const IS_FINISHED = 1;

  static $finishNames = array(
    self::NO_FINISHED => '未出帳',
    self::IS_FINISHED => '已出帳',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  
  public function mini_name ($length = 50) {
    if (!isset ($this->name)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->name), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;
    return $this->delete ();
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'          => isset ($this->id) ? $this->id : '',
      'user_id'     => isset ($this->user_id) ? $this->user_id : '',
      'name'        => isset ($this->name) ? $this->name : '',
      'money'       => isset ($this->money) ? $this->money : '',
      'memo'        => isset ($this->memo) ? $this->memo : '',
      'is_invoice'  => isset ($this->is_invoice) ? $this->is_invoice : '',
      'is_finished' => isset ($this->is_finished) ? $this->is_finished : '',
      'date_at'     => isset ($this->date_at) && $this->date_at ? $this->date_at->format ('Y-m-d') : '',
      'updated_at'  => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'user' => $this->user->to_array (),
        'name' => $this->name,
        'money' => $this->money,
        'is_invoice' => $this->is_invoice,
        'memo' => $this->memo,
        'is_finished' => $this->is_finished,
        'date_at' => $this->date_at->format ('Y-m-d'),
      );
  }
}