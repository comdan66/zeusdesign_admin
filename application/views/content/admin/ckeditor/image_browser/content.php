<div id='tit'>
  <?php 
  foreach ($types as $key => $value) { ?>
    <a<?php echo $key == $type ? ' class="active"' : '';?> href='<?php echo base_url ($uri_1 . '/' . $key . '?' . $gets);?>'><?php echo $value;?></a>
  <?php 
  } ?>
</div>

<div id='imgs'<?php echo !$objs ? ' class="empty"' : '';?>>
  <?php
  foreach ($objs as $obj) {
    if ($obj instanceof CkeditorImage) { ?>
      <div class='img' data-url='<?php echo $obj->name->url ('400h');?>'>
        <img src='<?php echo $obj->name->url ('400h');?>'>
        <time datetime='<?php echo $obj->created_at->format ('Y-m-d H:i:s');?>'><?php echo $obj->created_at->format ('Y-m-d H:i:s');?></span>
      </div>
  <?php
    } elseif ($obj instanceof ImageBase) { ?>
      <div class='img' data-url='<?php echo $obj->name->url ('500w');?>'>
        <img src='<?php echo $obj->name->url ('500w');?>'>
        <time datetime='<?php echo $obj->created_at->format ('Y-m-d H:i:s');?>'><?php echo $obj->created_at->format ('Y-m-d H:i:s');?></span>
      </div>
  <?php
    }
  } ?>
</div>

<div class='pagination'><?php echo $pagination;?></div>
