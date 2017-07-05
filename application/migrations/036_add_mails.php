<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_mails extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `mails` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        
        `to` text NOT NULL COMMENT 'to',
        `cc` text NOT NULL COMMENT 'cc',
        
        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `content` text NOT NULL COMMENT 'content',
        `uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'URI',
        
        `cnt_send` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '發送數量',
        `cnt_open` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '點閱次數',
        `status` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '是否寄出，1 未寄出，2 已寄出',
        `token` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Access Token md5(id+time())',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`),
        KEY `token_index` (`token`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `mails`;"
    );
  }
}

