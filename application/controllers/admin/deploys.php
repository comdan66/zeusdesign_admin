<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Deploys extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;
  private $users = array ();
  private $accept = null;


  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('deploy')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/deploys';
    $this->icon = 'icon-loop2';
    $this->title = '部署紀錄';

    if (in_array ($this->uri->rsegments (2, 0), array ('show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Deploy::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'status'    => array ('el' => 'select', 'text' => '是否成功', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => Deploy::$statusNames[$t], 'value' => $t,);}, array_keys (Deploy::$statusNames))),
        'type'    => array ('el' => 'select', 'text' => '類型', 'sql' => 'type = ?', 'items' => array_map (function ($t) { return array ('text' => Deploy::$typeNames[$t], 'value' => $t,);}, array_keys (Deploy::$typeNames))),
        'user_id[]' => array ('el' => 'checkbox', 'text' => '操作者', 'sql' => 'user_id IN (?)', 'items' => array_map (function ($u) { return array ('text' => $u->name, 'value' => $u->id); }, User::all ())),
        'res'     => array ('el' => 'input', 'text' => '回傳訊息', 'sql' => 'res LIKE ?'),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Deploy', array ('order' => 'id DESC', 'include' => array ('user')));

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
  
  public function add () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['user_id'] = User::current ()->id;

    if (($msg = $this->_validation_create ($posts)) || (!Deploy::transaction (function () use (&$obj, $posts) {
      return verifyCreateOrm ($obj = Deploy::create (array_intersect_key ($posts, Deploy::table ()->columns)));
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    $this->load->library ('DeployTool');
    DeployTool::genApi ();

    $obj->status = ($obj->type == Deploy::TYPE_2 ? DeployTool::callUpload ($obj) : DeployTool::callBuild ($obj)) ? Deploy::STATUS_2 : Deploy::STATUS_1;
    
    if (!Deploy::transaction (function () use ($obj) { return $obj->save (); })) return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！'));

    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '類型為：「' . Deploy::$typeNames[$obj->type] . '」，執行狀態：「' . Deploy::$statusNames[$obj->status] . '」',
      $obj->backup ());

    return redirect_message (array ($this->uri_1), array ('_fi' => '執行完成！'));
  }
  private function _validation_create (&$posts) {
    if (!(isset ($posts['type']) && is_string ($posts['type']) && is_numeric ($posts['type'] = trim ($posts['type'])) && in_array ($posts['type'], array_keys (Deploy::$typeNames)))) $posts['type'] = Deploy::TYPE_1;
    $posts['status'] = Deploy::STATUS_1;
    $posts['res'] = '';
    return '';
  }
}
