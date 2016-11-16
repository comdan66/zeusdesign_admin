<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Migration_Add_ftps extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `ftps` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,

        `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '專案名稱',
        `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '網站網址',
        
        `host` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'FTP 主機',
        `account` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'FTP 帳號',
        `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'FTP 密碼',

        `admin_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Server 管理頁面',

        `memo` text NOT NULL COMMENT '備註',
        
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `ftps`;"
    );
  }
}