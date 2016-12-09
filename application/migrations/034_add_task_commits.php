<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Migration_Add_task_commits extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `task_commits` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `task_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Task ID',
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Commit 的人',
        `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '內容',
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