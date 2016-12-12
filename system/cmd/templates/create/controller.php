{<{<{ defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class <?php echo ucfirst ($name);?> extends <?php echo ucfirst ($action);?>_controller {
<?php
  if ($methods) {
    foreach ($methods as $method) { ?>

  public function <?php echo $method;?> () {
    $this->load_view ();
  }
<?php
    }
  } ?>
}
