<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_deploys extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `deploys` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'User ID(操作者)',
        `type` tinyint(4) unsigned NOT NULL DEFAULT 2 COMMENT '類型，1 build，2 upload(rebuild and upload to s3)',
        `is_success` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否成功，1 是，0 否',
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `deploys`;"
    );
  }
}