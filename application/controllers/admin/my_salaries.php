<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class My_salaries extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('member')))
      return redirect_message (array ('admin'), array (
            '_flash_danger' => '您的權限不足，或者頁面不存在。'
          ));

    $this->uri_1 = 'admin/my-salaries';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Salary::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $id, User::current ()->id))))))
        return redirect_message (array ($this->uri_1), array (
            '_flash_danger' => '找不到該筆資料。'
          ));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }
  private function _search_columns () {
    return array ( 
        array ('key' => 'created_at', 'title' => '新增日期', 'sql' => 'YEAR(created_at) = ?', 'select' => array_map (function ($val) { return array ('value' => $val, 'text' => $val . '年');}, ['2017', '2016', '2015', '2014', '2013', '2012'])),
        array ('key' => 'is_finished', 'title' => '是否已給付', 'sql' => 'is_finished = ?', 'select' => array_map (function ($key) { return array ('value' => $key, 'text' => Salary::$finishNames[$key]);}, array_keys (Salary::$finishNames))),
        array ('key' => 'name',    'title' => '專案名稱', 'sql' => 'name LIKE ?'), 
      );
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);
    OaModel::addConditions ($conditions, 'user_id = ?', User::current ()->id);

    $limit = 10;
    $total = Salary::count (array ('conditions' => $conditions));
    $offset = $offset < $total ? $offset : 0;

    $this->load->library ('pagination');
    $pagination = $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
    $objs = Salary::find ('all', array (
        'offset' => $offset,
        'limit' => $limit,
        'order' => 'is_finished ASC, id DESC',
        'include' => array ('user'),
        'conditions' => $conditions
      ));
    $conditions1 = array_values ($conditions);
    $conditions2 = array_values ($conditions);
    OaModel::addConditions ($conditions1, 'is_finished = ?', Salary::NO_FINISHED);
    OaModel::addConditions ($conditions2, 'is_finished != ?', Salary::NO_FINISHED);

    return $this->load_view (array (
        'objs' => $objs,
        'money1' => array_sum (column_array (Salary::find ('all', array ('select' => 'money', 'conditions' => $conditions1)), 'money')),
        'money2' => array_sum (column_array (Salary::find ('all', array ('select' => 'money', 'conditions' => $conditions2)), 'money')),
        'pagination' => $pagination,
        'columns' => $columns
      ));
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

    $objs = Salary::find ('all', array (
        'order' => 'id DESC',
        'include' => array ('user'),
        'conditions' => $conditions
      ));

    $this->load->library ('OAExcel');
    $infos = array (array ('title' => '受薪人員',     'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,           'exp' => '$obj->user->name'),
                    array ('title' => '專案名稱',    'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,           'exp' => '$obj->name'),
                    array ('title' => '金額',       'format' => PHPExcel_Style_NumberFormat::FORMAT_MONEY,         'exp' => '$obj->money'),
                    array ('title' => '備註',       'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,           'exp' => '$obj->memo'));

    $excel = $this->_build_excel ($objs, $infos);
    $excel->getActiveSheet ()->setTitle ('薪資列表');

    $excel->setActiveSheetIndex (0);

    $filename = '宙思_薪資_' . date ('Ymd') . '.xlsx';
    header ('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf8');
    header ('Content-Disposition: attachment; filename=' . $filename);

    $objWriter = new PHPExcel_Writer_Excel2007 ($excel);
    $objWriter->save ("php://output");

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-p', 'content' => '匯出 ' . count ($objs) . ' 筆薪資。', 'desc' => '已經成功的匯出 ' . $filename . '，全部有 ' . count ($objs) . ' 筆薪資紀錄，細節可詢問工程師。', 'backup' => json_encode (array_map (function ($obj) { return $obj->to_array (); }, $objs))));
  }
}
