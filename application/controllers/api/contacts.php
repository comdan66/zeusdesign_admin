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
    $posts['status'] = Contact::STATUS_1;


    if (($msg = $this->_validation_create ($posts)) || (!Contact::transaction (function () use (&$obj, $posts) {
      return verifyCreateOrm ($obj = Contact::create (array_intersect_key ($posts, Contact::table ()->columns)));
    }) && $msg = '新增失敗！')) return $this->output_error_json ($msg);

    return $this->output_json ($obj->backup ());
  }

  private function _validation_create (&$posts) {
    if (!(isset ($posts['email']) && is_string ($posts['email']) && ($posts['email'] = trim ($posts['email'])))) return '未填寫「E-Mail」，或格式錯誤！';
    if (!(isset ($posts['message']) && is_string ($posts['message']) && ($posts['message'] = trim ($posts['message'])))) return '未填寫「留言內容」，或格式錯誤！';
    if (isset ($posts['name']) && !(is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) $posts['name'] = '';
    return '';
  }
}
