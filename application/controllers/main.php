<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Main extends Site_controller {

  public function index () {
    return redirect ('https://www.zeusdesign.com.tw/', 'refresh');
    $this->load_view ();
  }
}
