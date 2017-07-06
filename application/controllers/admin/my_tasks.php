<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class My_tasks extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;
  private $users = array ();
  private $accept = null;


  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('member')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/my-tasks';
    $this->icon = 'icon-shield';
    $this->title = '我的任務';
    $this->accept = array ('gif', 'jpeg', 'jpg', 'png', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'zip');

    if (in_array ($this->uri->rsegments (2, 0), array ('show', 'update')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Task::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    if ($this->obj) {
      $this->users = User::find ('all', array ('conditions' => array ('id IN (?)', ($user_ids = column_array ($this->obj->user_mappings, 'user_id')) ? $user_ids : array (0))));

      if (!($this->obj->user_id == User::current ()->id || ($user_ids && in_array (User::current ()->id, $user_ids))))
        return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    }

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
    $objs = conditions ($searches, $configs, $offset, 'Task', array ('order' => 'id DESC', 'include' => array ('user', 'commits'), 'joins' => 'LEFT JOIN (select user_id,task_id from task_user_mappings) as a ON(tasks.id = a.task_id)'), function ($conditions) {
      OaModel::addConditions ($conditions, 'a.user_id = ?', User::current ()->id);
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
  public function show () {
    UserLog::logRead ($this->icon, '檢視了一項' . $this->title);

    return $this->load_view (array (
        'obj' => $this->obj,
        'quota_day' => (int)(strtotime ($this->obj->date) - strtotime (date ('Y-m-d'))) / 86400
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'show'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $file = OAInput::file ('file');
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts, $file))
      return redirect_message (array ($this->uri_1, $obj->id, 'show'), array ('_fd' => $msg, 'posts' => $posts));

    if (!TaskCommit::transaction (function () use (&$commit, $obj, $posts, $file) { return verifyCreateOrm ($commit = TaskCommit::create (array_intersect_key (array_merge ($posts, array ('task_id' => $obj->id, 'user_id' => User::current ()->id)), TaskCommit::table ()->columns))) && (!$file || ($commit->file->put ($file))); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'show'), array ('_fd' => '留言失敗！', 'posts' => $posts));

    $users = array_filter (($user_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'user_id', 'conditions' => array ('task_id = ?', $obj->id))), 'user_id')) ? User::find ('all', array ('select' => 'id, name, email', 'conditions' => array ('id IN (?)', $user_ids))) : array (), function ($user) use ($commit) { return $user->id != $commit->user_id; });

    Notice::send (
      $users,
      $commit->user->name . ' 針對任務「' . $obj->title . '」' . $commit->action . '。',
      'admin/my-tasks/' . $obj->id . '/show');

    Mail::send (
      $users,
      '[宙思任務] ' . $obj->title . '',
      'admin/my-tasks/' . $obj->id . '/show',
      function ($o) use ($obj, $commit) {
        return array_merge (array (
            array ('type' => 'section', 'title' => '', 'content' => Mail::renderP (Mail::renderB ($commit->user->name) . ' 在您的任務「' . $obj->title . '」' . $commit->action . '' . ($commit->content ? '，留言內容是：「' . $commit->content . '」' : '') . '' . ((string)$commit->file ? '，上傳的檔案名稱為：「' . Mail::renderLink ((string)$commit->file, $commit->file->url ()) . '」' : '') . '，詳細內容請至' . Mail::renderLink ('宙思後台', base_url ('platform', 'mail', $o->token)) . '查看。')),
          ));
    });

    UserLog::logWrite (
      $this->icon,
      '針對一項任務做了留言',
      '針對專案「' . $obj->title . '」留言，內容大約是：「' . $commit->mini_content () . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1, $obj->id, 'show'), array ('_fi' => '留言成功！'));
  }
  private function _validation_update (&$posts, &$file) {
    if ($file && !is_upload_file_format ($file, 50 * 1024 * 1024, $this->accept)) return '檔案大小錯誤，或檔案類型錯誤！';
    $file = $file && is_upload_file_format ($file, 50 * 1024 * 1024, $this->accept) ? $file : array ();
    
    $posts['content'] = isset ($posts['content']) && is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])) ? $posts['content'] : '';
    if (!$posts['content'] && !$file) return '請輸入留言、註解 或 選擇檔案！';

    $posts['action'] = $posts['content'] && $file ? '留言與上傳附件檔' : ($posts['content'] && !$file ? '留言' : '上傳附件檔');
    $posts['size'] = $file ? $file['size'] : 0;
    return '';
  }
}
