<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Outcomes extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('outcome')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/outcomes';
    $this->icon = 'icon-ob';
    $this->title = '出帳';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'status', 'show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Outcome::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'title' => array ('el' => 'input', 'text' => '標題', 'sql' => 'title LIKE ?'),
        'status' => array ('el' => 'select', 'text' => '是否出帳', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => Outcome::$statusNames[$t], 'value' => $t,);}, array_keys (Outcome::$statusNames))),
        'type' => array ('el' => 'select', 'text' => '是否有開發票', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => Outcome::$typeNames[$t], 'value' => $t,);}, array_keys (Outcome::$typeNames))),
        'money1' => array ('el' => 'input', 'type' => 'number', 'text' => '金額大於等於', 'sql' => 'money >= ?'),
        'money2' => array ('el' => 'input', 'type' => 'number', 'text' => '金額小於等於', 'sql' => 'money <= ?'),
        'year[]' => array ('el' => 'checkbox', 'text' => '出帳年份', 'sql' => 'date IS NOT NULL AND YEAR(date) IN (?)', 'items' => array_map (function ($u) { return array ('text' => $u . '年', 'value' => $u); }, array ('2014', '2015', '2016', '2017'))),
        'date[]' => array ('el' => 'checkbox', 'text' => '出帳月份', 'sql' => 'date IS NOT NULL AND MONTH(date) IN (?)', 'items' => array_map (function ($u) { return array ('text' => $u . '月', 'value' => $u); }, array ('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'))),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Outcome', array ('order' => 'id DESC'));

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

    if (($msg = $this->_validation_create ($posts)) || (!Outcome::transaction (function () use (&$obj, $posts) {
      return verifyCreateOrm ($obj = Outcome::create (array_intersect_key ($posts, Outcome::table ()->columns)));
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '名稱為：「' . $obj->title . '」',
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

    if (!Outcome::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title,
      '名稱為：「' . $obj->title . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->backup (true);

    if (!Outcome::transaction (function () use ($obj) { return $obj->destroy (); }))
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

    return $this->load_view (array (
        'obj' => $this->obj,
      ));
  }
  public function status () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    $validation = function (&$posts) {
      if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Outcome::STATUS_2 : Outcome::STATUS_1) && in_array ($posts['status'], array_keys (Outcome::$statusNames)))) return '「設定上下架」發生錯誤！';
      $posts['date'] = $posts['status'] == Outcome::STATUS_2 ? date ('Y-m-d') : null;
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Outcome::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return $this->output_error_json ('更新失敗！');

    UserLog::logWrite (
      $this->icon,
      Outcome::$statusNames[$obj->status] . '一項' . $this->title,
      '將' . $this->title . '「' . $obj->title . '」調整為「' . Outcome::$statusNames[$obj->status] . '」',
      array ($backup, $obj->backup (true)));

    return $this->output_json ($obj->status == Article::STATUS_2);
  }
  private function _validation_create (&$posts) {
    if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && in_array ($posts['status'], array_keys (Outcome::$statusNames)))) $posts['status'] = Outcome::STATUS_1;
    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「' . $this->title . '標題」格式錯誤！';
    if (!(isset ($posts['user_id']) && is_string ($posts['user_id']) && is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find_by_id ($posts['user_id']))) return '「' . $this->title . '新增者」格式錯誤！';
    if (!(isset ($posts['money']) && is_string ($posts['money']) && is_numeric ($posts['money'] = trim ($posts['money'])) && ($posts['money'] > 0))) return '「金額」格式錯誤！';
    if (!(isset ($posts['type']) && is_string ($posts['type']) && is_numeric ($posts['type'] = trim ($posts['type'])) && in_array ($posts['type'], array_keys (Outcome::$typeNames)))) $posts['type'] = Outcome::TYPE_1;
    if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $$posts['memo'] = '';
    $posts['date'] = $posts['status'] == Outcome::STATUS_2 ? date ('Y-m-d') : null;

    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
