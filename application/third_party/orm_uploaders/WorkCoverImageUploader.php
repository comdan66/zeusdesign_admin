<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class WorkCoverImageUploader extends OrmImageUploader {

  public function getVersions () {
    return array (
        '' => array (),
        '300w' => array ('resize', 300, 300, 'width'),
        '1200x630c' => array ('adaptiveResizeQuadrant', 1200, 630, 'c'),
      );
  }
}