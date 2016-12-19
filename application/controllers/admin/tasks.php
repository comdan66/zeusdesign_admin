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
        'user_ids' => isset ($posts['user_ids']) ? $posts['user_ids'] : array (),
        'files' => isset ($posts['files']) ? $posts['files'] : array (),
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['description'] = OAInput::post ('description', false);
    $files = OAInput::file ();
    $files = $this->_validation_file ('files', $posts, $files);

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

    foreach ($files as $file)
      TaskAttachment::transaction (function () use ($file, $obj) {
        return verifyCreateOrm ($attachment = TaskAttachment::create (array_intersect_key (array ('task_id' => $obj->id, 'title' => $file['title'], 'file' => '', 'size' => $file['file']['size']), TaskAttachment::table ()->columns))) && $attachment->file->put ($file['file']);
      });

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一項任務。',
      'desc' => '任務標題為：「' . $obj->title . '」。',
      'backup'  => json_encode ($obj->columns_val ())));

    $users = array_filter (($user_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'user_id', 'conditions' => array ('task_id = ?', $obj->id))), 'user_id')) ? User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $user_ids))) : array (), function ($user) { return $user->id != User::current ()->id; });

    Notification::send (
      $users,
      User::current ()->name . '指派給您一項新的任務。',
      base_url ('admin', 'my-tasks', $obj->id, 'show'));

    Mail::send (
      '宙斯任務「' . $obj->title . '」',
      'mail/task_create',
      array (
        'user' => $obj->user->name,
        'url' => base_url ('platform', 'mail', 'admin', 'my-tasks', $obj->id, 'show'),
        'detail' => array (array ('title' => '任務名稱：', 'value' => $obj->title), array ('title' => '任務內容：', 'value' => $obj->description))
      ), $users);

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'user_ids' => isset ($posts['user_ids']) ? $posts['user_ids'] : column_array ($this->obj->user_mappings, 'user_id'),
        'files' => isset ($posts['files']) ? $posts['files'] : array (),
      ));
  }
  public function update () {
    $obj = $this->obj;

    $_title = $this->obj->title;
    $_description = $this->obj->description;
    $_date_at = $this->obj->date_at->format ('Y-m-d');
    $_level = $this->obj->level;
    $_finish = $this->obj->finish;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));
    
    $posts = OAInput::post ();
    $posts['description'] = OAInput::post ('description', false);
    $files = OAInput::file ();
    $files = $this->_validation_file ('files', $posts, $files);
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    if (!Task::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    $add_attachments = $del_attachments = array ();
    $ori_ids = column_array ($obj->attachments, 'id');
    if (($del_ids = array_diff ($ori_ids, $posts['old_attachment_ids'])) && ($tmps = TaskAttachment::find ('all', array ('select' => 'id, title, file', 'conditions' => array ('id IN (?)', $del_ids))))) 
      foreach ($tmps as $attachment)
        TaskAttachment::transaction (function () use ($attachment, &$del_attachments) {
          return array_push ($del_attachments, $attachment) && $attachment->destroy ();
        });

    foreach ($files as $file)
      TaskAttachment::transaction (function () use ($file, $obj, &$add_attachments) {
        return verifyCreateOrm ($attachment = TaskAttachment::create (array_intersect_key (array ('task_id' => $obj->id, 'title' => $file['title'], 'file' => '', 'size' => $file['file']['size']), TaskAttachment::table ()->columns))) && $attachment->file->put ($file['file']) && array_push ($add_attachments, $attachment);
      });

    $ori_ids = column_array ($obj->user_mappings, 'user_id');

    $posts['user_ids'] = array_unique (array_merge ($posts['user_ids'], array ('id' => $obj->user_id)));

    $new_users = $del_users = array ();
    if (($del_ids = array_diff ($ori_ids, $posts['user_ids'])) && ($mappings = TaskUserMapping::find ('all', array ('select' => 'id, user_id, task_id', 'include' => array ('user'), 'conditions' => array ('task_id = ? AND user_id IN (?)', $obj->id, $del_ids)))))
      foreach ($mappings as $mapping)
        TaskUserMapping::transaction (function () use ($mapping, &$del_users) {
          
          if ($schedules = Schedule::find ('all', array ('conditions' => array ('user_id = ? AND task_id = ?', $mapping->user_id, $mapping->task_id))))
            foreach ($schedules as $schedule)
              if (!$schedule->destroy ())
                return false;

          array_push ($del_users, $mapping->user);

          return $mapping->destroy ();
        });
    
    Notification::send (
      $del_users,
      '任務「' . $obj->title . '」已經將任務刪除囉。');

    Mail::send (
      '宙斯任務「' . $obj->title . '」',
      'mail/task_delete',
      array (
        'user' => $obj->user->name,
        'email' => $obj->user->email,
        'url' => base_url ('platform', 'mail', 'admin'),
      ), $del_users);

    Schedule::update_from_task ($obj);
    $users = array_filter (($user_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'user_id', 'conditions' => array ('task_id = ?', $obj->id))), 'user_id')) ? User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $user_ids))) : array (), function ($user) { return $user->id != User::current ()->id; });

    if (($add_ids = array_diff ($posts['user_ids'], $ori_ids)) && ($users = User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $add_ids)))))
      foreach ($users as $user)
        TaskUserMapping::transaction (function () use ($user, $obj, &$new_users) {
          $create1 = verifyCreateOrm (TaskUserMapping::create (array_intersect_key (array ('user_id' => $user->id, 'task_id' => $obj->id), TaskUserMapping::table ()->columns)));
          $create2 = verifyCreateOrm (Schedule::create (array_intersect_key (array_merge (Schedule::bind_column_from_task ($obj), array ('user_id' => $user->id, 'schedule_tag_id' => 0, 'task_id' => $obj->id, 'sort' => 0)), Schedule::table ()->columns)));
          array_push ($new_users, $user);
          return $create1 && $create2;
        });
    
    Notification::send (
      $new_users,
      User::current ()->name . '指派給您一項新的任務。',
      base_url ('admin', 'my-tasks', $obj->id, 'show'));

    Mail::send (
      '宙斯任務「' . $obj->title . '」',
      'mail/task_create',
      array (
        'user' => $obj->user->name,
        'url' => base_url ('platform', 'mail', 'admin', 'my-tasks', $obj->id, 'show'),
        'detail' => array (array ('title' => '任務名稱：', 'value' => $obj->title), array ('title' => '任務內容：', 'value' => $obj->description))
      ), $new_users);

    $changes = array ();
    if ($obj->title !== $_title) array_push ($changes, '調整任務標題，由「' . $_title . '」修改成「' . $obj->title . '」');
    if ($obj->description !== $_description) array_push ($changes, '修改任務敘述內容');
    if ($obj->date_at->format ('Y-m-d') !== $_date_at) array_push ($changes, '調整任務日期，由「' . $_date_at . '」改到「' . $obj->date_at->format ('Y-m-d') . '」');
    if ($obj->level !== $_level) array_push ($changes, '調整任務優先權，從「' . Task::$levelNames[$_level] . '」調整成「' . Task::$levelNames[$obj->level] . '」');
    if ($obj->finish !== $_finish) array_push ($changes, '調整任務狀態，從「' . Task::$finishNames[$_finish] . '」調整成「' . Task::$finishNames[$obj->finish] . '」');
    if ($del_users || $new_users) array_push ($changes, '指派人員異動');
    if ($del_users) array_push ($changes, '移除 ' . implode (', ', column_array ($del_users, 'name')));
    if ($new_users) array_push ($changes, '加入 ' . implode (', ', column_array ($new_users, 'name')));
    if ($del_attachments) array_push ($changes, count ($del_attachments) == 1 ? '刪除附件檔案 ' . $del_attachments[0]->title : ('刪除了 ' . count ($del_attachments) . ' 個附件，分別是 ' . implode ('、', column_array ($del_attachments, 'title'))));
    if ($add_attachments) array_push ($changes, count ($add_attachments) == 1 ? '新增附件檔案 ' . $add_attachments[0]->title : ('新增了 ' . count ($add_attachments) . ' 個附件，分別是 ' . implode ('、', column_array ($add_attachments, 'title'))));

    if ($changes) {
      $posts = array (
        'action' => '更新了任務細項',
        'content' => implode ('，', $changes) . '。');

      if (TaskCommit::transaction (function () use (&$commit, $obj, $posts) { return verifyCreateOrm ($commit = TaskCommit::create (array_intersect_key (array_merge ($posts, array ('task_id' => $obj->id, 'user_id' => User::current ()->id)), TaskCommit::table ()->columns))); })) {
        Notification::send (
          $users,
          '任務「' . $obj->title . '」內容有更新囉。',
          base_url ('admin', 'my-tasks', $obj->id, 'show'));

        Mail::send (
          '宙斯任務「' . $obj->title . '」',
          'mail/task_update',
          array (
            'user' => $obj->user->name,
            'url' => base_url ('platform', 'mail', 'admin', 'my-tasks', $obj->id, 'show'),
            'detail' => array (
              array ('title' => '任務名稱：', 'value' => $obj->title),
              array ('title' => '任務內容：', 'value' => $obj->description)),
            'action' => $commit->action,
            'content' => $commit->content,
          ), $users);
      }
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
    $users = array_filter (($user_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'user_id', 'conditions' => array ('task_id = ?', $obj->id))), 'user_id')) ? User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $user_ids))) : array (), function ($user) { return $user->id != User::current ()->id; });

    if (!Task::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    Notification::send (
      $users,
      '任務「' . $obj->title . '」已經將任務刪除囉。');

    Mail::send (
      '宙斯任務「' . $obj->title . '」',
      'mail/task_delete',
      array (
        'user' => User::current ()->name,
        'email' => $obj->user->email,
        'url' => base_url ('platform', 'mail', 'admin', 'my-tasks'),
      ), $users);

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

    $users = array_filter (($user_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'user_id', 'conditions' => array ('task_id = ?', $obj->id))), 'user_id')) ? User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $user_ids))) : array (), function ($user) { return $user->id != User::current ()->id; });

    Notification::send (
      $users,
      '任務「' . $obj->title . '」目前已經被標示 “' . Task::$finishNames[$obj->finish] . '” 囉。',
      base_url ('admin', 'my-tasks', $obj->id, 'show'));

    Mail::send (
      '宙斯任務「' . $obj->title . '」',
      'mail/task_finish',
      array (
        'user' => $obj->user->name,
        'url' => base_url ('platform', 'mail', 'admin', 'my-tasks', $obj->id, 'show'),
        'detail' => array (array ('title' => '任務名稱：', 'value' => $obj->title), array ('title' => '任務狀態：', 'value' => Task::$finishNames[$obj->finish]))
      ), $users);

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
    if (!isset ($posts['level'])) return '沒有選擇 優先權！';

    if (!(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['user_id']))))) return '新增者 不存在！';
    if (!(is_array ($posts['user_ids']) && $posts['user_ids'] && User::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['user_ids']))))) return '沒有選擇 指派會員！';
    if (!(is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '任務標題 格式錯誤！';
    if (!(($posts['date_at'] = trim ($posts['date_at'])) && is_date ($posts['date_at']))) return '任務日期 格式錯誤！';
    if (!(is_numeric ($posts['finish'] = trim ($posts['finish'])) && in_array ($posts['finish'], array_keys (Task::$finishNames)))) return '是否完成 格式錯誤！';
    if (!(is_numeric ($posts['level'] = trim ($posts['level'])) && in_array ($posts['level'], array_keys (Task::$levelNames)))) return '優先權 格式錯誤！';

    $posts['description'] = isset ($posts['description']) && is_string ($posts['description']) && ($posts['description'] = trim ($posts['description'])) ? $posts['description'] : '';
    $posts['old_attachment_ids'] = isset ($posts['old_attachment_ids']) && is_array ($posts['old_attachment_ids']) && $posts['old_attachment_ids'] ? $posts['old_attachment_ids'] : array ();
    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
  private function _validation_file ($key, &$posts, &$files) {
    if (!isset ($posts[$key])) return array ();
    if (!isset ($files[$key])) return array ();
    if (count ($posts[$key]) != count ($files[$key])) return array ();

    $new = array ();
      foreach ($posts[$key] as $i => $post)
        if (($file = array ('name' => $files[$key][$i]['name']['name'], 'type' => $files[$key][$i]['type']['name'], 'tmp_name' => $files[$key][$i]['tmp_name']['name'], 'error' => $files[$key][$i]['error']['name'], 'size' => $files[$key][$i]['size']['name'])) && is_upload_file_format ($file, 10 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'zip')))
          array_push ($new, array ('title' => ($post['title'] = trim ($post['title'])) ? $post['title'] : $file['name'], 'file' => $file));

    return $new;
  }
}
