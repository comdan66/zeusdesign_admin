<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
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

    Mail::send (
      User::find_by_id(1),
      '[聯絡宙思] 宙思官網有新的留言（' . date('Y-m-d H:i:s') . '）',
      'admin/contacts/' . $obj->id . '/show',
      function ($o) {
        return [[
          'type' => 'ol',
          'title' => 'Hi 管理者，宙思官網有新的留言，詳細內容請至' . Mail::renderLink ('宙思後台', base_url ('platform', 'mail', $o->token)) . '查看，以下是細節：',
          'li' => array_map(function($change) {
            return Mail::renderLi($change);
          }, ['稱呼：' . $obj->name, 'E-Mail：' . $obj->email, '內容：' . nl2br($obj->message)])
        ]];
    });

    return $this->output_json ($obj->backup ());
  }

  private function _validation_create (&$posts) {
    if (!(isset ($posts['email']) && is_string ($posts['email']) && ($posts['email'] = trim ($posts['email'])))) return '未填寫「E-Mail」，或格式錯誤！';
    if (!(isset ($posts['message']) && is_string ($posts['message']) && ($posts['message'] = trim ($posts['message'])))) return '未填寫「留言內容」，或格式錯誤！';
    if (isset ($posts['name']) && !(is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) $posts['name'] = '';
    return '';
  }
}
