<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Contacts extends Api_controller {

  public function __construct () {
    parent::__construct ();
    header ("Access-Control-Allow-Origin: https://www.zeusdesign.com.tw");
  }
  public function create () {
    $posts = OAInput::post ();

    if (($msg = $this->_validation_must ($posts)) || ($msg = $this->_validation ($posts)))
      return $this->output_error_json ($msg);

    $posts['ip'] = $this->input->ip_address ();

    $create = Contact::transaction (function () use (&$contact, $posts) {
      return verifyCreateOrm ($contact = Contact::create (array_intersect_key ($posts, Contact::table ()->columns)));
    });

    if (!$create) return $this->output_error_json ('新增失敗！');
    return $this->output_json ($contact->to_array ());
  }

  private function _validation (&$posts) {
    $keys = array ('name', 'email', 'message', 'is_readed');

    $new_posts = array (); foreach ($posts as $key => $value) if (in_array ($key, $keys)) $new_posts[$key] = $value;
    $posts = $new_posts;

    if (isset ($posts['name']) && !($posts['name'] = trim ($posts['name']))) return '稱呼 格式錯誤！';
    if (isset ($posts['email']) && !($posts['email'] = trim ($posts['email']))) return 'E-Mail 格式錯誤！';
    if (isset ($posts['message']) && !($posts['message'] = trim ($posts['message']))) return '留言內容 格式錯誤！';

    if (isset ($posts['is_readed']) && !(is_numeric ($posts['is_readed'] = trim ($posts['is_readed'])) && in_array ($posts['is_readed'], array_keys (Contact::$readNames)))) return '已讀格式錯誤！';
    return '';
  }
  private function _validation_must (&$posts) {
    if (!isset ($posts['name'])) return '沒有填寫 稱呼！';
    if (!isset ($posts['email'])) return '沒有填寫 E-Mail！';
    if (!isset ($posts['message'])) return '沒有填寫 留言內容！';
    return '';
  }
}
