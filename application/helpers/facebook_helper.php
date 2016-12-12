<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */
if (!function_exists ('fb_get_login_url')) {
  function facebook () {
    $CI =& get_instance ();
    if (!isset ($CI->fb))
      $CI->load->library ('fb');
    return $CI->fb;
  }
}
