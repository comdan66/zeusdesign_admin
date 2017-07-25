<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Surplus extends Admin_controller {
  private $uri_1 = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('surplus')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/surplus';
    $this->icon = 'icon-moneybag';
    $this->title = '盈餘';

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {

    $is = Income::find ('all', array ('select' => 'id,invoice_date,money,date', 'include' => array ('zbs'), 'conditions' => array ('status = ? AND date IS NOT NULL', Income::STATUS_2)));
    $os = Outcome::find ('all', array ('select' => 'money,date', 'conditions' => array ('status = ? AND date IS NOT NULL', Outcome::STATUS_2)));

    $objs = array ();
    for ($y = date ('Y'); $y >= 2014; $y--) {
      $obj = array ();

      for ($j = 0; $j < 6; $j++) {
        $m1 = $j * 2 + 1;
        $m2 = $j * 2 + 2;

        $all1 = array_sum (array_map (function ($t) { return $t->tax_money (); }, $u1 = array_filter ($is, function ($t) use ($y, $m1) { return $t->date->format ('Y-n') == $y . '-' . $m1; })));
        $zeus1 = array_sum (array_map (function ($t) { return $t->zeus_money (); }, $u1));
        $out1 = array_sum (array_map (function ($t) { return $t->money; }, array_filter ($os, function ($t) use ($y, $m1) { return $t->date->format ('Y-n') == $y . '-' . $m1; })));

        $sur1 = $zeus1 - $out1;

        $all2 = array_sum (array_map (function ($t) { return $t->tax_money (); }, $u2 = array_filter ($is, function ($t) use ($y, $m2) { return $t->date->format ('Y-n') == $y . '-' . $m2; })));
        $zeus2 = array_sum (array_map (function ($t) { return $t->zeus_money (); }, $u2));
        $out2 = array_sum (array_map (function ($t) { return $t->money; }, array_filter ($os, function ($t) use ($y, $m2) { return $t->date->format ('Y-n') == $y . '-' . $m2; })));

        $sur2 = $zeus2 - $out2;


        array_push ($obj, array (
            'm1' => $m1,
            'm2' => $m2,
            'all' => $all1 + $all2,
            'zeus' => $zeus1 + $zeus2,
            'out' => $out1 + $out2,
            'sur' => $sur1 + $sur2,
          ));
      }

      array_push ($objs, array ('y' => $y, 'months' => $obj));
    }

    UserLog::logRead (
      $this->icon,
      '檢視了' . $this->title . '列表',
      '搜尋條件細節可詢問工程師');

    return $this->load_view (array (
        'objs' => $objs
      ));
  }
}
