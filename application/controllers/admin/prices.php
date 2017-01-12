<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Prices extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('price')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/prices';
    $this->icon = 'icon-abacus';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Price::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  public function abacus ($offset = 0) {
    $limit = 25;
    $types = PriceType::find ('all', array ('include' => 'prices'));

    return $this->add_param ('now_url', base_url ($this->uri_1 . '/abacus'))
                ->load_view (array (
        'types' => $types,
      ));
  }
}
