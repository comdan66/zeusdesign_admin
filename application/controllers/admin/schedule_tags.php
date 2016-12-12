<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Schedule_tags extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();

    $this->uri_1 = 'admin/schedule-tags';
    $this->icon = 'icon-ta';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = ScheduleTag::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $columns = array ( 
        array ('key' => 'name', 'title' => '名稱', 'sql' => 'name LIKE ?'), 
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);
    OaModel::addConditions ($conditions, 'user_id = ?', User::current ()->id);

    $limit = 25;
    $total = ScheduleTag::count (array ('conditions' => $conditions));
    $objs = ScheduleTag::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'include' => array ('schedules'), 'order' => 'id DESC', 'conditions' => $conditions));

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

    $posts = OAInput::post ();
    
    if ($msg = $this->_validation_create ($posts))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    $posts['user_id'] = User::current ()->id;

    if (!ScheduleTag::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = ScheduleTag::create (array_intersect_key ($posts, ScheduleTag::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一項行程分類。',
      'desc' => '分類名稱為「' . $obj->name . '」，色碼：「#' . $obj->color . '」。',
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

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    if (!ScheduleTag::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一項行程分類。',
      'desc' => '分類名稱為「' . $obj->name . '」，色碼：「#' . $obj->color . '」。',
      'backup' => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->columns_val (true);

    if (!ScheduleTag::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！',));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一項行程分類。',
      'desc' => '已經備份了刪除紀錄，細節可詢問工程師。',
      'backup' => json_encode ($backup)));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '刪除成功！'));
  }

  private function _validation_create (&$posts) {
    if (!isset ($posts['name'])) return '沒有填寫 分類名稱！';
    if (!isset ($posts['color'])) return '沒有選擇 分類顏色！';
    if (!(is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '分類名稱 格式錯誤！';
    if (!(($posts['color'] = trim ($posts['color'])) && ($posts['color'] = preg_replace ('/#/', '', $posts['color'])))) return '分類顏色 格式錯誤！';
    return '';
  }

  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
