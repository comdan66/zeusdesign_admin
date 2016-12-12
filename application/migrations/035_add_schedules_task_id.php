<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_schedules_task_id extends CI_Migration {
  public function up () {
    $this->db->query (
      "ALTER TABLE `schedules` ADD `task_id` int(11) unsigned DEFAULT 0 COMMENT '任務 ID' AFTER `schedule_tag_id`;"
    );
  }
  public function down () {
    $this->db->query (
      "ALTER TABLE `schedules` DROP COLUMN `task_id`;"
    );
  }
}