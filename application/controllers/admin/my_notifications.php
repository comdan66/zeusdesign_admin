<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class My_notifications extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('admin')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/my-notifications';
    $this->icon = 'icon-no_a';

    if (in_array ($this->uri->rsegments (2, 0), array ('is_read')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Notification::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $id, User::current ()->id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }


  private function _search_columns () {
    return array ( 
        array ('key' => 'to', 'title' => '寄給誰', 'sql' => 'to LIKE ?'),
        array ('key' => 'content', 'title' => '內容', 'sql' => 'content LIKE ?'),
        array ('key' => 'title', 'title' => '標題', 'sql' => 'title LIKE ?'),
      );
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);
    OaModel::addConditions ($conditions, 'user_id = ?', User::current ()->id);

    $limit = 25;
    $total = Notification::count (array ('conditions' => $conditions));
    $objs = Notification::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
      ));
  }
  public function is_read () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);
    
    $validation = function (&$posts) {
      if (!isset ($posts['is_read'])) return '沒有選擇 是否已讀！';
      if (!(is_numeric ($posts['is_read'] = trim ($posts['is_read'])) && in_array ($posts['is_read'], array_keys (Notification::$readNames)))) return '是否已讀 格式錯誤！';    
      return '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Notification::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '標示一則通知 「' . Notification::$readNames[$obj->is_read] . '」。',
      'desc' => '將一則通知 “' . $obj->mini_description () . '” 設定成 「' . Notification::$readNames[$obj->is_read] . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json ($obj->is_read == Notification::READ_YES);
  }
}
