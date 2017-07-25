<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class UserSet extends OaModel {

  static $table_name = 'user_sets';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const ANI_1 = 1;
  const ANI_2 = 2;
  const ANI_3 = 3;
  const ANI_4 = 4;
  const ANI_5 = 5;
  const ANI_6 = 6;

  static $aniNames = array (
    self::ANI_1 => '關閉',
    self::ANI_2 => '由下而上',
    self::ANI_3 => '由上而下',
    self::ANI_4 => '由左而右',
    self::ANI_5 => '由右而左',
    self::ANI_6 => '淡出',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('banner', 'UserSetBannerImageUploader');
  }
  public function backup ($has = false) {
    $var = $this->getBackup ();
    return $has ? array (
        '_' => $var
      ) : $var;
  }
}