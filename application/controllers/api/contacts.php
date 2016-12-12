<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Contacts extends Api_controller {

  public function __construct () {
    parent::__construct ();
    header ("Access-Control-Allow-Origin: https://www.zeusdesign.com.tw");
  }
  public function create () {
    $posts = OAInput::post ();

    if ($msg = $this->_validation_create ($posts))
      return $this->output_error_json ($msg);

    $posts['ip'] = $this->input->ip_address ();
    $posts['is_readed'] = Contact::READ_NO;

    if (!Contact::transaction (function () use (&$contact, $posts) { return verifyCreateOrm ($contact = Contact::create (array_intersect_key ($posts, Contact::table ()->columns))); })) return $this->output_error_json ('新增失敗！');
    return $this->output_json ($contact->columns_val ());
  }

  private function _validation_create (&$posts) {
    if (!isset ($posts['email'])) return '沒有填寫 E-Mail！';
    if (!isset ($posts['message'])) return '沒有填寫 留言內容！';

    if (!(is_string ($posts['email']) && ($posts['email'] = trim ($posts['email'])))) return 'E-Mail 格式錯誤！';
    if (!(is_string ($posts['message']) && ($posts['message'] = trim ($posts['message'])))) return '留言內容 格式錯誤！';
    
    $posts['name'] = isset ($posts['name']) && is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])) ? $posts['name'] : '';
    return '';
  }
}
