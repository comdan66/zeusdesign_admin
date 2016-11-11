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

    $articles = array_map (function ($article) {
        return $article->to_array ();
      }, Article::find ('all', array ('include' => array ('user', 'tags', 'sources'), 'order' => 'id DESC', 'conditions' => array ('is_enabled = ?', Article::ENABLE_YES))));
    write_file (FCPATH . 'api/articles.json', json_encode ($articles));
    
    $works = array_map (function ($work) {
      return $work->to_array ();
    }, Work::find ('all', array ('include' => array ('user', 'images', 'tags', 'blocks'), 'order' => 'id DESC', 'conditions' => array ('is_enabled = ?', Work::ENABLE_YES))));
    write_file (FCPATH . 'api/works.json', json_encode ($works));
  }
}
