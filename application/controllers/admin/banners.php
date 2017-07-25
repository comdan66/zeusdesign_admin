<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Banners extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('banner')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/banners';
    $this->icon = 'icon-images';
    $this->title = '旗幟';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'status', 'show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Banner::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'status'  => array ('el' => 'select', 'text' => '是否上架', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => Banner::$statusNames[$t], 'value' => $t,);}, array_keys (Banner::$statusNames))),
        'target'  => array ('el' => 'select', 'text' => '鏈結開啟方式', 'sql' => 'target = ?', 'items' => array_map (function ($t) { return array ('text' => Banner::$targetNames[$t], 'value' => $t,);}, array_keys (Banner::$targetNames))),
        'title'   => array ('el' => 'input', 'text' => '標題', 'sql' => 'title LIKE ?'),
        'content' => array ('el' => 'input', 'text' => '內容', 'sql' => 'content LIKE ?'),
        'link'    => array ('el' => 'input', 'text' => '鏈結', 'sql' => 'link LIKE ?'),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Banner', array ('order' => 'sort DESC, id DESC'));

    UserLog::logRead (
      $this->icon,
      '檢視了' . $this->title . '列表',
      '搜尋條件細節可詢問工程師',
      $configs);

    return $this->load_view (array (
        'objs' => $objs,
        'total' => $offset,
        'searches' => $searches,
        'pagination' => $this->_get_pagination ($configs),
      ));
  }
  public function add () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    $cover = OAInput::file ('cover');
    $posts['sort'] = (($posts['sort'] = Banner::first (array ('select' => 'sort', 'order' => 'sort DESC'))) ? $posts['sort']->sort : 0) + 1;

    if (($msg = $this->_validation_create ($posts, $cover)) || (!Banner::transaction (function () use (&$obj, $posts, $cover) {
      if (!verifyCreateOrm ($obj = Banner::create (array_intersect_key ($posts, Banner::table ()->columns))))
        return false;
      return $obj->cover->put ($cover);
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '標題名稱為：「' . $obj->mini_title () . '」，內容是：「' . $obj->mini_content () . '」，點擊後使用 「' . Banner::$targetNames[$obj->target] . '」 的方式開啟 「' . $obj->mini_link () . '」',
      $obj->backup ());

    return redirect_message (array ($this->uri_1), array ('_fi' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    $cover = OAInput::file ('cover');
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts, $cover, $obj))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Banner::transaction (function () use ($obj, $posts, $cover) {
      if (!$obj->save ()) return false;
      if ($cover && !$obj->cover->put ($cover)) return false;
      return true;
    })) return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title,
      '標題名稱為：「' . $obj->mini_title () . '」，內容是：「' . $obj->mini_content () . '」，使用「' . Banner::$targetNames[$obj->target] . '」的方式開啟「' . $obj->mini_link () . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->backup (true);

    if (!Banner::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_fd' => '刪除失敗！'));

    UserLog::logWrite (
      $this->icon,
      '刪除一項' . $this->title,
      '已經備份了刪除紀錄，細節可詢問工程師',
      $backup);

    return redirect_message (array ($this->uri_1), array ('_fi' => '刪除成功！'));
  }

  public function show () {
    UserLog::logRead ($this->icon, '檢視了一項' . $this->title);

    return $this->load_view (array (
        'obj' => $this->obj,
      ));
  }
  public function status () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    $validation = function (&$posts) {
      return !(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Banner::STATUS_2 : Banner::STATUS_1) && in_array ($posts['status'], array_keys (Banner::$statusNames))) ? '「設定上下架」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Banner::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return $this->output_error_json ('更新失敗！');

    UserLog::logWrite (
      $this->icon,
      Banner::$statusNames[$obj->status] . '一項' . $this->title,
      '將' . $this->title . '「' . $obj->mini_title () . '」調整為「' . Banner::$statusNames[$obj->status] . '」',
      array ($backup, $obj->backup (true)));

    return $this->output_json ($obj->status == Banner::STATUS_2);
  }
  public function sort ($offset = 0) {
    $searches = array ();
    $configs = array_merge (explode ('/', $this->uri_1), array ('sort', '%s'));
    $objs = conditions ($searches, $configs, $offset, 'Banner', array ('order' => 'sort DESC, id DESC'), null, 0);

    UserLog::logRead (
      $this->icon,
      '檢視了旗幟排序');

    return $this->load_view (array (
        'objs' => $objs,
        'total' => $offset,
        'searches' => $searches,
        'pagination' => $this->_get_pagination ($configs),
      ));
  }
  public function sort_update ($offset = 0) {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'sort'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    
    $validation = function (&$posts) {
      return !(isset ($posts['ids']) && is_array ($posts['ids'])) ? '「排序」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return redirect_message (array ($this->uri_1, 'sort'), array ('_fd' => $msg, 'posts' => $posts));

    $objs = array_combine (column_array ($objs = Banner::find ('all', array ('select' => 'id, sort, updated_at', 'conditions' => array ('id IN (?)', $posts['ids'] ? $posts['ids'] : array (0)))), 'id'), $objs);
    $c = count ($objs);
    $backup = column_array ($objs, 'sort');

    foreach ($posts['ids'] as $sort => $id)
      if (isset ($objs[$id]) && ($objs[$id]->sort = $c - $sort) && !$objs[$id]->save ())
        return redirect_message (array ($this->uri_1, 'sort'), array ('_fd' => '排序錯誤。', 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '調整' . $this->title . '排序',
      '調整細節記錄可詢問工程師',
      array ('id:sort', $backup, column_array ($objs, 'sort')));

    return redirect_message (array ($this->uri_1, 'sort'), array ('_fi' => '排序成功。'));
  }
  private function _validation_create (&$posts, &$cover) {
    if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && in_array ($posts['status'], array_keys (Banner::$statusNames)))) $posts['status'] = Banner::STATUS_1;
    
    if (!(isset ($cover) && is_upload_image_format ($cover, array ('gif', 'jpeg', 'jpg', 'png')))) return '「' . $this->title . '封面」格式錯誤！';

    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「' . $this->title . '標題」格式錯誤！';
    if (!(isset ($posts['content']) && is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '「' . $this->title . '內容」格式錯誤！';
    if (!(isset ($posts['link']) && is_string ($posts['link']) && ($posts['link'] = trim ($posts['link'])))) return '「' . $this->title . '鏈結」格式錯誤！';

    if (!(isset ($posts['target']) && is_string ($posts['target']) && is_numeric ($posts['target'] = trim ($posts['target'])) && in_array ($posts['target'], array_keys (Banner::$targetNames)))) return '「鏈結開啟方式」格式錯誤！';

    return '';
  }
  private function _validation_update (&$posts, &$cover, $obj) {
    if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && in_array ($posts['status'], array_keys (Banner::$statusNames)))) $posts['status'] = Banner::STATUS_1;
    
    if (!((string)$obj->cover || isset ($cover))) return '「' . $this->title . '封面」格式錯誤！';
    if (isset ($cover) && !(is_upload_image_format ($cover, array ('gif', 'jpeg', 'jpg', 'png')))) return '「' . $this->title . '封面」格式錯誤！';

    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「' . $this->title . '標題」格式錯誤！';
    if (!(isset ($posts['content']) && is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '「' . $this->title . '內容」格式錯誤！';
    if (!(isset ($posts['link']) && is_string ($posts['link']) && ($posts['link'] = trim ($posts['link'])))) return '「' . $this->title . '鏈結」格式錯誤！';

    if (!(isset ($posts['target']) && is_string ($posts['target']) && is_numeric ($posts['target'] = trim ($posts['target'])) && in_array ($posts['target'], array_keys (Banner::$targetNames)))) return '「鏈結開啟方式」格式錯誤！';

    return '';
  }
}
