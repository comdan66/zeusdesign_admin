<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Customers extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('customer')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/customers';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Customer::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $columns = array ( 
        array ('key' => 'customer_company_id', 'title' => '公司名稱', 'sql' => 'customer_company_id = ?', 'select' => array_map (function ($company) { return array ('value' => $company->id, 'text' => $company->name);}, CustomerCompany::all (array ('select' => 'id, name')))),
        array ('key' => 'name', 'title' => '聯絡人名稱', 'sql' => 'name LIKE ?'), 
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 25;
    $total = Customer::count (array ('conditions' => $conditions));
    $offset = $offset < $total ? $offset : 0;

    $this->load->library ('pagination');
    $pagination = $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
    $objs = Customer::find ('all', array (
        'offset' => $offset,
        'limit' => $limit,
        'order' => 'id DESC',
        'include' => array ('invoices', 'emails', 'company'),
        'conditions' => $conditions
      ));

    return $this->load_view (array (
        'objs' => $objs,
        'pagination' => $pagination,
        'columns' => $columns
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
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $post_emails = isset ($posts['emails']) ? $posts['emails'] : array ();

    if ($msg = $this->_validation_create ($posts))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if (!Customer::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Customer::create (array_intersect_key ($posts, Customer::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    if ($posts['emails'])
      foreach ($posts['emails'] as $email)
        CustomerEmail::transaction (function () use ($email, $obj) { return verifyCreateOrm (CustomerEmail::create (array_intersect_key (array ('customer_id' => $obj->id, 'email' => $email), CustomerEmail::table ()->columns))); });

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ab', 'content' => '新增一項聯絡人。', 'desc' => '名稱為「' . $obj->name . '」。', 'backup' => json_encode ($obj->columns_val ())));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '新增成功！'));
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

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    if (!Customer::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    if ($obj->emails)
      foreach ($obj->emails as $email)
        CustomerEmail::transaction (function () use ($email) { return $email->destroy (); });

    if ($posts['emails'])
      foreach ($posts['emails'] as $email)
        CustomerEmail::transaction (function () use ($email, $obj) { return verifyCreateOrm (CustomerEmail::create (array_intersect_key (array ('customer_id' => $obj->id, 'email' => $email), CustomerEmail::table ()->columns))); });

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ab', 'content' => '修改一項聯絡人。', 'desc' => '聯絡人名稱為「' . $obj->name . '」。', 'backup' => json_encode (array ( 'ori' => $backup, 'now' => $obj->columns_val (true) ))));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->columns_val (true);
    
    if (!Customer::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ab', 'content' => '刪除一項聯絡人。', 'desc' => '已經備份了刪除紀錄，細節可詢問工程師。', 'backup'  => json_encode ($backup)));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '刪除成功！'));
  }

  private function _validation_create (&$posts) {
    if (!isset ($posts['name'])) return '沒有填寫 聯絡人名稱！';
    if (!isset ($posts['customer_company_id'])) return '沒有選擇 聯絡人公司！';

    if (!(($posts['name'] = trim ($posts['name'])) && is_string ($posts['name']))) return '聯絡人名稱 格式錯誤！';
    if (!(is_numeric ($posts['customer_company_id'] = trim ($posts['customer_company_id'])) && CustomerCompany::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['customer_company_id']))))) return '聯絡人公司 不存在！';
    
    $posts['emails'] = isset ($posts['emails']) && $posts['emails'] && is_array ($posts['emails']) ? array_values (array_filter ($posts['emails'], function ($email) { return $email; })) : array ();
    $posts['extension'] = isset ($posts['extension']) && ($posts['extension'] = trim ($posts['extension'])) && is_string ($posts['extension']) ? $posts['extension'] : '';
    $posts['cellphone'] = isset ($posts['cellphone']) && ($posts['cellphone'] = trim ($posts['cellphone'])) && is_string ($posts['cellphone']) ? $posts['cellphone'] : '';
    $posts['experience'] = isset ($posts['experience']) && ($posts['experience'] = trim ($posts['experience'])) && is_string ($posts['experience']) ? $posts['experience'] : '';
    $posts['memo'] = isset ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) && is_string ($posts['memo']) ? $posts['memo'] : '';
    
    return '';
  }

  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
