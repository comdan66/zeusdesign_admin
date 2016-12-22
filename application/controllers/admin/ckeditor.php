<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Ckeditor extends Admin_controller {

  public function image_browser ($type = 'cke', $offset = 0) {
    $gets = http_build_query (OAInput::get ());
    
    $types = array ('cke' => '上傳紀錄', 'imb' => '我的圖庫');

    $uri_1 = 'admin/ckeditor/image_browser';

    $columns = array ();
    $configs = array_merge (explode ('/', $uri_1), array (in_array ($type = trim ($type), array_keys ($types)) ? $type : 'cke', '%s', '?' . $gets));
    $conditions = conditions ($columns, $configs);

    $limit = 12;
    switch ($type) {
      case 'imb':
        OaModel::addConditions ($conditions, 'user_id = ?', User::current () ? User::current ()->id : 0);
        $total = ImageBase::count (array ('conditions' => $conditions));
        $objs = ImageBase::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'conditions' => $conditions));
        break;
      
      default:
        $total = CkeditorImage::count (array ('conditions' => $conditions));
        $objs = CkeditorImage::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'conditions' => $conditions));
        break;
    }
    return $this->set_frame_path ('frame', 'pure')->load_view (array (
        'objs' => $objs,
        'gets' => $gets,
        'type' => $type,
        'types' => $types,
        'uri_1' => $uri_1,
        // 'imbs' => $imbs
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
      ));
  }
  public function image_upload () {
    $funcNum = $_GET['CKEditorFuncNum'];
    $upload = OAInput::file ('upload');

    if (!($upload && verifyCreateOrm ($img = CkeditorImage::create (array ('name' => ''))) && $img->name->put ($upload, true))) echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction ($funcNum, '', '上傳失敗！');</script>";
    else echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction ($funcNum, '" . $img->name->url ('400h') . "', '上傳成功！');</script>";
  }
}
