<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class DeployTool {
  public static function genApi () {
    $CI =& get_instance ();
    $CI->load->helper ('directory_helper');
    $api = FCPATH . 'api' . DIRECTORY_SEPARATOR;
    @directory_delete ($api, false);

    $banners = array_map (function ($obj) {
      return array (
          'id' => $obj->id,
          'title' => $obj->title,
          'content' => $obj->content,
          'link' => $obj->link,
          'blank' => $obj->target == Banner::TARGET_2,
          'cover' => array (
              'ori' => $obj->cover->url (),
              'w800' => $obj->cover->url ('800w'),
            ),
        );
    }, Banner::find ('all', array ('order' => 'sort DESC', 'conditions' => array ('status = ?', Banner::STATUS_2))));

    write_file ($api . 'banners.json', json_encode ($banners));
    @chmod ($api . 'banners.json', 0777);

    $promos = array_map (function ($obj) {
      return array (
          'id' => $obj->id,
          'title' => $obj->title,
          'content' => $obj->content,
          'link' => $obj->link,
          'blank' => $obj->target == Promo::TARGET_2,
          'cover' => array (
              'ori' => $obj->cover->url (),
              'w500' => $obj->cover->url ('500w'),
            ),
        );
    }, Promo::find ('all', array ('order' => 'sort DESC', 'conditions' => array ('status = ?', Promo::STATUS_2))));
    write_file ($api . 'promos.json', json_encode ($promos));
    @chmod ($api . 'promos.json', 0777);

    $articles = array_map (function ($article) {
      return array (
          'id' => $article->id,
          'user' => array (
              'id' => $article->user->id,
              'url' => $article->user->url (),
              'name' => $article->user->name,
              'avatar' => $article->user->avatar (300, 300),
            ),
          'tags' => array_map (function ($tag) {
            return array (
                'id' => $tag->id,
                'name' => $tag->name,
              );
          }, $article->tags),
          'title' => $article->title,
          'cover' => array (
              'c450' => $article->cover->url ('450x180c'),
              'c1200' => $article->cover->url ('1200x630c'),
            ),
          'content' => $article->content,
          'pv' => $article->pv,
          'sources' => array_map (function ($source) {
            return array (
                'id' => $source->id,
                'href' => $source->href,
                'title' => $source->title,
              );
          }, $article->sources),
          'status' => $article->status == Article::STATUS_2,
          'updated_at' => $article->updated_at->format ('Y-m-d H:i:s'),
          'created_at' => $article->created_at->format ('Y-m-d H:i:s'),
        );
    }, Article::find ('all', array ('include' => array ('user', 'mappings', 'sources'), 'order' => 'id DESC', 'conditions' => array ('status = ?', Article::STATUS_2))));
    write_file ($api . 'articles.json', json_encode ($articles));
    @chmod ($api . 'articles.json', 0777);
    
    $work_tags = array_map (function ($tag) {
      return array (
          'id' => $tag->id,
          'name' => $tag->name,
          'subs' => array_map (function ($tag) {
            return array (
              'id' => $tag->id,
              'name' => $tag->name); }, $tag->tags)); }, WorkTag::find ('all', array ('include' => array ('tags'), 'order' => 'sort DESC', 'conditions' => array ('work_tag_id = ?', 0))));

    write_file ($api . 'work_tags.json', json_encode ($work_tags));
    @chmod ($api . 'work_tags.json', 0777);

    $works = array_map (function ($work) {
      return array (
          'id' => $work->id,
          'user' => array (
              'id' => $work->user->id,
              'url' => $work->user->url (),
              'name' => $work->user->name,
              'avatar' => $work->user->avatar (300, 300),
            ),
          'tags' => array_map (function ($tag) {
            return array (
                'id' => $tag->id,
                'name' => $tag->name,
                'sort' => $tag->sort,
                'par_id' => $tag->work_tag_id
              );
          }, $work->tags),
          'title' => $work->title,
          'content' => $work->content,
          'cover' => array (
              'w300' => $work->cover->url ('300w'),
              'c1200' => $work->cover->url ('1200x630c'),
            ),
          'images' => array_map (function ($image) {
            return array (
              'id' => $image->id,
              'ori' => $image->name->url (),
              'w800' => $image->name->url ('800w'),
            );
          }, $work->images),

          'blocks' => array_map (function ($type) use ($work) {
            return array (
                'title' => WorkItem::$typeNames[$type],
                'items' => array_map (function ($item) {
                  return array (
                      'id' => $item->id,
                      'title' => $item->title,
                      'href' => $item->href,
                    );
                }, array_filter ($work->items, function ($item) use ($type) {
                  return $item->type == $type;
                }))
              );
          }, array_unique (column_array ($work->items, 'type'))),

          'pv' => $work->pv,
          'status' => $work->status == Work::STATUS_2,
          'updated_at' => $work->updated_at->format ('Y-m-d H:i:s'),
          'created_at' => $work->created_at->format ('Y-m-d H:i:s'),
        );
    }, Work::find ('all', array ('include' => array ('user', 'images', 'items'), 'order' => 'id DESC', 'conditions' => array ('status = ?', Work::STATUS_2))));

    write_file ($api . 'works.json', json_encode ($works));
    @chmod ($api . 'works.json', 0777);

    return true;
  }

  public static function userAgent () {
    $t = array (
      'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
      'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.76 Safari/537.36',
      'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
      'Mozilla/5.0 (Linux; Android 4.3; Nexus 7 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
      'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
    );
    return $t[array_rand ($t)];
  }
  public static function crud ($opts, &$obj) {
    $options = array (
      CURLOPT_URL => $opts['url'],
      CURLOPT_USERAGENT => self::userAgent (),
      CURLOPT_POSTFIELDS => http_build_query ($opts['data']),
      CURLOPT_TIMEOUT => 240, CURLOPT_HEADER => false, CURLOPT_POST => true, CURLOPT_MAXREDIRS => 10, CURLOPT_AUTOREFERER => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true,
    );

    $ch = curl_init ($opts['url']);
    curl_setopt_array ($ch, $options);
    $data = curl_exec ($ch);
    curl_close ($ch);

    $obj->res = $data;

    
    if ($data && ($data = json_decode ($data, true)) && isset ($data['status']) && $data['status']) {
      return true;
    } else {
      // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
      // var_dump ($data);
      // exit ();
      return false;
    };
  }
  public static function callBuild (&$obj) {
    return self::crud (Cfg::setting ('deploy', 'build'), $obj);
  }
  public static function callUpload (&$obj) {
    return self::crud (Cfg::setting ('deploy', 'upload'), $obj);
  }
}