<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Companies extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('billing')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/companies';
    $this->icon = 'icon-b';
    $this->title = '配合廠商';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Company::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'name'   => array ('el' => 'input', 'text' => $this->title . '名稱', 'sql' => 'name LIKE ?'),
        'tax_no'   => array ('el' => 'input', 'text' => $this->title . '統一編號', 'sql' => 'tax_no LIKE ?'),
        'address'   => array ('el' => 'input', 'text' => $this->title . '住址', 'sql' => 'address LIKE ?'),
        'phone'   => array ('el' => 'input', 'text' => $this->title . '電話', 'sql' => 'address LIKE ?'),
        'pmemail' => array ('el' => 'input', 'text' => 'PM、聯絡窗口名稱', 'sql' => 'id IN (?)', 'vs' => "column_array (CompanyPm::find ('all' , array ('select' => 'company_id', 'conditions' => array ('name LIKE ?', " . '"%" . ' . '$val' . ' . "%"' . "))), 'company_id')"),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Company', array ('order' => 'id DESC', 'include' => array ('pms')));

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

    if (($msg = $this->_validation_create ($posts)) || (!Company::transaction (function () use (&$obj, $posts) {
      return verifyCreateOrm ($obj = Company::create (array_intersect_key ($posts, Company::table ()->columns)));
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '新增一間' . $this->title . '',
      $this->title . '名稱為：「' . $obj->name . '」',
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

    if ($msg = $this->_validation_update ($posts, $obj))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Company::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '修改一間' . $this->title,
      $this->title . '名稱為：「' . $obj->name . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->backup (true);

    if (!Company::transaction (function () use ($obj) { return $obj->destroy (); }))
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
  private function _validation_create (&$posts) {
    if (!(isset ($posts['name']) && is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '「' . $this->title . '名稱」格式錯誤！';
    if (isset ($posts['phone']) && !(is_string ($posts['phone']) && ($posts['phone'] = trim ($posts['phone'])))) $$posts['phone'] = '';
    if (isset ($posts['tax_no']) && !(is_string ($posts['tax_no']) && ($posts['tax_no'] = trim ($posts['tax_no'])))) $$posts['tax_no'] = '';
    if (isset ($posts['address']) && !(is_string ($posts['address']) && ($posts['address'] = trim ($posts['address'])))) $$posts['address'] = '';
    if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $$posts['memo'] = '';

    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
