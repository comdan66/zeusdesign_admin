<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class My_notices extends Admin_controller {
  private $uri_1 = null;
  private $icon = null;
  private $title = null;


  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('member')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/my-notices';
    $this->icon = 'icon-notifications_active';
    $this->title = '我的通知';

    if (in_array ($this->uri->rsegments (2, 0), array ('status')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Notice::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $id, User::current ()->id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url',  base_url ('admin', 'my', User::current ()->id));
  }

  public function index ($offset = 0) {
    $searches = array (
        'content'     => array ('el' => 'input', 'text' => '內容', 'sql' => 'content LIKE ?'),
        'status'    => array ('el' => 'select', 'text' => '是否已讀', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => Notice::$statusNames[$t], 'value' => $t,);}, array_keys (Notice::$statusNames))),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Notice', array ('order' => 'id DESC'), function ($conditions) {
      OaModel::addConditions ($conditions, 'user_id = ?', User::current ()->id);
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
  public function status () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    $validation = function (&$posts) {
      return !(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Notice::STATUS_2 : Notice::STATUS_1) && in_array ($posts['status'], array_keys (Notice::$statusNames))) ? '「設定上下架」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Notice::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return $this->output_error_json ('更新失敗！');

    UserLog::logWrite (
      $this->icon,
      Notice::$statusNames[$obj->status] . '一項' . $this->title,
      '將一項' . $this->title . '調整為「' . Notice::$statusNames[$obj->status] . '」',
      array ($backup, $obj->backup (true)));

    return $this->output_json ($obj->status == Notice::STATUS_2);
  }
}
