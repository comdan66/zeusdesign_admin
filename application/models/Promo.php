<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Promo extends OaModel {

  static $table_name = 'promos';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );
  
  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '下架',
    self::STATUS_2 => '上架',
  );

  const TARGET_1 = 1;
  const TARGET_2 = 2;

  static $targetNames = array (
    self::TARGET_1 => '本頁',
    self::TARGET_2 => '分頁',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('cover', 'PromoCoverImageUploader');
  }
  public function mini_title ($length = 50) {
    if (!isset ($this->title)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->title), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function mini_content ($length = 100) {
    if (!isset ($this->content)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->content), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function mini_link ($length = 80) {
    if (!isset ($this->link)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->link), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->link);
  }
  public function destroy () {
    if (!isset ($this->id))
      return false;
    return $this->delete ();
  }

  public function to_api (array $opt = array ()) {
    return array (
        'id'      => $this->id,
        'title'   => $this->title,
        'content' => $this->content,
        'link'    => $this->link,
        'sort'    => $this->sort,
        'target'  => $this->target,
        'status'  => $this->status,
        'cover'   => array (
            'ori' => $this->cover->url (),
            'w500' => $this->cover->url ('500w'),
          ),
      );
  }
  public function backup ($has = false) {
    $var = array (
      'id'         => $this->id,
      'title'      => $this->title,
      'content'    => $this->content,
      'link'       => $this->link,
      'sort'       => $this->sort,
      'target'     => $this->target,
      'status'     => $this->status,
      'cover'      => (string)$this->cover ? (string)$this->cover : '',
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('_' => $var) : $var;
  }
}