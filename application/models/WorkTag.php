<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class WorkTag extends OaModel {

  static $table_name = 'work_tags';

  static $has_one = array (
  );

  static $has_many = array (
    array ('mappings', 'class_name' => 'WorkTagMapping'),
    array ('works', 'class_name' => 'Work', 'through' => 'mappings'),
    array ('tags', 'class_name' => 'WorkTag', 'order' => 'sort ASC')
  );

  static $belongs_to = array (
    array ('parent', 'class_name' => 'WorkTag')
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public function columns_val () {
    return array (
      'id'          => isset ($this->id) ? $this->id : '',
      'name'        => isset ($this->name) ? $this->name : '',
      'work_tag_id' => isset ($this->work_tag_id) ? $this->work_tag_id : '',
      'sort'        => isset ($this->sort) ? $this->sort : '',
      'updated_at'  => isset ($this->updated_at) && $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'  => isset ($this->created_at) && $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'name' => $this->name,
        'sort' => $this->sort,
        'par_id' => $this->work_tag_id
      );
  }
  public function destroy () {
    if ($this->mappings)
      foreach ($this->mappings as $mapping)
        if (!$mapping->destroy ())
          return false;
    
    if ($this->tags)
      foreach ($this->tags as $tag)
        if (!$tag->destroy ())
          return false;

    return $this->delete ();
  }
  public function site_show_page_last_uri () {
    return $this->id . '-' . oa_url_encode ($this->name);
  }
}