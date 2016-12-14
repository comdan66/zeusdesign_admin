<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Tasks extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('project')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/tasks';
    $this->icon = 'icon-shield';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'finish')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Task::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  private function _search_columns () {
    return array ( 
        array ('key' => 'title', 'title' => '標題', 'sql' => 'title LIKE ?'), 
        array ('key' => 'description', 'title' => '敘述', 'sql' => 'description LIKE ?'), 
      );
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = Task::count (array ('conditions' => $conditions));
    $objs = Task::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'include' => array ('user'), 'conditions' => $conditions));

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
        'user_ids' => isset ($posts['user_ids']) ? $posts['user_ids'] : array ()
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['description'] = OAInput::post ('description', false);

    if ($msg = $this->_validation_create ($posts))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if (!Task::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Task::create (array_intersect_key ($posts, Task::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));
    
    $posts['user_ids'] = array_unique (array_merge ($posts['user_ids'], array ('id' => $obj->user_id)));
  
    foreach ($posts['user_ids'] as $user_id)
      TaskUserMapping::transaction (function () use ($user_id, $obj) {
        $create1 = verifyCreateOrm (TaskUserMapping::create (array_intersect_key (array ('user_id' => $user_id, 'task_id' => $obj->id), TaskUserMapping::table ()->columns)));
        $create2 = verifyCreateOrm (Schedule::create (array_intersect_key (array_merge (Schedule::bind_column_from_task ($obj), array ('user_id' => $user_id, 'schedule_tag_id' => 0, 'task_id' => $obj->id, 'sort' => 0)), Schedule::table ()->columns)));
        return $create1 && $create2;
      });

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一項任務。',
      'desc' => '任務標題為：「' . $obj->title . '」。',
      'backup'  => json_encode ($obj->columns_val ())));

    $this->load->library ('fb');
    $content = Mail::renderContent ('mail/create_task', array (
        'user' => $obj->user->name,
        'url' => Fb::loginUrl ('platform', 'fb_sign_in', 'admin', 'my-tasks', $obj->id, 'show'),
        'detail' => array (array ('title' => '任務名稱：', 'value' => $obj->title), array ('title' => '任務內容：', 'value' => $obj->description))
      ));
    $users = ($user_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'user_id', 'conditions' => array ('task_id = ?', $obj->id))), 'user_id')) ? User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $user_ids))) : array ();
    Mail::send ('指派了一項任務「' . $obj->title . '」', $content, $users);

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'user_ids' => isset ($posts['user_ids']) ? $posts['user_ids'] : column_array ($this->obj->user_mappings, 'user_id'),
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));
    
    $posts = OAInput::post ();
    $posts['description'] = OAInput::post ('description', false);
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    if (!Task::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    $ori_ids = column_array ($obj->user_mappings, 'user_id');

    $posts['user_ids'] = array_unique (array_merge ($posts['user_ids'], array ('id' => $obj->user_id)));

    if (($del_ids = array_diff ($ori_ids, $posts['user_ids'])) && ($mappings = TaskUserMapping::find ('all', array ('select' => 'id, user_id, task_id', 'conditions' => array ('task_id = ? AND user_id IN (?)', $obj->id, $del_ids)))))
      foreach ($mappings as $mapping)
        TaskUserMapping::transaction (function () use ($mapping) {
          if ($schedules = Schedule::find ('all', array ('conditions' => array ('user_id = ? AND task_id = ?', $mapping->user_id, $mapping->task_id))))
            foreach ($schedules as $schedule)
              if (!$schedule->destroy ())
                return false;

          return $mapping->destroy ();
        });

    Schedule::update_from_task ($obj);

    $this->load->library ('fb');
    $content = Mail::renderContent ('mail/update_task', array (
        'user' => $obj->user->name,
        'url' => Fb::loginUrl ('platform', 'fb_sign_in', 'admin', 'my-tasks', $obj->id, 'show'),
        'detail' => array (array ('title' => '任務名稱：', 'value' => $obj->title), array ('title' => '任務內容：', 'value' => $obj->description))
      ));
    $users = ($user_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'user_id', 'conditions' => array ('task_id = ?', $obj->id))), 'user_id')) ? User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $user_ids))) : array ();
    Mail::send ('更新了任務「' . $obj->title . '」', $content, $users);

    $new_users = array ();
    if (($add_ids = array_diff ($posts['user_ids'], $ori_ids)) && ($users = User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $add_ids)))))
      foreach ($users as $user)
        TaskUserMapping::transaction (function () use ($user, $obj, &$new_users) {
          $create1 = verifyCreateOrm (TaskUserMapping::create (array_intersect_key (array ('user_id' => $user->id, 'task_id' => $obj->id), TaskUserMapping::table ()->columns)));
          $create2 = verifyCreateOrm (Schedule::create (array_intersect_key (array_merge (Schedule::bind_column_from_task ($obj), array ('user_id' => $user->id, 'schedule_tag_id' => 0, 'task_id' => $obj->id, 'sort' => 0)), Schedule::table ()->columns)));
          array_push ($new_users, $user);
          return $create1 && $create2;
        });
    
    if ($new_users) {
      $this->load->library ('fb');
      $content = Mail::renderContent ('mail/create_task', array (
          'user' => $obj->user->name,
          'url' => Fb::loginUrl ('platform', 'fb_sign_in', 'admin', 'my-tasks', $obj->id, 'show'),
          'detail' => array (array ('title' => '任務名稱：', 'value' => $obj->title), array ('title' => '任務內容：', 'value' => $obj->description))
        ));
      Mail::send ('指派了一項任務「' . $obj->title . '」', $content, $new_users);
    }

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一項任務。',
      'desc' => '任務標題為：「' . $obj->title . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }
  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->columns_val (true);

    if (!Task::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一項任務。',
      'desc' => '已經備份了刪除紀錄，細節可詢問工程師。',
      'backup'  => json_encode ($backup)));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '刪除成功！'));
  }

  public function finish () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);
    
    $validation = function (&$posts) {
      if (!isset ($posts['finish'])) return '沒有選擇 是否完成！';
      if (!(is_numeric ($posts['finish'] = trim ($posts['finish'])) && in_array ($posts['finish'], array_keys (Task::$finishNames)))) return '是否完成 格式錯誤！';
      return '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Task::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    Schedule::update_from_task ($obj);

    $this->load->library ('fb');
    $content = Mail::renderContent ('mail/finish_task', array (
        'user' => $obj->user->name,
        'url' => Fb::loginUrl ('platform', 'fb_sign_in', 'admin', 'my-tasks', $obj->id, 'show'),
        'detail' => array (array ('title' => '任務名稱：', 'value' => $obj->title), array ('title' => '任務狀態：', 'value' => Task::$finishNames[$obj->finish]))
      ));
    $users = ($user_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'user_id', 'conditions' => array ('task_id = ?', $obj->id))), 'user_id')) ? User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $user_ids))) : array ();
    Mail::send ('任務「' . $obj->title . '」被設定為 “' . Task::$finishNames[$obj->finish] . '”', $content, $users);

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '將一項任務設定成 「' . Task::$finishNames[$obj->finish] . '」。',
      'desc' => '將任務 “' . $obj->title . '” 設定成 「' . Task::$finishNames[$obj->finish] . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json ($obj->finish == Task::IS_FINISHED);
  }
  private function _validation_create (&$posts) {
    if (!isset ($posts['user_id'])) return '沒有選擇 新增者！';
    if (!isset ($posts['user_ids'])) return '沒有選擇 指派會員！';
    if (!isset ($posts['title'])) return '沒有填寫 任務標題！';
    if (!isset ($posts['date_at'])) return '沒有填寫 任務日期！';
    if (!isset ($posts['finish'])) return '沒有選擇 是否完成！';

    if (!(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['user_id']))))) return '新增者 不存在！';
    if (!(is_array ($posts['user_ids']) && $posts['user_ids'] && User::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['user_ids']))))) return '沒有選擇 指派會員！';
    if (!(is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '任務標題 格式錯誤！';
    if (!(($posts['date_at'] = trim ($posts['date_at'])) && is_date ($posts['date_at']))) return '任務日期 格式錯誤！';
    if (!(is_numeric ($posts['finish'] = trim ($posts['finish'])) && in_array ($posts['finish'], array_keys (Task::$finishNames)))) return '是否完成 格式錯誤！';

    $posts['description'] = isset ($posts['description']) && is_string ($posts['description']) && ($posts['description'] = trim ($posts['description'])) ? $posts['description'] : '';
    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
