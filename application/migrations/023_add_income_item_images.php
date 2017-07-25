<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_income_item_images extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `income_item_images` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `income_item_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '請款 ID',
        `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '檔案名稱',
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `income_item_images`;"
    );
  }
}