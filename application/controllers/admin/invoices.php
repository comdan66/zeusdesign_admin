<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Invoices extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();
    
    $this->uri_1 = 'admin/invoices';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Invoice::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array (
            '_flash_danger' => '找不到該筆資料。'
          ));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = Invoice::count (array ('conditions' => $conditions));
    $offset = $offset < $total ? $offset : 0;

    $this->load->library ('pagination');
    $pagination = $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
    $objs = Invoice::find ('all', array (
        'offset' => $offset,
        'limit' => $limit,
        'order' => 'id DESC',
        'include' => array ('user', 'tag', 'contact'),
        'conditions' => $conditions
      ));

    return $this->load_view (array (
        'objs' => $objs,
        'pagination' => $pagination,
        'columns' => $columns
      ));
  }
  public function add () {
    $posts = Session::getData ('posts', true);

    $posts['sources'] = array_values (array_filter (isset ($posts['sources']) && $posts['sources'] ? $posts['sources'] : array (), function ($source) {
      return (isset ($source['title']) && $source['title']) || (isset ($source['href']) && $source['href']);
    }));

    return $this->load_view (array (
        'posts' => $posts
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => '非 POST 方法，錯誤的頁面請求。'
        ));

    $posts = OAInput::post ();

    if (($msg = $this->_validation_must ($posts)) || ($msg = $this->_validation ($posts)))
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    $create = Invoice::transaction (function () use (&$obj, $posts) {
      return verifyCreateOrm ($obj = Invoice::create (array_intersect_key ($posts, Invoice::table ()->columns)));
    });

    if (!$create)
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => '新增失敗！',
          'posts' => $posts
        ));

    return redirect_message (array ($this->uri_1), array (
        '_flash_info' => '新增成功！'
      ));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
                    'posts' => $posts,
                    'obj' => $this->obj
                  ));
  }
  public function update () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => '非 POST 方法，錯誤的頁面請求。'
        ));

    $posts = OAInput::post ();
    $is_api = isset ($posts['_type']) && ($posts['_type'] == 'api') ? true : false;
    
    if ($msg = $this->_validation ($posts))
      return $is_api ? $this->output_error_json ($msg) : redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    if ($columns = array_intersect_key ($posts, $this->obj->table ()->columns))
      foreach ($columns as $column => $value)
        $this->obj->$column = $value;

    $obj = $this->obj;
    $update = Invoice::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    });

    if (!$update)
      return $is_api ? $this->output_error_json ('更新失敗！') : redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => '更新失敗！',
          'posts' => $posts
        ));

    return $is_api ? $this->output_json ($obj->to_array ()) : redirect_message (array ($this->uri_1), array (
        '_flash_info' => '更新成功！'
      ));
  }
  public function destroy () {
    $obj = $this->obj;
    $delete = Invoice::transaction (function () use ($obj) {
      return $obj->destroy ();
    });

    if (!$delete)
      return redirect_message (array ($this->uri_1), array (
          '_flash_danger' => '刪除失敗！',
        ));

    return redirect_message (array ($this->uri_1), array (
        '_flash_info' => '刪除成功！'
      ));
  }
  private function _validation (&$posts) {
    $keys = array ('invoice_tag_id', 'invoice_contact_id', 'user_id', 'name', 'quantity', 'single_money', 'all_money', 'closing_at', 'is_finished', 'memo');

    $new_posts = array (); foreach ($posts as $key => $value) if (in_array ($key, $keys)) $new_posts[$key] = $value;
    $posts = $new_posts;

    if (isset ($posts['user_id']) && !(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find_by_id ($posts['user_id']))) return '作者 ID 格式錯誤！';
    if (isset ($posts['name']) && !($posts['name'] = trim ($posts['name']))) return '專案名稱格式錯誤！';
    
    if (isset ($posts['invoice_tag_id']) && ($posts['invoice_tag_id'] = trim ($posts['invoice_tag_id'])) && !(is_numeric ($posts['invoice_tag_id']) && InvoiceTag::find_by_id ($posts['invoice_tag_id']))) return '分類 ID 格式錯誤！';
    if (isset ($posts['invoice_contact_id']) && ($posts['invoice_contact_id'] = trim ($posts['invoice_contact_id'])) && !(is_numeric ($posts['invoice_contact_id'])) && InvoiceContact::find_by_id ($posts['invoice_contact_id'], array ('conditions' => array ('invoice_contact_id != 0')))) return '窗口 ID 格式錯誤！';
    if (isset ($posts['quantity']) && ($posts['quantity'] = trim ($posts['quantity'])) && !is_numeric ($posts['quantity'])) return '數量格式錯誤！';
    if (isset ($posts['single_money']) && ($posts['single_money'] = trim ($posts['single_money'])) && !is_numeric ($posts['single_money'])) return '單價格式錯誤！';
    if (isset ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) && !is_string ($posts['memo'])) return '備註格式錯誤！';
    
    if (isset ($posts['all_money']) && !is_numeric ($posts['all_money'] = trim ($posts['all_money']))) return '總金額格式錯誤！';
    if (isset ($posts['closing_at']) && !($posts['closing_at'] = trim ($posts['closing_at']))) return '結案日期格式錯誤！';
    if (isset ($posts['is_finished']) && !(is_numeric ($posts['is_finished'] = trim ($posts['is_finished'])) && in_array ($posts['is_finished'], array_keys (Invoice::$finishNames)))) return '請款狀態格式錯誤！';
    return '';
  }
  private function _validation_must (&$posts) {
    if (!isset ($posts['invoice_tag_id'])) return '沒有選擇 分類！';
    if (!isset ($posts['invoice_contact_id'])) return '沒有選擇 窗口！';
    if (!isset ($posts['user_id'])) return '沒有選擇 負責人！';
    if (!isset ($posts['name'])) return '沒有填寫 專案名稱！';
    if (!isset ($posts['all_money'])) return '沒有填寫 總金額！';
    if (!isset ($posts['closing_at'])) return '沒有填寫 結案日期！';
    return '';
  }


  private function _search_columns () {
    return array ( 
        array ('key' => 'name', 'title' => '專案名稱', 'sql' => 'name LIKE ?'), 
        array ('key' => 'invoice_contact_id', 'title' => '窗口', 'sql' => 'invoice_contact_id = ?', 'select' => array_map (function ($contact) { return array ('value' => $contact->id, 'text' => $contact->parent->name . ' - ' . $contact->name);}, InvoiceContact::find ('all', array ('select' => 'id, name, invoice_contact_id', 'order' => 'invoice_contact_id ASC', 'conditions' => array ('invoice_contact_id != 0'))))),
        array ('key' => 'is_finished', 'title' => '是否請款', 'sql' => 'is_finished = ?', 'select' => array_map (function ($key) { return array ('value' => $key, 'text' => Invoice::$finishNames[$key]);}, array_keys (Invoice::$finishNames))),
        array ('key' => 'user_id', 'title' => '作者', 'sql' => 'user_id = ?', 'select' => array_map (function ($user) { return array ('value' => $user->id, 'text' => $user->name);}, User::all (array ('select' => 'id, name')))),
      );
  }


  private function _build_excel ($objs, $infos) {
    $excel = new OAExcel ();
    
    $excel->getActiveSheet ()->getRowDimension (1)->setRowHeight (25);
    $excel->getActiveSheet ()->getColumnDimension ('A')->setWidth (15);
    $excel->getActiveSheet ()->getColumnDimension ('B')->setWidth (10);
    $excel->getActiveSheet ()->getColumnDimension ('C')->setWidth (10);
    $excel->getActiveSheet ()->getColumnDimension ('D')->setWidth (8);
    $excel->getActiveSheet ()->getColumnDimension ('E')->setWidth (8);
    $excel->getActiveSheet ()->getColumnDimension ('F')->setWidth (8);
    $excel->getActiveSheet ()->getColumnDimension ('G')->setWidth (11);
    $excel->getActiveSheet ()->getColumnDimension ('H')->setWidth (15);
    $excel->getActiveSheet ()->freezePaneByColumnAndRow (0, 2);
    $excel->getActiveSheet ()->getStyle ('G')->getAlignment ()->setWrapText (true); 

    $excel->getActiveSheet ()->getStyle ('A1:H1')->applyFromArray (array (
      'fill' => array (
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'fff3ca')
      ),
      'borders' => array (
        'allborders' => array (
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('rgb' => '888888')))));

    foreach ($objs as $i => $obj) {
      $j = 0;
      foreach ($infos as $info) {
        if ($i == 0) {
          $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 1))->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_TOP);
          $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 1))->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 1))->getFont ()->setName ('新細明體');
          $excel->getActiveSheet ()->SetCellValue (chr (65 + $j) . ($i + 1), $info['title']);
        }
        eval ('$val = ' . $info['exp'] . ';');
        
        $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 2))->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_TOP);
        $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 2))->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 2))->getFont ()->setName ("新細明體");
        $excel->getActiveSheet ()->SetCellValue (chr (65 + $j) . ($i + 2), $val);
        $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 2))->getNumberFormat ()->setFormatCode ($info['format']);
        $j++;
      }
    }
    return $excel;
  }

  public function export () {
    $columns = $this->_search_columns ();
    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $objs = Invoice::find ('all', array (
        'order' => 'id DESC',
        'include' => array ('user', 'tag', 'contact'),
        'conditions' => $conditions
      ));

    $this->load->library ('OAExcel');
    $infos = array (array ('title' => '專案名稱', 'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,          'exp' => '$obj->name'),
                    array ('title' => '窗口',     'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,          'exp' => '$obj->contact && $obj->contact->parent ? $obj->contact->parent->name . " - " . $obj->contact->name : "-"'),
                    array ('title' => '數量',   'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER,        'exp' => '$obj->quantity'),
                    array ('title' => '單價',   'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER,        'exp' => '$obj->single_money'),
                    array ('title' => '總金額',   'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER,        'exp' => '$obj->all_money'),
                    array ('title' => '分類',     'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,          'exp' => '$obj->tag ? $obj->tag->name : "-"'),
                    array ('title' => '結案日期', 'format' => PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2, 'exp' => '$obj->closing_at ? $obj->closing_at->format ("Y-m-d") : ""'),
                    array ('title' => '備註',    'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,           'exp' => '$obj->memo'));

    $excel = $this->_build_excel ($objs, $infos);
    $excel->getActiveSheet ()->setTitle ('帳務列表');

    // $excel->createSheet (1);
    // $excel->setActiveSheetIndex (1)->setTitle ('圖片列表');

    // $that = $this;

    // $filepaths = array_filter ($this->_array_2d_to_1d (array_map (function ($invoice) use ($that, $excel, &$i) {
    //   return array_merge (array ($that->_excel_add_image ($excel->getActiveSheet (), $invoice->cover, ($i = isset ($i) ? $i + 1 : 1))),
    //     array_map (function ($picture) use ($that, $excel, &$i) {
    //       return $that->_excel_add_image ($excel->getActiveSheet (), $picture->name, ++$i);
    //     }, $invoice->pictures));
    // }, $objs)));

    $excel->setActiveSheetIndex (0);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf8');
    header('Content-Disposition: attachment; filename=宙思_帳務_' . date ('Ymd') . '.xlsx');

    $objWriter = new PHPExcel_Writer_Excel2007 ($excel);
    $objWriter->save ("php://output");
    
    // array_map (function ($filepath) {return @unlink ($filepath); }, $filepaths);
  }
}
