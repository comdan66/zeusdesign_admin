<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

if ($hidden_list) foreach ($hidden_list as $hidden) echo oa_hidden ($hidden) . (ENVIRONMENT !== 'production' ? "\n" : '');