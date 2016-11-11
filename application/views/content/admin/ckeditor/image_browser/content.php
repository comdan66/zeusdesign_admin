<div class='ckes'>
<?php
  if ($ckes) {
    foreach ($ckes as $cke) { ?>
      <div class='cke _ic' data-url='<?php echo $cke->name->url ('400h');?>'>
        <img src='<?php echo $cke->name->url ('400h');?>'>
        <time datetime='<?php echo $cke->created_at->format ('Y-m-d H:i:s');?>'><?php echo $cke->created_at->format ('Y-m-d H:i:s');?></span>
      </div>
<?php
    }
  } else { ?>
      <div>目前沒有任何圖片。</div>
  <?php
  } ?>
</div>