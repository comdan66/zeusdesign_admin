<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_income_item_detail_tag_id extends CI_Migration {
  public function up () {
    $this->db->query (
      "ALTER TABLE `income_item_details` ADD `income_item_detail_tag_id` int(11) unsigned DEFAULT '0' COMMENT 'Tag ID' AFTER `title`;"
    );
  }
  public function down () {
    $this->db->query (
      "ALTER TABLE `income_item_details` DROP COLUMN `income_item_detail_tag_id`;"
    );
  }
}