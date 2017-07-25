<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_work_items extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `work_items` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `work_id` int(11) unsigned NOT NULL COMMENT 'Work ID',
        
        `type` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '類型，1 Client，2 Details，3 Technologies，4 Lives，5 Demos',

        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `href` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '網址',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `work_items`;"
    );
  }
}