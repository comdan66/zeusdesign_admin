<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class ImageBaseNameImageUploader extends OrmImageUploader {

  public function getVersions () {
    return array (
        '' => array (),
        '500w' => array ('resize', 500, 500, 'width'),
      );
  }
}