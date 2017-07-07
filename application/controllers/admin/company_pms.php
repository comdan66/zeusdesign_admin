
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Company_pms extends Admin_controller {
  private $uri_1 = null;
  private $uri_2 = null;
  private $uri_b = null;
  private $parent = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('company')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/company';
    $this->uri_2 = 'pms';
    $this->uri_b = 'admin/companies';
    $this->icon = 'icon-profile2';

    if (!(($id = $this->uri->rsegments (3, 0)) && ($this->parent = Company::find_by_id ($id))))
      return redirect_message (array ('admin', 'work-tags'), array ('_fd' => '找不到該筆資料。'));

    $this->title = '「' . $this->parent->name . '」的聯絡人';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'show')))
      if (!(($id = $this->uri->rsegments (4, 0)) && ($this->obj = CompanyPm::find_by_id ($id))))
        return redirect_message (array ($this->uri_1, $this->parent_tag->id, $this->uri_2), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('uri_2', $this->uri_2)
         ->add_param ('uri_b', $this->uri_b)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('parent', $this->parent)
         ->add_param ('_url', base_url ($this->uri_b));
  }

  public function index ($id, $offset = 0) {
    $parent = $this->parent;

    $searches = array (
        'name' => array ('el' => 'input', 'text' => '名稱', 'sql' => 'name LIKE ?'),
        'extension' => array ('el' => 'input', 'text' => '分機', 'sql' => 'extension LIKE ?'),
        'memo' => array ('el' => 'input', 'text' => '備註', 'sql' => 'memo LIKE ?'),
        'email' => array ('el' => 'input', 'text' => 'E-Mail', 'sql' => 'id IN (?)', 'vs' => "column_array (CompanyPmItem::find ('all' , array ('select' => 'company_pm_id', 'conditions' => array ('type = ? AND content LIKE ?', " . CompanyPmItem::TYPE_1 . ", " . '"%" . ' . '$val' . ' . "%"' . "))), 'company_pm_id')"),
        'phone' => array ('el' => 'input', 'text' => '手機', 'sql' => 'id IN (?)', 'vs' => "column_array (CompanyPmItem::find ('all' , array ('select' => 'company_pm_id', 'conditions' => array ('type = ? AND content LIKE ?', " . CompanyPmItem::TYPE_2 . ", " . '"%" . ' . '$val' . ' . "%"' . "))), 'company_pm_id')"),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ($parent->id, $this->uri_2, '%s'));
    $objs = conditions ($searches, $configs, $offset, 'CompanyPm', array ('order' => 'id DESC'), function ($conditions) use ($parent) {
      OaModel::addConditions ($conditions, 'company_id = ?', $parent->id);
      return $conditions;
    });

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
    
    $items = $row_muti = array ();
    foreach (CompanyPmItem::$typeNames as $type => $typeName) {
      $items[$type] = isset ($posts['items' . $type]) ? $posts['items' . $type] : array ();
      $row_muti[$type] = array (
          array ('type' => 'text', 'name' => 'items' . $type, 'key' => 'content', 'placeholder' => '請輸入' . $typeName),
        );
    }

    return $this->load_view (array (
        'posts' => $posts,
        'items' => $items,
        'row_muti' => $row_muti,
      ));
  }
  public function create () {
    $parent = $this->parent;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['company_id'] = $parent->id;

    if (($msg = $this->_validation_create ($posts)) || (!CompanyPm::transaction (function () use (&$obj, $posts) {
      return verifyCreateOrm ($obj = CompanyPm::create (array_intersect_key ($posts, CompanyPm::table ()->columns)));
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_fd' => $msg, 'posts' => $posts));


    foreach (array_keys (CompanyPmItem::$typeNames) as $type)
      if ($posts['items' . $type])
        foreach ($posts['items' . $type] as $i => $item)
          CompanyPmItem::transaction (function () use ($i, $item, $obj, $type) { return verifyCreateOrm (CompanyPmItem::create (array_intersect_key (array_merge ($item, array ('company_pm_id' => $obj->id, 'type' => $type)), CompanyPmItem::table ()->columns))); });
    
    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '名稱為：「' . $obj->name . '」',
      $obj->backup ());

    return redirect_message (array ($this->uri_1, $parent->id,  $this->uri_2), array ('_fi' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    $items = $row_muti = array ();
    foreach (CompanyPmItem::$typeNames as $type => $typeName) {
      $items[$type] = isset ($posts['items' . $type]) ? $posts['items' . $type] : array_map (function ($item) { return array ('content' => $item->content); }, $this->obj->typeItems ($type));
      $row_muti[$type] = array (
          array ('type' => 'text', 'name' => 'items' . $type, 'key' => 'content', 'placeholder' => '請輸入' . $typeName),
        );
    }

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'items' => $items,
        'row_muti' => $row_muti,
      ));
  }
  public function update () {
    $parent = $this->parent;
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!CompanyPm::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    if ($obj->items)
      foreach ($obj->items as $item)
        CompanyPmItem::transaction (function () use ($item) { return $item->destroy (); });

    foreach (array_keys (CompanyPmItem::$typeNames) as $type)
      if ($posts['items' . $type])
        foreach ($posts['items' . $type] as $i => $source)
          CompanyPmItem::transaction (function () use ($i, $source, $obj, $type) { return verifyCreateOrm (CompanyPmItem::create (array_intersect_key (array_merge ($source, array ('company_pm_id' => $obj->id, 'type' => $type)), CompanyPmItem::table ()->columns))); });

    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title,
      '名稱為：「' . $obj->name . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1, $parent->id,  $this->uri_2), array ('_fi' => '新增成功！'));
  }

  public function destroy () {
    $parent = $this->parent;
    $obj = $this->obj;
    $backup = $obj->backup (true);

    if (!CompanyPm::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_fd' => '刪除失敗！'));

    UserLog::logWrite (
      $this->icon,
      '刪除一項' . $this->title,
      '已經備份了刪除紀錄，細節可詢問工程師',
      $backup);

    return redirect_message (array ($this->uri_1, $parent->id,  $this->uri_2), array ('_fi' => '刪除成功！'));
  }
  public function show () {
    UserLog::logRead ($this->icon, '檢視了一項' . $this->title);

    return $this->load_view (array (
        'obj' => $this->obj,
      ));
  }
  private function _validation_create (&$posts) {
    if (!(isset ($posts['name']) && is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '「' . $this->title . '名稱」格式錯誤！';
    if (isset ($posts['extension']) && !(is_string ($posts['extension']) && ($posts['extension'] = trim ($posts['extension'])))) $posts['extension'] = '';
    if (isset ($posts['experience']) && !(is_string ($posts['experience']) && ($posts['experience'] = trim ($posts['experience'])))) $posts['experience'] = '';
    if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $posts['memo'] = '';

    foreach (array_keys (CompanyPmItem::$typeNames) as $type)
      $posts['items' . $type] = isset ($posts['items' . $type]) && is_array ($posts['items' . $type]) && $posts['items' . $type] ? array_values (array_filter (array_map (function ($item) {
        if (!(isset ($item['content']) && is_string ($item['content']) && ($item['content'] = trim ($item['content'])))) $item['content'] = '';
        return $item;
      }, $posts['items' . $type]), function ($item) {
        return $item['content'];
      })) : array ();

    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
