<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_company_pms extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `company_pms` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `company_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '公司 ID',

        `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '名稱',
        `extension` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '電話分機',
        `experience` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '個性、合作心得',
        `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '備註',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `company_pms`;"
    );
  }
}