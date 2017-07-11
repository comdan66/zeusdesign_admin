<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Ftp extends OaModel {

  static $table_name = 'ftps';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function mini_link ($length = 80) {
    if (!isset ($this->link)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->link), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->link);
  }
  public function mini_content ($length = 100) {
    if (!isset ($this->content)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->content), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
  public function destroy () {
    if (!isset ($this->id)) return false;
    
    return $this->delete ();
  }
  public function backup ($has = false) {
    $var = array (
      'id'         => $this->id,
      'name'       => $this->name,
      'link'       => $this->link,
      'content'    => $this->content,
      'memo'       => $this->memo,
      'updated_at' => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at' => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array (
        '_' => $var
      ) : $var;
  }
}