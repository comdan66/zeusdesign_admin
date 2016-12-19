<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Works extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('work')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/works';
    $this->icon = 'icon-g';

    if (in_array ($this->uri->rsegments (2, 0), array ('show', 'edit', 'update', 'destroy', 'is_enabled')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Work::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  public function show ($id) {
    $this->load->library ('DeployTool');

    if (!(DeployTool::genApi (true) && DeployTool::callBuild ()))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '預覽失敗！'));

    return redirect_message (Cfg::setting ('deploy', 'view', ENVIRONMENT) . 'work/' . $this->obj->id . '-' . rawurlencode (preg_replace ('/[\/%]/u', ' ', $this->obj->title)) . '.html', array ('_flash_info' => ''));
  }
  public function index ($offset = 0) {
    $columns = array ( 
        array ('key' => 'title', 'title' => '標題', 'sql' => 'title LIKE ?'), 
        array ('key' => 'user_id', 'title' => '作者', 'sql' => 'user_id = ?', 'select' => array_map (function ($user) { return array ('value' => $user->id, 'text' => $user->name);}, User::all (array ('select' => 'id, name')))),
        array ('key' => 'tag_id', 'title' => '分類', 'sql' => 'id IN (?)', 'values' => "column_array (WorkTagMapping::find ('all' , array ('select' => 'work_id', 'conditions' => array ('work_tag_id = ?', " . '$val' . "))), 'work_id')", 'select' => array_map (function ($tag) { return array ('value' => $tag->id, 'text' => $tag->name);}, WorkTag::all (array ('select' => 'id, name')))),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = Work::count (array ('conditions' => $conditions));
    $objs = Work::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'include' => array ('user'), 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
      ));
  }
  public function add () {
    $posts = Session::getData ('posts', true);

    // $blocks = array_values (array_filter (array_map (function ($block) {
    //   if (!$block['title'] = htmlentities (trim ($block['title']))) return array ();
      
    //   $block['items'] = array_values (array_filter (array_map (function ($item) {
    //       $item['title'] = htmlentities (isset ($item['title']) ? trim ($item['title']) : '');
    //       $item['link'] = htmlentities (isset ($item['link']) ? trim ($item['link']) : '');
    //       return $item['title'] || $item['link'] ? $item : array ();
    //     }, isset ($block['items']) && $block['items'] ? $block['items'] : array ())));
    //   return $block;
    // }, isset ($posts['blocks']) && $posts['blocks'] ? $posts['blocks'] : array ())));

    $tag_ids = isset ($posts['tag_ids']) ? $posts['tag_ids'] : array ();
    $blocks = isset ($posts['blocks']) ? $posts['blocks'] : array ();

    return $this->load_view (array (
        'posts' => $posts,
        'tag_ids' => $tag_ids,
        'blocks' => $blocks
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    $cover = OAInput::file ('cover');
    $images = OAInput::file ('images[]');

    if ($msg = $this->_validation_create ($posts, $cover, $images))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if (!Work::transaction (function () use (&$obj, $posts, $cover) { return verifyCreateOrm ($obj = Work::create (array_intersect_key ($posts, Work::table ()->columns))) && $obj->cover->put ($cover); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($images)
      foreach ($images as $image)
        WorkImage::transaction (function () use ($image, $obj) { return verifyCreateOrm ($img = WorkImage::create (array_intersect_key (array ('work_id' => $obj->id), WorkImage::table ()->columns))) && $img->name->put ($image); });

    if ($posts['tag_ids'])
      foreach ($posts['tag_ids'] as $tag_id)
        WorkTagMapping::transaction (function () use ($tag_id, $obj) { return verifyCreateOrm (WorkTagMapping::create (array_intersect_key (array ('article_tag_id' => $tag_id, 'article_id' => $obj->id), WorkTagMapping::table ()->columns))); });

    if ($posts['blocks'])
      foreach ($posts['blocks'] as $block)
        if (!($b = null) && WorkBlock::transaction (function () use ($block, $obj, &$b) { return verifyCreateOrm ($b = WorkBlock::create (array_intersect_key (array_merge ($block, array ('work_id' => $obj->id)), WorkBlock::table ()->columns))); }))
          if (($items = $block['items']) && $b)
            foreach ($items as $item)
              WorkBlockItem::transaction (function () use ($item, $b) { return verifyCreateOrm (WorkBlockItem::create (array_intersect_key (array_merge ($item, array ('work_block_id' => $b->id)), WorkBlockItem::table ()->columns))); });

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一項作品。',
      'desc' => '標題名稱為：「' . $obj->mini_title () . '」，內容為：「' . $obj->mini_content () . '」。',
      'backup'  => json_encode ($obj->columns_val ())));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    $tag_ids = isset ($posts['tag_ids']) ? $posts['tag_ids'] : column_array ($this->obj->mappings, 'work_tag_id'); 
    $blocks = isset ($posts['blocks']) ? $posts['blocks'] : array_map (function ($block) { return array ('title' => $block->title, 'items' => array_map (function ($item) { return array ('title' => $item->title, 'link' => $item->link); }, $block->items)); }, $this->obj->blocks);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'tag_ids' => $tag_ids,
        'blocks' => $blocks,
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    $cover = OAInput::file ('cover');
    $images = OAInput::file ('images[]');
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts, $cover, $images, $obj))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    if (!Work::transaction (function () use ($obj, $posts, $cover) { if (!$obj->save () || ($cover && !$obj->cover->put ($cover))) return false; return true; }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));


    if (($del_ids = array_diff (column_array ($obj->images, 'id'), $posts['oldimg'])) && ($imgs = WorkImage::find ('all', array ('select' => 'id, name', 'conditions' => array ('id IN (?)', $del_ids)))))
      foreach ($imgs as $img)
        WorkImage::transaction (function () use ($img) { return $img->destroy (); });

    if ($images)
      foreach ($images as $image)
        WorkImage::transaction (function () use ($image, $obj) { return verifyCreateOrm ($img = WorkImage::create (array_intersect_key (array ('work_id' => $obj->id), WorkImage::table ()->columns))) && $img->name->put ($image); });

    $ori_ids = column_array ($obj->mappings, 'work_tag_id');

    if (($del_ids = array_diff ($ori_ids, $posts['tag_ids'])) && ($mappings = WorkTagMapping::find ('all', array ('select' => 'id, work_tag_id', 'conditions' => array ('work_id = ? AND work_tag_id IN (?)', $obj->id, $del_ids)))))
      foreach ($mappings as $mapping)
        WorkTagMapping::transaction (function () use ($mapping) { return $mapping->destroy (); });

    if (($add_ids = array_diff ($posts['tag_ids'], $ori_ids)) && ($tags = WorkTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $add_ids)))))
      foreach ($tags as $tag)
        WorkTagMapping::transaction (function () use ($tag, $obj) { return verifyCreateOrm (WorkTagMapping::create (Array_intersect_key (array ('work_tag_id' => $tag->id, 'work_id' => $obj->id), WorkTagMapping::table ()->columns))); });

    WorkBlock::transaction (function () use ($obj) { foreach ($obj->blocks as $block) if (!$block->destroy ()) return false; return true; });

    if ($posts['blocks'])
      foreach ($posts['blocks'] as $block)
        if (!($b = null) && WorkBlock::transaction (function () use ($block, $obj, &$b) { return verifyCreateOrm ($b = WorkBlock::create (array_intersect_key (array_merge ($block, array ('work_id' => $obj->id)), WorkBlock::table ()->columns))); }))
          if (($items = $block['items']) && $b)
            foreach ($items as $item)
              WorkBlockItem::transaction (function () use ($item, $b) { return verifyCreateOrm (WorkBlockItem::create (array_intersect_key (array_merge ($item, array ('work_block_id' => $b->id)), WorkBlockItem::table ()->columns))); });

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => 'icon-g',
      'content' => '修改一項作品。',
      'desc' => '標題名稱為：「' . $obj->mini_title () . '」，內容為：「' . $obj->mini_content () . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }
  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->columns_val (true);

    if (!Work::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => 'icon-g',
      'content' => '刪除一項作品。',
      'desc' => '已經備份了刪除紀錄，細節可詢問工程師。',
      'backup'  => json_encode ($backup)));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '刪除成功！'));
  }
  public function is_enabled () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);
    
    $validation = function (&$posts) {
      if (!isset ($posts['is_enabled'])) return '沒有選擇 是否公開！';
      if (!(is_numeric ($posts['is_enabled'] = trim ($posts['is_enabled'])) && in_array ($posts['is_enabled'], array_keys (Work::$enableNames)))) return '是否公開 格式錯誤！';    
      return '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Work::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => Work::$enableNames[$obj->is_enabled] . '一項作品。',
      'desc' => '將作品 “' . $obj->mini_title () . '” 設定成 「' . Work::$enableNames[$obj->is_enabled] . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json ($obj->is_enabled == Work::ENABLE_YES);
  }
  private function _validation_create (&$posts, &$cover, &$images) {
    if (!isset ($posts['is_enabled'])) return '沒有選擇 是否公開！';
    if (!isset ($posts['user_id'])) return '沒有選擇 作品作者！';
    if (!isset ($posts['title'])) return '沒有填寫 作品標題！';
    if (!isset ($posts['content'])) return '沒有填寫 作品內容！';
    if (!isset ($cover)) return '沒有選擇 作品封面！';
    
    if (!(is_numeric ($posts['is_enabled'] = trim ($posts['is_enabled'])) && in_array ($posts['is_enabled'], array_keys (Work::$enableNames)))) return '是否公開 格式錯誤！';
    if (!(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['user_id']))))) return '作品作者 不存在！';
    if (!(is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '作品標題 格式錯誤！';
    if (!is_upload_image_format ($cover, 2 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png'))) return '作品封面 格式錯誤！';
    if (!(is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '作品內容 格式錯誤！';

    $posts['tag_ids'] = isset ($posts['tag_ids']) && is_array ($posts['tag_ids']) && $posts['tag_ids'] ? column_array (WorkTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['tag_ids']))), 'id') : array ();

    $posts['blocks'] = isset ($posts['blocks']) && is_array ($posts['blocks']) && $posts['blocks'] ? array_values (array_filter (array_map (function ($block) {
        if (!(isset ($block['title']) && is_string ($block['title']) && ($block['title'] = trim ($block['title'])))) return array ();
      
        $block['items'] = isset ($block['items']) && is_array ($block['items']) && $block['items'] ? array_values (array_filter (array_map (function ($item) {
          $title = isset ($item['title']) && is_string ($item['title']) && ($item['title'] = trim ($item['title'])) ? $item['title'] : '';
          $link = isset ($item['link']) && is_string ($item['link']) && ($item['link'] = trim ($item['link'])) ? $item['link'] : '';
          return $title || $link ? array ('title' => $title, 'link' => $link) : array ();
        }, $block['items']))) : array ();

        return array (
            'title' => $block['title'],
            'items' => $block['items'],
          );
      }, $posts['blocks']))) : array ();

    $images = array_filter ($images, function ($image) { return is_upload_image_format ($image, 2 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png')); });

    return '';
  }
  private function _validation_update (&$posts, &$cover, &$images, $obj) {
    if (!isset ($posts['is_enabled'])) return '沒有選擇 是否公開！';
    if (!isset ($posts['user_id'])) return '沒有選擇 作品作者！';
    if (!isset ($posts['title'])) return '沒有填寫 作品標題！';
    if (!isset ($posts['content'])) return '沒有填寫 作品內容！';
    if (!((string)$obj->cover || isset ($cover))) return '沒有選擇 文章封面！';
    
    if (!(is_numeric ($posts['is_enabled'] = trim ($posts['is_enabled'])) && in_array ($posts['is_enabled'], array_keys (Work::$enableNames)))) return '是否公開 格式錯誤！';
    if (!(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['user_id']))))) return '作品作者 不存在！';
    if (!(is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '作品標題 格式錯誤！';
    if ($cover && !is_upload_image_format ($cover, 2 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png'))) return '文章封面 格式錯誤！';
    if (!(is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '作品內容 格式錯誤！';

    $posts['tag_ids'] = isset ($posts['tag_ids']) && is_array ($posts['tag_ids']) && $posts['tag_ids'] ? column_array (WorkTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['tag_ids']))), 'id') : array ();

    $posts['blocks'] = isset ($posts['blocks']) && is_array ($posts['blocks']) && $posts['blocks'] ? array_values (array_filter (array_map (function ($block) {
        if (!(isset ($block['title']) && is_string ($block['title']) && ($block['title'] = trim ($block['title'])))) return array ();
        
        $block['items'] = isset ($block['items']) && is_array ($block['items']) && $block['items'] ? array_values (array_filter (array_map (function ($item) {
          $title = isset ($item['title']) && is_string ($item['title']) && ($item['title'] = trim ($item['title'])) ? $item['title'] : '';
          $link = isset ($item['link']) && is_string ($item['link']) && ($item['link'] = trim ($item['link'])) ? $item['link'] : '';
          return $title || $link ? array ('title' => $title, 'link' => $link) : array ();
        }, $block['items']))) : array ();
  
        return array (
            'title' => $block['title'],
            'items' => $block['items'],
          );
      }, $posts['blocks']))) : array ();

    $images = array_filter ($images, function ($image) { return is_upload_image_format ($image, 2 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png')); });

    $posts['oldimg'] = isset ($posts['oldimg']) ? $posts['oldimg'] : array ();

    return '';
  }
}
