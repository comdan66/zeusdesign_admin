<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Cronjobs extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('cronjob')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/cronjobs';
    $this->icon = 'icon-clipboard';
    $this->title = '排程紀錄';

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'title' => array ('el' => 'input', 'text' => '標題', 'sql' => 'title LIKE ?'),
        'content' => array ('el' => 'input', 'text' => '吐出訊息', 'sql' => 'content LIKE ?'),
        'status' => array ('el' => 'select', 'text' => '是否成功', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => Cronjob::$statusNames[$t], 'value' => $t,);}, array_keys (Cronjob::$statusNames))),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Cronjob', array ('order' => 'id DESC'));

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
}
