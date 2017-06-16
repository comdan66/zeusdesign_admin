<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Income_items extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('website')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/income-items';
    $this->icon = 'icon-ti';
    $this->title = '請款';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = IncomeItem::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'title'   => array ('el' => 'input', 'text' => '標題', 'sql' => 'title LIKE ?'),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'IncomeItem', array ('order' => 'id DESC'));

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
    $sources = isset ($posts['sources']) ? $posts['sources'] : array (array ('user_id' => '2', 'title' => '2', 'quantity' => '2', 'sgl_money' => '2', 'all_money' => '2'));

    $row_muti = array (
        array ('el' => 'select', 'name' => 'sources', 'key' => 'user_id', 'options' => array (array ('value' => '1', 'text' => '吳政賢'), array ('value' => '2', 'text' => '吳政賢2'))),
        array ('el' => 'input', 'type' => 'text', 'name' => 'sources', 'key' => 'title', 'placeholder' => '細項標題'),
        array ('el' => 'input', 'type' => 'number', 'name' => 'sources', 'key' => 'quantity', 'placeholder' => '數量', 'class' => '_q'),
        array ('el' => 'input', 'type' => 'number', 'name' => 'sources', 'key' => 'sgl_money', 'placeholder' => '單價', 'class' => '_s'),
        array ('el' => 'input', 'type' => 'number', 'name' => 'sources', 'key' => 'all_money', 'placeholder' => '總價'),
      );

    return $this->load_view (array (
        'posts' => $posts,
        'sources' => $sources,
        'row_muti' => $row_muti,
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    $cover = OAInput::file ('cover');
    $posts['sort'] = (($posts['sort'] = IncomeItem::first (array ('select' => 'sort', 'order' => 'sort DESC'))) ? $posts['sort']->sort : 0) + 1;

    if (($msg = $this->_validation_create ($posts, $cover)) || (!IncomeItem::transaction (function () use (&$obj, $posts, $cover) {
      if (!verifyCreateOrm ($obj = IncomeItem::create (array_intersect_key ($posts, IncomeItem::table ()->columns))))
        return false;
      return $obj->cover->put ($cover);
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '標題名稱為：「' . $obj->mini_title () . '」，內容是：「' . $obj->mini_content () . '」，點擊後使用 「' . IncomeItem::$targetNames[$obj->target] . '」 的方式開啟 「' . $obj->mini_link () . '」',
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

    if (!IncomeItem::transaction (function () use ($obj, $posts, $cover) {
      if (!$obj->save ()) return false;
      if ($cover && !$obj->cover->put ($cover)) return false;
      return true;
    })) return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title,
      '標題名稱為：「' . $obj->mini_title () . '」，內容是：「' . $obj->mini_content () . '」，使用「' . IncomeItem::$targetNames[$obj->target] . '」的方式開啟「' . $obj->mini_link () . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->backup (true);

    if (!IncomeItem::transaction (function () use ($obj) { return $obj->destroy (); }))
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
      return !(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? IncomeItem::STATUS_2 : IncomeItem::STATUS_1) && in_array ($posts['status'], array_keys (IncomeItem::$statusNames))) ? '「設定上下架」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!IncomeItem::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return $this->output_error_json ('更新失敗！');

    UserLog::logWrite (
      $this->icon,
      IncomeItem::$statusNames[$obj->status] . '一項' . $this->title,
      '將' . $this->title . '「' . $obj->mini_title () . '」調整為「' . IncomeItem::$statusNames[$obj->status] . '」',
      array ($backup, $obj->backup (true)));

    return $this->output_json ($obj->status == IncomeItem::STATUS_2);
  }
  private function _validation_create (&$posts, &$cover) {
    if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && in_array ($posts['status'], array_keys (IncomeItem::$statusNames)))) $posts['status'] = IncomeItem::STATUS_1;
    
    if (!(isset ($cover) && is_upload_image_format ($cover, array ('gif', 'jpeg', 'jpg', 'png')))) return '「' . $this->title . '封面」格式錯誤！';

    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「' . $this->title . '標題」格式錯誤！';
    if (!(isset ($posts['content']) && is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '「' . $this->title . '內容」格式錯誤！';
    if (!(isset ($posts['link']) && is_string ($posts['link']) && ($posts['link'] = trim ($posts['link'])))) return '「' . $this->title . '鏈結」格式錯誤！';

    if (!(isset ($posts['target']) && is_string ($posts['target']) && is_numeric ($posts['target'] = trim ($posts['target'])) && in_array ($posts['target'], array_keys (IncomeItem::$targetNames)))) return '「鏈結開啟方式」格式錯誤！';

    return '';
  }
  private function _validation_update (&$posts, &$cover, $obj) {
    if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && in_array ($posts['status'], array_keys (IncomeItem::$statusNames)))) $posts['status'] = IncomeItem::STATUS_1;
    
    if (!((string)$obj->cover || isset ($cover))) return '「' . $this->title . '封面」格式錯誤！';
    if (isset ($cover) && !(is_upload_image_format ($cover, array ('gif', 'jpeg', 'jpg', 'png')))) return '「' . $this->title . '封面」格式錯誤！';

    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「' . $this->title . '標題」格式錯誤！';
    if (!(isset ($posts['content']) && is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '「' . $this->title . '內容」格式錯誤！';
    if (!(isset ($posts['link']) && is_string ($posts['link']) && ($posts['link'] = trim ($posts['link'])))) return '「' . $this->title . '鏈結」格式錯誤！';

    if (!(isset ($posts['target']) && is_string ($posts['target']) && is_numeric ($posts['target'] = trim ($posts['target'])) && in_array ($posts['target'], array_keys (IncomeItem::$targetNames)))) return '「鏈結開啟方式」格式錯誤！';

    return '';
  }
}
