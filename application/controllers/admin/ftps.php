<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Ftps extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('ftp')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/ftps';
    $this->icon = 'icon-sev';
    $this->title = 'FTP';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Ftp::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'name' => array ('el' => 'input', 'text' => '專案名稱', 'sql' => 'name LIKE ?'),
        'url' => array ('el' => 'input', 'text' => '網站網址', 'sql' => 'url LIKE ?'),
        'content' => array ('el' => 'input', 'text' => '內容', 'sql' => 'content LIKE ?'),
        'memo' => array ('el' => 'input', 'text' => '備註', 'sql' => 'memo LIKE ?'),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Ftp', array ('order' => 'id DESC'));

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
    $posts['content'] = OAInput::post ('content', false);

    if (($msg = $this->_validation_create ($posts)) || (!Ftp::transaction (function () use (&$obj, $posts) {
      return verifyCreateOrm ($obj = Ftp::create (array_intersect_key ($posts, Ftp::table ()->columns)));
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '名稱為：「' . $obj->name . '」',
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
    $posts['content'] = OAInput::post ('content', false);
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Ftp::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title,
      '名稱為：「' . $obj->name . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->backup (true);

    if (!Ftp::transaction (function () use ($obj) { return $obj->destroy (); }))
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
    if (!(isset ($posts['name']) && is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '「' . $this->title . '專案名稱」格式錯誤！';
    if (isset ($posts['link']) && !(is_string ($posts['link']) && ($posts['link'] = trim ($posts['link'])))) $posts['link'] = '';
    if (isset ($posts['content']) && !(is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) $posts['content'] = '';
    if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $posts['memo'] = '';

    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
