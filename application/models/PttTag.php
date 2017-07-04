<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class PttTag extends OaModel {

  static $table_name = 'ptt_tags';

  static $has_one = array (
  );

  static $has_many = array (
    array ('user_mappings', 'class_name' => 'PttTagUserMapping'),
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;
    
    if ($this->user_mappings)
      foreach ($this->user_mappings as $user_mapping)
        if (!$user_mapping->destroy ())
          return false;

    return $this->delete ();
  }

  public function backup ($has = false) {
    $var = array (
      'id'         => $this->id,
      'name'       => $this->name,
      'uri'        => $this->uri,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );

    return $has ? array (
        '_' => $var,
        'user_mappings' => $this->subBackup ('PttTagUserMapping', $has),
      ) : $var;
  }
}