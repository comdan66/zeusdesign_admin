<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class TaskCommit extends OaModel {

  static $table_name = 'task_commits';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmFileUploader::bind ('file', 'TaskCommitFileFileUploader');
  }
  public function file_icon () {
    $name = 'd4.png';
    switch (pathinfo ((string)$this->file, PATHINFO_EXTENSION)) {
      case 'jpg': case 'jpeg': $name = 'jpg.png'; break;
      
      case 'ppt': case 'pptx': $name = 'ppt.png'; break;
      
      // case 'doc': case 'docx':
      //   $name = 'doc.png';
      //   break;
      
      case 'xls': case 'xlsx': $name = 'xls.png'; break;
      case 'gif': $name = 'gif.png'; break;
      case 'png': $name = 'png.png'; break;
      case 'pdf': $name = 'pdf.png'; break;
      case 'zip': $name = 'zip.png'; break;
      default: $name = 'd4.png'; break;
    }
    return base_url ('res', 'image', 'extension', $name);
  }
  public function mini_content ($length = 100) {
    if (!isset ($this->content)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->content), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = $this->getBackup ();
    return $has ? array (
        '_' => $var,
      ) : $var;
  }
}