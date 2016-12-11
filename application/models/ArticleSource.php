<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class ArticleSource extends OaModel {

  static $table_name = 'article_sources';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function columns_val ($has = false) {
    $var = array (
      'id'         => isset ($this->id) ? $this->id : '',
      'article_id' => isset ($this->article_id) ? $this->article_id : '',
      'title'      => isset ($this->title) ? $this->title : '',
      'href'       => isset ($this->href) ? $this->href : '',
      'sort'       => isset ($this->sort) ? $this->sort : '',
      'updated_at' => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'title' => $this->title,
        'href' => $this->href,
      );
  }
  public function destroy () {
    return $this->delete ();
  }
  public function mini_href ($length = 80) {
    if (!isset ($this->href)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->href), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->href);
  }
}