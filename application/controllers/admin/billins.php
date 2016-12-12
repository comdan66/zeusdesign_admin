<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Billins extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = 'icon-ib';

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('bills')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/billins';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'is_finished', 'is_pay')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Billin::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }

  private function _search_columns () {
    return array ( 
        array ('key' => 'month',   'title' => '月份',    'sql' => 'MONTH(date_at) = ?', 'select' => array (array ('value' => '1', 'text' => '一月'), array ('value' => '2', 'text' => '二月'), array ('value' => '3', 'text' => '三月'), array ('value' => '4', 'text' => '四月'), array ('value' => '5', 'text' => '五月'), array ('value' => '6', 'text' => '六月'), array ('value' => '7', 'text' => '七月'), array ('value' => '8', 'text' => '八月'), array ('value' => '9', 'text' => '九月'), array ('value' => '10', 'text' => '十月'), array ('value' => '11', 'text' => '十一月'), array ('value' => '12', 'text' => '十二月'))), 
        array ('key' => 'year',    'title' => '年份',    'sql' => 'YEAR(date_at) = ?', 'select' => array_reverse (array (array ('value' => '2014', 'text' => '2014 年'), array ('value' => '2015', 'text' => '2015 年'), array ('value' => '2016', 'text' => '2016 年'), array ('value' => '2017', 'text' => '2017 年')))), 
        array ('key' => 'name',    'title' => '專案名稱', 'sql' => 'name LIKE ?'), 
        array ('key' => 'user_id', 'title' => '負責人',   'sql' => 'user_id = ?', 'select' => array_map (function ($user) { return array ('value' => $user->id, 'text' => $user->name);}, User::all (array ('select' => 'id, name')))),
      );
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = Billin::count (array ('conditions' => $conditions));
    $offset = $offset < $total ? $offset : 0;

    $this->load->library ('pagination');
    $pagination = $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
    $objs = Billin::find ('all', array ('offset' => $offset, 'limit' => $limit, 'order' => 'id DESC', 'include' => array ('user'), 'conditions' => $conditions));

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
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    
    if ($msg = $this->_validation_create ($posts))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if (!Billin::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Billin::create (array_intersect_key ($posts, Billin::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一筆入帳。',
      'desc' => '專案名稱為：「' . $obj->mini_name () . '」。',
      'backup' => json_encode ($obj->columns_val ())));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    if (!Billin::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一筆入帳。',
      'desc' => '專案名稱為：「' . $obj->mini_name () . '」。', 'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }
  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->columns_val (true);

    if (!Billin::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一筆入帳。',
      'desc' => '已經備份了刪除紀錄，細節可詢問工程師。',
      'backup'  => json_encode ($backup)));
    
    return redirect_message (array ($this->uri_1), array ('_flash_info' => '刪除成功！'));
  }
  public function is_finished () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);
    
    $validation = function (&$posts) {
      if (!isset ($posts['is_pay']))      return '沒有選擇 是否支付！';
      if (!(is_numeric ($posts['is_pay'] = trim ($posts['is_pay'])) && in_array ($posts['is_pay'], array_keys (Billin::$payNames)))) return '是否支付 格式錯誤！';
      return '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Billin::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '將一筆入帳標示為「' . Billin::$finishNames[$obj->is_finished] . '」。',
      'desc' => '將入帳 “' . $obj->name . '” 標示為 「' . Billin::$finishNames[$obj->is_finished] . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json ($obj->is_finished == Billin::IS_FINISHED);
  }
  public function is_pay () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);
    
    $validation = function (&$posts) {
      if (!isset ($posts['is_pay'])) return '沒有選擇 是否入帳！';
      if (!(is_numeric ($posts['is_pay'] = trim ($posts['is_pay'])) && in_array ($posts['is_pay'], array_keys (Billin::$finishNames)))) return '是否入帳 格式錯誤！';
      return '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Billin::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '將一筆入帳標示為「' . Billin::$payNames[$obj->is_pay] . '」。',
      'desc' => '將入帳 “' . $obj->name . '” 標示為 「' . Billin::$payNames[$obj->is_pay] . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json ($obj->is_pay == Billin::IS_FINISHED);
  }
  private function _validation_create (&$posts) {
    if (!isset ($posts['user_id'])) return '沒有選擇 負責人！';
    if (!isset ($posts['name'])) return '沒有填寫 專案名稱！';
    if (!isset ($posts['money'])) return '沒有填寫 總金額！';
    if (!isset ($posts['rate_name'])) return '沒有填寫 類型！';
    if (!isset ($posts['rate'])) return '沒有填寫 扣款百分比！';
    if (!isset ($posts['zeus_money'])) return '沒有填寫 宙思獲得！';
    if (!isset ($posts['date_at'])) return '沒有選擇 日期！';
    if (!isset ($posts['is_finished'])) return '沒有選擇 是否入帳！';
    if (!isset ($posts['is_pay'])) return '沒有選擇 是否支付！';

    if (!(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['user_id']))))) return '負責人 不存在！';
    if (!(is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '專案名稱 格式錯誤！';
    if (!(is_numeric ($posts['money'] = trim ($posts['money'])) && ($posts['money'] >= 0))) return '總金額 格式錯誤！';
    if (!(is_string ($posts['rate_name']) && ($posts['rate_name'] = trim ($posts['rate_name'])))) return '類型 格式錯誤！';
    if (!(is_numeric ($posts['rate'] = trim ($posts['rate'])) && ($posts['rate'] >= 0))) return '扣款百分比 格式錯誤！';
    if (!(is_numeric ($posts['zeus_money'] = trim ($posts['zeus_money'])) && ($posts['zeus_money'] >= 0))) return '宙思獲得 格式錯誤！';
    if (!(($posts['date_at'] = trim ($posts['date_at'])) && is_date ($posts['date_at']))) return '日期 格式錯誤！';
    if (!(is_numeric ($posts['is_finished'] = trim ($posts['is_finished'])) && in_array ($posts['is_finished'], array_keys (Billin::$finishNames)))) return '是否入帳 格式錯誤！';
    if (!(is_numeric ($posts['is_pay'] = trim ($posts['is_pay'])) && in_array ($posts['is_pay'], array_keys (Billin::$payNames)))) return '是否支付 格式錯誤！';

    $posts['memo'] = isset ($posts['memo']) && is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) ? $posts['memo'] : '';

    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
  public function export () {
    $columns = $this->_search_columns ();
    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $objs = Billin::find ('all', array ('order' => 'id DESC', 'include' => array ('user'), 'conditions' => $conditions));

    $this->load->library ('OAExcel');
    $infos = array (
      array ('title' => '負責人',       'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,          'exp' => '$obj->user->name'),
      array ('title' => '專案名稱',      'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,          'exp' => '$obj->name'),
      array ('title' => '總金額',       'format' => PHPExcel_Style_NumberFormat::FORMAT_MONEY,        'exp' => '$obj->money'),
      array ('title' => '類型',         'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER,        'exp' => '$obj->rate_name'),
      array ('title' => '扣款百分比(%)', 'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER,        'exp' => '$obj->rate;'),
      array ('title' => '宙思獲得',      'format' => PHPExcel_Style_NumberFormat::FORMAT_MONEY,        'exp' => '$obj->zeus_money'),
      array ('title' => '小計',         'format' => PHPExcel_Style_NumberFormat::FORMAT_MONEY,         'exp' => '$obj->money - $obj->zeus_money'),
      array ('title' => '備註',         'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,          'exp' => '$obj->memo'),
      array ('title' => '日期',         'format' => PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2, 'exp' => '$obj->date_at->format ("Y-m-d")'));

    $excel = $this->_build_excel ($objs, $infos);
    $excel->getActiveSheet ()->setTitle ('入帳列表');
    $excel->setActiveSheetIndex (0);

    $filename = '宙思入帳_' . date ('Ymd') . '.xlsx';
    header ('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf8');
    header ('Content-Disposition: attachment; filename=' . $filename);

    $objWriter = new PHPExcel_Writer_Excel2007 ($excel);
    $objWriter->save ("php://output");
   
    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-p', 'content' => '匯出 ' . count ($objs) . ' 筆入帳。', 'desc' => '已經成功的匯出 ' . $filename . '，全部有 ' . count ($objs) . ' 筆入帳紀錄。', 'backup' => json_encode (array_map (function ($obj) { return $obj->columns_val (); }, $objs))));
  }
}
