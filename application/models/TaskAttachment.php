<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class TaskAttachment extends OaModel {

  static $table_name = 'task_attachments';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmFileUploader::bind ('file', 'TaskAttachmentFileFileUploader');
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'         => $this->id,
      'task_id'    => $this->task_id,
      'title'     => $this->title,
      'file'    => $this->file,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public function destroy () {
    return $this->delete ();
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
    return res_url ('res', 'image', 'extension', $name);
  }
}