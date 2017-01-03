<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class My_weights extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $range = null;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('member')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/my-weights';
    $this->icon = 'icon-balance-scale';
    $this->range = '-2 day';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Weight::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $id, User::current ()->id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('range', $this->range)
         ->add_param ('now_url', base_url ($this->uri_1));
  }

  private function _search_columns () {
    return array ( 
        array ('key' => 'date_at',   'title' => '日期', 'sql' => 'date_at = ?'), 
      );
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);
    OaModel::addConditions ($conditions, 'user_id = ?', User::current ()->id);

    $limit = 10;
    $total = Weight::count (array ('conditions' => $conditions));
    $objs = Weight::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
      ));
  }
  public function add () {
    if ($obj = Weight::find ('one', array ('select' => 'id', 'conditions' => array ('date_at = ? AND user_id = ?', date ('Y-m-d'), User::current ()->id))))
      return redirect (base_url ($this->uri_1, $obj->id, 'edit'), 'refresh');

    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $cover = OAInput::file ('cover');
    
    if ($msg = $this->_validation_create ($posts, $cover))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));
    
    $posts['date_at'] = date ('Y-m-d');
    $posts['user_id'] = User::current ()->id;

    if (!Weight::transaction (function () use (&$obj, $posts, $cover) { return verifyCreateOrm ($obj = Weight::create (array_intersect_key ($posts, Weight::table ()->columns))) && ($cover ? $obj->cover->put ($cover) : true) ; }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一筆體重紀錄。',
      'desc' => '新增一筆體重紀錄。',
      'backup' => json_encode ($obj->columns_val ())));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '新增成功！'));
  }
  public function edit () {
    if ($this->obj->date_at->format ('Y-m-d') < date ('Y-m-d', strtotime (date ('Y-m-d') . ' ' . $this->range)))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '超過修改期限囉。'));

    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $cover = OAInput::file ('cover');
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts, $cover, $obj))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Weight::transaction (function () use ($obj, $posts, $cover) { if (!$obj->save () || ($cover && !$obj->cover->put ($cover))) return false; return true; }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一筆體重紀錄。',
      'desc' => '修改一筆體重紀錄。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }
  public function destroy () {
    if ($this->obj->date_at->format ('Y-m-d') < date ('Y-m-d', strtotime (date ('Y-m-d') . ' ' . $this->range)))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '超過修改期限囉。'));

    $obj = $this->obj;
    $backup = $obj->columns_val (true);

    if (!Weight::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一筆體重紀錄。',
      'desc' => '刪除一筆體重紀錄。',
      'backup' => json_encode ($backup)));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '刪除成功！'));
  }
  private function _validation_create (&$posts, &$cover) {
    if (!($cover && ((string)$obj->cover || isset ($cover)))) $cover = null;
    if ($cover && !is_upload_image_format ($cover, 20 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png'))) return '文章封面 格式錯誤！';

    $posts['weight'] = isset ($posts['weight']) && is_numeric ($posts['weight'] = trim ($posts['weight'])) && ($posts['weight'] > 0) && ($posts['weight'] < 150) ? $posts['weight'] : 0;
    $posts['rate'] = isset ($posts['rate']) && is_numeric ($posts['rate'] = trim ($posts['rate'])) && ($posts['rate'] > 0) && ($posts['rate'] < 100) ? $posts['rate'] : 0;
    $posts['calorie'] = isset ($posts['calorie']) && is_numeric ($posts['calorie'] = trim ($posts['calorie'])) && ($posts['calorie'] > 0) && ($posts['calorie'] < 3000) ? $posts['calorie'] : 0;

    return '';
  }
  private function _validation_update (&$posts, &$cover, $obj) {
    if (!($cover && ((string)$obj->cover || isset ($cover)))) $cover = null;
    if ($cover && !is_upload_image_format ($cover, 20 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png'))) return '文章封面 格式錯誤！';

    $posts['weight'] = isset ($posts['weight']) && is_numeric ($posts['weight'] = trim ($posts['weight'])) && ($posts['weight'] > 0) && ($posts['weight'] < 150) ? $posts['weight'] : 0;
    $posts['rate'] = isset ($posts['rate']) && is_numeric ($posts['rate'] = trim ($posts['rate'])) && ($posts['rate'] > 0) && ($posts['rate'] < 100) ? $posts['rate'] : 0;
    $posts['calorie'] = isset ($posts['calorie']) && is_numeric ($posts['calorie'] = trim ($posts['calorie'])) && ($posts['calorie'] > 0) && ($posts['calorie'] < 3000) ? $posts['calorie'] : 0;

    return '';
  }
}
