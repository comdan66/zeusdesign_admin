<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Users extends Api_controller {

  public function __construct () {
    parent::__construct ();

  }
  public function token () {
    if (!(($posts = OAInput::post ()) && isset ($posts['id']) && $posts['id'] && ($user = User::find ('one', array ('conditions' => array ('id = ?', $posts['id']))))))
      return $this->output_error_json ('錯誤，沒有該位使用者！');

    return $this->output_json ($user->to_array ());
  }
  public function notification () {
    if (!(($posts = OAInput::post ()) && isset ($posts['id']) && $posts['id'] && isset ($posts['token']) && $posts['token'] && ($user = User::find ('one', array ('select' => 'id, device_token', 'conditions' => array ('id = ?', $posts['id']))))))
      return $this->output_error_json ('錯誤，沒有該位使用者！');
    
    $user->device_token = $posts['token'];
    Schedule::transaction (function () use ($user) { return $user->save (); });

    return $this->output_json (array ('message' => 'success'));
  }
}
