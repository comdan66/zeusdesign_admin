<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_ptts extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `ptts` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `ptt_tag_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Ptt Tag ID',

        `pid` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Ptt Article ID',
        `cnt` tinyint(4) NOT NULL DEFAULT 1 COMMENT '推文數',
        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `uri` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '網址 uri',
        `date` date DEFAULT NULL COMMENT '發文日期',
        `author` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Ptt 帳號',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`),
        KEY `pid_index` (`pid`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `ptts`;"
    );
  }
}

