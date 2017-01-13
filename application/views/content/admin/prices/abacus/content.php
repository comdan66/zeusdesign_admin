
<div class='panel'>
  <header>
    <h2>報價機算機</h2>
    <a class='icon-p' id='export' data-url='<?php echo base_url ('admin', 'prices', 'export');?>'></a>
  </header>
</div>

<div class='price'>
  <div class='my-row'>

    <div class='left'>
      <div class='types'>
  <?php if ($types) { ?>
          <span>分類</span>
          <select id='types' data-types='<?php echo json_encode ($types);?>'>
      <?php foreach ($types as $type) { ?>
              <option value='<?php echo $type['id'];?>'><?php echo $type['name'];?></option>
      <?php } ?>
          </select>
  <?php } else { ?>
          <span class='n'>沒有分類</span>
  <?php }?>
      </div>
      <div class='features<?php echo !$types ? ' no' : '';?>'>
  <?php foreach ($types as $i => $type) {
          foreach ($type['prices'] as $price) {?>
            <div class='feature type_<?php echo $type['id'];?><?php echo !$i ? ' show' : '';?>' data-id='<?php echo $price['id'];?>' data-money='<?php echo $price['money'];?>'>
              <span><?php echo $price['name'];?></span>
              <span><?php echo $price['desc'];?></span>
              <span><?php echo number_format ($price['money']);?>元</span>
              <a class='icon-x'></a>
            </div>
    <?php }
        } ?>
      </div>
    </div>

    <div class='space'></div>
    <div class='right '>
      <div class='features'></div>
      <div class='sum' id='sum'>總價<span>0</span>元</div>

    </div>
  </div>
</div>