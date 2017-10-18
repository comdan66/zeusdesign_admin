<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Test extends Api_controller {

  public function __construct () {
    parent::__construct ();
  }
  public function index () {
    return $this->output_json (array (
        array ('id' => 1, 'name' => 'oa'),
        array ('id' => 2, 'name' => 'ob'),
        array ('id' => 3, 'name' => 'oc'),
      ));
  }
}
