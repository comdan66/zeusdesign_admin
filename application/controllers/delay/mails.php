<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Mails extends Delay_controller {

  public function new_task () {
    $id = OAInput::post ('id');

    $this->load->library ('fb');

    $content = Mail::renderContent ('mail/new_task', array (
        'user' => 'Teresa',
        'url' => Fb::loginUrl ('platform', 'fb_sign_in', 'admin', 'my-tasks', '1', 'show'),
        'detail' => array (
            array ('title' => '任務名稱：', 'value' => '回家'),
            array ('title' => '任務名稱：', 'value' => '回家'),
          )
      ));

    Mail::send ('title', $content, array (
        'comdan66@gmail.com' => 'oa',
      ), array (
        'teresa@zeusdesign.com.tw' => 'Teresa',
      ));
  }
}
