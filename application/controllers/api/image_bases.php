<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Image_bases extends Api_controller {
  private $icon = null;

  public function __construct () {
    parent::__construct ();
    
    $this->icon = 'icon-cs';
    header ("Access-Control-Allow-Origin: *");
  }
  public function index ($k = '', $id = 0) {
    
    return $this->output_json ('呼叫成功！');
  }
  public function create () {
    $posts = OAInput::post ();

    if ($msg = $this->_validation_create ($posts, $user))
      return $this->output_error_json ($msg);

    if (!ImageBase::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = ImageBase::create (array_intersect_key ($posts, ImageBase::table ()->columns))) && $obj->name->put_url ($obj->image_url); }))
      return $this->output_error_json ('新增失敗！');

    UserLog::create (array (
      'user_id' => $user->id,
      'icon' => $this->icon,
      'content' => '在宙思圖庫新增一張圖片。',
      'desc' => '在宙思圖庫新增一張圖片' . ($obj->from_url ? '，來源：「' . $obj->from_url . '」' : '') . '。',
      'backup'  => json_encode ($obj->columns_val ())));

    return $this->output_json ('新增成功！');
  }
  private function _validation_create (&$posts, &$user) {
    if (!isset ($posts['user_id'])) return '沒有登入！';
    if (!isset ($posts['image_url'])) return '沒有圖片網址！';
    if (!(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && ($user = User::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['user_id'])))))) return '使用者 不存在！';
    if (!(is_string ($posts['image_url']) && ($posts['image_url'] = trim ($posts['image_url'])))) return '圖片網址 格式錯誤！';


    $posts['from_url'] = isset ($posts['from_url']) && is_string ($posts['from_url']) && ($posts['from_url'] = trim ($posts['from_url'])) ? $posts['from_url'] : '';
    $posts['name'] = '';

    return '';
  }
}
