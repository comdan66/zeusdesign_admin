
<div class='panel'>
  <header>
    <h2>報價機算機</h2>
    <a class='icon-p'></a>
  </header>
</div>

<div class='price'>
  <div class='my-row'>

    <div class='left'>
      <div class='ti'>功能列表</div>
      <div class='features'>
  <?php foreach ($objs as $obj) {?>
          <div class='feature' data-id='<?php echo $obj->id;?>' data-money='<?php echo $obj->money;?>'>
            <span><?php echo $obj->name;?></span>
            <span><?php echo $obj->description;?></span>
            <span><?php echo number_format ($obj->money);?>元</span>
            <a class='icon-x'></a>
          </div>
  <?php } ?>
      </div>
    </div>

    <div class='space'></div>
    <div class='right '>
      <div class='features'></div>
      <div class='sum' id='sum'>總價<span>0</span>元</div>

    </div>
  </div>
</div>