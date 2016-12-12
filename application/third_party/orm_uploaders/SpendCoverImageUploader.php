<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class SpendCoverImageUploader extends OrmImageUploader {

  public function getVersions () {
    return array (
        '' => array (),
        '100x100c' => array ('adaptiveResizeQuadrant', 100, 100, 'c'),
        '500x500c' => array ('adaptiveResizeQuadrant', 500, 500, 'c'),
      );
  }
}