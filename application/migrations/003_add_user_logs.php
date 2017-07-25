<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_user_logs extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `user_logs` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'User ID(作者)',

        `icon` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '圖示',
        `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '內容',
        
        `backup` text NOT NULL COMMENT '備份 json',
        `status` tinyint(4) unsigned NOT NULL DEFAULT 0 COMMENT '記錄動作，0 未知，1 讀，2 寫',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `user_logs`;"
    );
  }
}