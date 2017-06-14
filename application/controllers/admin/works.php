<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Works extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('work')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/works';
    $this->icon = 'icon-g';
    $this->title = '作品';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'status', 'show')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Work::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index ($offset = 0) {
    $searches = array (
        'status'    => array ('el' => 'select', 'text' => '是否上架', 'sql' => 'status = ?', 'items' => array_map (function ($t) { return array ('text' => Work::$statusNames[$t], 'value' => $t,);}, array_keys (Work::$statusNames))),
        'title'     => array ('el' => 'input', 'text' => '標題', 'sql' => 'title LIKE ?'),
        'content'   => array ('el' => 'input', 'text' => '內容', 'sql' => 'content LIKE ?'),
        'user_id[]' => array ('el' => 'checkbox', 'text' => '作者', 'sql' => 'user_id IN (?)', 'items' => array_map (function ($u) { return array ('text' => $u->name, 'value' => $u->id); }, User::all ()))
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Work', array ('order' => 'id DESC', 'include' => array ('images')));

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
    $tag_ids = isset ($posts['tag_ids']) ? $posts['tag_ids'] : array ();
    
    $sources = $row_muti = array ();
    foreach (array_keys (WorkItem::$typeNames) as $type) {
      $sources[$type] = isset ($posts['sources' . $type]) ? $posts['sources' . $type] : array ();
      $row_muti[$type] = array (
          array ('type' => 'text', 'name' => 'sources' . $type, 'key' => 'title', 'placeholder' => '標題文字'),
          array ('type' => 'text', 'name' => 'sources' . $type, 'key' => 'href', 'placeholder' => '網址'),
        );
    }

    return $this->load_view (array (
        'posts' => $posts,
        'tag_ids' => $tag_ids,
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
    $images = OAInput::file ('images[]');

    if (($msg = $this->_validation_create ($posts, $cover, $images)) || (!Work::transaction (function () use (&$obj, $posts, $cover) {
      if (!verifyCreateOrm ($obj = Work::create (array_intersect_key ($posts, Work::table ()->columns))))
        return false;
      return $obj->cover->put ($cover);
    }) && $msg = '新增失敗！')) return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    if ($posts['tag_ids'])
      foreach ($posts['tag_ids'] as $tag_id)
        WorkTagMapping::transaction (function () use ($tag_id, $obj) { return verifyCreateOrm (WorkTagMapping::create (array_intersect_key (array ('work_tag_id' => $tag_id, 'work_id' => $obj->id), WorkTagMapping::table ()->columns))); });

    foreach (array_keys (WorkItem::$typeNames) as $type)
      if ($posts['sources' . $type])
        foreach ($posts['sources' . $type] as $i => $source)
          WorkItem::transaction (function () use ($i, $source, $obj, $type) { return verifyCreateOrm (WorkItem::create (array_intersect_key (array_merge ($source, array ('sort' => $i, 'work_id' => $obj->id, 'type' => $type)), WorkItem::table ()->columns))); });
    
    if ($images)
      foreach ($images as $image)
        WorkImage::transaction (function () use ($image, $obj) { return verifyCreateOrm ($img = WorkImage::create (array_intersect_key (array ('work_id' => $obj->id), WorkImage::table ()->columns))) && $img->name->put ($image); });

    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '標題名稱為：「' . $obj->mini_title () . '」，內容是：「' . $obj->mini_content () . '」',
      $obj->backup ());

    return redirect_message (array ($this->uri_1), array ('_fi' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);
    $tag_ids = isset ($posts['tag_ids']) ? $posts['tag_ids'] : column_array ($this->obj->mappings, 'work_tag_id');

    $sources = $row_muti = array ();
    foreach (array_keys (WorkItem::$typeNames) as $type) {
      $sources[$type] = isset ($posts['sources' . $type]) ? $posts['sources' . $type] : array_map (function ($source) { return array ('title' => $source->title, 'href' => $source->href); }, $this->obj->typeItems ($type));
      $row_muti[$type] = array (
          array ('type' => 'text', 'name' => 'sources' . $type, 'key' => 'title', 'placeholder' => '標題文字'),
          array ('type' => 'text', 'name' => 'sources' . $type, 'key' => 'href', 'placeholder' => '網址'),
        );
    }

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'tag_ids' => $tag_ids,
        'sources' => $sources,
        'row_muti' => $row_muti,
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    $cover = OAInput::file ('cover');
    $images = OAInput::file ('images[]');
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts, $cover, $images, $obj))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Work::transaction (function () use ($obj, $posts, $cover) {
      if (!$obj->save ()) return false;
      if ($cover && !$obj->cover->put ($cover)) return false;
      return true;
    })) return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    $ori_ids = column_array ($obj->mappings, 'work_tag_id');

    if (($del_ids = array_diff ($ori_ids, $posts['tag_ids'])) && ($mappings = WorkTagMapping::find ('all', array ('select' => 'id, work_tag_id', 'conditions' => array ('work_id = ? AND work_tag_id IN (?)', $obj->id, $del_ids)))))
      foreach ($mappings as $mapping)
        WorkTagMapping::transaction (function () use ($mapping) { return $mapping->destroy (); });

    if (($add_ids = array_diff ($posts['tag_ids'], $ori_ids)) && ($tags = WorkTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $add_ids)))))
      foreach ($tags as $tag)
        WorkTagMapping::transaction (function () use ($tag, $obj) { return verifyCreateOrm (WorkTagMapping::create (Array_intersect_key (array ('work_tag_id' => $tag->id, 'work_id' => $obj->id), WorkTagMapping::table ()->columns))); });

    if ($obj->items)
      foreach ($obj->items as $item)
        WorkItem::transaction (function () use ($item) { return $item->destroy (); });

    foreach (array_keys (WorkItem::$typeNames) as $type)
      if ($posts['sources' . $type])
        foreach ($posts['sources' . $type] as $i => $source)
          WorkItem::transaction (function () use ($i, $source, $obj, $type) { return verifyCreateOrm (WorkItem::create (array_intersect_key (array_merge ($source, array ('sort' => $i, 'work_id' => $obj->id, 'type' => $type)), WorkItem::table ()->columns))); });

    if (($del_ids = array_diff (column_array ($obj->images, 'id'), $posts['oldimg'])) && ($imgs = WorkImage::find ('all', array ('select' => 'id, name', 'conditions' => array ('id IN (?)', $del_ids)))))
      foreach ($imgs as $img)
        WorkImage::transaction (function () use ($img) { return $img->destroy (); });

    if ($images)
      foreach ($images as $image)
        WorkImage::transaction (function () use ($image, $obj) { return verifyCreateOrm ($img = WorkImage::create (array_intersect_key (array ('work_id' => $obj->id), WorkImage::table ()->columns))) && $img->name->put ($image); });


    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title,
      '標題名稱為：「' . $obj->mini_title () . '」，內容是：「' . $obj->mini_content () . '」',
      array ($backup, $obj->backup (true)));

    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->backup (true);

    if (!Work::transaction (function () use ($obj) { return $obj->destroy (); }))
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
    $tag_ids = column_array ($this->obj->mappings, 'work_tag_id');

    return $this->load_view (array (
        'obj' => $this->obj,
        'tag_ids' => $tag_ids,
      ));
  }
  public function status () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    $validation = function (&$posts) {
      return !(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? Work::STATUS_2 : Work::STATUS_1) && in_array ($posts['status'], array_keys (Work::$statusNames))) ? '「設定上下架」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Work::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return $this->output_error_json ('更新失敗！');

    UserLog::logWrite (
      $this->icon,
      Work::$statusNames[$obj->status] . '一項' . $this->title,
      '將' . $this->title . '「' . $obj->mini_title () . '」調整為「' . Work::$statusNames[$obj->status] . '」',
      array ($backup, $obj->backup (true)));

    return $this->output_json ($obj->status == Work::STATUS_2);
  }
  
  private function _validation_create (&$posts, &$cover, &$images) {
    if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && in_array ($posts['status'], array_keys (Work::$statusNames)))) $posts['status'] = Work::STATUS_1;
    if (!(isset ($posts['user_id']) && is_string ($posts['user_id']) && is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find_by_id ($posts['user_id']))) return '「文章作者」發生錯誤！';
    $posts['tag_ids'] = isset ($posts['tag_ids']) && is_array ($posts['tag_ids']) && $posts['tag_ids'] ? column_array (WorkTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['tag_ids']))), 'id') : array ();
    
    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「' . $this->title . '標題」格式錯誤！';
    if (!(isset ($cover) && is_upload_image_format ($cover, array ('gif', 'jpeg', 'jpg', 'png')))) return '「' . $this->title . '封面」格式錯誤！';
    $images = array_values (array_filter ($images, function ($image) { return is_upload_image_format ($image, array ('gif', 'jpeg', 'jpg', 'png')); }));
    
    if (!(isset ($posts['content']) && is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '「' . $this->title . '內容」格式錯誤！';

    foreach (array_keys (WorkItem::$typeNames) as $type)
      $posts['sources' . $type] = isset ($posts['sources' . $type]) && is_array ($posts['sources' . $type]) && $posts['sources' . $type] ? array_values (array_filter ($posts['sources' . $type], function ($source) { return (isset ($source['title']) && is_string ($source['title']) &&  ($source['title'] = trim ($source['title']))) || (isset ($source['href']) && is_string ($source['href']) &&  ($source['href'] = trim ($source['href']))); })) : array ();

    return '';
  }
  private function _validation_update (&$posts, &$cover, &$images, $obj) {
    if (!(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && in_array ($posts['status'], array_keys (Work::$statusNames)))) $posts['status'] = Work::STATUS_1;
    if (!(isset ($posts['user_id']) && is_string ($posts['user_id']) && is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find_by_id ($posts['user_id']))) return '「文章作者」發生錯誤！';
    $posts['tag_ids'] = isset ($posts['tag_ids']) && is_array ($posts['tag_ids']) && $posts['tag_ids'] ? column_array (WorkTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['tag_ids']))), 'id') : array ();
    
    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「' . $this->title . '標題」格式錯誤！';
    if (!((string)$obj->cover || isset ($cover))) return '「' . $this->title . '封面」格式錯誤！';
    if (isset ($cover) && !(is_upload_image_format ($cover, array ('gif', 'jpeg', 'jpg', 'png')))) return '「' . $this->title . '封面」格式錯誤！';
    $images = array_values (array_filter ($images, function ($image) { return is_upload_image_format ($image, array ('gif', 'jpeg', 'jpg', 'png')); }));
    if (!(isset ($posts['content']) && is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '「' . $this->title . '內容」格式錯誤！';

    foreach (array_keys (WorkItem::$typeNames) as $type)
      $posts['sources' . $type] = isset ($posts['sources' . $type]) && is_array ($posts['sources' . $type]) && $posts['sources' . $type] ? array_values (array_filter ($posts['sources' . $type], function ($source) { return (isset ($source['title']) && is_string ($source['title']) &&  ($source['title'] = trim ($source['title']))) || (isset ($source['href']) && is_string ($source['href']) &&  ($source['href'] = trim ($source['href']))); })) : array ();

    $posts['oldimg'] = isset ($posts['oldimg']) ? column_array (WorkImage::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['oldimg']))), 'id') : array ();

    return '';
  }
}
