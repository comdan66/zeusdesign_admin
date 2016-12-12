<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Tag_work_tags extends Admin_controller {
  private $uri_1 = null;
  private $uri_2 = null;
  private $parent = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('work')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/tag';
    $this->uri_2 = 'work-tags';
    $this->icon = 'icon-ta';

    if (!(($id = $this->uri->rsegments (3, 0)) && ($this->parent = WorkTag::find_by_id ($id))))
      return redirect_message (array ('work-tags'), array ('_flash_danger' => '找不到該筆資料。'));

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'sort')))
      if (!(($id = $this->uri->rsegments (4, 0)) && ($this->obj = WorkTag::find_by_id ($id))))
        return redirect_message (array ($this->uri_1, $this->parent_tag->id, $this->uri_2), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('uri_2', $this->uri_2)
         ->add_param ('parent', $this->parent)
         ->add_param ('now_url', base_url ('admin', 'work-tags'));
  }
  public function index ($id, $offset = 0) {
    $columns = array ( 
        array ('key' => 'name', 'title' => '名稱', 'sql' => 'name LIKE ?'), 
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ($this->parent->id, $this->uri_2, '%s'));
    $conditions = conditions ($columns, $configs);
    OaModel::addConditions ($conditions, 'work_tag_id = ?', $this->parent->id);

    $limit = 25;
    $total = WorkTag::count (array ('conditions' => $conditions));
    $objs = WorkTag::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'sort DESC', 'include' => array ('mappings'), 'conditions' => $conditions));

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
    $parent = $this->parent;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    
    if ($msg = $this->_validation_create ($posts))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    $posts['sort'] = WorkTag::count (array ('conditions' => array ('work_tag_id = ?', $parent->id)));
    $posts['work_tag_id'] = $parent->id;

    if (!WorkTag::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = WorkTag::create (array_intersect_key ($posts, WorkTag::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一項作品分類內的子分類。',
      'desc' => '在作品分類 “' . $parent->name . '” 下新增了一項子分類，其名稱為「' . $obj->name . '」。',
      'backup' => json_encode ($obj->columns_val ())));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '新增成功！'));
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

    if (!WorkTag::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一項作品分類內的子分類。',
      'desc' => '在作品分類 “' . $parent->name . '” 下修改了一項子分類，其名稱為「' . $obj->name . '」。',
      'backup' => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $parent = $this->parent;
    $backup = $obj->columns_val (true);

    if (!WorkTag::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一項作品分類內的子分類。',
      'desc' => '在作品分類 “' . $parent->name . '” 下刪除了一項子分類，已經備份了刪除紀錄，細節可詢問工程師。',
      'backup' => json_encode ($backup)));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '刪除成功！'));
  }

  public function sort ($tag_id, $obj_id, $sort) {
    $obj = $this->obj;
    $parent = $this->parent;

    if (!in_array ($sort, array ('up', 'down')))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_danger' => '排序失敗！'));

    OaModel::addConditions ($conditions, 'work_tag_id = ?', $parent->id);
    $total = WorkTag::count (array ('conditions' => $conditions));

    switch ($sort) {
      case 'up': $sort = $obj->sort; $obj->sort = $obj->sort + 1 >= $total ? 0 : $obj->sort + 1; break;
      case 'down': $sort = $obj->sort; $obj->sort = $obj->sort - 1 < 0 ? $total - 1 : $obj->sort - 1; break;
    }

    $change = array ();
    array_push ($change, array ('id' => $obj->id, 'old' => $sort, 'new' => $obj->sort));
    OaModel::addConditions ($conditions, 'sort = ?', $obj->sort);

    if (!WorkTag::transaction (function () use ($conditions, $obj, $sort, &$change) { if (($next = WorkTag::find ('one', array ('conditions' => $conditions))) && array_push ($change, array ('id' => $next->id, 'old' => $next->sort, 'new' => $sort))) { $next->sort = $sort; if (!$next->save ()) return false; } return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_danger' => '排序失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '調整了一項作品分類下子分類的順序。',
      'desc' => '已經備份了調整紀錄，細節可詢問工程師。',
      'backup' => json_encode ($change)));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '排序成功！'));
  }
  private function _validation_create (&$posts) {
    if (!isset ($posts['name'])) return '沒有填寫 分類名稱！';
    if (!(is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '分類名稱 格式錯誤！';
    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
