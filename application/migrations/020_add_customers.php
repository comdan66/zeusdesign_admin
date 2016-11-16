<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Migration_Add_customers extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `customers` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `company_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '公司 ID',
        `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '名稱',
        `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '郵件',
        `telephone` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '電話',
        `extension` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '電話分機',
        `cellphone` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '手機',
        `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '電話分機',
        `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '備註',
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `customers`;"
    );
  }
}