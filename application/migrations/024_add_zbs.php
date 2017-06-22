<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_zbs extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `zbs` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'User ID(作者)',
        `income_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '入帳 ID',
        
        `money` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '金額',
        `percentage` float unsigned NOT NULL DEFAULT '0' COMMENT '百分比',
        
        `status` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '是否給付，1 未給付，2 已給付',
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `zbs`;"
    );
  }
}