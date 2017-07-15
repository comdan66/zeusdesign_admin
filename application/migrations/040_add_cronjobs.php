<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_cronjobs extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `cronjobs` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        
        `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `rule` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '規則',
        `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '內容',

        `start_at` double unsigned NOT NULL DEFAULT '0' COMMENT '開始時間',
        `end_at` double unsigned NOT NULL DEFAULT '0' COMMENT '結束時間',

        `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '是否成功，1 失敗，2 成功',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `cronjobs`;"
    );
  }
}