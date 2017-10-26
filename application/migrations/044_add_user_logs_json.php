<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_user_logs_json extends CI_Migration {
  public function up () {
    $this->db->query (
      "ALTER TABLE `user_logs` ADD `json` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '檔案' AFTER `status`;"
    );
  }
  public function down () {
    $this->db->query (
      "ALTER TABLE `user_logs` DROP COLUMN `json`;"
    );
  }
}