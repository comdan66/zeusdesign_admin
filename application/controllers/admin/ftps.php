<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Ftps extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('project')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/ftps';
    $this->icon = 'icon-sev';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Ftp::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
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
    $objs = Ftp::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
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
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post (null, false);
    
    if ($msg = $this->_validation_create ($posts))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if (! Ftp::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Ftp::create (array_intersect_key ($posts, Ftp::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一筆 FTP 資訊。',
      'desc' => '專案名稱為：「' . $obj->name . '」，網址為：「' . $obj->url . '」。',
      'backup' => json_encode ($obj->columns_val ())));
    
    return redirect_message (array ($this->uri_1), array ('_flash_info' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post (null, false);
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    if (!Ftp::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一筆 FTP 資訊。',
      'desc' => '專案名稱為：「' . $obj->name . '」，網址為：「' . $obj->url . '」。',
      'backup' => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));
    
    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }
  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->columns_val (true);

    if (!Ftp::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一筆 FTP 資訊。',
      'desc' => '已經備份了刪除紀錄，細節可詢問工程師。',
      'backup' => json_encode ($backup)));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '刪除成功！'));
  }
  private function _validation_create (&$posts) {
    if (!isset ($posts['name'])) return '沒有填寫 專案名稱！';
    if (!isset ($posts['url'])) return '沒有填寫 網站網址！';

    if (!(is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '專案名稱 格式錯誤！';
    if (!(is_string ($posts['url']) && ($posts['url'] = trim ($posts['url'])))) return '網站網址 格式錯誤！';
    
    $posts['ftp_url'] = isset ($posts['ftp_url']) && is_string ($posts['ftp_url']) && ($posts['ftp_url'] = trim ($posts['ftp_url'])) ? $posts['ftp_url'] : '';
    $posts['ftp_account'] = isset ($posts['ftp_account']) && is_string ($posts['ftp_account']) && ($posts['ftp_account'] = trim ($posts['ftp_account'])) ? $posts['ftp_account'] : '';
    $posts['ftp_password'] = isset ($posts['ftp_password']) && is_string ($posts['ftp_password']) && ($posts['ftp_password'] = trim ($posts['ftp_password'])) ? $posts['ftp_password'] : '';
    $posts['admin_url'] = isset ($posts['admin_url']) && is_string ($posts['admin_url']) && ($posts['admin_url'] = trim ($posts['admin_url'])) ? $posts['admin_url'] : '';
    $posts['admin_account'] = isset ($posts['admin_account']) && is_string ($posts['admin_account']) && ($posts['admin_account'] = trim ($posts['admin_account'])) ? $posts['admin_account'] : '';
    $posts['admin_password'] = isset ($posts['admin_password']) && is_string ($posts['admin_password']) && ($posts['admin_password'] = trim ($posts['admin_password'])) ? $posts['admin_password'] : '';
    $posts['memo'] = isset ($posts['memo']) && is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) ? $posts['memo'] : '';
    
    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
