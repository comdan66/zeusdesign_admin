<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_schedule_items extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `schedule_items` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `schedule_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Schedule ID',

        `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '事項',
        `status` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '是否完成，1 未完成，2 已完成',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `schedule_items`;"
    );
  }
}