<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
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
      'id'         => $this->id,
      'article_id' => $this->article_id,
      'title'      => $this->title,
      'href'       => $this->href,
      'sort'       => $this->sort,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public function to_api (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'title' => $this->title,
        'href' => $this->href,
      );
  }
  public function destroy () {
    return $this->delete ();
  }
}