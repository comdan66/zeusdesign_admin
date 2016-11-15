<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Billous extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('bills')))
      return redirect_message (array ('admin'), array (
            '_flash_danger' => '您的權限不足，或者頁面不存在。'
          ));

    $this->uri_1 = 'admin/billous';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Billou::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array (
            '_flash_danger' => '找不到該筆資料。'
          ));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }
  private function _search_columns () {
    return array ( 
        array ('key' => 'month',   'title' => '月份',    'sql' => 'MONTH(date_at) = ?', 'select' => array (array ('value' => '1', 'text' => '一月'), array ('value' => '2', 'text' => '二月'), array ('value' => '3', 'text' => '三月'), array ('value' => '4', 'text' => '四月'), array ('value' => '5', 'text' => '五月'), array ('value' => '6', 'text' => '六月'), array ('value' => '7', 'text' => '七月'), array ('value' => '8', 'text' => '八月'), array ('value' => '9', 'text' => '九月'), array ('value' => '10', 'text' => '十月'), array ('value' => '11', 'text' => '十一月'), array ('value' => '12', 'text' => '十二月'))), 
        array ('key' => 'year',    'title' => '年份',    'sql' => 'YEAR(date_at) = ?', 'select' => array (array ('value' => '2014', 'text' => '2014 年'), array ('value' => '2015', 'text' => '2015 年'), array ('value' => '2016', 'text' => '2016 年'), array ('value' => '2017', 'text' => '2017 年'))), 
        array ('key' => 'name',    'title' => '項目名稱', 'sql' => 'name LIKE ?'), 
        array ('key' => 'user_id', 'title' => '新增者',   'sql' => 'user_id = ?', 'select' => array_map (function ($user) { return array ('value' => $user->id, 'text' => $user->name);}, User::all (array ('select' => 'id, name')))),
      );
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = Billou::count (array ('conditions' => $conditions));
    $offset = $offset < $total ? $offset : 0;

    $this->load->library ('pagination');
    $pagination = $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
    $objs = Billou::find ('all', array (
        'offset' => $offset,
        'limit' => $limit,
        'order' => 'id DESC',
        'include' => array ('user'),
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

    $create = Billou::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Billou::create (array_intersect_key ($posts, Billou::table ()->columns))); });

    if (!$create)
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => '新增失敗！',
          'posts' => $posts
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ob', 'content' => '新增一筆出帳。', 'desc' => '專案名稱為：「' . $obj->mini_name () . '」，金額為：「' . number_format ($obj->money) . '」。', 'backup' => json_encode ($obj->to_array ())));
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
    $update = Billou::transaction (function () use ($obj, $posts) { return $obj->save (); });

    if (!$update)
      return $is_api ? $this->output_error_json ('更新失敗！') : redirect_message (array ($this->uri_1, $this->obj->id, 'edit'), array (
          '_flash_danger' => '更新失敗！',
          'posts' => $posts
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ob', 'content' => '修改一筆出帳。', 'desc' => '專案名稱為：「' . $obj->mini_name () . '」，金額為：「' . number_format ($obj->money) . '」。', 'backup' => json_encode ($obj->to_array ())));
    return $is_api ? $this->output_json ($obj->to_array ()) : redirect_message (array ($this->uri_1), array (
        '_flash_info' => '更新成功！'
      ));
  }
  public function destroy () {
    $obj = $this->obj;
    $backup = json_encode ($obj->to_array ());
    $delete = Billou::transaction (function () use ($obj) { return $obj->destroy (); });

    if (!$delete)
      return redirect_message (array ($this->uri_1), array (
          '_flash_danger' => '刪除失敗！',
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ob', 'content' => '刪除一筆出帳。', 'desc' => '已經備份了刪除紀錄，細節可詢問工程師。', 'backup' => $backup));
    return redirect_message (array ($this->uri_1), array (
        '_flash_info' => '刪除成功！'
      ));
  }
  private function _validation (&$posts) {
    $keys = array ('user_id', 'name', 'money', 'date_at', 'is_invoice', 'memo');

    $new_posts = array (); foreach ($posts as $key => $value) if (in_array ($key, $keys)) $new_posts[$key] = $value;
    $posts = $new_posts;

    if (isset ($posts['user_id']) && !(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find_by_id ($posts['user_id']))) return '新增者 ID 格式錯誤或未填寫！';
    if (isset ($posts['name']) && !($posts['name'] = trim ($posts['name']))) return '項目名稱格式錯誤或未填寫！';
    if (isset ($posts['money']) && !(is_numeric ($posts['money'] = trim ($posts['money'])) && $posts['money'] >= 0)) return '金額格式錯誤或未填寫！';
    if (isset ($posts['date_at']) && !($posts['date_at'] = trim ($posts['date_at']))) return '日期格式錯誤！';
    if (isset ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) && !is_string ($posts['memo'])) return '備註格式錯誤！';
    if (isset ($posts['is_invoice']) && !(is_numeric ($posts['is_invoice'] = trim ($posts['is_invoice'])) && in_array ($posts['is_invoice'], array_keys (Billou::$invoiceNames)))) return '是否有發票格式錯誤！';
    
    return '';
  }
  private function _validation_must (&$posts) {
    if (!isset ($posts['user_id'])) return '沒有填寫 新增者！';
    if (!isset ($posts['name'])) return '沒有填寫 項目名稱！';
    if (!isset ($posts['money'])) return '沒有填寫 金額！';
    if (!isset ($posts['date_at'])) return '沒有填寫 日期！';
    if (!isset ($posts['is_invoice'])) return '沒有填寫 是否有發票！';
    return '';
  }
  private function _build_excel ($objs, $infos) {
    $excel = new OAExcel ();
    
    $excel->getActiveSheet ()->getRowDimension (1)->setRowHeight (20);
    $excel->getActiveSheet ()->freezePaneByColumnAndRow (0, 2);
    $excel->getActiveSheet ()->getStyle ('A1:' . chr (65 + count ($infos) - 1) . '1')->applyFromArray (array (
      'fill' => array (
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'fff3ca')
      ),));

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

    $objs = Billou::find ('all', array (
        'order' => 'id DESC',
        'include' => array ('user'),
        'conditions' => $conditions
      ));

    $this->load->library ('OAExcel');
    $infos = array (array ('title' => '新增者',     'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,           'exp' => '$obj->user->name'),
                    array ('title' => '項目名稱',    'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,           'exp' => '$obj->name'),
                    array ('title' => '金額',       'format' => PHPExcel_Style_NumberFormat::FORMAT_MONEY,         'exp' => '$obj->money'),
                    array ('title' => '是否有發票',  'format' => PHPExcel_Style_NumberFormat::FORMAT_MONEY,         'exp' => 'Billou::$invoiceNames[$obj->is_invoice]'),
                    array ('title' => '備註',       'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,           'exp' => '$obj->memo'),
                    array ('title' => '日期',       'format' => PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2, 'exp' => '$obj->date_at->format ("Y-m-d")'));

    $excel = $this->_build_excel ($objs, $infos);
    $excel->getActiveSheet ()->setTitle ('出帳列表');

    $excel->setActiveSheetIndex (0);

    $filename = '宙思_出帳_' . date ('Ymd') . '.xlsx';
    header ('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf8');
    header ('Content-Disposition: attachment; filename=' . $filename);

    $objWriter = new PHPExcel_Writer_Excel2007 ($excel);
    $objWriter->save ("php://output");

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-p', 'content' => '匯出 ' . count ($objs) . ' 筆出帳。', 'desc' => '已經成功的匯出 ' . $filename . '，全部有 ' . count ($objs) . ' 筆出帳紀錄，細節可詢問工程師。', 'backup' => json_encode (array_map (function ($obj) { return $obj->to_array (); }, $objs))));
  }
}
