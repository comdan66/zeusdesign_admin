<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Tasks extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('project')))
      return redirect_message (array ('admin'), array (
            '_flash_danger' => '您的權限不足，或者頁面不存在。'
          ));

    $this->uri_1 = 'admin/tasks';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Task::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array (
            '_flash_danger' => '找不到該筆資料。'
          ));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  private function _search_columns () {
    return array ( 
        array ('key' => 'title',    'title' => '標題', 'sql' => 'title LIKE ?'), 
        array ('key' => 'description',    'title' => '敘述', 'sql' => 'description LIKE ?'), 
      );
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = Task::count (array ('conditions' => $conditions));
    $objs = Task::find ('all', array (
        'offset' => $offset < $total ? $offset : 0,
        'limit' => $limit,
        'order' => 'id DESC',
        'include' => array ('user'),
        'conditions' => $conditions
      ));

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
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => '非 POST 方法，錯誤的頁面請求。'
        ));

    $posts = OAInput::post ();
    if (isset ($posts['description'])) $posts['description'] = OAInput::post ('description', false);

    if (!$post_tag_ids = isset ($posts['tag_ids']) && ($posts['tag_ids'] = array_filter ($posts['tag_ids'], function ($f) { return $f && is_numeric ($f);})) ? column_array (User::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['tag_ids']))), 'id') : array ())
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => '請選擇被指派會員',
          'posts' => $posts
        ));

    if (($msg = $this->_validation_must ($posts)) || ($msg = $this->_validation ($posts)))
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    $create = Task::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Task::create (array_intersect_key ($posts, Task::table ()->columns))); });

    if (!$create)
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => '新增失敗！',
          'posts' => $posts
        ));

    if ($post_tag_ids && ($user_ids = column_array (User::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $post_tag_ids))), 'id')))
      foreach ($user_ids as $user_id)
        TaskUserMapping::transaction (function () use ($user_id, $obj) {
          return verifyCreateOrm (TaskUserMapping::create (array_intersect_key (array ('task_id' => $obj->id, 'user_id' => $user_id), TaskUserMapping::table ()->columns)));
        });

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ruler', 'content' => '新增一項任務。', 'desc' => '任務標題為：「' . $obj->title . '」。', 'backup' => json_encode ($obj->to_array ())));
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
    if (isset ($posts['description'])) $posts['description'] = OAInput::post ('description', false);
    $is_api = isset ($posts['_type']) && ($posts['_type'] == 'api') ? true : false;

    if (!($is_api || ($post_tag_ids = isset ($posts['tag_ids']) && ($posts['tag_ids'] = array_filter ($posts['tag_ids'], function ($f) { return $f && is_numeric ($f);})) ? column_array (User::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['tag_ids']))), 'id') : array ())))
      return redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => '請選擇被指派會員',
          'posts' => $posts
        ));

    if ($msg = $this->_validation ($posts))
      return $is_api ? $this->output_error_json ($msg) : redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    if ($columns = array_intersect_key ($posts, $this->obj->table ()->columns))
      foreach ($columns as $column => $value)
        $this->obj->$column = $value;
    
    $obj = $this->obj;
    $update = Task::transaction (function () use ($obj, $posts) { return $obj->save (); });

    if (!$update)
      return $is_api ? $this->output_error_json ('更新失敗！') : redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => '更新失敗！',
          'posts' => $posts
        ));
    if ($is_api)
      return $is_api ? $this->output_json ($obj->to_array ()) : redirect_message (array ($this->uri_1), array (
          '_flash_info' => '更新成功！'
        ));

    $ori_ids = column_array ($obj->task_mappings, 'user_id');

    if (($del_ids = array_diff ($ori_ids, $post_tag_ids)) && ($mappings = TaskUserMapping::find ('all', array ('select' => 'id, user_id', 'conditions' => array ('task_id = ? AND user_id IN (?)', $obj->id, $del_ids)))))
      foreach ($mappings as $mapping)
        TaskUserMapping::transaction (function () use ($mapping) {
          return $mapping->destroy ();
        });

    if (($add_ids = array_diff ($post_tag_ids, $ori_ids)) && ($users = User::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $add_ids)))))
      foreach ($users as $user)
        TaskUserMapping::transaction (function () use ($user, $obj) {
          return verifyCreateOrm (TaskUserMapping::create (Array_intersect_key (array ('user_id' => $user->id, 'task_id' => $obj->id), TaskUserMapping::table ()->columns)));
        });

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ruler', 'content' => '修改一項任務。', 'desc' => '任務標題為：「' . $obj->title . '」。', 'backup' => json_encode ($obj->to_array ())));
    return $is_api ? $this->output_json ($obj->to_array ()) : redirect_message (array ($this->uri_1), array (
        '_flash_info' => '更新成功！'
      ));
  }
  public function destroy () {
    $obj = $this->obj;
    $backup = json_encode ($obj->to_array ());
    $delete = Task::transaction (function () use ($obj) { return $obj->destroy (); });

    if (!$delete)
      return redirect_message (array ($this->uri_1), array (
          '_flash_danger' => '刪除失敗！',
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ruler', 'content' => '刪除一項任務。', 'desc' => '已經備份了刪除紀錄，細節可詢問工程師。', 'backup' => $backup));
    return redirect_message (array ($this->uri_1), array (
        '_flash_info' => '刪除成功！'
      ));
  }
  private function _validation (&$posts) {
    $keys = array ('user_id', 'title', 'description', 'date_at', 'finish');

    $new_posts = array (); foreach ($posts as $key => $value) if (in_array ($key, $keys)) $new_posts[$key] = $value;
    $posts = $new_posts;

    if (isset ($posts['user_id']) && !(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find_by_id ($posts['user_id']))) return '新增者 ID 格式錯誤或未填寫！';
    if (isset ($posts['title']) && !($posts['title'] = trim ($posts['title']))) return '標題格式錯誤或未填寫！';
    if (isset ($posts['description']) && !($posts['description'] = trim ($posts['description']))) return '內容格式錯誤或未填寫！';
    if (isset ($posts['date_at']) && !($posts['date_at'] = trim ($posts['date_at']))) return '日期格式錯誤！';
    if (isset ($posts['finish']) && !(is_numeric ($posts['finish'] = trim ($posts['finish'])) && in_array ($posts['finish'], array_keys (Task::$finishNames)))) return '是否完成格式錯誤！';
    return '';
  }
  private function _validation_must (&$posts) {
    if (!isset ($posts['user_id'])) return '沒有填寫 新增者！';
    if (!isset ($posts['title'])) return '沒有填寫 標題！';
    if (!isset ($posts['description'])) return '沒有填寫 內容！';
    if (!isset ($posts['date_at'])) return '沒有填寫 日期！';
    return '';
  }
}
