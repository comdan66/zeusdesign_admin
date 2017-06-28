<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_tasks extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `tasks` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'User ID(產生者)',

        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `content` text NOT NULL COMMENT '內容',
        `status` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '是否完成，1 未完成，2 完成',
        `level` tinyint(4) DEFAULT '4' COMMENT '優先權，1 有空處理，2 一般事件，3 重要事件，4 非常重要，5 非常緊急',

        `date` date DEFAULT NULL COMMENT '日期',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `tasks`;"
    );
  }
}