<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Pv extends Api_controller {

  public function __construct () {
    parent::__construct ();
    header ("Access-Control-Allow-Origin: https://www.zeusdesign.com.tw");
  }
  public function index ($k = '', $id = 0) {
    if (!($k && in_array ($k, array ('work', 'article')))) return $this->output_error_json ('呼叫錯誤！');
    $model = $k == 'work' ? 'Work' : 'Article';
    if (!$obj = $model::find ('one', array ('select' => 'id, pv', 'conditions' => array ('id = ?', $id)))) return $this->output_error_json ('呼叫錯誤！');

    $obj->pv += 1;
    $update = $model::transaction (function () use ($obj) { return $obj->save (); });

    if (!$update) return $this->output_error_json ('呼叫錯誤！');

    return $this->output_json ('呼叫成功！');
  }
}
