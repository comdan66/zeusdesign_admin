<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_outcomes extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `outcomes` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '新增者',

        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `money` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '金額',

        `date` date DEFAULT NULL COMMENT '日期',
        `type` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '類型，1 沒發票，2 有發票',
        `status` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '是否出帳，1 未出帳，2 已出帳',

        `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '備註',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `outcomes`;"
    );
  }
}