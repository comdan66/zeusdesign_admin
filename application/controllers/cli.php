<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Cli extends Oa_controller {

  public function index () {
    $banners = array_map (function ($banner) {
      return array (
          'id' => $banner->id,
          'title' => $banner->title,
          'content' => $banner->content,
          'cover' => $banner->cover->url ('800w'),
          'link' => $banner->link,
          'target' => $banner->target,
        );
    }, Banner::find ('all', array ('order' => 'sort DESC', 'conditions' => array ('is_enabled = ?', Banner::ENABLE_YES))));
    write_file (FCPATH . 'api/banners.json', json_encode ($banners));

    
    $promos = array_map (function ($promo) {
      return array (
          'id' => $promo->id,
          'title' => $promo->title,
          'content' => $promo->content,
          'cover' => $promo->cover->url ('500w'),
          'link' => $promo->link,
          'target' => $promo->target,
        );
    }, Promo::find ('all', array ('order' => 'sort DESC', 'conditions' => array ('is_enabled = ?', Promo::ENABLE_YES))));
    write_file (FCPATH . 'api/promos.json', json_encode ($promos));

    $articles = array_map (array ($this, '_article'), Article::find ('all', array ('include' => array ('user', 'tags', 'sources'), 'order' => 'id DESC', 'conditions' => array ('is_enabled = ?', Article::ENABLE_YES))));
    write_file (FCPATH . 'api/articles.json', json_encode ($articles));
    
    // $that = $this;
    // $tags = array_map (function ($tag) use ($that) {
    //   return array (
    //       'id' => $tag->id,
    //       'name' => $tag->name,
    //       'articles' => array_map (array ($that, '_article'), $tag->articles),
    //     );
    // }, ArticleTag::find ('all', array ('order' => 'RAND()', 'include' => array ('articles'))));
    // write_file (FCPATH . 'api/article-tags.json', json_encode ($tags));


    $works = array_map (array ($this, '_work'), Work::find ('all', array ('include' => array ('user', 'images', 'tags', 'blocks'), 'order' => 'id DESC', 'conditions' => array ('is_enabled = ?', Work::ENABLE_YES))));
    write_file (FCPATH . 'api/works.json', json_encode ($works));
  }
  private function _work ($work) {
    return array (
      'id' => $work->id,
      'user' => array (
          'id' => $work->user->id,
          'name' => $work->user->name,
          'fid' => $work->user->uid,
        ),
      'tags' => array_map (function ($tag) {
        return array (
            'id' => $tag->id,
            'name' => $tag->name,
            'sort' => $tag->sort,
            'par_id' => $tag->work_tag_id
          );
      }, WorkTag::find ('all', array ('conditions' => array ('id IN (?)', ($tag_ids = column_array ($work->mappings, 'work_tag_id')) ? $tag_ids : array (0))))),
      'title' => $work->title,
      'cover' => array (
          'c450' => $work->cover->url ('450x180c'),
          'c1200' => $work->cover->url ('1200x630c'),
        ),
      'images' => array_map (function ($image) {
        return array (
            'ori' => $image->name->url (),
            'w800' => $image->name->url ('800w'),
          );
      }, $work->images),
      'content' => $work->content,
      'blocks' => array_map (array ($this, '_block'), $work->blocks),
      'pv' => $work->pv,
      'updated_at' => $work->updated_at->format ('Y-m-d H:i:s'),
      'created_at' => $work->created_at->format ('Y-m-d H:i:s'),
    );
  }
  private function _block ($block) {
    return array (
      'title' => $block->title,
      'items' => array_map (function ($item) {
        return array (
          'title' => $item->title,
          'link' => $item->link,
        );
      }, $block->items),
    );
  }
  private function _article ($article) {
    return array (
      'id' => $article->id,
      'user' => array (
          'id' => $article->user->id,
          'name' => $article->user->name,
          'fid' => $article->user->uid,
        ),
      'tags' => array_map (function ($tag) {
        return array (
            'id' => $tag->id,
            'name' => $tag->name,
          );
      }, ArticleTag::find ('all', array ('conditions' => array ('id IN (?)', ($tag_ids = column_array ($article->mappings, 'article_tag_id')) ? $tag_ids : array (0))))),
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
            'title' => $source->title,
            'href' => $source->href,
          );
      }, $article->sources),
      'updated_at' => $article->updated_at->format ('Y-m-d H:i:s'),
      'created_at' => $article->created_at->format ('Y-m-d H:i:s'),
    );
  }
}
