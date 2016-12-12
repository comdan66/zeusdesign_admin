<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Users extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();
    if (!User::current ()->in_roles (array ('user')))
      return redirect_message (array ('admin'), array (
            '_flash_danger' => '您的權限不足，或者頁面不存在。'
          ));
    
    $this->uri_1 = 'admin/users';

    if (in_array ($this->uri->rsegments (2, 0), array ('roles', 'update', 'show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = User::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array (
            '_flash_danger' => '找不到該筆資料。'
          ));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $columns = array ( 
        array ('key' => 'content', 'title' => '內容', 'sql' => 'content LIKE ?'), 
        array ('key' => 'title', 'title' => '標題', 'sql' => 'title LIKE ?'), 
      );
    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = User::count (array ('conditions' => $conditions));
    $objs = User::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
      ));
  }

  public function show ($user_id = 0, $type = 'schedules', $offset = 0) {
    $pagination = '';
    $user_logs = $schedules = $columns = array ();
    $configs = array_merge (explode ('/', $this->uri_1), array ($user_id, 'show', $type, '%s'));
    $conditions = conditions ($columns, $configs);
    $this->load->library ('pagination');

    if ($type == 'user_logs') {
      $limit = 5;
      OaModel::addConditions ($conditions, 'user_id = ?', $this->obj->id);
      
      $total = UserLog::count (array ('conditions' => $conditions));
      $logs = UserLog::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'conditions' => $conditions));
      $pagination = $this->_get_pagination ($limit, $total, $configs);

      foreach ($logs as $log)
        if (!isset ($user_logs[$log->created_at->format ('Y-m-d')])) $user_logs[$log->created_at->format ('Y-m-d')] = array ($log);
        else array_push ($user_logs[$log->created_at->format ('Y-m-d')], $log);
    } else {
      $limit = 10;
      OaModel::addConditions ($conditions, 'user_id = ?', $this->obj->id);
      OaModel::addConditions ($conditions, 'year = ?', date ('Y'));
      OaModel::addConditions ($conditions, 'month = ?', date ('m'));
      OaModel::addConditions ($conditions, 'day = ?', date ('d'));

      $total = Schedule::count (array ('conditions' => $conditions));
      $schedules = Schedule::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'include' => array ('tag'), 'order' => 'sort ASC, id DESC', 'conditions' => $conditions));
      $pagination = $this->_get_pagination ($limit, $total, $configs);
    }

    $logs = UserLog::find ('all', array (
      'select' => 'count(id) AS cnt, created_at, DATE(`created_at`) AS date',
      'limit' => 365,
      'group' => 'date',
      'order' => 'date DESC',
      'conditions' => array ('user_id = ?', $this->obj->id)));
    $logs = array_combine (column_array ($logs, 'date'), $logs);

    $chart = array ();
    for ($i = 0; $i < 12; $i++) $chart[$date = date ('Y-m-d', strtotime (date ('Y-m-d') . $i ? '-' . $i . ' day' : ''))] = isset ($logs[$date]) ? $logs[$date]->cnt : 0;
    $chart = array_reverse ($chart);
    
    $roles = Cfg::setting ('role', 'role_names');

    return $this->load_view (array (
        'user' => $this->obj,
        'chart' => $chart,
        'logs' => $logs,
        'type' => $type,
        'user_logs' => $user_logs,
        'schedules' => $schedules,
        'pagination' => $pagination,
        'roles' => $roles
      ));
  }
  public function roles () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);

    $validation = function (&$posts) {
      if (!isset ($posts['roles'])) return '權限格式錯誤！';
      $np = array (); foreach ($posts['roles'] as $key => $bool) if (Cfg::setting ('role', 'role_names', $key)) $np[$key] = $bool; if (!$np) return '權限格式錯誤！'; $posts['roles'] = $np;
      return '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);
    
    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    if (!User::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    if ($posts['roles'])
      foreach ($posts['roles'] as $key => $bool)
        $bool ? (!UserRole::find ('one', array ('conditions' => array ('user_id = ? AND name = ?', $obj->id, $key))) && UserRole::transaction (function () use ($obj, $key) { return verifyCreateOrm (UserRole::create (array ('user_id' => $obj->id, 'name' => $key))); })) : (($role = UserRole::find ('one', array ('conditions' => array ('user_id = ? AND name = ?', $obj->id, $key)))) && UserRole::transaction (function () use ($role) { return $role->destroy (); }));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => 'icon-bo',
      'content' => '調整了人員權限。',
      'desc' => '已經備份了修改紀錄，細節可詢問工程師。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json (array (
        'roles' => array_map (function ($role) { return array (
            'key' => $role->name,
            'name' => Cfg::setting('role', 'role_names', $role->name)
          );
      }, $obj->roles)));
  }
}
