<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class ArticleTagMapping extends OaModel {

  static $table_name = 'article_tag_mappings';

  static $has_one = array (
  );

  static $has_many = array (
    array ('tags', 'class_name' => 'ArticleTag'),
    array ('articles', 'class_name' => 'Article'),
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'             => isset ($this->id) ? $this->id : '',
      'article_id'     => isset ($this->article_id) ? $this->article_id : '',
      'article_tag_id' => isset ($this->article_tag_id) ? $this->article_tag_id : '',
      'updated_at'     => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'     => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public function destroy () {
    return $this->delete ();
  }
}