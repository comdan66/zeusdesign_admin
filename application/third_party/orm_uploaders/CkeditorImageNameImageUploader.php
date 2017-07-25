<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class CkeditorImageNameImageUploader extends OrmImageUploader {

  public function d4Url () {
    return res_url ('res', 'image', 'image.jpg');
  }
  public function getVersions () {
    return array (
        '' => array (),
        '400h' => array ('resize', 400, 400, 'height'),
      );
  }
}