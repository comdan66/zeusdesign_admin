<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_backups extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `backups` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        
        `file` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '檔案',
        `size` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '檔案大小',

        `type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '備份類型，1 資料庫，2 Query',
        `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '是否成功，1 失敗，2 成功',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `backups`;"
    );
  }
}