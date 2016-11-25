<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
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
}
