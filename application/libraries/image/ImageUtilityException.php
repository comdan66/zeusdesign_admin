<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class ImageUtilityException extends Exception {
  private $messages = array ();

  public function __construct () {
    $this->messages = array_2d_to_1d (func_get_args ());
  }
  // return array
  public function getMessages () {
    return $this->messages;
  }
}