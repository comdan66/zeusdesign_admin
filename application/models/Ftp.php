<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
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
  public function columns_val ($has = false) {
    $var = array (
      'id'             => $this->id,
      'name'           => $this->name,
      'url'            => $this->url,
      'ftp_url'        => $this->ftp_url,
      'ftp_account'    => $this->ftp_account,
      'ftp_password'   => $this->ftp_password,
      'admin_url'      => $this->admin_url,
      'admin_account'  => $this->admin_account,
      'admin_password' => $this->admin_password,
      'memo'           => $this->memo,
      'updated_at'     => $this->updated_at ? $this->updated_at->format ('Y-m-d H:i:s') : '',
      'created_at'     => $this->created_at ? $this->created_at->format ('Y-m-d H:i:s') : '',
    );
    return $has ? array ('this' => $var) : $var;
  }
  public function to_array (array $opt = array ()) {
    return array (
        'id' => $this->id,
        'name' => $this->name,
        'url' => $this->url,
        
        'ftp_url' => $this->ftp_url,
        'ftp_account' => $this->ftp_account,
        'ftp_password' => $this->ftp_password,
        
        'admin_url' => $this->admin_url,
        'admin_account' => $this->admin_account,
        'admin_password' => $this->admin_password,
        
        'memo' => $this->memo,
      );
  }
  public function destroy () {
    return $this->delete ();
  }
  public function mini_memo ($length = 100) {
    if (!isset ($this->memo)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->memo), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->memo);
  }
}