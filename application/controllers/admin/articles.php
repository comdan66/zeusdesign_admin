<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Articles extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = 'icon-f';

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('article')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/articles';

    if (in_array ($this->uri->rsegments (2, 0), array ('show', 'edit', 'update', 'destroy', 'is_enabled')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Article::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1);
    $this->add_param ('now_url', base_url ($this->uri_1));
  }
  public function show ($id) {
    $this->load->library ('DeployTool');

    if (!(DeployTool::genApi (true) && DeployTool::callBuild ()))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '預覽失敗！'));

    return redirect_message (Cfg::setting ('deploy', 'view', ENVIRONMENT) . 'article/' . $this->obj->id . '-' . rawurlencode (preg_replace ('/[\/%]/u', ' ', $this->obj->title)) . '.html', array ('_flash_info' => ''));
  }
  public function index ($offset = 0) {
    $columns = array ( 
        array ('key' => 'content', 'title' => '內容', 'sql' => 'content LIKE ?'), 
        array ('key' => 'title',   'title' => '標題', 'sql' => 'title LIKE ?'), 
        array ('key' => 'user_id', 'title' => '作者', 'sql' => 'user_id = ?', 'select' => array_map (function ($user) { return array ('value' => $user->id, 'text' => $user->name);}, User::all (array ('select' => 'id, name')))),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = Article::count (array ('conditions' => $conditions));
    $offset = $offset < $total ? $offset : 0;

    $this->load->library ('pagination');
    $pagination = $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
    $objs = Article::find ('all', array ('offset' => $offset, 'limit' => $limit, 'order' => 'id DESC', 'include' => array ('user'), 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'pagination' => $pagination,
        'columns' => $columns
      ));
  }
  public function add () {
    $posts = Session::getData ('posts', true);
    $tag_ids = isset ($posts['tag_ids']) ? $posts['tag_ids'] : array ();
    $sources = isset ($posts['sources']) ? $posts['sources'] : array ();

    return $this->load_view (array (
        'posts' => $posts,
        'tag_ids' => $tag_ids,
        'sources' => $sources,
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    $cover = OAInput::file ('cover');

    if ($msg = $this->_validation_create ($posts, $cover))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if (!Article::transaction (function () use (&$obj, $posts, $cover) { return verifyCreateOrm ($obj = Article::create (array_intersect_key ($posts, Article::table ()->columns))) && $obj->cover->put ($cover); }))
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    if ($posts['tag_ids'])
      foreach ($posts['tag_ids'] as $tag_id)
        ArticleTagMapping::transaction (function () use ($tag_id, $obj) { return verifyCreateOrm (ArticleTagMapping::create (array_intersect_key (array ('article_tag_id' => $tag_id, 'article_id' => $obj->id), ArticleTagMapping::table ()->columns))); });

    if ($posts['sources'])
      foreach ($posts['sources'] as $i => $source)
        ArticleSource::transaction (function () use ($i, $source, $obj) { return verifyCreateOrm (ArticleSource::create (array_intersect_key (array_merge ($source, array ('sort' => $i, 'article_id' => $obj->id)), ArticleSource::table ()->columns))); });

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '新增一篇文章。',
      'desc' => '標題名稱為：「' . $obj->mini_title () . '」，內容是：「' . $obj->mini_content () . '」。',
      'backup'  => json_encode ($obj->columns_val ())));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'tag_ids' => isset ($posts['tag_ids']) ? $posts['tag_ids'] : column_array ($this->obj->mappings, 'article_tag_id'),
        'sources' => isset ($posts['sources']) ? $posts['sources'] : array_map (function ($source) { return array ('title' => $source->title, 'href' => $source->href); }, $this->obj->sources),
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['content'] = OAInput::post ('content', false);
    $cover = OAInput::file ('cover');
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts, $cover, $obj))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Article::transaction (function () use ($obj, $posts, $cover) { if (!$obj->save () || ($cover && !$obj->cover->put ($cover))) return false; return true; }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    $ori_ids = column_array ($obj->mappings, 'article_tag_id');

    if (($del_ids = array_diff ($ori_ids, $posts['tag_ids'])) && ($mappings = ArticleTagMapping::find ('all', array ('select' => 'id, article_tag_id', 'conditions' => array ('article_id = ? AND article_tag_id IN (?)', $obj->id, $del_ids)))))
      foreach ($mappings as $mapping)
        ArticleTagMapping::transaction (function () use ($mapping) { return $mapping->destroy (); });

    if (($add_ids = array_diff ($posts['tag_ids'], $ori_ids)) && ($tags = ArticleTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $add_ids)))))
      foreach ($tags as $tag)
        ArticleTagMapping::transaction (function () use ($tag, $obj) { return verifyCreateOrm (ArticleTagMapping::create (Array_intersect_key (array ('article_tag_id' => $tag->id, 'article_id' => $obj->id), ArticleTagMapping::table ()->columns))); });

    if ($obj->sources)
      foreach ($obj->sources as $source)
        ArticleSource::transaction (function () use ($source) { return $source->destroy (); });

    if ($posts['sources'])
      foreach ($posts['sources'] as $i => $source)
        ArticleSource::transaction (function () use ($i, $source, $obj) { return verifyCreateOrm (ArticleSource::create (array_intersect_key (array_merge ($source, array ('sort' => $i, 'article_id' => $obj->id)), ArticleSource::table ()->columns))); });

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '修改一篇文章。',
      'desc' => '標題名稱為：「' . $obj->mini_title () . '」，內容是：「' . $obj->mini_content () . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }
  public function destroy () {
    $obj = $this->obj;
    $backup = $obj->columns_val (true);

    if (!Article::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '刪除一篇文章。',
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
      if (!(is_numeric ($posts['is_enabled'] = trim ($posts['is_enabled'])) && in_array ($posts['is_enabled'], array_keys (Article::$enableNames)))) return '是否公開 格式錯誤！';    
      return '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Article::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => Article::$enableNames[$obj->is_enabled] . '一篇文章。',
      'desc' => '將文章 “' . $obj->mini_title () . '” 設定成 「' . Work::$enableNames[$obj->is_enabled] . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json ($obj->is_enabled == Article::ENABLE_YES);
  }

  private function _validation_create (&$posts, &$cover) {
    if (!isset ($posts['is_enabled'])) return '沒有選擇 是否公開！';
    if (!isset ($posts['user_id'])) return '沒有選擇 文章作者！';
    if (!isset ($posts['title'])) return '沒有填寫 文章標題！';
    if (!isset ($posts['content'])) return '沒有填寫 文章內容！';
    if (!isset ($cover)) return '沒有選擇 文章封面！';
    
    if (!(is_numeric ($posts['is_enabled'] = trim ($posts['is_enabled'])) && in_array ($posts['is_enabled'], array_keys (Article::$enableNames)))) return '是否公開 格式錯誤！';
    if (!(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['user_id']))))) return '文章作者 不存在！';
    if (!(is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '文章標題 格式錯誤！';
    if (!is_upload_file_format ($cover, 2 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png'))) return '文章封面 格式錯誤！';
    if (!(is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '文章內容 格式錯誤！';

    $posts['sources'] = isset ($posts['sources']) && is_array ($posts['sources']) && $posts['sources'] ? array_values (array_filter ($posts['sources'], function ($source) { return isset ($source['href']) && is_string ($source['href']) && ($source['href'] = trim ($source['href'])); })) : array ();
    $posts['tag_ids'] = isset ($posts['tag_ids']) && is_array ($posts['tag_ids']) && $posts['tag_ids'] ? column_array (ArticleTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['tag_ids']))), 'id') : array ();
    
    return '';
  }
  private function _validation_update (&$posts, &$cover, $obj) {
    if (!isset ($posts['is_enabled'])) return '沒有選擇 是否公開！';
    if (!isset ($posts['user_id'])) return '沒有選擇 文章作者！';
    if (!isset ($posts['title'])) return '沒有填寫 文章標題！';
    if (!isset ($posts['content'])) return '沒有填寫 文章內容！';
    if (!((string)$obj->cover || isset ($cover))) return '沒有選擇 文章封面！';

    if (!(is_numeric ($posts['is_enabled'] = trim ($posts['is_enabled'])) && in_array ($posts['is_enabled'], array_keys (Article::$enableNames)))) return '是否公開 格式錯誤！';
    if (!(is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find ('one', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['user_id']))))) return '文章作者 不存在！';
    if (!(is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '文章標題 格式錯誤！';
    if ($cover && !is_upload_file_format ($cover, 2 * 1024 * 1024, array ('gif', 'jpeg', 'jpg', 'png'))) return '文章封面 格式錯誤！';
    if (!(is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '文章內容 格式錯誤！';

    $posts['sources'] = isset ($posts['sources']) && is_array ($posts['sources']) && $posts['sources'] ? array_values (array_filter ($posts['sources'], function ($source) { return isset ($source['href']) && is_string ($source['href']) && ($source['href'] = trim ($source['href'])); })) : array ();
    $posts['tag_ids'] = isset ($posts['tag_ids']) && is_array ($posts['tag_ids']) && $posts['tag_ids'] ? column_array (ArticleTag::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?)', $posts['tag_ids']))), 'id') : array ();
    
    return '';
  }
}
