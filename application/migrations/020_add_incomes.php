<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_incomes extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `incomes` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,

        `invoice_date` date DEFAULT NULL COMMENT '發票日期',
        `status` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '是否入帳，1 未入帳，2 已入帳',
        `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '類型，1 有發票，2 沒發票',
        `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '備註',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `incomes`;"
    );
  }
}