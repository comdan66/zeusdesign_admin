<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Contact_invoice_contacts extends Admin_controller {
  private $uri_1     = null;
  private $uri_2     = null;
  private $parent    = null;
  private $obj  = null;

  public function __construct () {
    parent::__construct ();

    $this->uri_1     = 'admin/contact';
    $this->uri_2     = 'invoice-contacts';

    if (!(($id = $this->uri->rsegments (3, 0)) && ($this->parent = InvoiceContact::find_by_id ($id))))
      return redirect_message (array ('work-tags'), array (
          '_flash_danger' => '找不到該筆資料。'
        ));

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (4, 0)) && ($this->obj = InvoiceContact::find_by_id ($id))))
        return redirect_message (array ($this->uri_1, $this->parent_tag->id, $this->uri_2), array (
            '_flash_danger' => '找不到該筆資料。'
          ));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('uri_2', $this->uri_2);

    $this->add_param ('parent', $this->parent);
    $this->add_param ('now_url', base_url ('admin', 'invoice-contacts'));
  }
  public function index ($id, $offset = 0) {
    $columns = array ( 
        array ('key' => 'name', 'title' => '名稱', 'sql' => 'name LIKE ?'), 
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ($this->parent->id, $this->uri_2, '%s'));
    $conditions = conditions ($columns, $configs);
    OaModel::addConditions ($conditions, 'invoice_contact_id = ?', $this->parent->id);

    $limit = 25;
    $total = InvoiceContact::count (array ('conditions' => $conditions));
    $offset = $offset < $total ? $offset : 0;

    $this->load->library ('pagination');
    $pagination = $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
    $objs = InvoiceContact::find ('all', array (
        'offset' => $offset,
        'limit' => $limit,
        'order' => 'id DESC',
        'include' => array ('invoices'),
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
      return redirect_message (array ($this->uri_1, $this->parent->id, $this->uri_2, 'add'), array (
          '_flash_danger' => '非 POST 方法，錯誤的頁面請求。'
        ));

    $posts = OAInput::post ();
    
    if (($msg = $this->_validation_must ($posts)) || ($msg = $this->_validation ($posts)))
      return redirect_message (array ($this->uri_1, $this->parent->id, $this->uri_2, 'add'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    $posts['invoice_contact_id'] = $this->parent->id;
    $create = InvoiceContact::transaction (function () use (&$tag, $posts) {
      return verifyCreateOrm ($tag = InvoiceContact::create (array_intersect_key ($posts, InvoiceContact::table ()->columns)));
    });

    if (!$create)
      return redirect_message (array ($this->uri_1, $this->parent->id, $this->uri_2, 'add'), array (
          '_flash_danger' => '新增失敗！',
          'posts' => $posts
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ua', 'content' => '新增一項請款公司內的聯絡窗口。', 'desc' => '新增了 “' . $this->parent->name . '” 的一位聯絡窗口，其名稱為「' . $tag->name . '」。', 'backup' => json_encode ($tag->to_array ())));
    return redirect_message (array ($this->uri_1, $this->parent->id, $this->uri_2), array (
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
      return redirect_message (array ($this->uri_1, $this->parent->id, $this->uri_2, $this->obj->id, 'edit'), array (
          '_flash_danger' => '非 POST 方法，錯誤的頁面請求。'
        ));

    $posts = OAInput::post ();

    if ($msg = $this->_validation ($posts))
      return redirect_message (array ($this->uri_1, $this->parent->id, $this->uri_2, $this->obj->id, 'edit'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    if ($columns = array_intersect_key ($posts, $this->obj->table ()->columns))
      foreach ($columns as $column => $value)
        $this->obj->$column = $value;
    
    $obj = $this->obj;
    $update = InvoiceContact::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    });

    if (!$update)
      return redirect_message (array ($this->uri_1, $this->parent->id, $this->uri_2, $this->obj->id, 'edit'), array (
          '_flash_danger' => '更新失敗！',
          'posts' => $posts
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ua', 'content' => '修改一項請款公司內的聯絡窗口。', 'desc' => '修改了 “' . $this->parent->name . '” 的一位聯絡窗口，其名稱為「' . $obj->name . '」。', 'backup' => json_encode ($obj->to_array ())));
    return redirect_message (array ($this->uri_1, $this->parent->id, $this->uri_2), array (
        '_flash_info' => '更新成功！'
      ));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = json_encode ($obj->to_array ());
    $delete = InvoiceContact::transaction (function () use ($obj) { return $obj->destroy (); });

    if (!$delete)
      return redirect_message (array ($this->uri_1, $this->parent->id, $this->uri_2), array (
          '_flash_danger' => '刪除失敗！',
        ));

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-ua', 'content' => '刪除一項請款公司內的聯絡窗口。', 'desc' => '刪除了 “' . $this->parent->name . '” 的一位聯絡窗口，已經備份了刪除紀錄，細節可詢問工程師。', 'backup' => json_encode ($obj->to_array ())));
    return redirect_message (array ($this->uri_1, $this->parent->id, $this->uri_2), array (
        '_flash_info' => '刪除成功！'
      ));
  }

  private function _validation (&$posts) {
    $keys = array ('name');

    $new_posts = array (); foreach ($posts as $key => $value) if (in_array ($key, $keys)) $new_posts[$key] = $value;
    $posts = $new_posts;

    if (isset ($posts['name']) && !($posts['name'] = trim ($posts['name']))) return '名稱格式錯誤！';
    return '';
  }
  private function _validation_must (&$posts) {
    if (!isset ($posts['name'])) return '沒有填寫 名稱！';
    return '';
  }
}
