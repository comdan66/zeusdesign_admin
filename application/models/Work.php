<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Work extends OaModel {

  static $table_name = 'works';

  static $has_one = array (
  );

  static $has_many = array (
    array ('mappings', 'class_name' => 'WorkTagMapping'),
    array ('images', 'class_name' => 'WorkImage'),
    array ('tags', 'class_name' => 'WorkTag', 'through' => 'mappings'),

    array ('items', 'class_name' => 'WorkItem'),
  );

  static $belongs_to = array (
    array ('user', 'class_name' => 'User'),
  );

  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '下架',
    self::STATUS_2 => '上架',
  );

  private $typeItems = array ();

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
    
    OrmImageUploader::bind ('cover', 'WorkCoverImageUploader');
  }
  public function mini_title ($length = 50) {
    if (!isset ($this->title)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->title), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function mini_content ($length = 100) {
    if (!isset ($this->content)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->content), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function typeItems ($type) {
    if (isset ($typeItems[$type])) return $typeItems[$type];
    return $typeItems[$type] = array_values (array_filter ($this->items, function ($t) use ($type) {
      return $t->type == $type;
    }));
  }
  public function destroy () {
    if (!isset ($this->id)) return false;
    
    if ($this->items)
      foreach ($this->items as $item)
        if (!$item->destroy ())
          return false;

    if ($this->images)
      foreach ($this->images as $image)
        if (!$image->destroy ())
          return false;

    if ($this->mappings)
      foreach ($this->mappings as $mapping)
        if (!$mapping->destroy ())
          return false;

    return $this->delete ();
  }

  public function backup ($has = false) {
    $var = array (
      'id'         => $this->id,
      'user_id'    => $this->user_id,
      'title'      => $this->title,
      'cover'      => (string)$this->cover ? (string)$this->cover : '',
      'content'    => $this->content,
      'status'     => $this->status,
      'pv'         => $this->pv,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
        '_' => $var,
        'mappings' => $this->subBackup ('WorkTagMapping'),
        'images'   => $this->subBackup ('WorkImage'),
        'items'   => $this->subBackup ('WorkItem'),
      ) : $var;
  }
}