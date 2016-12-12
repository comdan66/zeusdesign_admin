<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

if ($css_list) foreach ($css_list as $css) echo link_tag ($css) . (ENVIRONMENT !== 'production' ? "\n" : '');