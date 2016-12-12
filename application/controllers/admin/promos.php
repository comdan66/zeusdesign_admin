<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Promos extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = 'icon-im';

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('site')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/promos';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'sort', 'is_enabled')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Promo::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $columns = array ( 
        array ('key' => 'content', 'title' => '內容', 'sql' => 'content LIKE ?'), 
        array ('key' => 'title', 'title' => '標題', 'sql' => 'title LIKE ?'), 
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = Promo::count (array ('conditions' => $conditions));
    $offset = $offset < $total ? $offset : 0;

    $this->load->library ('pagination');
    $pagination = $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
    $objs = Promo::find ('all', array ('offset' => $offset, 'limit' => $limit, 'order' => 'sort DESC', 'conditions' => $conditions));

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
    $cover = OAInput::file ('cover');
    
    if ($msg = $this->_validation_create ($posts, $cover))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    $posts['sort'] = Promo::count ();

    if (!Promo::transaction (function () use (&$obj, $posts, $cover) { return verifyCreateOrm ($obj = Promo::create (array_intersect_key ($posts, Promo::table ()->columns))) && $obj->cover->put ($cover); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一項 Promo。',
      'desc' => '標題名稱為：「' . $obj->mini_title () . '」，內容是：「' . $obj->mini_content () . '」，點擊 Promo 後使用 ”' . Promo::$targetNames[$obj->target] . '“ 的方式開啟 ”' . $obj->mini_link () . '“。',
      'backup'  => json_encode ($obj->columns_val ())));
    
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
    $cover = OAInput::file ('cover');
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts, $cover, $obj))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    if (!Promo::transaction (function () use ($obj, $posts, $cover) { if (!$obj->save () || ($cover && !$obj->cover->put ($cover))) return false; return true; }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一項 Promo。',
      'desc' => '標題名稱為：「' . $obj->mini_title () . '」，內容是：「' . $obj->mini_content () . '」，點擊 Promo 後使用 ”' . Promo::$targetNames[$obj->target] . '“ 的方式開啟 ”' . $obj->mini_link () . '“。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }
  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->columns_val (true);

    if (!Promo::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一項 Promo。',
      'desc' => '已經備份了刪除紀錄，細節可詢問工程師。',
      'backup'  => json_encode ($backup)));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '刪除成功！'));
  }
  public function sort ($id, $sort) {
    $obj = $this->obj;
    
    if (!in_array ($sort, array ('up', 'down')))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '排序失敗！'));

    $total = Promo::count ();

    switch ($sort) {
      case 'up': $sort = $obj->sort; $obj->sort = $obj->sort + 1 >= $total ? 0 : $obj->sort + 1; break;
      case 'down': $sort = $obj->sort; $obj->sort = $obj->sort - 1 < 0 ? $total - 1 : $obj->sort - 1; break;
    }

    $change = array ();
    array_push ($change, array ('id' => $obj->id, 'old' => $sort, 'new' => $obj->sort));
    OaModel::addConditions ($conditions, 'sort = ?', $obj->sort);

    if (!Promo::transaction (function () use ($conditions, $obj, $sort, &$change) { if (($next = Promo::find ('one', array ('conditions' => $conditions))) && array_push ($change, array ('id' => $next->id, 'old' => $next->sort, 'new' => $sort))) { $next->sort = $sort; if (!$next->save ()) return false; } return $obj->save (); }))
      return redirect_message (array ($this->uri_1), array (
          '_flash_danger' => '排序失敗！'
        ));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '調整了 Promo 順序。',
      'desc' => '已經備份了調整紀錄，細節可詢問工程師。',
      'backup' => json_encode ($change)));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '排序成功！'));
  }
  public function is_enabled () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);
    
    $validation = function (&$posts) {
      if (!isset ($posts['is_enabled'])) return '沒有選擇 是否公開！';
      if (!(is_numeric ($posts['is_enabled'] = trim ($posts['is_enabled'])) && in_array ($posts['is_enabled'], array_keys (Promo::$enableNames)))) return '是否公開 格式錯誤！';
      return '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Promo::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => Promo::$enableNames[$obj->is_enabled] . '一項 Promo。',
      'desc' => '將 Promo “' . $obj->mini_title () . '” ' . Promo::$enableNames[$obj->is_enabled] . '。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json ($obj->is_enabled == Promo::ENABLE_YES);
  }
  private function _validation_create (&$posts, &$cover) {
    if (!isset ($posts['title'])) return '沒有填寫 標題！';
    if (!isset ($posts['content'])) return '沒有填寫 內容！';
    if (!isset ($posts['link'])) return '沒有填寫 鏈結！';
    if (!isset ($cover)) return '沒有選擇 封面！';
    if (!isset ($posts['target'])) return '沒有選擇 開啟方式！';
    if (!isset ($posts['is_enabled'])) return '沒有選擇 是否公開！';
    
    if (!(is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '標題 格式錯誤！';
    if (!(is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '內容 格式錯誤！';
    if (!(is_string ($posts['link']) && ($posts['link'] = trim ($posts['link'])))) return '鏈結 格式錯誤！';
    if (!is_upload_file_format ($cover, 2 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png'))) return '文章封面 格式錯誤！';
    if (!(is_numeric ($posts['is_enabled'] = trim ($posts['is_enabled'])) && in_array ($posts['is_enabled'], array_keys (Promo::$enableNames)))) return '是否公開 格式錯誤！';
    if (!(is_numeric ($posts['target'] = trim ($posts['target'])) && in_array ($posts['target'], array_keys (Promo::$targetNames)))) return '開啟方式 格式錯誤！';

    return '';
  }
  private function _validation_update (&$posts, &$cover, $obj) {
    if (!isset ($posts['title'])) return '沒有填寫 標題！';
    if (!isset ($posts['content'])) return '沒有填寫 內容！';
    if (!isset ($posts['link'])) return '沒有填寫 鏈結！';
    if (!((string)$obj->cover || isset ($cover))) return '沒有選擇 文章封面！';
    if (!isset ($posts['target'])) return '沒有選擇 開啟方式！';
    if (!isset ($posts['is_enabled'])) return '沒有選擇 是否公開！';
    
    if (!(is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '標題 格式錯誤！';
    if (!(is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '內容 格式錯誤！';
    if (!(is_string ($posts['link']) && ($posts['link'] = trim ($posts['link'])))) return '鏈結 格式錯誤！';
    if ($cover && !is_upload_file_format ($cover, 2 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png'))) return '文章封面 格式錯誤！';
    if (!(is_numeric ($posts['is_enabled'] = trim ($posts['is_enabled'])) && in_array ($posts['is_enabled'], array_keys (Promo::$enableNames)))) return '是否公開 格式錯誤！';
    if (!(is_numeric ($posts['target'] = trim ($posts['target'])) && in_array ($posts['target'], array_keys (Promo::$targetNames)))) return '開啟方式 格式錯誤！';

    return '';
  }
}
