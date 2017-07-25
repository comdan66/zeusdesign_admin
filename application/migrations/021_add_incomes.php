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
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'User ID(合帳者)',

        `invoice_date` date DEFAULT NULL COMMENT '發票日期',
        `status` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '是否入帳，1 未入帳，2 已入帳',
        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '備註',
        `money` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '總金額',
        `date` date DEFAULT NULL COMMENT '入帳時間',
        
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