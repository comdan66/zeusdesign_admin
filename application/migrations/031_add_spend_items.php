<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Migration_Add_spend_items extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `spend_items` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '新增人員',
        `spend_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '話費 ID',

        `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `money` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '金額',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `spend_items`;"
    );
  }
}