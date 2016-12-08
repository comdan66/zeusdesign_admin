<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Ftps extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('project')))
      return redirect_message (array ('admin'), array (
            '_flash_danger' => '您的權限不足，或者頁面不存在。'
          ));

    $this->uri_1 = 'admin/ftps';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Ftp::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array (
            '_flash_danger' => '找不到該筆資料。'
          ));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }

  private function _search_columns () {
    return array ( 
        array ('key' => 'admin_url', 'title' => '管理頁網址', 'sql' => 'admin_url LIKE ?'), 
        array ('key' => 'ftp_url', 'title' => 'FTP 主機', 'sql' => 'ftp_url LIKE ?'), 
        array ('key' => 'url',  'title' => '網站網址', 'sql' => 'url LIKE ?'), 
        array ('key' => 'name', 'title' => '專案名稱', 'sql' => 'name LIKE ?'), 
      );
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = Ftp::count (array ('conditions' => $conditions));
    $offset = $offset < $total ? $offset : 0;

    $this->load->library ('pagination');
    $pagination = $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
    $objs = Ftp::find ('all', array (
        'offset' => $offset,
        'limit' => $limit,
        'order' => 'id DESC',
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
    if (isset ($posts['ftp_url'])) $posts['ftp_url'] = OAInput::post ('ftp_url', false);
    if (isset ($posts['ftp_account'])) $posts['ftp_account'] = OAInput::post ('ftp_account', false);
    if (isset ($posts['ftp_password'])) $posts['ftp_password'] = OAInput::post ('ftp_password', false);
    if (isset ($posts['admin_url'])) $posts['admin_url'] = OAInput::post ('admin_url', false);
    if (isset ($posts['admin_account'])) $posts['admin_account'] = OAInput::post ('admin_account', false);
    if (isset ($posts['admin_password'])) $posts['admin_password'] = OAInput::post ('admin_password', false);
    
    if (($msg = $this->_validation_must ($posts)) || ($msg = $this->_validation ($posts)))
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    $create = Ftp::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Ftp::create (array_intersect_key ($posts, Ftp::table ()->columns))); });

    if (!$create)
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => '新增失敗！',
          'posts' => $posts
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-sev', 'content' => '新增一筆 FTP 資訊。', 'desc' => '專案名稱為：「' . $obj->name . '」，網址為：「' . $obj->url . '」。', 'backup' => json_encode ($obj->to_array ())));
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
    if (isset ($posts['ftp_url'])) $posts['ftp_url'] = OAInput::post ('ftp_url', false);
    if (isset ($posts['ftp_account'])) $posts['ftp_account'] = OAInput::post ('ftp_account', false);
    if (isset ($posts['ftp_password'])) $posts['ftp_password'] = OAInput::post ('ftp_password', false);
    if (isset ($posts['admin_url'])) $posts['admin_url'] = OAInput::post ('admin_url', false);
    if (isset ($posts['admin_account'])) $posts['admin_account'] = OAInput::post ('admin_account', false);
    if (isset ($posts['admin_password'])) $posts['admin_password'] = OAInput::post ('admin_password', false);
    $is_api = isset ($posts['_type']) && ($posts['_type'] == 'api') ? true : false;

    if ($msg = $this->_validation ($posts))
      return $is_api ? $this->output_error_json ($msg) : redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    if ($columns = array_intersect_key ($posts, $this->obj->table ()->columns))
      foreach ($columns as $column => $value)
        $this->obj->$column = $value;
    
    $obj = $this->obj;
    $update = Ftp::transaction (function () use ($obj, $posts) { return $obj->save (); });

    if (!$update)
      return $is_api ? $this->output_error_json ('更新失敗！') : redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => '更新失敗！',
          'posts' => $posts
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-sev', 'content' => '修改一筆 FTP 資訊。', 'desc' => '專案名稱為：「' . $obj->name . '」，網址為：「' . $obj->url . '」。', 'backup' => json_encode ($obj->to_array ())));
    return $is_api ? $this->output_json ($obj->to_array ()) : redirect_message (array ($this->uri_1), array (
        '_flash_info' => '更新成功！'
      ));
  }
  public function destroy () {
    $obj = $this->obj;
    $backup = json_encode ($obj->to_array ());
    $delete = Ftp::transaction (function () use ($obj) { return $obj->destroy (); });

    if (!$delete)
      return redirect_message (array ($this->uri_1), array (
          '_flash_danger' => '刪除失敗！',
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-sev', 'content' => '刪除一筆 FTP 資訊。', 'desc' => '已經備份了刪除紀錄，細節可詢問工程師。', 'backup' => $backup));
    return redirect_message (array ($this->uri_1), array (
        '_flash_info' => '刪除成功！'
      ));
  }
  private function _validation (&$posts) {
    $keys = array ('name', 'url', 'ftp_url', 'ftp_account', 'ftp_password', 'admin_url', 'admin_account', 'admin_password', 'memo');

    $new_posts = array (); foreach ($posts as $key => $value) if (in_array ($key, $keys)) $new_posts[$key] = $value;
    $posts = $new_posts;

    if (isset ($posts['name']) && !($posts['name'] = trim ($posts['name']))) return '專案名稱格式錯誤或未填寫！';
    if (isset ($posts['url']) && !($posts['url'] = trim ($posts['url']))) return '網站網址格式錯誤或未填寫！';
    
    if (isset ($posts['ftp_url']) && ($posts['ftp_url'] = trim ($posts['ftp_url'])) && !is_string ($posts['ftp_url'])) return 'FTP 主機格式錯誤！';
    if (isset ($posts['ftp_account']) && ($posts['ftp_account'] = trim ($posts['ftp_account'])) && !is_string ($posts['ftp_account'])) return 'FTP 帳號格式錯誤！';
    if (isset ($posts['ftp_password']) && ($posts['ftp_password'] = trim ($posts['ftp_password'])) && !is_string ($posts['ftp_password'])) return 'FTP 密碼格式錯誤！';
    
    if (isset ($posts['admin_url']) && ($posts['admin_url'] = trim ($posts['admin_url'])) && !is_string ($posts['admin_url'])) return '管理頁網址格式錯誤！';
    if (isset ($posts['admin_account']) && ($posts['admin_account'] = trim ($posts['admin_account'])) && !is_string ($posts['admin_account'])) return '管理頁帳號格式錯誤！';
    if (isset ($posts['admin_password']) && ($posts['admin_password'] = trim ($posts['admin_password'])) && !is_string ($posts['admin_password'])) return '管理頁密碼格式錯誤！';
    
    if (isset ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) && !is_string ($posts['memo'])) return '備註格式錯誤！';
    
    return '';
  }
  private function _validation_must (&$posts) {
    if (!isset ($posts['name'])) return '沒有填寫 專案名稱！';
    if (!isset ($posts['url'])) return '沒有填寫 網站網址！';
    return '';
  }
}
