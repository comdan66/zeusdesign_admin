<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Deploys extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('site')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/deploys';
    $this->icon = 'icon-pi';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Deploy::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

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
    $total = Deploy::count (array ('conditions' => $conditions));
    $objs = Deploy::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'include' => array ('user'), 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
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
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();

    if ($msg = $this->_validation_create ($posts))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    $posts['user_id'] = User::current ()->id;
    if (!Deploy::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Deploy::create (array_intersect_key ($posts, Deploy::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    $this->load->library ('DeployTool');

    if ($obj->type == Deploy::TYPE_BUILD)
      if (!(DeployTool::genApi () && DeployTool::callBuild ()))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '新增失敗！'));

    if ($obj->type == Deploy::TYPE_UPLOAD)
      if (!(DeployTool::genApi () && DeployTool::callUpload ()))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '新增失敗！'));

    DeployTool::callBuild ();

    $obj->is_success = Deploy::SUCCESS_YES;
    if (!Deploy::transaction (function () use ($obj) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '新增失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '執行了一次部署。',
      'desc' => '在 ”' . $obj->created_at->format ('Y-m-d H:i:s') . '“ 執行一次部署，其類型為「' . Deploy::$typeNames[$obj->type] . '」，執行後狀態「' . Deploy::$successNames[$obj->is_success] . '」。',
      'backup' => json_encode ($obj->columns_val ())));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '新增成功！'));
  }
  private function _validation_create (&$posts) {
    if (!isset ($posts['type'])) return '沒有選擇 類型！';
    if (!(is_numeric ($posts['type'] = trim ($posts['type'])) && in_array ($posts['type'], array_keys (Deploy::$typeNames)))) return '類型 格式錯誤！';
    return '';
  }
}
