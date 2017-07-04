<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Ptt extends OaModel {

  static $table_name = 'ptts';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('tag', 'class_name' => 'PttTag'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;

    return $this->delete ();
  }

  public function backup ($has = false) {
    $var = array (
      'id'         => $this->id,
      'ptt_tag_id' => $this->ptt_tag_id,
      'pid'        => $this->pid,
      'cnt'        => $this->cnt,
      'title'      => $this->title,
      'uri'        => $this->uri,
      'date'       => $this->date,
      'author'     => $this->author,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );

    return $has ? array (
        '_' => $var,
      ) : $var;
  }
}