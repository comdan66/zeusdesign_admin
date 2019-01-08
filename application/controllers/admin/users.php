<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Users extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('user')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/users';
    $this->icon = 'icon-ua';
    $this->title = '人員';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = User::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'name' => array ('el' => 'input', 'text' => '名稱', 'sql' => 'name LIKE ?'),
        'email' => array ('el' => 'input', 'text' => 'E-Mail', 'sql' => 'email LIKE ?'),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'User', array ('order' => 'id DESC', 'include' => array ('set')), function ($conditions) {
      OaModel::addConditions ($conditions, 'user_id != ?', 11);
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
  public function edit () {
    $posts = Session::getData ('posts', true);

    if (!$this->obj->set) $this->obj->create_set ();

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
      ));
  }
  public function update () {
    $obj = $this->obj;
    if (!$obj->set) $obj->create_set ();

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if ($columns = array_intersect_key ($posts, $obj->set->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->set->$column = $value;

    if (!User::transaction (function () use ($obj, $posts) {
      return $obj->save () && $obj->set->save ();
    })) return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    if ($obj->roles)
      foreach ($obj->roles as $role)
        UserRole::transaction (function () use ($role) { return $role->destroy (); });

    if ($posts['keys'])
      foreach ($posts['keys'] as $i => $key)
        UserRole::transaction (function () use ($i, $key, $obj) { return verifyCreateOrm (UserRole::create (array_intersect_key (array ('user_id' => $obj->id, 'name' => $key), UserRole::table ()->columns))); });


    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title,
      '名稱為：「' . $obj->name . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }

  private function _validation_update (&$posts) {
    if (!(isset ($posts['name']) && is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '「' . $this->title . '名稱」格式錯誤！';
    if (!(isset ($posts['email']) && is_string ($posts['email']) && ($posts['email'] = trim ($posts['email'])))) return '「' . $this->title . ' E-Mail」格式錯誤！';
    if (isset ($posts['link_facebook']) && !(is_string ($posts['link_facebook']) && ($posts['link_facebook'] = trim ($posts['link_facebook'])))) $posts['link_facebook'] = '';
    if (isset ($posts['phone']) && !(is_string ($posts['phone']) && ($posts['phone'] = trim ($posts['phone'])))) $posts['phone'] = '';
    
    $posts['keys'] = isset ($posts['keys']) && is_array ($posts['keys']) && ($posts['keys'] = array_filter ($posts['keys'], function ($k) {
            return Cfg::setting ('role', 'role_names', $k, 'name');
        })) ? $posts['keys'] : array ();
    return '';
  }
}
