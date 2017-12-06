<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_pwd_tips extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `pwd_tips` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'User ID(作者)',
        `tip` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '提示',
        `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Token',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `pwd_tips`;"
    );
  }
}