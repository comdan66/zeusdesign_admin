<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_ftps extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `ftps` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,

        `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '專案名稱',
        `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '網站網址',
        
        `ftp_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'FTP 主機網址',
        `ftp_account` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'FTP 帳號',
        `ftp_password` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'FTP 密碼',

        `admin_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '管理頁 網址',
        `admin_account` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '管理頁 帳號',
        `admin_password` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '管理頁 密碼',

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