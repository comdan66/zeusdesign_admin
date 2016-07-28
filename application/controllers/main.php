<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Main extends Site_controller {

  public function index () {
    if (User::current () && User::current ()->is_login ())
      return redirect_message (array ('index'), array ());
    else 
      return redirect_message (array ('login'), array ());
  }
}
