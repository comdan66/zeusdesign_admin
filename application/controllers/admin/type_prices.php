<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Type_prices extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('price')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/type';
    $this->uri_2 = 'prices';
    $this->icon = 'icon-abacus';

    if (!(($id = $this->uri->rsegments (3, 0)) && ($this->parent = PriceType::find_by_id ($id))))
      return redirect_message (array ('work-tags'), array ('_flash_danger' => '找不到該筆資料。'));

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (4, 0)) && ($this->obj = Price::find_by_id ($id))))
        return redirect_message (array ($this->uri_1, $this->parent_tag->id, $this->uri_2), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('uri_2', $this->uri_2)
         ->add_param ('parent', $this->parent)
         ->add_param ('now_url', base_url ($this->uri_1, $this->parent->id, $this->uri_2));
  }
  public function index ($id, $offset = 0) {
    $columns = array ( 
        array ('key' => 'memo', 'title' => '備註', 'sql' => 'memo LIKE ?'), 
        array ('key' => 'desc', 'title' => '描述', 'sql' => 'desc LIKE ?'), 
        array ('key' => 'name', 'title' => '名稱', 'sql' => 'name LIKE ?'), 
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ($this->parent->id, $this->uri_2, '%s'));
    $conditions = conditions ($columns, $configs);
    OaModel::addConditions ($conditions, 'price_type_id = ?', $this->parent->id);

    $limit = 25;
    $total = Price::count (array ('conditions' => $conditions));
    $objs = Price::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'include' => array ('sources'), 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
      ));
  }
  public function add () {
    $posts = Session::getData ('posts', true);
    $sources = isset ($posts['sources']) ? $posts['sources'] : array ();

    return $this->load_view (array (
        'posts' => $posts,
        'sources' => $sources,
      ));
  }
  public function create () {
    $parent = $this->parent;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    
    if ($msg = $this->_validation_create ($posts))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    $posts['price_type_id'] = $parent->id;

    if (!Price::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Price::create (array_intersect_key ($posts, Price::table ()->columns))); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    if ($posts['sources'])
      foreach ($posts['sources'] as $i => $source)
        PriceSource::transaction (function () use ($i, $source, $obj) { return verifyCreateOrm (PriceSource::create (array_intersect_key (array_merge ($source, array ('price_id' => $obj->id)), PriceSource::table ()->columns))); });

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一項報價。',
      'desc' => '在報價系統分類 “' . $parent->name . '” 下新增了一項功能報價。',
      'backup' => json_encode ($obj->columns_val ())));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '新增成功！'));
  }

  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'sources' => isset ($posts['sources']) ? $posts['sources'] : array_map (function ($source) { return array ('title' => $source->title, 'href' => $source->href); }, $this->obj->sources),
      ));
  }
  public function update () {
    $obj = $this->obj;
    $parent = $this->parent;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Price::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    if ($obj->sources)
      foreach ($obj->sources as $source)
        PriceSource::transaction (function () use ($source) { return $source->destroy (); });

    if ($posts['sources'])
      foreach ($posts['sources'] as $i => $source)
        PriceSource::transaction (function () use ($i, $source, $obj) { return verifyCreateOrm (PriceSource::create (array_intersect_key (array_merge ($source, array ('price_id' => $obj->id)), PriceSource::table ()->columns))); });

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一項報價。',
      'desc' => '在報價系統分類 “' . $parent->name . '” 下修改了一項功能報價。',
      'backup' => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $parent = $this->parent;
    $backup = $obj->columns_val (true);

    if (!Price::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一項報價。',
      'desc' => '在報價系統分類 “' . $parent->name . '” 下刪除了一項功能報價，已經備份了刪除紀錄，細節可詢問工程師。',
      'backup' => json_encode ($backup)));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_flash_info' => '刪除成功！'));
  }

  private function _validation_create (&$posts) {
    if (!isset ($posts['name'])) return '沒有填寫 功能名稱！';
    if (!(is_string ($posts['name']) && ($posts['name'] = trim ($posts['name'])))) return '分類名稱 格式錯誤！';

    if (!isset ($posts['money'])) return '沒有填寫 價格！';
    if (!(is_numeric ($posts['money']) && ($posts['money'] = trim ($posts['money'])) > 0)) return '價格 格式錯誤！';
    
    $posts['desc'] = isset ($posts['desc']) && is_string ($posts['desc']) && ($posts['desc'] = trim ($posts['desc'])) ? $posts['desc'] : '';
    $posts['memo'] = isset ($posts['memo']) && is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) ? $posts['memo'] : '';
    $posts['sources'] = isset ($posts['sources']) && is_array ($posts['sources']) && $posts['sources'] ? array_values (array_filter ($posts['sources'], function ($source) { return isset ($source['href']) && is_string ($source['href']) && ($source['href'] = trim ($source['href'])); })) : array ();

    return '';
  }

  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
