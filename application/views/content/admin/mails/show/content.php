<div class='panel'>
  <header>
    <h2>信件標題</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>
  <div class='content'>
    <?php echo $obj->title;?>
  </div>
  <header>
    <h2>收件者</h2>
  </header>
  <div class='content'><?php echo htmlentities ($obj->to);?></div>

<?php 
  if ($obj->cc) {?>
    <header>
      <h2>副本</h2>
    </header>
    <div class='content'><?php echo htmlentities ($obj->cc);?></div>
<?php
  } ?>
  <header>
    <h2>信件內容</h2>
  </header>
  <div class='content'>
    <?php echo $obj->content;?>
  </div>
</div>