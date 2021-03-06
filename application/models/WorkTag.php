<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class WorkTag extends OaModel {

  static $table_name = 'work_tags';

  static $has_one = array (
  );

  static $has_many = array (
    array ('mappings', 'class_name' => 'WorkTagMapping'),
    array ('works', 'class_name' => 'Work', 'through' => 'mappings'),
    array ('tags', 'class_name' => 'WorkTag', 'order' => 'sort DESC')
  );

  static $belongs_to = array (
    array ('parent', 'class_name' => 'WorkTag')
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public function destroy () {
    if (!isset ($this->id)) return false;
    
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
    $var = $this->getBackup ();
    return $has ? array (
        '_' => $var,
        'mappings' => $this->subBackup ('WorkTagMapping', $has),
        'tags' => $this->subBackup ('WorkTag', $has),
      ) : $var;
  }
}