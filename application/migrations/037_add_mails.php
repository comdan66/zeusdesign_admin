<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_mails extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `mails` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,

        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `content` text NOT NULL COMMENT 'content',
        `to` text NOT NULL COMMENT 'to',
        `cc` text NOT NULL COMMENT 'cc',
        `count_send` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '發送數量',
        `count_open` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '點閱次數',
        `finish` tinyint(4) unsigned NOT NULL DEFAULT 0 COMMENT '是否寄出，1 已寄出，0 未寄出',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `mails`;"
    );
  }
}

