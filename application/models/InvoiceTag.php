<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class InvoiceTag extends OaModel {

  static $table_name = 'invoice_tags';

  static $has_one = array (
  );

  static $has_many = array (
    array ('invoices', 'class_name' => 'Invoice'),
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public function columns_val ($has = false) {
    $var = array (
      'id'         => $this->id,
      'name'       => $this->name,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
      'this' => $var,
      'invoices' => array_map (function ($invoice) {
        return $invoice->columns_val ();
      }, Invoice::find ('all', array ('conditions' => array ('invoice_tag_id = ?', $this->id))))) : $var;
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'name' => $this->name,
      );
  }
  public function destroy () {
    if ($this->invoices)
      foreach ($this->invoices as $invoice)
        if (!(!($invoice->invoice_tag_id = 0) && $invoice->save ()))
          return false;

    return $this->delete ();
  }
  public function site_show_page_last_uri () {
    return oa_url_encode ($this->name);
  }
}