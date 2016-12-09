<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Migration_Add_billous extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `billous` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '新增者',

        `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '項目名稱',
        `money` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '金額',

        `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '備註',
        `is_invoice` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '是否有發票，1 有，0 沒有',
        `is_finished` tinyint(4) unsigned NOT NULL DEFAULT 0 COMMENT '是否出帳，1 是，0 否',
        `date_at` date DEFAULT NULL COMMENT '日期',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `billous`;"
    );
  }
}