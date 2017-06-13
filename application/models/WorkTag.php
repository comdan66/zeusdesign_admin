<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class WorkTag extends OaModel {

  static $table_name = 'work_tags';

  static $has_one = array (
  );

  static $has_many = array (
    // array ('mappings', 'class_name' => 'WorkTagMapping'),
    // array ('works', 'class_name' => 'Work', 'through' => 'mappings'),
    array ('tags', 'class_name' => 'WorkTag', 'order' => 'sort ASC')
  );

  static $belongs_to = array (
    array ('parent', 'class_name' => 'WorkTag')
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public function destroy () {
    if ($this->mappings)
      foreach ($this->mappings as $mapping)
        if (!$mapping->destroy ())
          return false;
    
    $sort = ($t = WorkTag::find ('one', array ('select' => 'sort', 'order' => 'sort DESC', 'conditions' => array ('work_tag_id = ?', $this->work_tag_id)))) ? $t->sort : 0;
    if ($this->tags)
      foreach ($this->tags as $tag)
        if (!(!($tag->work_tag_id = 0) && ($tag->sort = ++$sort) && $tag->save ()))
          return false;

    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = array (
      'id'          => $this->id,
      'name'        => $this->name,
      'work_tag_id' => $this->work_tag_id,
      'sort'        => $this->sort,
      'updated_at'  => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
        '_' => $var,
        'mappings' => $this->subBackup ('WorkTagMapping'),
        'tags' => $this->subBackup ('WorkTag'),
      ) : $var;
  }
}