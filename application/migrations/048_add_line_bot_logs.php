<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Migration_Add_line_bot_logs extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `line_bot_logs` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '類型',
        `reply_token` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '回覆 Token',
        `instanceof` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '對方類型',
        
        `source_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '來源 ID',
        `source_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '來源類型',
        `timestamp` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '時間',
        
        `is_echo` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '結果，1 回傳，0 沒回傳',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `line_bot_logs`;"
    );
  }
}