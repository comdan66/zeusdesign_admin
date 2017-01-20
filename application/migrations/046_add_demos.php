<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_demos extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `demos` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `uid` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'uniq ID',
        `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '名稱',
        `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '密碼',
        `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '備註',
        `is_mobile` tinyint(4) unsigned NOT NULL DEFAULT 0 COMMENT '是否為手機版，1 是，0 否',
        `is_enabled` tinyint(4) unsigned NOT NULL DEFAULT 0 COMMENT '是否使用，1 使用，0 停用',
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `demos`;"
    );
  }
}