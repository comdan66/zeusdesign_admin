<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Invoices extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = 'icon-ti';

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('invoice')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/invoices';
    $this->icon = 'icon-ti';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'is_finished')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Invoice::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }


  private function _search_columns () {
    return array ( 
        array ('key' => 'name', 'title' => '專案名稱', 'sql' => 'name LIKE ?'), 
        array ('key' => 'customer_id', 'title' => '聯絡人', 'sql' => 'customer_id = ?', 'select' => array_map (function ($customer) { return array ('value' => $customer->id, 'text' => $customer->name, 'group' => $customer->company ? $customer->company->name : '');}, Customer::find ('all', array ('select' => 'id, name, customer_company_id', 'order' => 'id ASC')))),
        array ('key' => 'is_finished', 'title' => '是否請款', 'sql' => 'is_finished = ?', 'select' => array_map (function ($key) { return array ('value' => $key, 'text' => Invoice::$finishNames[$key]);}, array_keys (Invoice::$finishNames))),
        array ('key' => 'user_id', 'title' => '作者', 'sql' => 'user_id = ?', 'select' => array_map (function ($user) { return array ('value' => $user->id, 'text' => $user->name);}, User::all (array ('select' => 'id, name')))),
      );
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 25;
    $total = Invoice::count (array ('conditions' => $conditions));
    $objs = Invoice::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'include' => array ('user', 'tag', 'customer'), 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
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

    if (!Invoice::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Invoice::create (array_intersect_key ($posts, Invoice::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一筆請款。',
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

    if (!Invoice::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一筆請款。',
      'desc' => '專案名稱為：「' . $obj->mini_name () . '」。',
      'backup' => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }
  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->columns_val (true);

    if (!Invoice::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一筆請款。',
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
      if (!isset ($posts['is_finished'])) return '沒有選擇 是否請款！';
      if (!(is_numeric ($posts['is_finished'] = trim ($posts['is_finished'])) && in_array ($posts['is_finished'], array_keys (Invoice::$finishNames)))) return '是否請款 格式錯誤！';
      return '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Invoice::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '將一項請款標記成 ”' . Invoice::$finishNames[$obj->is_finished] . '“。',
      'desc' => '將請款 “' . $obj->name . '” 標記成 「' . Invoice::$finishNames[$obj->is_finished] . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json ($obj->is_finished == Invoice::IS_FINISHED);
  }
  private function _validation_create (&$posts) {
    if (!isset ($posts['is_finished'])) return '沒有選擇 是否請款！';
    if (!isset ($posts['user_id'])) return '沒有選擇 負責人！';
    if (!isset ($posts['customer_id'])) return '沒有選擇 聯絡人！';
    if (!isset ($posts['invoice_tag_id'])) return '沒有選擇 分類！';
    if (!isset ($posts['name'])) return '沒有填寫 專案名稱！';
    if (!isset ($posts['all_money'])) return '沒有填寫 總金額！';

    if (!(is_numeric ($posts['is_finished'] = trim ($posts['is_finished'])) && in_array ($posts['is_finished'], array_keys (Invoice::$finishNames)))) return '是否請款 格式錯誤！';
    if (!(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['user_id']))))) return '負責人 不存在！';
    if (!(is_numeric ($posts['customer_id'] = trim ($posts['customer_id'])) && Customer::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['customer_id']))))) return '聯絡人 不存在！';
    if (!(is_numeric ($posts['invoice_tag_id'] = trim ($posts['invoice_tag_id'])) && InvoiceTag::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['invoice_tag_id']))))) return '請款分類 不存在！';

    if (!(is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '專案名稱 格式錯誤！';
    if (!(is_numeric ($posts['all_money'] = trim ($posts['all_money'])) && ($posts['all_money'] >= 0))) return '總金額 格式錯誤！';

    $posts['quantity'] = isset ($posts['quantity']) && is_numeric ($posts['quantity'] = trim ($posts['quantity'])) && ($posts['quantity'] >= 0) ? $posts['quantity'] : 0;
    $posts['single_money'] = isset ($posts['single_money']) && is_numeric ($posts['single_money'] = trim ($posts['single_money'])) && ($posts['single_money'] >= 0) ? $posts['single_money'] : 0;
    
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

    $objs = Invoice::find ('all', array (
        'order' => 'id DESC',
        'include' => array ('user', 'tag', 'customer'),
        'conditions' => $conditions
      ));

    $this->load->library ('OAExcel');
    $infos = array (array ('title' => '專案名稱', 'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,          'exp' => '$obj->name'),
                    array ('title' => '聯絡人',  'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,          'exp' => '$obj->customer && $obj->customer->company ? $obj->customer->name : ""'),
                    array ('title' => '聯絡人電話',  'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,          'exp' => '$obj->customer && $obj->customer->company ? $obj->customer->company->telephone . (($e = trim ($obj->customer->extension, "#")) ? " #" . $e : "") : ""'),
                    array ('title' => '數量',   'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER,        'exp' => '$obj->quantity ? $obj->quantity : "-"'),
                    array ('title' => '單價',   'format' => PHPExcel_Style_NumberFormat::FORMAT_MONEY,        'exp' => '$obj->single_money ? $obj->single_money : "-"'),
                    array ('title' => '總金額',  'format' => PHPExcel_Style_NumberFormat::FORMAT_MONEY,        'exp' => '$obj->all_money'),
                    array ('title' => '分類',    'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,          'exp' => '$obj->tag ? $obj->tag->name : "-"'),
                    array ('title' => '結案日期', 'format' => PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2, 'exp' => '$obj->closing_at ? $obj->closing_at->format ("Y-m-d") : ""'),
                    array ('title' => '備註',    'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,           'exp' => '$obj->memo ? $obj->memo : "-"'));

    $excel = $this->_build_excel ($objs, $infos);
    $excel->getActiveSheet ()->setTitle ('請款列表');
    $excel->setActiveSheetIndex (0);

    $filename = '宙思請款_' . date ('Ymd') . '.xlsx';
    header ('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf8');
    header ('Content-Disposition: attachment; filename=' . $filename);

    $objWriter = new PHPExcel_Writer_Excel2007 ($excel);
    $objWriter->save ("php://output");

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => 'icon-p',
      'content' => '匯出 ' . count ($objs) . ' 筆請款。',
      'desc' => '已經成功的匯出 ' . $filename . '，全部有 ' . count ($objs) . ' 筆請款紀錄，細節可詢問工程師。',
      'backup' => json_encode (array_map (function ($obj) { return $obj->columns_val (); }, $objs))));
  }
}
