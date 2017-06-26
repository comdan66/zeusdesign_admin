<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class My_calendar extends Admin_controller {
  private $uri_1 = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('member')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/my-calendar';

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function ajax () {
    // type 1 自己的
    // type 2 系統的
    // type 3 朋友的

    return $this->output_json (array (
            array (
                'y' => '2017',
                'm' => '4',
                'd' => '30',
                'c' => array (
                    array ('img' => 'https://graph.facebook.com/1222557214424285/picture?width=100&height=100',
                           'text' => '上學1',
                           'color' => 'ff0080',
                           'type' => '1'),
                  )
              ),
            array (
                'y' => '2017',
                'm' => '5',
                'd' => '30',
                'c' => array (
                    array ('img' => 'https://graph.facebook.com/1222557214424285/picture?width=100&height=100',
                           'text' => '上學1',
                           'color' => 'ff0080',
                           'type' => '1'),
                  )
              ),
            array (
                'y' => '2017',
                'm' => '6',
                'd' => '23',
                'c' => array (
                    array ('img' => 'https://graph.facebook.com/1222557214424285/picture?width=100&height=100',
                           'text' => '上學',
                           'color' => 'ff0080',
                           'type' => '2'),
                  )
              ),
            array (
                'y' => '2017',
                'm' => '6',
                'd' => '24',
                'c' => array (
                    array ('img' => 'https://graph.facebook.com/1222557214424285/picture?width=100&height=100',
                           'text' => '下課',
                           'color' => 'ff0080',
                           'type' => '2'),
                    array ('img' => 'https://graph.facebook.com/1222557214424285/picture?width=100&height=100',
                           'text' => '吃飯',
                           'color' => 'ffcc66',
                           'type' => '3'),
                    array ('img' => 'https://graph.facebook.com/1222557214424285/picture?width=100&height=100',
                           'text' => '吃飯',
                           'color' => 'ff0080',
                           'type' => '1'),
                    array ('img' => 'https://graph.facebook.com/1222557214424285/picture?width=100&height=100',
                           'text' => '吃飯',
                           'color' => 'ffcc66',
                           'type' => '1'),
                    array ('img' => 'https://graph.facebook.com/1222557214424285/picture?width=100&height=100',
                           'text' => '吃飯',
                           'color' => 'ff0080',
                           'type' => '3'),
                  )
              ),
      ));
  }
  public function index () {
    $this->load_view ();
  }
}
