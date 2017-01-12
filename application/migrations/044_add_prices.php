<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_prices extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `prices` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `price_type_id` int(11) unsigned NOT NULL COMMENT '分類 ID',

        `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '功能名稱',
        `money` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '金額',
        `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
        `source_link` text  COMMENT '參考網址',
       
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `prices`;"
    );
  }
}