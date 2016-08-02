<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Promos extends Admin_controller {
  private $uri_1 = null;
  private $promo = null;

  public function __construct () {
    parent::__construct ();
    
    $this->uri_1 = 'promos';


    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'sort')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->promo = Promo::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array (
            '_flash_danger' => '找不到該筆資料。'
          ));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $columns = array ( 
        array ('key' => 'content', 'title' => '內容', 'sql' => 'content LIKE ?'), 
        array ('key' => 'title', 'title' => '標題', 'sql' => 'title LIKE ?'), 
      );

    $configs = array ($this->uri_1, '%s');
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = Promo::count (array ('conditions' => $conditions));
    $offset = $offset < $total ? $offset : 0;

    $this->load->library ('pagination');
    $pagination = $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
    $promos = Promo::find ('all', array (
        'offset' => $offset,
        'limit' => $limit,
        'order' => 'sort DESC',
        'conditions' => $conditions
      ));

    return $this->load_view (array (
        'promos' => $promos,
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
    $cover = OAInput::file ('cover');
    
    if (!$cover)
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => '請選擇照片(gif、jpg、png)檔案!',
          'posts' => $posts
        ));

    if (($msg = $this->_validation_must ($posts)) || ($msg = $this->_validation ($posts)))
      return redirect_message (array ($this->uri_1, 'add'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    $posts['sort'] = Promo::count ();
    $create = Promo::transaction (function () use (&$promo, $posts, $cover) {
      return verifyCreateOrm ($promo = Promo::create (array_intersect_key ($posts, Promo::table ()->columns))) && $promo->cover->put ($cover);
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
                    'promo' => $this->promo
                  ));
  }
  public function update () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $this->promo->id, 'edit'), array (
          '_flash_danger' => '非 POST 方法，錯誤的頁面請求。'
        ));

    $posts = OAInput::post ();
    $is_api = isset ($posts['_type']) && ($posts['_type'] == 'api') ? true : false;
    $cover = OAInput::file ('cover');

    if (!((string)$this->promo->cover || $cover))
      return $is_api ? $this->output_error_json ('Pic Format Error!') : redirect_message (array ($this->get_class (), $this->promo->id, 'edit'), array (
          '_flash_danger' => '請選擇圖片(gif、jpg、png)檔案!',
          'posts' => $posts
        ));
    
    if ($msg = $this->_validation ($posts))
      return $is_api ? $this->output_error_json ($msg) : redirect_message (array ($this->uri_1, $this->promo->id, 'edit'), array (
          '_flash_danger' => $msg,
          'posts' => $posts
        ));

    if ($columns = array_intersect_key ($posts, $this->promo->table ()->columns))
      foreach ($columns as $column => $value)
        $this->promo->$column = $value;
    
    $promo = $this->promo;
    $update = Promo::transaction (function () use ($promo, $posts, $cover) {
      if (!$promo->save ())
        return false;

      if ($cover && !$promo->cover->put ($cover))
        return false;
      
      return true;
    });

    if (!$update)
      return $is_api ? $this->output_error_json ('更新失敗！') : redirect_message (array ($this->uri_1, $this->promo->id, 'edit'), array (
          '_flash_danger' => '更新失敗！',
          'posts' => $posts
        ));

    return $is_api ? $this->output_json ($promo->to_array ()) : redirect_message (array ($this->uri_1), array (
        '_flash_info' => '更新成功！'
      ));
  }
  public function destroy () {
    $promo = $this->promo;
    $delete = Promo::transaction (function () use ($promo) {
      return $promo->destroy ();
    });

    if (!$delete)
      return redirect_message (array ($this->uri_1), array (
          '_flash_danger' => '刪除失敗！',
        ));

    return redirect_message (array ($this->uri_1), array (
        '_flash_info' => '刪除成功！'
      ));
  }
  public function sort ($id, $sort) {
    if (!in_array ($sort, array ('up', 'down')))
      return redirect_message (array ($this->uri_1), array (
          '_flash_danger' => '排序失敗！'
        ));

    $total = Promo::count ();

    switch ($sort) {
      case 'up':
        $sort = $this->promo->sort;
        $this->promo->sort = $this->promo->sort + 1 >= $total ? 0 : $this->promo->sort + 1;
        break;

      case 'down':
        $sort = $this->promo->sort;
        $this->promo->sort = $this->promo->sort - 1 < 0 ? $total - 1 : $this->promo->sort - 1;
        break;
    }

    Promo::addConditions ($conditions, 'sort = ?', $this->promo->sort);

    $promo = $this->promo;
    $update = Promo::transaction (function () use ($conditions, $promo, $sort) {
      if (($next = Promo::find ('one', array ('conditions' => $conditions))) && (($next->sort = $sort) || true))
        if (!$next->save ()) return false;
      if (!$promo->save ()) return false;

      return true;
    });

    if (!$update)
      return redirect_message (array ($this->uri_1), array (
          '_flash_danger' => '排序失敗！',
          'posts' => $posts
        ));
      return redirect_message (array ($this->uri_1), array (
        '_flash_info' => '排序成功！'
      ));
  }
  private function _validation (&$posts) {
    $keys = array ('title', 'content', 'link', 'target', 'is_enabled');

    $new_posts = array (); foreach ($posts as $key => $value) if (in_array ($key, $keys)) $new_posts[$key] = $value;
    $posts = $new_posts;

    if (isset ($posts['title']) && !($posts['title'] = trim ($posts['title']))) return '標題格式錯誤！';
    if (isset ($posts['content']) && !($posts['content'] = trim ($posts['content']))) return '內容格式錯誤！';
    if (isset ($posts['link']) && !($posts['link'] = trim ($posts['link']))) return '鏈結格式錯誤！';
    if (isset ($posts['target']) && !(is_numeric ($posts['target'] = trim ($posts['target'])) && in_array ($posts['target'], array_keys (Promo::$targetNames)))) return '開啟方式格式錯誤！';
    if (isset ($posts['is_enabled']) && !(is_numeric ($posts['is_enabled'] = trim ($posts['is_enabled'])) && in_array ($posts['is_enabled'], array_keys (Promo::$enableNames)))) return '狀態格式錯誤！';
    return '';
  }
  private function _validation_must (&$posts) {
    if (!isset ($posts['title'])) return '沒有填寫 標題！';
    if (!isset ($posts['content'])) return '沒有填寫 內容！';
    if (!isset ($posts['link'])) return '沒有填寫 鏈結！';
    return '';
  }
}
