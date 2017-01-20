<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Demo_demo_images extends Admin_controller {
  private $uri_1 = null;
  private $uri_2 = null;
  private $parent = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('demo')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/demo';
    $this->uri_2 = 'images';
    $this->icon = 'icon-ims';

    if (!(($id = $this->uri->rsegments (3, 0)) && ($this->parent = Demo::find_by_id ($id))))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'sort')))
      if (!(($id = $this->uri->rsegments (4, 0)) && ($this->obj = DemoImage::find_by_id ($id))))
        return redirect_message (array ($this->uri_1, $this->parent_tag->id, $this->uri_2), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('uri_2', $this->uri_2)
         ->add_param ('parent', $this->parent)
         ->add_param ('now_url', base_url ($this->uri_1, $this->parent->id, $this->uri_2));
  }
  public function index ($id, $offset = 0) {
    $columns = array ( 
        array ('key' => 'message', 'title' => '內容', 'sql' => 'message LIKE ?'), 
        array ('key' => 'email', 'title' => 'E-Mail', 'sql' => 'email LIKE ?'), 
        array ('key' => 'name', 'title' => '名稱', 'sql' => 'name LIKE ?'), 
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ($this->parent->id, $this->uri_2, '%s'));
    $conditions = conditions ($columns, $configs);
    OaModel::addConditions ($conditions, 'demo_id = ?', $this->parent->id);

    $limit = 25;
    $total = DemoImage::count (array ('conditions' => $conditions));
    $objs = DemoImage::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'sort ASC', 'conditions' => $conditions));

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
      ));
  }
  public function create () {
    $parent = $this->parent;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $name = OAInput::file ('name');

    if ($msg = $this->_validation_create ($posts, $name))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    $posts['demo_id'] = $parent->id;
    $posts['sort'] = DemoImage::count (array ('conditions' => array ('demo_id = ?', $parent->id)));

    if (!DemoImage::transaction (function () use (&$obj, $posts, $name) { return verifyCreateOrm ($obj = DemoImage::create (array_intersect_key ($posts, DemoImage::table ()->columns))) && $obj->name->put ($name); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一項提案內容。',
      'desc' => '在提案名稱 “' . $parent->name . '” 下新增了一項內容。',
      'backup' => json_encode ($obj->columns_val ())));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '新增成功！'));
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
    $parent = $this->parent;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $name = OAInput::file ('name');
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts, $name, $obj))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!DemoImage::transaction (function () use ($obj, $posts, $name) { if (!$obj->save () || ($name && !$obj->name->put ($name))) return false; return true; }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一項提案內容。',
      'desc' => '在提案名稱 “' . $parent->name . '” 下修改了一項內容。',
      'backup' => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $parent = $this->parent;
    $backup = $obj->columns_val (true);

    if (!DemoImage::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一項提案內的內容。',
      'desc' => '在提案 “' . $parent->name . '” 下刪除了一項內容，已經備份了刪除紀錄，細節可詢問工程師。',
      'backup' => json_encode ($backup)));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '刪除成功！'));
  }

  public function sort ($tag_id, $obj_id, $sort) {
    $obj = $this->obj;
    $parent = $this->parent;

    if (!in_array ($sort, array ('up', 'down')))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_danger' => '排序失敗！'));

    OaModel::addConditions ($conditions, 'demo_id = ?', $parent->id);
    $total = DemoImage::count (array ('conditions' => $conditions));

    switch ($sort) {
      case 'down': $sort = $obj->sort; $obj->sort = $obj->sort + 1 >= $total ? 0 : $obj->sort + 1; break;
      case 'up': $sort = $obj->sort; $obj->sort = $obj->sort - 1 < 0 ? $total - 1 : $obj->sort - 1; break;
    }

    $change = array ();
    array_push ($change, array ('id' => $obj->id, 'old' => $sort, 'new' => $obj->sort));
    OaModel::addConditions ($conditions, 'sort = ?', $obj->sort);

    if (!DemoImage::transaction (function () use ($conditions, $obj, $sort, &$change) { if (($next = DemoImage::find ('one', array ('conditions' => $conditions))) && array_push ($change, array ('id' => $next->id, 'old' => $next->sort, 'new' => $sort))) { $next->sort = $sort; if (!$next->save ()) return false; } return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_danger' => '排序失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '調整了一項提案下內容的順序。',
      'desc' => '已經備份了調整紀錄，細節可詢問工程師。',
      'backup' => json_encode ($change)));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '排序成功！'));
  }
  private function _validation_create (&$posts, &$name) {
    if (!isset ($name)) return '沒有選擇 內容圖片！';
    if (!is_upload_image_format ($name, 20 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png'))) return '內容圖片 格式錯誤！';
    return '';
  }

  private function _validation_update (&$posts, &$name, $obj) {
    if (!((string)$obj->name || isset ($name))) return '沒有選擇 內容圖片！';
    if ($name && !is_upload_image_format ($name, 20 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png'))) return '內容圖片 格式錯誤！';
    return '';
  }
}
