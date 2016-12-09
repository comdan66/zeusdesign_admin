<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Migration_Add_spends extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `spends` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '新增人員',

        `cover` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '封面',
        `number` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '發票號碼',
        `timed_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '時間',
        `money` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '總金額',

        `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '地址',
        `lat` DOUBLE NOT NULL COMMENT '緯度',
        `lng` DOUBLE NOT NULL COMMENT '經度',

        `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '備註',
        
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `spends`;"
    );
  }
}