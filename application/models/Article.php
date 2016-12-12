<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Article extends OaModel {

  static $table_name = 'articles';

  static $has_one = array (
  );

  static $has_many = array (
    array ('mappings', 'class_name' => 'ArticleTagMapping'),
    array ('tags', 'class_name' => 'ArticleTag', 'through' => 'mappings'),
    array ('sources', 'class_name' => 'ArticleSource', 'order' => 'sort ASC')
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );
  
  const ENABLE_NO  = 0;
  const ENABLE_YES = 1;

  static $enableNames = array(
    self::ENABLE_NO  => '停用',
    self::ENABLE_YES => '啟用',
  );
  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('cover', 'ArticleCoverImageUploader');
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'         => $this->id,
      'user_id'    => $this->user_id,
      'title'      => $this->title,
      'content'    => $this->content,
      'is_enabled' => $this->is_enabled,
      'pv'         => $this->pv,
      'cover'      => (string)$this->cover ? (string)$this->cover : '',
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
      'this' => $var,
      'mappings' => array_map (function ($mapping) {
        return $mapping->columns_val ();
      }, ArticleTagMapping::find ('all', array ('conditions' => array ('article_id = ?', $this->id)))),
      'sources' => array_map (function ($source) {
        return $source->columns_val ();
      }, ArticleSource::find ('all', array ('conditions' => array ('article_id = ?', $this->id))))) : $var;
  }
  public function to_api () {
    return array (
      'id' => $this->id,
      'user' => $this->user->to_api (),
      'tags' => array_map (function ($tag) {
        return $tag->to_api ();
      }, ArticleTag::find ('all', array ('conditions' => array ('id IN (?)', ($tag_ids = column_array ($this->mappings, 'article_tag_id')) ? $tag_ids : array (0))))),
      'title' => $this->title,
      'cover' => array (
          'c450' => $this->cover->url ('450x180c'),
          'c1200' => $this->cover->url ('1200x630c'),
        ),
      'content' => $this->content,
      'pv' => $this->pv,
      'sources' => array_map (function ($source) {
        return $source->to_api ();
      }, $this->sources),
      'is_enabled' => $this->is_enabled,
      'updated_at' => $this->updated_at->format ('Y-m-d H:i:s'),
      'created_at' => $this->created_at->format ('Y-m-d H:i:s'),
    );
  }
  public function destroy () {
    if ($this->mappings)
      foreach ($this->mappings as $mapping)
        if (!$mapping->destroy ())
          return false;

    if ($this->sources)
      foreach ($this->sources as $source)
        if (!$source->destroy ())
          return false;

    return $this->delete ();
  }
  public function mini_title ($length = 50) {
    if (!isset ($this->title)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->title), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function mini_content ($length = 100) {
    if (!isset ($this->content)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->content), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
}