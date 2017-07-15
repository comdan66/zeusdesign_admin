<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class My extends Admin_controller {
  private $obj = null;
  private $icon = null;
  private $title = null;
  private $self = false;
  private $uri_1 = false;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('member')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/my/' . User::current ()->id;

    if (!((($id = $this->uri->rsegments (3, 0)) || ($id = User::current ()->id)) && ($this->obj = User::find ('one', array ('conditions' => array ('id = ?', $id))))))
      return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->self = $this->obj->id == User::current ()->id;
    if (!$this->obj->set) $this->obj->create_set ();

    if (!$this->self && !User::current ()->in_roles (array ('user')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));

    $this->icon = 'icon-home';
    $this->title = (!$this->self ? $this->obj->name : '個人');

    $this->add_param ('icon', $this->icon)
         ->add_param ('uri_1', $this->uri_1)
         ->add_param ('title', $this->title)
         ->add_param ('self', $this->self)
         ->add_param ('obj', $this->obj)
         ->add_param ('_url', !$this->self ? base_url ('admin', 'users') : base_url ('admin', 'my', $this->obj->id));
  }

  public function index () {
    $obj = $this->obj;

    $logs = UserLog::find ('all', array (
      'select' => 'count(id) AS cnt, DATE(`created_at`) AS date',
      'limit' => 365,
      'group' => 'date',
      'order' => 'date DESC',
      'conditions' => array ('user_id = ? AND status = ?', $obj->id, UserLog::STATUS_2)));
    $logs = array_combine (column_array ($logs, 'date'), column_array ($logs, 'cnt', function ($t) { return (int) $t;}));

    $tls = array_filter ($logs);
    arsort ($tls);
    $u = floor (count ($tls = array_values ($tls)) / 5);
    $s = isset ($tls[$u * 4]) && isset ($tls[$u * 3]) && isset ($tls[$u * 2]) && isset ($tls[$u * 1]) ? array (0, $tls[$u * 4], $tls[$u * 3], $tls[$u * 2], $tls[$u * 1]) : array (0, 1, 2, 3, 4);
    $logs = array_map (function ($t) use ($s) { return array ('cnt' => $t, 's' => $t <= $s[4] ? $t <= $s[3] ? $t <= $s[2] ? $t <= $s[1] ? 's0' : 's1' : 's2' : 's3' : 's4');}, $logs);

    $today = date ('Y-m-d');
    $tasks = Task::find ('all', array ('order' => 'id DESC', 'joins' => 'LEFT JOIN (select user_id,task_id from task_user_mappings) as a ON(tasks.id = a.task_id)', 'conditions' => array ('date = ? AND a.user_id = ?', $today, $obj->id)));

    $ls = UserLog::find ('all', array ('select' => 'icon, title, content, created_at', 'offset' => 0, 'limit' => 10, 'order' => 'id DESC', 'conditions' => array ('user_id = ? AND status = ?', $obj->id, UserLog::STATUS_2)));
    $user_logs = array ();
    foreach ($ls as $l)
      if (!isset ($user_logs[$l->created_at->format ('Y-m-d')])) $user_logs[$l->created_at->format ('Y-m-d')] = array ($l);
      else array_push ($user_logs[$l->created_at->format ('Y-m-d')], $l);


    UserLog::logRead (
      $this->icon,
      '檢視了' . $this->title . '頁面',
      '搜尋條件細節可詢問工程師',
      $this->obj->id);

    $this->load_view (array (
        'obj' => $obj,
        'logs' => $logs,
        'tasks' => $tasks,
        'today' => $today,
        'user_logs' => $user_logs,
        'schedules1' => Schedule::find ('all', array ('include' => array ('tag', 'user'), 'order' => 'sort ASC', 'conditions' => array ('date = ? AND user_id = ?', $today, $obj->id))),
        'schedules3' => Schedule::find ('all', array ('include' => array ('tag', 'user'), 'order' => 'sort ASC', 'joins' => 'LEFT JOIN (select user_id,schedule_id from schedule_shares) as a ON(schedules.id = a.schedule_id)', 'conditions' => array ('a.user_id = ? AND schedules.user_id != ? AND date = ?', $obj->id, $obj->id, $today))),
      ));
  }
  public function logs ($id, $offset = 0) {
    $searches = array (
        'status'    => array ('el' => 'select', 'text' => '動作', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => UserLog::$statusNames[$t], 'value' => $t,);}, array_keys (UserLog::$statusNames))),
        'title'     => array ('el' => 'input', 'text' => '標題', 'sql' => 'title LIKE ?'),
        'content'   => array ('el' => 'input', 'text' => '內容', 'sql' => 'content LIKE ?'),
        'date'      => array ('el' => 'input', 'type' => 'date', 'text' => '日期', 'sql' => 'DATE(created_at) = ?'),
      );
    $obj = $$this->obj;
    $configs = array_merge (explode ('/', 'admin/my/logs/' . $this->obj->id), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'UserLog', array ('select' => 'icon,title,content,status,created_at', 'order' => 'id DESC'), function ($conditions) use ($obj) {
      OaModel::addConditions ($conditions, 'user_id = ?', $obj->id);
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
        'pagination' => $this->_get_pagination ($configs)
      ));
  }
  public function edit ($id = 0) {
    if (!$this->self)
      return redirect_message (array ('admin', 'my', $this->obj->id), array ('_fd' => '您的權限不足，或者頁面不存在。'));

    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
      ));
  }
  public function update ($id) {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ('admin', 'my', $obj->id, 'edit'), array ('_fd' => '您的權限不足，或者頁面不存在。'));

    $posts = OAInput::post ();
    $banner = OAInput::file ('banner');
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts, $banner, $obj))
      return redirect_message (array ('admin', 'my', $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if ($columns = array_intersect_key ($posts, $obj->set->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->set->$column = $value;

    if (!UserSet::transaction (function () use ($obj, $posts, $banner) {
      if (!$obj->save ()) return false;
      if (!$obj->set->save ()) return false;
      if ($banner && !$obj->set->banner->put ($banner)) return false;
      return true;
    })) return redirect_message (array ('admin', 'my', $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title . '設定',
      '修改細節可詢問工程師',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ('admin', 'my', $id), array ('_fi' => '更新成功！'));
  }

  private function _validation_update (&$posts, &$banner, $obj) {
    if (!(isset ($posts['ani']) && is_string ($posts['ani']) && is_numeric ($posts['ani'] = trim ($posts['ani'])) && in_array ($posts['ani'], array_keys (UserSet::$aniNames)))) $posts['ani'] = UserSet::ANI_2;
    if (!(isset ($posts['name']) && is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '「' . $this->title . '名稱」格式錯誤！';
    if (!(isset ($posts['email']) && is_string ($posts['email']) && ($posts['email'] = trim ($posts['email'])))) return '「' . $this->title . ' E-Mail」格式錯誤！';
    if (isset ($banner) && !(is_upload_image_format ($banner, array ('gif', 'jpeg', 'jpg', 'png')))) $banner = null;

    if (isset ($posts['link_facebook']) && !(is_string ($posts['link_facebook']) && ($posts['link_facebook'] = trim ($posts['link_facebook'])))) $posts['link_facebook'] = '';
    if (isset ($posts['phone']) && !(is_string ($posts['phone']) && ($posts['phone'] = trim ($posts['phone'])))) $posts['phone'] = '';

    return '';
  }
}
