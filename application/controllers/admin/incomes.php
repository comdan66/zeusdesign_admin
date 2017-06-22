<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Incomes extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('website')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/incomes';
    $this->icon = 'icon-bil';
    $this->title = '入帳';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'status', 'show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Income::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }
  public function create_choice () {
    if (!$this->has_post ())
      return redirect_message (array ('admin', 'income-items'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();

    $validation = function (&$posts) {
      if (!(isset ($posts['ids']) && $posts['ids'] && is_array ($posts['ids']) && ($posts['ids'] = column_array (IncomeItem::find ('all', array ('select' => 'id, income_id, updated_at', 'conditions' => array ('id IN (?) AND income_id = 0', $posts['ids']))), 'id')))) return '「請款」資料有誤！';

      if (!(isset ($posts['money']) && is_string ($posts['money']) && is_numeric ($posts['money'] = trim ($posts['money'])) && ($posts['money'] > 0))) return '「總金額」資料有誤！';
      if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Income::STATUS_2 : Income::STATUS_1) && in_array ($posts['status'], array_keys (Income::$statusNames)))) $posts['status'] = Income::STATUS_1;
      
      if (isset ($posts['invoice_date']) && !(is_string ($posts['invoice_date']) && is_date ($posts['invoice_date'] = trim ($posts['invoice_date'])))) $posts['invoice_date'] = NULL;
      if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $posts['memo'] = '';
      return '';
    };

    if (($msg = $validation ($posts)) || (!Income::transaction (function () use (&$obj, $posts) {
      if (!verifyCreateOrm ($obj = Income::create (array_intersect_key ($posts, Income::table ()->columns)))) return false;
      IncomeItem::update_all (array (
        'set' => 'income_id = ' . $obj->id,
        'conditions' => array ('id IN (?) AND income_id = 0', $posts['ids'])
      ));
    }) && $msg = '新增失敗！')) return redirect_message (array ('admin', 'income_items', 'check'), array ('_fd' => $msg, 'posts' => $posts));

    return redirect_message (array ($this->uri_1, $obj->id, 'show'), array ('_fi' => '新增成功，已經成立一張入帳！'));
  }

  public function index ($offset = 0) {
    $searches = array (
        'status' => array ('el' => 'select', 'text' => '是否入帳', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => Income::$statusNames[$t], 'value' => $t,);}, array_keys (Income::$statusNames))),
        'type' => array ('el' => 'select', 'text' => '是否有開發票', 'sql' => function ($a) { return $a ? 'invoice_date IS NOT NULL' : 'invoice_date IS NULL'; }, 'vs' => '$val' . " ? " . '1' . " : " . '0' . "", 'items' => array (array ('text' => '有開發票', 'value' => '1'), array ('text' => '未開發票', 'value' => '0'))),
        'memo' => array ('el' => 'input', 'text' => '備註', 'sql' => 'memo LIKE ?'),
        'invoice_date' => array ('el' => 'input', 'type' => 'date', 'text' => '發票日期', 'sql' => 'date LIKE ?'),
        'money1' => array ('el' => 'input', 'type' => 'number', 'text' => '金額大於等於', 'sql' => 'money >= ?'),
        'money2' => array ('el' => 'input', 'type' => 'number', 'text' => '金額小於等於', 'sql' => 'money <= ?'),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Income', array ('order' => 'id DESC', 'include' => array ('items')));

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
      ));
  }
  public function add () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
      ));
  }
  
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();

    if (($msg = $this->_validation_create ($posts)) || (!Income::transaction (function () use (&$obj, $posts) {
      return verifyCreateOrm ($obj = Income::create (array_intersect_key ($posts, Income::table ()->columns)));
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '此項' . $this->title . '為：「' . ($obj->has_tax () ? '有開發票' : '未開發票') . '」，目前狀態：「' . Income::$statusNames[$obj->status] . '」',
      $obj->backup ());

    return redirect_message (array ($this->uri_1), array ('_fi' => '新增成功！'));
  }

  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
      ));
  }

  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Income::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title,
      '此項' . $this->title . '為：「' . ($obj->has_tax () ? '有開發票' : '未開發票') . '」，目前狀態：「' . Income::$statusNames[$obj->status] . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->backup (true);

    if (!Income::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_fd' => '刪除失敗！'));

    UserLog::logWrite (
      $this->icon,
      '刪除一項' . $this->title,
      '已經備份了刪除紀錄，細節可詢問工程師',
      $backup);

    return redirect_message (array ($this->uri_1), array ('_fi' => '刪除成功！'));
  }

  public function show () {
    UserLog::logRead ($this->icon, '檢視了一項' . $this->title);

    $users = array ();
    foreach ($this->obj->items as $item)
      foreach ($item->details as $detail)
        if (!isset ($users[$detail->user_id]))
          $users[$detail->user_id] = array ('user' => $detail->user, 'details' => array ($detail));
        else
          array_push ($users[$detail->user_id]['details'], $detail);

    return $this->load_view (array (
        'obj' => $this->obj,
        'users' => $users,
      ));
  }
  public function status () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    $validation = function (&$posts) {
      return !(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Income::STATUS_2 : Income::STATUS_1) && in_array ($posts['status'], array_keys (Income::$statusNames))) ? '「設定入帳」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Income::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return $this->output_error_json ('更新失敗！');

    UserLog::logWrite (
      $this->icon,
      Income::$statusNames[$obj->status] . '一項' . $this->title,
      '將一筆' . $this->title . '調整為「' . Income::$statusNames[$obj->status] . '」',
      array ($backup, $obj->backup (true)));

    return $this->output_json ($obj->status == Income::STATUS_2);
  }

  private function _validation_create (&$posts) {
    if (!(isset ($posts['money']) && is_string ($posts['money']) && is_numeric ($posts['money'] = trim ($posts['money'])) && ($posts['money'] > 0))) return '「總金額」資料有誤！';
    if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Income::STATUS_2 : Income::STATUS_1) && in_array ($posts['status'], array_keys (Income::$statusNames)))) $posts['status'] = Income::STATUS_1;
    
    if (isset ($posts['invoice_date']) && !(is_string ($posts['invoice_date']) && is_date ($posts['invoice_date'] = trim ($posts['invoice_date'])))) $posts['invoice_date'] = NULL;
    if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $posts['memo'] = '';
    
    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
