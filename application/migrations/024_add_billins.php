<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Migration_Add_billins extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `billins` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '負責人',

        `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '專案名稱',
        `money` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '總金額',

        `rate_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '%數名稱，未開發票 10%，有開發票 2%，其他 5%',
        `rate` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '%數',
        `zeus_money` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '宙思獲得金額',

        `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '備註',
        `is_finished` tinyint(4) unsigned NOT NULL DEFAULT 0 COMMENT '是否入帳，1 是，0 否',
        `is_pay` tinyint(4) unsigned NOT NULL DEFAULT 0 COMMENT '是否支付，1 是，0 否',
        `date_at` date DEFAULT NULL COMMENT '日期',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `billins`;"
    );
  }
}