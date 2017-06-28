
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Tag_work_tags extends Admin_controller {
  private $uri_1 = null;
  private $uri_2 = null;
  private $parent = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('work')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/tag';
    $this->uri_2 = 'work-tags';
    $this->icon = 'icon-price-tags';

    if (!(($id = $this->uri->rsegments (3, 0)) && ($this->parent = WorkTag::find_by_id ($id))))
      return redirect_message (array ('admin', 'work-tags'), array ('_fd' => '找不到該筆資料。'));

    $this->title = '「' . $this->parent->name . '」的子分類';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (4, 0)) && ($this->obj = WorkTag::find_by_id ($id))))
        return redirect_message (array ($this->uri_1, $this->parent_tag->id, $this->uri_2), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('uri_2', $this->uri_2)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('parent', $this->parent)
         ->add_param ('_url', base_url ('admin', 'work-tags'));
  }

  public function index ($fid, $offset = 0) {
    $parent = $this->parent;

    $searches = array (
        'name' => array ('el' => 'input', 'text' => '名稱', 'sql' => 'name LIKE ?'),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ($parent->id, $this->uri_2, '%s'));
    $objs = conditions ($searches, $configs, $offset, 'WorkTag', array ('order' => 'sort DESC, id DESC'), function ($conditions) use ($parent) {
      OaModel::addConditions ($conditions, 'work_tag_id = ?', $parent->id);
      return $conditions;
    });

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
    $parent = $this->parent;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['work_tag_id'] = $parent->id;
    $posts['sort'] = (($posts['sort'] = WorkTag::first (array ('select' => 'sort', 'order' => 'sort DESC', 'conditions' => array ('work_tag_id = ?', $posts['work_tag_id'])))) ? $posts['sort']->sort : 0) + 1;

    if (($msg = $this->_validation_create ($posts)) || (!WorkTag::transaction (function () use (&$obj, $posts) {
      return verifyCreateOrm ($obj = WorkTag::create (array_intersect_key ($posts, WorkTag::table ()->columns)));
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '名稱為：「' . $obj->name . '」',
      $obj->backup ());

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_fi' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
      ));
  }
  public function update () {
    $parent = $this->parent;
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!WorkTag::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title,
      '名稱為：「' . $obj->name . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_fi' => '更新成功！'));
  }

  public function destroy () {
    $parent = $this->parent;
    $obj = $this->obj;
    $backup = $obj->backup (true);

    if (!WorkTag::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_fd' => '刪除失敗！'));

    UserLog::logWrite (
      $this->icon,
      '刪除一項' . $this->title,
      '已經備份了刪除紀錄，細節可詢問工程師',
      $backup);

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_fi' => '刪除成功！'));
  }
  public function sort ($fid = 0, $offset = 0) {
    $parent = $this->parent;

    $searches = array ();
    $configs = array_merge (explode ('/', $this->uri_1), array ($parent->id, $this->uri_2, 'sort', '%s'));
    $objs = conditions ($searches, $configs, $offset, 'WorkTag', array ('order' => 'sort DESC, id DESC'), function ($conditions) use ($parent) {
      OaModel::addConditions ($conditions, 'work_tag_id = ?', $parent->id);
      return $conditions;
    }, 0);

    UserLog::logRead (
      $this->icon,
      '檢視了旗幟排序');

    return $this->load_view (array (
        'objs' => $objs,
        'total' => $offset,
        'searches' => $searches,
        'pagination' => $this->_get_pagination ($configs),
      ));
  }
  public function sort_update ($offset = 0) {
    $parent = $this->parent;
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'sort'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    
    $validation = function (&$posts) {
      return !(isset ($posts['ids']) && is_array ($posts['ids'])) ? '「排序」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'sort'), array ('_fd' => $msg, 'posts' => $posts));

    $objs = array_combine (column_array ($objs = WorkTag::find ('all', array ('select' => 'id, sort, updated_at', 'conditions' => array ('id IN (?)', $posts['ids'] ? $posts['ids'] : array (0)))), 'id'), $objs);
    $c = count ($objs);
    $backup = column_array ($objs, 'sort');

    foreach ($posts['ids'] as $sort => $id)
      if (isset ($objs[$id]) && ($objs[$id]->sort = $c - $sort) && !$objs[$id]->save ())
        return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'sort'), array ('_fd' => '排序錯誤。', 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '調整' . $this->title . '排序',
      '調整細節記錄可詢問工程師',
      array ('id:sort', $backup, column_array ($objs, 'sort')));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'sort'), array ('_fi' => '排序成功。'));
  }
  private function _validation_create (&$posts) {
    if (!(isset ($posts['name']) && is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '「' . $this->title . '名稱」格式錯誤！';

    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
