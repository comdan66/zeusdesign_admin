<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_schedules extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `schedules` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL COMMENT 'User ID',
        `schedule_tag_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Tag ID',

        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '標題',
        `date` date DEFAULT NULL COMMENT '結束日期',
        `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '備註',
        `sort`   tinyint(4) unsigned  DEFAULT '0' COMMENT '排序，ASC 為主',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `schedules`;"
    );
  }
}