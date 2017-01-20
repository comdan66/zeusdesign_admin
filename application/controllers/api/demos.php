<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Demos extends Api_controller {

  public function __construct () {
    parent::__construct ();
    header ("Access-Control-Allow-Origin: http://" . (ENVIRONMENT != 'production' ? 'dev.' : '') . "demo.zeusdesign.com.tw");
  }
  public function psw () {
    $gets = OAInput::get ();

    if (!(isset ($gets['uid']) && ($obj = Demo::find ('one', array ('conditions' => array ('uid = ?', $gets['uid']))))))
      return $this->output_error_json ('找不到該筆資料。');

    if ($obj->is_enabled != Demo::ENABLE_YES)
      return $this->output_error_json ('該筆資料目前不公開。');

    return $this->output_json (!!$obj->password);
  }
  public function show () {
    $gets = OAInput::get ();

    if (!(isset ($gets['uid']) && ($obj = Demo::find ('one', array ('conditions' => array ('uid = ?', $gets['uid']))))))
      return $this->output_error_json ('找不到該筆資料。');

    if ($obj->is_enabled != Demo::ENABLE_YES)
      return $this->output_error_json ('該筆資料目前不公開。');
    
    if ($obj->password && !(isset ($gets['psw']) && ($gets['psw'] = trim ($gets['psw'])) && ($gets['psw'] == $obj->password)))
      return $this->output_error_json ('該筆資料密碼不正確。');

    return $this->output_json (array_merge (array (
          'id' => $obj->id,
          'name' => $obj->name,
          'memo' => $obj->memo,
          'enabled' => $obj->is_enabled == Demo::ENABLE_YES,
          'mobile' => $obj->is_mobile == Demo::MOBILE_YES,
      ), array ('images' => array_map (function ($image) {
        return array (
            'id' => $image->id,
            'sort' => $image->sort,
            'url' => array (
                'ori' => $image->name->url (),
                'w100' => $image->name->url ('100w'),
              ),
          );
    }, $obj->images))));
  }
}
