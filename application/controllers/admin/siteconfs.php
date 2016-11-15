<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Siteconfs extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();

    $this->uri_1 = 'admin/siteconfs';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Siteconf::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array (
            '_flash_danger' => '找不到該筆資料。'
          ));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1), array (
          '_flash_danger' => '非 POST 方法，錯誤的頁面請求。'
        ));

    $posts = OAInput::post ();
    
    if (($msg = $this->_validation_must ($posts)) || ($msg = $this->_validation ($posts)))
      return redirect_message (array ($this->uri_1), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));
    
    foreach (Siteconf::all () as $obj)
      Siteconf::transaction (function () use ($obj) { return $obj->destroy (); });

    foreach ($posts as $key => $val)
      Siteconf::transaction (function () use (&$obj, $key, $val) {
        return verifyCreateOrm ($obj = Siteconf::create (array (
            'key' => $key,
            'val' => $val,
          )));
      });
    
    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-se', 'content' => '修改了網站設定。', 'desc' => '網站名稱為「' . Siteconf::getVal ('site_title') . '」。', 'backup' => json_encode (Siteconf::toArray ())));
    return redirect_message (array ($this->uri_1), array (
        '_flash_info' => '修改成功！'
      ));
  }
  private function _validation (&$posts) {
    $keys = array ('site_title', 'site_desc', 'site_keyword');

    $new_posts = array (); foreach ($posts as $key => $value) if (in_array ($key, $keys)) $new_posts[$key] = $value;
    $posts = $new_posts;

    if (isset ($posts['site_title']) && !($posts['site_title'] = trim ($posts['site_title']))) return '網站名稱 格式錯誤！';
    if (isset ($posts['site_desc']) && !($posts['site_desc'] = trim ($posts['site_desc']))) return '網站敘述 格式錯誤！';
    if (isset ($posts['site_keyword']) && !($posts['site_keyword'] = trim ($posts['site_keyword']))) return '網站關鍵字 格式錯誤！';
    return '';
  }
  private function _validation_must (&$posts) {
    if (!isset ($posts['site_title'])) return '沒有填寫 網站名稱！';
    if (!isset ($posts['site_desc'])) return '沒有填寫 網站敘述！';
    if (!isset ($posts['site_keyword'])) return '沒有填寫 網站關鍵字！';
    return '';
  }
}
