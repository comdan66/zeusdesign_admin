<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

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
    
    if (!User::current ()->in_roles (array ('income')))
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

  public function index ($offset = 0) {
    $searches = array (
        'status' => array ('el' => 'select', 'text' => '是否入帳', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => Income::$statusNames[$t], 'value' => $t,);}, array_keys (Income::$statusNames))),
        'type' => array ('el' => 'select', 'text' => '是否有開發票', 'sql' => function ($a) { return $a ? 'invoice_date IS NOT NULL' : 'invoice_date IS NULL'; }, 'vs' => '$val' . " ? " . '1' . " : " . '0' . "", 'items' => array (array ('text' => '有開發票', 'value' => '1'), array ('text' => '未開發票', 'value' => '0'))),
        'memo' => array ('el' => 'input', 'text' => '備註', 'sql' => 'memo LIKE ?'),
        'invoice_date' => array ('el' => 'input', 'type' => 'date', 'text' => '發票日期', 'sql' => 'date LIKE ?'),
        'money1' => array ('el' => 'input', 'type' => 'number', 'text' => '金額大於等於', 'sql' => 'money >= ?'),
        'money2' => array ('el' => 'input', 'type' => 'number', 'text' => '金額小於等於', 'sql' => 'money <= ?'),
        'year[]' => array ('el' => 'checkbox', 'text' => '入帳年份', 'sql' => 'date IS NOT NULL AND YEAR(date) IN (?)', 'items' => array_map (function ($u) { return array ('text' => $u . '年', 'value' => $u); }, array ('2014', '2015', '2016', '2017'))),
        'date[]' => array ('el' => 'checkbox', 'text' => '入帳月份', 'sql' => 'date IS NOT NULL AND MONTH(date) IN (?)', 'items' => array_map (function ($u) { return array ('text' => $u . '月', 'value' => $u); }, array ('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'))),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Income', array ('order' => 'id DESC', 'include' => array ('zbs', 'user')));

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

    if (!$posts = OAInput::post ())
      $posts = Session::getData ('posts', true);

    if (!(isset ($posts['ids']) && $posts['ids'] && is_array ($posts['ids']) && ($objs = IncomeItem::find ('all', array ('include' => array ('details', 'user', 'pm'), 'conditions' => array ('id IN (?) AND income_id = 0', $posts['ids']))))))
      return redirect_message (array ('admin', 'income-items'), array ('_fd' => '選取資訊錯誤，請重新選取！', 'posts' => $posts));

    UserLog::logRead (
      $this->icon,
      '進入確認' . $this->title . '程序',
      '準備合併' . count ($objs) . '筆請款',
      $posts['ids']);

    return $this->load_view (array (
        'objs' => $objs,
        'posts' => $posts,
      ));
  }
  
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ('admin', 'income-items'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['user_id'] = User::current ()->id;

    if (($msg = $this->_validation_create ($posts, $objs)) || (!Income::transaction (function () use (&$obj, $posts, $objs) {
      if (!verifyCreateOrm ($obj = Income::create (array_intersect_key ($posts, Income::table ()->columns)))) return false;

      $users = array ();
      foreach ($objs as $o)
        foreach ($o->details as $detail) {
          if (($o->income_id = $obj->id) && !$o->save ())
            return false;

          if (!isset ($users[$detail->user_id]))
            $users[$detail->user_id] = array ('money' => 0, 'detail_ids' => array ());
        
          $users[$detail->user_id]['money'] += $detail->all_money;
          array_push ($users[$detail->user_id]['detail_ids'], $detail->id);
        }

      if (!$users) return false;
      foreach ($users as $user_id => $user)
        if (!(verifyCreateOrm ($z = Zb::create (array ('user_id' => $user_id, 'income_id' => $obj->id, 'money' => $user['money'], 'percentage' => $user['money'] / $posts['money']))) && IncomeItemDetail::update_all (array ('set' => 'zb_id = ' . $z->id, 'conditions' => array ('id IN (?)', $user['detail_ids']))) && Notice::send ($z->user_id, '新增了一筆宙思幣給您囉，詳細內容請到「我的宙思幣」檢視。', 'admin/my-zbs/' . $z->id . '/show')))
          return false;

      return true;
    }) && $msg = '新增失敗！')) return redirect_message (array ('admin', 'income_items', 'check'), array ('_fd' => $msg, 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '' . $this->title . '標題：「' . $obj->title . '」是：「' . ($obj->has_tax () ? '有開發票' : '未開發票') . '」，目前狀態：「' . Income::$statusNames[$obj->status] . '」',
      $obj->backup ());

    return redirect_message (array ($this->uri_1, $obj->id, 'show'), array ('_fi' => '新增成功，已經成立一張入帳！'));
  }

  public function edit () {
    if ($this->obj->status == Income::STATUS_2) return redirect_message (array ($this->uri_1), array ('_fd' => '此' . $this->title . '已經入帳，所以不能修改！'));

    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
      ));
  }

  public function update () {
    if ($this->obj->status == Income::STATUS_2) return redirect_message (array ($this->uri_1), array ('_fd' => '此' . $this->title . '已經入帳，所以不能修改！'));
    
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts, $obj))
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
      '' . $this->title . '標題：「' . $obj->title . '」是：「' . ($obj->has_tax () ? '有開發票' : '未開發票') . '」，目前狀態：「' . Income::$statusNames[$obj->status] . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }

  public function destroy () {
    if ($this->obj->status == Income::STATUS_2) return redirect_message (array ($this->uri_1), array ('_fd' => '此' . $this->title . '已經入帳，所以不能刪除！'));
    $obj = $this->obj;
    $backup = $obj->backup (true);
    
    $zbs = $obj->zbs;
    $title = $obj->title;

    if (!Income::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_fd' => '刪除失敗！'));

    Notice::send (column_array ($zbs, 'user_id'), '您有一筆入帳「' . $title . '」的宙思幣已刪除囉，詳細內容請詢問管理員。');

    UserLog::logWrite (
      $this->icon,
      '刪除一項' . $this->title,
      '已經備份了刪除紀錄，細節可詢問工程師',
      $backup);

    return redirect_message (array ($this->uri_1), array ('_fi' => '刪除成功！'));
  }

  public function show () {
    UserLog::logRead ($this->icon, '檢視了一項' . $this->title);

    return $this->load_view (array (
        'obj' => $this->obj,
        'users' => User::idAll (),
      ));
  }

  public function status () {
    if (!User::current ()->in_roles (array ('income_status')))
      return $this->output_error_json ('您的權限不足。');

    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    $validation = function (&$posts) use ($obj) {
      if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Income::STATUS_2 : Income::STATUS_1) && in_array ($posts['status'], array_keys (Income::$statusNames)))) return '「設定入帳」發生錯誤！';
      $posts['date'] = $posts['status'] == Income::STATUS_2 && $obj->status != $posts['status'] ? date ('Y-m-d') : null;

      return '';
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

  public function zb_status ($id) {
    if (!User::current ()->in_roles (array ('to_zb')))
      return $this->output_error_json ('您的權限不足。');
    
    if (!($id && ($this->obj = Zb::find ('one', array ('conditions' => array ('id = ?', $id))))))
      return $this->output_error_json ('找不到該筆資料。');

    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    $validation = function (&$posts) {
      return !(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Zb::STATUS_2 : Zb::STATUS_1) && in_array ($posts['status'], array_keys (Zb::$statusNames))) ? '「設定給付」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Zb::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return $this->output_error_json ('更新失敗！');

    UserLog::logWrite (
      'icon-moneybag',
      Zb::$statusNames[$obj->status] . '一項薪資',
      '將一筆請款單薪資調整為「' . Zb::$statusNames[$obj->status] . '」',
      array ($backup, $obj->backup (true)));

    Notice::send ($obj->user_id, '您有一筆宙思幣狀態調整已經調整成「' . Zb::$statusNames[$obj->status] . '」，詳細內容請到「我的宙思幣」檢視。', 'admin/my-zbs/' . $obj->id . '/show');

    return $this->output_json ($obj->status == Zb::STATUS_2);
  }

  private function _validation_create (&$posts, &$objs) {
    if (!(isset ($posts['ids']) && $posts['ids'] && is_array ($posts['ids']) && ($posts['ids'] = column_array ($objs = IncomeItem::find ('all', array ('select' => 'id, income_id, updated_at', 'include' => array ('details'), 'conditions' => array ('id IN (?) AND income_id = 0', $posts['ids']))), 'id')))) return '「請款」資料有誤！';

    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「標題」格式錯誤！';
    if (!(isset ($posts['money']) && is_string ($posts['money']) && is_numeric ($posts['money'] = trim ($posts['money'])) && ($posts['money'] > 0))) return '「總金額」格式錯誤！';
    
    if (User::current ()->in_roles (array ('income_status'))) {
      if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Income::STATUS_2 : Income::STATUS_1) && in_array ($posts['status'], array_keys (Income::$statusNames)))) $posts['status'] = Income::STATUS_1;
      $posts['date'] = $posts['status'] == Income::STATUS_2 ? date ('Y-m-d') : null;
    } else {
      $posts['status'] = Income::STATUS_1;      
      $posts['date'] = null;
    }

    if (isset ($posts['invoice_date']) && !(is_string ($posts['invoice_date']) && is_date ($posts['invoice_date'] = trim ($posts['invoice_date'])))) $posts['invoice_date'] = NULL;
    if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $posts['memo'] = '';

    return '';
  }
  private function _validation_update (&$posts, $obj) {
    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「標題」格式錯誤！';
    if (!(isset ($posts['money']) && is_string ($posts['money']) && is_numeric ($posts['money'] = trim ($posts['money'])) && ($posts['money'] > 0))) return '「總金額」格式錯誤！';
    
    if (User::current ()->in_roles (array ('income_status')) && isset ($posts['status'])) {
      if (!(is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Income::STATUS_2 : Income::STATUS_1) && in_array ($posts['status'], array_keys (Income::$statusNames)))) $posts['status'] = Income::STATUS_1;
      $posts['date'] = $posts['status'] == Income::STATUS_2 ? date ('Y-m-d') : null;
    }

    if (isset ($posts['invoice_date']) && !(is_string ($posts['invoice_date']) && is_date ($posts['invoice_date'] = trim ($posts['invoice_date'])))) $posts['invoice_date'] = NULL;
    if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $posts['memo'] = '';

    return '';
  }
}
