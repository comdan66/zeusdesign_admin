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
        'status' => array ('el' => 'select', 'text' => '狀態', 'sql' => function ($a) { return $a ? 'income_id != 0' : 'income_id = 0'; }, 'vs' => '$val' . " ? " . '1' . " : " . '0' . "", 'items' => array (array ('text' => '已請款', 'value' => '1'), array ('text' => '未請款', 'value' => '0'))),
        'user_id[]' => array ('el' => 'checkbox', 'text' => '負責人', 'sql' => 'user_id IN (?)', 'items' => array_map (function ($u) { return array ('text' => $u->name, 'value' => $u->id); }, User::all ())),
        'pms[]' => array ('el' => 'dysltckb', 'text1' => '請選擇公司', 'text2' => '窗口、PM', 'sql' => 'company_pm_id IN (?)', 'items1' => array_map (function ($u) { return array ('text' => $u->name, 'value' => $u->id); }, Company::all ()), 'items2' => array_map (function ($u) { return array ('text' => $u->name, 'value' => $u->id); }, Company::all ()), 'items2' => array_map (function ($u) { return array ('text' => $u->name, 'value' => $u->id); }, Company::all ()), 'items2' => array_map (function ($u) { return array ('text' => $u->name, 'parent_id' => $u->company_id, 'value' => $u->id); }, CompanyPm::all ())),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'IncomeItem', array ('order' => 'id DESC', 'include' => array ('details', 'user', 'income')));

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
    $details = isset ($posts['details']) ? $posts['details'] : array ();

    $row_muti = array (
        array ('need' => true, 'el' => 'select', 'name' => 'details', 'key' => 'user_id', 'options' => array_map (function ($user) { return array ('text' => $user->name, 'value' => $user->id); }, User::find ('all'))),
        array ('need' => false, 'el' => 'input', 'type' => 'text', 'name' => 'details', 'key' => 'title', 'placeholder' => '細項標題'),
        array ('need' => true, 'el' => 'input', 'type' => 'number', 'name' => 'details', 'key' => 'quantity', 'placeholder' => '數量', 'class' => '_q'),
        array ('need' => true, 'el' => 'input', 'type' => 'number', 'name' => 'details', 'key' => 'sgl_money', 'placeholder' => '單價', 'class' => '_s'),
        array ('need' => true, 'el' => 'input', 'type' => 'number', 'name' => 'details', 'key' => 'all_money', 'placeholder' => '總價'),
      );

    return $this->load_view (array (
        'posts' => $posts,
        'details' => $details,
        'row_muti' => $row_muti,
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $images = OAInput::file ('images[]');

    if (($msg = $this->_validation_create ($posts, $images)) || (!IncomeItem::transaction (function () use (&$obj, $posts) {
      return verifyCreateOrm ($obj = IncomeItem::create (array_intersect_key ($posts, IncomeItem::table ()->columns)));
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    if ($images)
      foreach ($images as $image)
        IncomeItemImage::transaction (function () use ($image, $obj) { return verifyCreateOrm ($img = IncomeItemImage::create (array_intersect_key (array ('income_item_id' => $obj->id), IncomeItemImage::table ()->columns))) && $img->name->put ($image); });

    if ($posts['details'])
      foreach ($posts['details'] as $i => $detail)
        IncomeItemDetail::transaction (function () use ($i, $detail, $obj) { return verifyCreateOrm (IncomeItemDetail::create (array_intersect_key (array_merge ($detail, array ('income_item_id' => $obj->id)), IncomeItemDetail::table ()->columns))); });

    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '標題名稱為：「' . $obj->title . '」',
      $obj->backup ());

    return redirect_message (array ($this->uri_1), array ('_fi' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);
    $details = isset ($posts['details']) ? $posts['details'] : array_map (function ($detail) { return array (
        'user_id' => $detail->user_id,
        'title' => $detail->title,
        'quantity' => $detail->quantity,
        'sgl_money' => $detail->sgl_money,
        'all_money' => $detail->all_money,
      ); }, $this->obj->details);;

    $row_muti = array (
        array ('need' => true, 'el' => 'select', 'name' => 'details', 'key' => 'user_id', 'options' => array_map (function ($user) { return array ('text' => $user->name, 'value' => $user->id); }, User::find ('all'))),
        array ('need' => false, 'el' => 'input', 'type' => 'text', 'name' => 'details', 'key' => 'title', 'placeholder' => '細項標題'),
        array ('need' => true, 'el' => 'input', 'type' => 'number', 'name' => 'details', 'key' => 'quantity', 'placeholder' => '數量', 'class' => '_q'),
        array ('need' => true, 'el' => 'input', 'type' => 'number', 'name' => 'details', 'key' => 'sgl_money', 'placeholder' => '單價', 'class' => '_s'),
        array ('need' => true, 'el' => 'input', 'type' => 'number', 'name' => 'details', 'key' => 'all_money', 'placeholder' => '總價'),
      );

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'details' => $details,
        'row_muti' => $row_muti,
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $images = OAInput::file ('images[]');
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts, $images))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!IncomeItem::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    if (($del_ids = array_diff (column_array ($obj->images, 'id'), $posts['oldimg'])) && ($imgs = IncomeItemImage::find ('all', array ('select' => 'id, name', 'conditions' => array ('id IN (?)', $del_ids)))))
      foreach ($imgs as $img)
        IncomeItemImage::transaction (function () use ($img) { return $img->destroy (); });

    if ($images)
      foreach ($images as $image)
        IncomeItemImage::transaction (function () use ($image, $obj) { return verifyCreateOrm ($img = IncomeItemImage::create (array_intersect_key (array ('income_item_id' => $obj->id), IncomeItemImage::table ()->columns))) && $img->name->put ($image); });

    if ($obj->details)
      foreach ($obj->details as $detail)
        IncomeItemDetail::transaction (function () use ($detail) { return $detail->destroy (); });

    if ($posts['details'])
      foreach ($posts['details'] as $i => $detail)
        IncomeItemDetail::transaction (function () use ($i, $detail, $obj) { return verifyCreateOrm (IncomeItemDetail::create (array_intersect_key (array_merge ($detail, array ('income_item_id' => $obj->id)), IncomeItemDetail::table ()->columns))); });

    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title,
      '標題名稱為：「' . $obj->title . '」',
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
  private function _validation_create (&$posts, &$images) {
    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「' . $this->title . '標題」格式錯誤！';
    if (!(isset ($posts['user_id']) && is_string ($posts['user_id']) && is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find_by_id ($posts['user_id']))) return '「文章作者」發生錯誤！';
    $images = array_values (array_filter ($images, function ($image) { return is_upload_image_format ($image, array ('gif', 'jpeg', 'jpg', 'png')); }));
    if (!(isset ($posts['close_date']) && is_string ($posts['close_date']) && is_date ($posts['close_date'] = trim ($posts['close_date'])))) return '「' . $this->title . '結束日期」格式錯誤！';
    if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $posts['memo'] = '';

    $posts['details'] = isset ($posts['details']) && is_array ($posts['details']) && $posts['details'] ? array_values (array_filter (array_map (function ($detail) {
      if (!(isset ($detail['user_id']) && is_string ($detail['user_id']) && is_numeric ($detail['user_id'] = trim ($detail['user_id'])) && User::find_by_id ($detail['user_id']))) unset ($detail['user_id']);
      if (isset ($detail['title']) && !(is_string ($detail['title']) && ($detail['title'] = trim ($detail['title'])))) $detail['title'] = '';
      if (!(isset ($detail['quantity']) && is_string ($detail['quantity']) && is_numeric ($detail['quantity'] = trim ($detail['quantity'])) && $detail['quantity'] > 0)) unset ($detail['quantity']);
      if (!(isset ($detail['sgl_money']) && is_string ($detail['sgl_money']) && is_numeric ($detail['sgl_money'] = trim ($detail['sgl_money'])) && $detail['sgl_money'] > 0)) unset ($detail['sgl_money']);
      if (!(isset ($detail['all_money']) && is_string ($detail['all_money']) && is_numeric ($detail['all_money'] = trim ($detail['all_money'])) && $detail['all_money'] > 0)) unset ($detail['all_money']);
      return $detail;
    }, $posts['details']), function ($detail) {
      return isset ($detail['user_id']) && isset ($detail['title']) && isset ($detail['quantity']) && isset ($detail['sgl_money']) && isset ($detail['all_money']);
    })) : array ();

    if (!$posts['details']) return '「' . $this->title . '細項」格式錯誤，至少要有一項！';
    return '';
  }
  private function _validation_update (&$posts, &$images) {
    $posts['oldimg'] = isset ($posts['oldimg']) ? column_array (IncomeItemImage::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['oldimg']))), 'id') : array ();
    return $this->_validation_create ($posts, $images);
  }
}
