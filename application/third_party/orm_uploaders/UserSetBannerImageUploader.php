<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class UserSetBannerImageUploader extends OrmImageUploader {

  public function d4Url () {
    return res_url ('res', 'image', 'banner.jpg');
  }
  public function getVersions () {
    return array (
        '' => array (),
        '1200x630c' => array ('adaptiveResizeQuadrant', 1200, 630, 't'),
      );
  }
}