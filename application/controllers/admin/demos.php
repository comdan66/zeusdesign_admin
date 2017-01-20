<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Demos extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('demo')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/demos';
    $this->icon = 'icon-ta';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Demo::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  public function add () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();

    if ($msg = $this->_validation_create ($posts))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    $posts['uid'] = uniqid ();
    if (!Demo::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Demo::create (array_intersect_key ($posts, Demo::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一項提案。',
      'desc' => '提案名稱為「' . $obj->name . '」。',
      'backup' => json_encode ($obj->columns_val ())));

    return redirect_message (array ('admin', 'demo', $obj->id, 'images'), array ('_flash_info' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->add_param ('now_url', base_url ('admin/demo', $this->obj->id, 'images'))
                ->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Demo::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一項提案。',
      'desc' => '提案名稱為「' . $obj->name . '」。',
      'backup' => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ('admin', 'demo', $obj->id, 'images'), array ('_flash_info' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->columns_val (true);

    if (!Demo::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一項提案。',
      'desc' => '已經備份了刪除紀錄，細節可詢問工程師。',
      'backup' => json_encode ($backup)));

    if ($next = Demo::find ('one', array ('select' => 'id', 'order' => 'id DESC', 'conditions' => array ())))
      return redirect_message (array ('admin', 'demo', $next->id, 'images'), array ('_flash_info' => '刪除成功！'));
    else 
      return redirect_message (array ('admin', 'demo-demos', 'add'), array ('_flash_info' => '刪除成功！'));
  }

  private function _validation_create (&$posts) {
    if (!isset ($posts['name'])) return '沒有填寫 名稱！';
    if (!(is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '名稱 格式錯誤！';
    if (!isset ($posts['is_enabled'])) return '沒有選擇 是否公開！';
    if (!isset ($posts['is_mobile'])) return '沒有選擇 是否為手機版！';

    $posts['password'] = isset ($posts['password']) && is_string ($posts['password']) && ($posts['password'] = trim ($posts['password'])) ? $posts['password'] : '';
    $posts['memo'] = isset ($posts['memo']) && is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) ? $posts['memo'] : '';

    if (!(is_numeric ($posts['is_enabled'] = trim ($posts['is_enabled'])) && in_array ($posts['is_enabled'], array_keys (Demo::$enableNames)))) return '是否公開 格式錯誤！';
    if (!(is_numeric ($posts['is_mobile'] = trim ($posts['is_mobile'])) && in_array ($posts['is_mobile'], array_keys (Demo::$mobileNames)))) return '是否為手機版 格式錯誤！';

    return '';
  }

  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
