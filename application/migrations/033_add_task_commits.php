<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_task_commits extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `task_commits` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `task_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Task ID',
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Commit 的人',

        `action` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '動作',
        `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '內容',
        
        `file` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '檔案',
        `size` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '檔案大小',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `task_commits`;"
    );
  }
}