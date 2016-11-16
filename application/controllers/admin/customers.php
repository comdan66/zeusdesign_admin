<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Customers extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('article')))
      return redirect_message (array ('admin'), array (
            '_flash_danger' => '您的權限不足，或者頁面不存在。'
          ));

    $this->uri_1 = 'admin/customers';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Customer::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array (
            '_flash_danger' => '找不到該筆資料。'
          ));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $columns = array ( 
        array ('key' => 'telephone', 'title' => '電話', 'sql' => 'telephone LIKE ?'), 
        array ('key' => 'email', 'title' => '電子郵件', 'sql' => 'email LIKE ?'), 
        array ('key' => 'name', 'title' => '名稱', 'sql' => 'name LIKE ?'), 
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
        'include' => array ('company'),
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
        'posts' => $posts
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => '非 POST 方法，錯誤的頁面請求。'
        ));

    $posts = OAInput::post ();
    
    if (($msg = $this->_validation_must ($posts)) || ($msg = $this->_validation ($posts)))
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    $create = Customer::transaction (function () use (&$obj, $posts) {
      return verifyCreateOrm ($obj = Customer::create (array_intersect_key ($posts, Customer::table ()->columns)));
    });

    if (!$create)
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => '新增失敗！',
          'posts' => $posts
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ab', 'content' => '新增一項聯絡人。', 'desc' => '聯絡人名稱為「' . $obj->name . '」。', 'backup' => json_encode ($obj->to_array ())));
    return redirect_message (array ($this->uri_1), array (
        '_flash_info' => '新增成功！'
      ));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
                    'posts' => $posts,
                    'obj' => $this->obj
                  ));
  }
  public function update () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => '非 POST 方法，錯誤的頁面請求。'
        ));

    $posts = OAInput::post ();

    if ($msg = $this->_validation ($posts))
      return redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    if ($columns = array_intersect_key ($posts, $this->obj->table ()->columns))
      foreach ($columns as $column => $value)
        $this->obj->$column = $value;
    
    $obj = $this->obj;
    $update = Customer::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    });

    if (!$update)
      return redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => '更新失敗！',
          'posts' => $posts
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ab', 'content' => '修改一項聯絡人。', 'desc' => '聯絡人名稱為「' . $obj->name . '」。', 'backup' => json_encode ($obj->to_array ())));
    return redirect_message (array ($this->uri_1), array (
        '_flash_info' => '更新成功！'
      ));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = json_encode ($obj->to_array ());
    $delete = Customer::transaction (function () use ($obj) { return $obj->destroy (); });
    
    if (!$delete)
      return redirect_message (array ($this->uri_1), array (
          '_flash_danger' => '刪除失敗！',
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ab', 'content' => '刪除一項聯絡人。', 'desc' => '已經備份了刪除紀錄，細節可詢問工程師。', 'backup' => $backup));
    return redirect_message (array ($this->uri_1), array (
        '_flash_info' => '刪除成功！'
      ));
  }

  private function _validation (&$posts) {
    $keys = array ('name', 'company_id', 'email', 'telephone', 'extension', 'address', 'cellphone', 'memo');

    $new_posts = array (); foreach ($posts as $key => $value) if (in_array ($key, $keys)) $new_posts[$key] = $value;
    $posts = $new_posts;

    if (isset ($posts['name']) && !($posts['name'] = trim ($posts['name']))) return '名稱格式錯誤！';
    if (isset ($posts['company_id']) && !(($posts['company_id'] = trim ($posts['company_id'])) && Company::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['company_id']))))) return '公司格式錯誤！';
    
    if (isset ($posts['email']) && ($posts['email'] = trim ($posts['email'])) && !is_string ($posts['email'])) return '電子郵件信箱格式錯誤！';
    if (isset ($posts['telephone']) && ($posts['telephone'] = trim ($posts['telephone'])) && !is_string ($posts['telephone'])) return '電話格式錯誤！';
    if (isset ($posts['extension']) && ($posts['extension'] = trim ($posts['extension'])) && ($posts['extension'] = trim ($posts['extension'], '#')) && !is_string ($posts['extension'])) return '分機格式錯誤！';
    if (isset ($posts['address']) && ($posts['address'] = trim ($posts['address'])) && !is_string ($posts['address'])) return '住址格式錯誤！';
    if (isset ($posts['cellphone']) && ($posts['cellphone'] = trim ($posts['cellphone'])) && !is_string ($posts['cellphone'])) return '手機格式錯誤！';
    if (isset ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) && !is_string ($posts['memo'])) return '備註格式錯誤！';
    return '';
  }
  private function _validation_must (&$posts) {
    if (!isset ($posts['company_id'])) return '沒有填寫 公司！';
    if (!isset ($posts['name'])) return '沒有填寫 名稱！';
    return '';
  }
}
