<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_customer_companies extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `customer_companies` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '名稱',
        `business_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '統一編號',
        `telephone` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '電話',
        `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '地址',
        `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '備註',
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `customer_companies`;"
    );
  }
}