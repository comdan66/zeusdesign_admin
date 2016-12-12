<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_task_user_mappings extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `task_user_mappings` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `task_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Task ID',
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '接受 Task 的 User ID',
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `task_user_mappings`;"
    );
  }
}