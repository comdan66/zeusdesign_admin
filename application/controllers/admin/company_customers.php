<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Company_customers extends Admin_controller {
  private $uri_1  = null;
  private $uri_2  = null;
  private $parent = null;
  private $obj    = null;
  private $icon   = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('customer')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/company';
    $this->uri_2 = 'customers';
    $this->icon = 'icon-ab';

    if (!(($id = $this->uri->rsegments (3, 0)) && ($this->parent = CustomerCompany::find_by_id ($id))))
      return redirect_message (array ('customer-companies'), array ('_flash_danger' => '找不到該筆資料。'));

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (4, 0)) && ($this->obj = Customer::find_by_id ($id))))
        return redirect_message (array ($this->uri_1, $this->parent_tag->id, $this->uri_2), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('uri_2', $this->uri_2)
         ->add_param ('parent', $this->parent)
         ->add_param ('now_url', base_url ('admin', 'customer-companies'));
  }
  public function index ($id, $offset = 0) {
    $columns = array ( 
        array ('key' => 'name', 'title' => '聯絡人名稱', 'sql' => 'name LIKE ?'), 
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ($this->parent->id, $this->uri_2, '%s'));
    $conditions = conditions ($columns, $configs);
    OaModel::addConditions ($conditions, 'customer_company_id = ?', $this->parent->id);

    $limit = 25;
    $total = Customer::count (array ('conditions' => $conditions));
    $objs = Customer::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'include' => array ('invoices', 'emails', 'company'), 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
      ));
  }
  public function add () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'emails' => isset ($posts['emails']) ? $posts['emails'] : array ()
      ));
  }
  public function create () {
    $parent = $this->parent;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();

    if ($msg = $this->_validation_create ($posts))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    $posts['customer_company_id'] = $parent->id;

    if (!Customer::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Customer::create (array_intersect_key ($posts, Customer::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    if ($posts['emails'])
      foreach ($posts['emails'] as $email)
        CustomerEmail::transaction (function () use ($email, $obj) { return verifyCreateOrm (CustomerEmail::create (array_intersect_key (array ('customer_id' => $obj->id, 'email' => $email), CustomerEmail::table ()->columns))); });

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '在 “' . $parent->name . '” 下新增一項聯絡人。',
      'desc' => '聯絡人名稱為「' . $obj->name . '」。',
      'backup' => json_encode ($obj->columns_val ())));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'emails' => isset ($posts['emails']) ? $posts['emails'] : array_map (function ($email) { return $email->email; }, $this->obj->emails),
      ));
  }
  public function update () {
    $obj = $this->obj;
    $parent = $this->parent;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    if (!Customer::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    if ($obj->emails)
      foreach ($obj->emails as $email)
        CustomerEmail::transaction (function () use ($email) { return $email->destroy (); });

    if ($posts['emails'])
      foreach ($posts['emails'] as $email)
        CustomerEmail::transaction (function () use ($email, $obj) { return verifyCreateOrm (CustomerEmail::create (array_intersect_key (array ('customer_id' => $obj->id, 'email' => $email), CustomerEmail::table ()->columns))); });

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改了 “' . $parent->name . '” 下的一項聯絡人。',
      'desc' => '聯絡人名稱為「' . $obj->name . '」。',
      'backup' => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $parent = $this->parent;
    $backup = $obj->columns_val (true);

    if (!Customer::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除了 “' . $parent->name . '” 下的一項聯絡人。',
      'desc' => '已經備份了刪除紀錄，細節可詢問工程師。',
      'backup'  => json_encode ($backup)));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '刪除成功！'));
  }

  private function _validation_create (&$posts) {
    if (!isset ($posts['name'])) return '沒有填寫 聯絡人名稱！';

    if (!(is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '聯絡人名稱 格式錯誤！';

    $posts['emails'] = isset ($posts['emails']) && is_array ($posts['emails']) && $posts['emails'] ? array_values (array_filter ($posts['emails'], function ($email) { return $email; })) : array ();
    $posts['extension'] = isset ($posts['extension']) && is_string ($posts['extension']) && ($posts['extension'] = trim ($posts['extension'])) ? $posts['extension'] : '';
    $posts['cellphone'] = isset ($posts['cellphone']) && is_string ($posts['cellphone']) && ($posts['cellphone'] = trim ($posts['cellphone'])) ? $posts['cellphone'] : '';
    $posts['experience'] = isset ($posts['experience']) && is_string ($posts['experience']) && ($posts['experience'] = trim ($posts['experience'])) ? $posts['experience'] : '';
    $posts['memo'] = isset ($posts['memo']) && is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) ? $posts['memo'] : '';
    
    return '';
  }

  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
