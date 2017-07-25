<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class My_zbs extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('member')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/my-zbs';
    $this->icon = 'icon-moneybag';
    $this->title = '我的宙思幣';

    if (in_array ($this->uri->rsegments (2, 0), array ('show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Zb::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $id, User::current ()->id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'title' => array ('el' => 'input', 'text' => '入帳標題', 'sql' => 'income_id IN (?)', 'vs' => "column_array (Income::find ('all' , array ('select' => 'id', 'conditions' => array ('title LIKE ?', " . '"%" . ' . '$val' . ' . "%"' . "))), 'id')"),
        'm1' => array ('el' => 'input', 'type' => 'number', 'text' => '金額大於等於', 'sql' => 'money >= ?'),
        'm2' => array ('el' => 'input', 'type' => 'number', 'text' => '金額小於等於', 'sql' => 'money <= ?'),
        'status' => array ('el' => 'select', 'text' => '是否給付', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => Zb::$statusNames[$t], 'value' => $t,);}, array_keys (Zb::$statusNames))),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Zb', array ('order' => 'id DESC', 'include' => array ('income', 'details')), function ($conditions) {
      OaModel::addConditions ($conditions, 'user_id = ?', User::current ()->id);
      return $conditions;
    });

    UserLog::logRead (
      $this->icon,
      '檢視了' . $this->title . '列表',
      '搜尋條件細節可詢問工程師',
      $configs);

    return $this->load_view (array (
        'objs' => $objs,
        'total' => $offset,
        'searches' => $searches,
        'pagination' => $this->_get_pagination ($configs),
        'status1' => Zb::find ('one', array ('select' => 'SUM(money) as a', 'conditions' => array ('user_id = ? AND status = ?', User::current ()->id, Zb::STATUS_1))),
        'status2' => Zb::find ('one', array ('select' => 'SUM(money) as a', 'conditions' => array ('user_id = ? AND status = ?', User::current ()->id, Zb::STATUS_2))),
      ));
  }
  public function show () {
    UserLog::logRead ($this->icon, '檢視了一項' . $this->title);

    return $this->load_view (array (
        'obj' => $this->obj->income,
        'users' => User::idAll (),
      ));
  }
}
