<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Salaries extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = 'icon-moneybag';

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('salary')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/salaries';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'is_finished')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Salary::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }
  private function _search_columns () {
    return array ( 
        array ('key' => 'is_finished', 'title' => '是否已給付', 'sql' => 'is_finished = ?', 'select' => array_map (function ($key) { return array ('value' => $key, 'text' => Salary::$finishNames[$key]);}, array_keys (Salary::$finishNames))),
        array ('key' => 'name',        'title' => '專案名稱',   'sql' => 'name LIKE ?'), 
        array ('key' => 'user_id',     'title' => '受薪人員',   'sql' => 'user_id = ?', 'select' => array_map (function ($user) { return array ('value' => $user->id, 'text' => $user->name);}, User::all (array ('select' => 'id, name')))),
      );
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = Salary::count (array ('conditions' => $conditions));
    $offset = $offset < $total ? $offset : 0;

    $this->load->library ('pagination');
    $pagination = $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
    $objs = Salary::find ('all', array ('offset' => $offset, 'limit' => $limit, 'order' => 'id DESC', 'include' => array ('user'), 'conditions' => $conditions));

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

    if (!Salary::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Salary::create (array_intersect_key ($posts, Salary::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一筆薪資。',
      'desc' => '設定了一項給薪資。',
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
    
    if (!Salary::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一筆薪資。',
      'desc' => '設定更新了一項給薪資。',
      'backup' => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }
  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->columns_val (true);

    if (!Salary::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一筆薪資。',
      'desc' => '已經備份了刪除紀錄，細節可詢問工程師。',
      'backup' => json_encode ($backup)));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '刪除成功！'));
  }

  public function is_finished () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);
    
    $validation = function (&$posts) {
      if (!isset ($posts['is_finished'])) return '沒有選擇 是否已給付！';
      if (!(is_numeric ($posts['is_finished'] = trim ($posts['is_finished'])) && in_array ($posts['is_finished'], array_keys (Salary::$finishNames)))) return '是否已給付 格式錯誤！';
      return '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Salary::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '將一項薪資標示為 「' . Salary::$finishNames[$obj->is_finished] . '」。',
      'desc' => '將專案 ”' . $obj->name . '“ 的薪資標示為 「' . Salary::$finishNames[$obj->is_finished] . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json ($obj->is_finished == Salary::IS_FINISHED);
  }

  private function _validation_create (&$posts) {
    if (!isset ($posts['user_id'])) return '沒有選擇 受薪人員！';
    if (!isset ($posts['name'])) return '沒有填寫 專案名稱！';
    if (!isset ($posts['money'])) return '沒有填寫 金額！';
    if (!isset ($posts['is_finished'])) return '沒有選擇 是否已給付！';

    if (!(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['user_id']))))) return '受薪人員 不存在！';
    if (!(is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '專案名稱 格式錯誤！';
    if (!(is_numeric ($posts['money'] = trim ($posts['money'])) && ($posts['money'] >= 0))) return '金額 格式錯誤！';
    if (!(is_numeric ($posts['is_finished'] = trim ($posts['is_finished'])) && in_array ($posts['is_finished'], array_keys (Salary::$finishNames)))) return '是否已給付 格式錯誤！';
    
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

    $objs = Salary::find ('all', array ('order' => 'id DESC', 'include' => array ('user'), 'conditions' => $conditions));

    $this->load->library ('OAExcel');
    $infos = array (array ('title' => '受薪人員',     'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,           'exp' => '$obj->user->name'),
                    array ('title' => '專案名稱',    'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,           'exp' => '$obj->name'),
                    array ('title' => '金額',       'format' => PHPExcel_Style_NumberFormat::FORMAT_MONEY,         'exp' => '$obj->money'),
                    array ('title' => '備註',       'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,           'exp' => '$obj->memo'));

    $excel = $this->_build_excel ($objs, $infos);
    $excel->getActiveSheet ()->setTitle ('薪資列表');

    $excel->setActiveSheetIndex (0);

    $filename = '宙思薪資_' . date ('Ymd') . '.xlsx';
    header ('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf8');
    header ('Content-Disposition: attachment; filename=' . $filename);

    $objWriter = new PHPExcel_Writer_Excel2007 ($excel);
    $objWriter->save ("php://output");

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => 'icon-p',
      'content' => '匯出 ' . count ($objs) . ' 筆薪資。',
      'desc' => '已經成功的匯出 ' . $filename . '，全部有 ' . count ($objs) . ' 筆薪資紀錄，細節可詢問工程師。',
      'backup' => json_encode (array_map (function ($obj) { return $obj->columns_val (); }, $objs))));
  }
}
