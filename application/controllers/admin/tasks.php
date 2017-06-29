<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Tasks extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;
  private $accept = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('task')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/tasks';
    $this->icon = 'icon-shield';
    $this->title = '任務';
    $this->accept = array ('gif', 'jpeg', 'jpg', 'png', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'zip');

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'status', 'show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Task::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'user_id[]' => array ('el' => 'checkbox', 'text' => '擁有者', 'sql' => 'user_id IN (?)', 'items' => array_map (function ($u) { return array ('text' => $u->name, 'value' => $u->id); }, User::all ())),
        'title'     => array ('el' => 'input', 'text' => '標題', 'sql' => 'title LIKE ?'),
        'content'     => array ('el' => 'input', 'text' => '內容', 'sql' => 'content LIKE ?'),
        'status'    => array ('el' => 'select', 'text' => '是否完成', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => Task::$statusNames[$t], 'value' => $t,);}, array_keys (Task::$statusNames))),
        'level'    => array ('el' => 'select', 'text' => '優先權', 'sql' => 'level = ?', 'items' => array_map (function ($t) { return array ('text' => Task::$levelNames[$t], 'value' => $t,);}, array_keys (Task::$levelNames))),
        'date'     => array ('el' => 'input', 'text' => '日期', 'sql' => 'date LIKE ?'),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Task', array ('order' => 'id DESC', 'include' => array ('user', 'user_mappings')));

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
    $user_ids = isset ($posts['user_ids']) ? $posts['user_ids'] : array ();
    $attachments = isset ($posts['attachments']) ? $posts['attachments'] : array ();

    $row_muti = array (
        array ('type' => 'text', 'name' => 'attachments', 'key' => 'title', 'placeholder' => '標題'),
        array ('type' => 'file', 'name' => 'attachments', 'key' => 'file', 'accept' => '.' . implode (', .', $this->accept)),
      );

    return $this->load_view (array (
        'posts' => $posts,
        'user_ids' => $user_ids,
        'attachments' => $attachments,
        'row_muti' => $row_muti,
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    
    $files = OAInput::file ();
    $files = $this->_validation_file ('attachments', $posts, $files, 'file');

    if (($msg = $this->_validation_create ($posts)) || (!Task::transaction (function () use (&$obj, $posts) {
        return verifyCreateOrm ($obj = Task::create (array_intersect_key ($posts, Task::table ()->columns)));
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    $posts['user_ids'] = array_unique (array_merge ($posts['user_ids'], array ($obj->user_id)));
    foreach ($posts['user_ids'] as $user_id)
      TaskUserMapping::transaction (function () use ($user_id, $obj) {
        return verifyCreateOrm (TaskUserMapping::create (array_intersect_key (array ('user_id' => $user_id, 'task_id' => $obj->id), TaskUserMapping::table ()->columns)));
      });

    foreach ($files as $file)
      TaskAttachment::transaction (function () use ($file, $obj) {
        return verifyCreateOrm ($attachment = TaskAttachment::create (array_intersect_key (array ('task_id' => $obj->id, 'title' => $file['title'], 'file' => '', 'size' => $file['file']['size']), TaskAttachment::table ()->columns))) && $attachment->file->put ($file['file']);
      });

    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '標題名稱為：「' . $obj->title . '」' . ($obj->content ? '，內容是：「' . $obj->mini_content () . '」' : ''),
      $obj->backup ());

    return redirect_message (array ($this->uri_1), array ('_fi' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);
    $user_ids = isset ($posts['user_ids']) ? $posts['user_ids'] : column_array ($this->obj->user_mappings, 'user_id');
    $attachments = isset ($posts['attachments']) ? $posts['attachments'] : array ();

    $row_muti = array (
        array ('type' => 'text', 'name' => 'attachments', 'key' => 'title', 'placeholder' => '標題'),
        array ('type' => 'file', 'name' => 'attachments', 'key' => 'file', 'accept' => '.' . implode (', .', $this->accept)),
      );

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'user_ids' => $user_ids,
        'attachments' => $attachments,
        'row_muti' => $row_muti,
      ));
  }
  public function update () {
    $obj = $this->obj;

    $_title = $obj->title;
    $_content = $obj->content;
    $_date = $obj->date->format ('Y-m-d');
    $_level = $obj->level;
    $_status = $obj->status;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    
    $files = OAInput::file ();
    $files = $this->_validation_file ('attachments', $posts, $files, 'file');

    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Task::transaction (function () use ($obj) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));


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

    $posts['user_ids'] = array_unique (array_merge ($posts['user_ids'], array ($obj->user_id)));

    $new_users = $del_users = array ();
    if (($del_ids = array_diff ($ori_ids, $posts['user_ids'])) && ($mappings = TaskUserMapping::find ('all', array ('select' => 'id, user_id, task_id', 'include' => array ('user'), 'conditions' => array ('task_id = ? AND user_id IN (?)', $obj->id, $del_ids)))))
      foreach ($mappings as $mapping)
        TaskUserMapping::transaction (function () use ($mapping, &$del_users) {
          array_push ($del_users, $mapping->user);
          return $mapping->destroy ();
        });

    $users = array_filter (($user_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'user_id', 'conditions' => array ('task_id = ?', $obj->id))), 'user_id')) ? User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $user_ids))) : array (), function ($user) { return $user->id != User::current ()->id; });
    if (($add_ids = array_diff ($posts['user_ids'], $ori_ids)) && ($users = User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $add_ids)))))
      foreach ($users as $user)
        TaskUserMapping::transaction (function () use ($user, $obj, &$new_users) {
          array_push ($new_users, $user);
          return verifyCreateOrm (TaskUserMapping::create (array_intersect_key (array ('user_id' => $user->id, 'task_id' => $obj->id), TaskUserMapping::table ()->columns)));
        });
      

    $changes = array ();
    if ($obj->title !== $_title) array_push ($changes, '調整任務標題，由「' . $_title . '」修改成「' . $obj->title . '」');
    if ($obj->content !== $_content) array_push ($changes, '修改任務敘述內容');
    if ($obj->date->format ('Y-m-d') !== $_date) array_push ($changes, '調整任務日期，由「' . $_date . '」改到「' . $obj->date->format ('Y-m-d') . '」');
    if ($obj->level !== $_level) array_push ($changes, '調整任務優先權，從「' . Task::$levelNames[$_level] . '」調整成「' . Task::$levelNames[$obj->level] . '」');
    if ($obj->status !== $_status) array_push ($changes, '調整任務狀態，從「' . Task::$statusNames[$_status] . '」調整成「' . Task::$statusNames[$obj->status] . '」');

    if ($del_users || $new_users) array_push ($changes, '指派人員異動');
    if ($del_users) array_push ($changes, '移除 ' . implode (', ', column_array ($del_users, 'name')));
    if ($new_users) array_push ($changes, '加入 ' . implode (', ', column_array ($new_users, 'name')));
    if ($del_attachments) array_push ($changes, count ($del_attachments) == 1 ? '刪除附件檔案 ' . $del_attachments[0]->title : ('刪除了 ' . count ($del_attachments) . ' 個附件，分別是 ' . implode ('、', column_array ($del_attachments, 'title', function ($v) { return '「' . $v . '」'; }))));
    if ($add_attachments) array_push ($changes, count ($add_attachments) == 1 ? '新增附件檔案 ' . $add_attachments[0]->title : ('新增了 ' . count ($add_attachments) . ' 個附件，分別是 ' . implode ('、', column_array ($add_attachments, 'title', function ($v) { return '「' . $v . '」'; }))));

    if ($changes) {
      $posts = array (
        'action' => '更新了任務細項',
        'content' => implode ('，', $changes) . '。');

      if (TaskCommit::transaction (function () use (&$commit, $obj, $posts) { return verifyCreateOrm ($commit = TaskCommit::create (array_intersect_key (array_merge ($posts, array ('task_id' => $obj->id, 'user_id' => User::current ()->id, 'file' => '', 'size' => 0)), TaskCommit::table ()->columns))); })) {
        
      }
    }

    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title . '',
      '標題名稱為：「' . $obj->title . '」' . ($obj->content ? '，內容是：「' . $obj->mini_content () . '」' : ''),
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->backup (true);

    if (!Task::transaction (function () use ($obj) { return $obj->destroy (); }))
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
  public function status () {
    $obj = $this->obj;
    $_status = $obj->status;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    $validation = function (&$posts) {
      return !(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Task::STATUS_2 : Task::STATUS_1) && in_array ($posts['status'], array_keys (Task::$statusNames))) ? '「設定上下架」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Task::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return $this->output_error_json ('更新失敗！');
    
    $changes = array ();
    array_push ($changes, '從列表調整任務狀態，從「' . Task::$statusNames[$_status] . '」調整成「' . Task::$statusNames[$obj->status] . '」');

    $posts = array (
      'action' => '更新了任務細項',
      'content' => implode ('，', $changes) . '。');
    if (TaskCommit::transaction (function () use (&$commit, $obj, $posts) { return verifyCreateOrm ($commit = TaskCommit::create (array_intersect_key (array_merge ($posts, array ('task_id' => $obj->id, 'user_id' => User::current ()->id, 'file' => '', 'size' => 0)), TaskCommit::table ()->columns))); })) {

    }

    UserLog::logWrite (
      $this->icon,
      Task::$statusNames[$obj->status] . '一項' . $this->title,
      '將' . $this->title . '「' . $obj->title . '」調整為「' . Task::$statusNames[$obj->status] . '」',
      array ($backup, $obj->backup (true)));

    return $this->output_json ($obj->status == Task::STATUS_2);
  }
  private function _validation_create (&$posts) {
    if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && in_array ($posts['status'], array_keys (Task::$statusNames)))) $posts['status'] = Task::STATUS_1;
    if (!(isset ($posts['user_id']) && is_string ($posts['user_id']) && is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find_by_id ($posts['user_id']))) return '「' . $this->title . '擁有者」格式錯誤！';
    $posts['user_ids'] = isset ($posts['user_ids']) && is_array ($posts['user_ids']) && $posts['user_ids'] ? column_array (User::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['user_ids']))), 'id') : array ();
    // if (!$posts['user_ids']) return '「' . $this->title . '參與者」格式錯誤！';
    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「' . $this->title . '標題」格式錯誤！';
    if (!(isset ($posts['level']) && is_string ($posts['level']) && is_numeric ($posts['level'] = trim ($posts['level'])) && in_array ($posts['level'], array_keys (Task::$levelNames)))) $posts['level'] = Task::LEVEL_4;
    if (!(isset ($posts['date']) && is_string ($posts['date']) && is_date ($posts['date'] = trim ($posts['date'])))) return '「' . $this->title . '日期」格式錯誤！';

    if (isset ($posts['content']) && !(is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) $posts['content'] = '';
    
    $posts['attachments'] = isset ($posts['attachments']) && is_array ($posts['attachments']) && $posts['attachments'] ? array_values (array_filter (array_map (function ($source) {
      if (!(isset ($source['title']) && is_string ($source['title']) && ($source['title'] = trim ($source['title'])))) $source['title'] = '';
      if (!(isset ($source['href']) && is_string ($source['href']) && ($source['href'] = trim ($source['href'])))) $source['href'] = '';
      return $source;
    }, $posts['attachments']), function ($source) {
      return $source['title'] || $source['href'];
    })) : array ();
    
    $posts['old_attachment_ids'] = isset ($posts['old_attachment_ids']) && is_array ($posts['old_attachment_ids']) && $posts['old_attachment_ids'] ? $posts['old_attachment_ids'] : array ();

    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
  private function _validation_file ($key, &$posts, &$files, $name) {
    if (!isset ($posts[$key])) return array ();
    if (!isset ($files[$key])) return array ();
    if (count ($posts[$key]) != count ($files[$key])) return array ();

    $new = array ();
      foreach ($posts[$key] as $i => $post)
        if (($file = array ('name' => $files[$key][$i]['name'][$name], 'type' => $files[$key][$i]['type'][$name], 'tmp_name' => $files[$key][$i]['tmp_name'][$name], 'error' => $files[$key][$i]['error'][$name], 'size' => $files[$key][$i]['size'][$name])) && is_upload_file_format ($file, 50 * 1024 * 1024, $this->accept))
          array_push ($new, array ('title' => ($post['title'] = trim ($post['title'])) ? $post['title'] : $file['name'], 'file' => $file));

    return $new;
  }
}
