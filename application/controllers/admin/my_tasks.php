<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class My_tasks extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $users = array ();

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('member')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/my-tasks';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'show', 'finish')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Task::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    if ($this->obj) {
      $this->users = User::find ('all', array ('conditions' => array ('id IN (?)', ($user_ids = column_array ($this->obj->user_mappings, 'user_id')) ? $user_ids : array (0))));
      if (!($this->obj->user_id == User::current ()->id || in_array (User::current ()->id, $user_ids)))
        return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));
    }

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  private function _search_columns () {
    return array ( 
        array ('key' => 'title',    'title' => '任務標題', 'sql' => 'title LIKE ?'), 
      );
  }
  public function show ($id = 0) {
    return $this->load_view (array (
        'obj' => $this->obj,
        'users' => $this->users,
        'quota_day' => (int)(strtotime ($this->obj->date_at) - strtotime (date ('Y-m-d'))) / 86400
      ));
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);
    $task_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'task_id', 'conditions' => array ('user_id = ?', User::current ()->id))), 'task_id');
    OaModel::addConditions ($conditions, 'user_id = ? || (id IN (?))', User::current ()->id, $task_ids ? $task_ids : array (0));
    

    $limit = 10;
    $total = Task::count (array ('conditions' => $conditions));
    $objs = Task::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'include' => array ('user', 'commits'), 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'show'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'show'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if (!TaskCommit::transaction (function () use (&$commit, $obj, $posts) { return verifyCreateOrm ($commit = TaskCommit::create (array_intersect_key (array_merge ($posts, array ('task_id' => $obj->id, 'user_id' => User::current ()->id)), TaskCommit::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'show'), array ('_flash_danger' => '留言失敗！', 'posts' => $posts));

    Mail::send ('宙斯任務「' . $obj->title . '」', Mail::renderContent ('mail/task_commit', array (
        'user' => $obj->user->name,
        'title' => $obj->title,
        'url' => base_url ('platform', 'mail', 'admin', 'my-tasks', $obj->id, 'show'),
        'content' => $commit->content,
        'detail' => array (array ('title' => '任務名稱：', 'value' => $obj->title), array ('title' => '任務內容：', 'value' => $obj->description))
      )), ($user_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'user_id', 'conditions' => array ('task_id = ?', $obj->id))), 'user_id')) ? User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $user_ids))) : array ());
    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => 'icon-d2',
      'content' => '針對一項任務做了留言。',
      'desc' => '針對專案「' . $obj->title . '」留言，內容大約是：「' . $commit->mini_content () . '」。',
      'backup' => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1, $obj->id, 'show'), array ('_flash_info' => '留言成功！'));
  }
  public function finish () {
    $obj = $this->obj;

    if (!(User::current ()->in_roles (array ('project')) && ($obj->user_id == User::current ()->id)))
      return $this->output_error_json ('您沒有此動作的權限。');

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

    $content = Mail::renderContent ('mail/finish_task', array (
        'user' => $obj->user->name,
        'url' => base_url ('platform', 'mail', 'admin', 'my-tasks', $obj->id, 'show'),
        'detail' => array (array ('title' => '任務名稱：', 'value' => $obj->title), array ('title' => '任務狀態：', 'value' => Task::$finishNames[$obj->finish]))
      ));
    $users = ($user_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'user_id', 'conditions' => array ('task_id = ?', $obj->id))), 'user_id')) ? User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $user_ids))) : array ();
    Mail::send ('宙斯任務「' . $obj->title . '」', $content, $users);

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => 'icon-shield',
      'content' => '將一項任務設定成 「' . Task::$finishNames[$obj->finish] . '」。',
      'desc' => '將任務 “' . $obj->title . '” 設定成 「' . Task::$finishNames[$obj->finish] . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json ($obj->finish == Task::IS_FINISHED);
  }
  private function _validation_update (&$posts) {
    if (!isset ($posts['content'])) return '沒有填寫 留言內容！';
    if (!(is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '留言內容 格式錯誤！';

    return '';
  }
}
