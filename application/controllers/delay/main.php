<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */
class Main extends Delay_controller {

  public function index () {
    $sec = OAInput::post ('sec');
    sleep ($sec);
  }
}
