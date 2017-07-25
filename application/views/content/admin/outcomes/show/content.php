<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>檢視<?php echo $title;?></h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回列表頁</a>

  <a class='icon-bin' href='<?php echo base_url ($uri_1, $obj->id);?>' data-method='delete'>刪除</a>
  <a class='icon-pencil2' href='<?php echo base_url ($uri_1, $obj->id, 'edit');?>'>編輯</a>
</div>

<div class='panel'>
  <div class='show-type1'>

    <div class='row min'>
      <b>是否出帳</b>
      <span><?php echo Outcome::$statusNames[$obj->status];?></span>
    </div>

    <div class='row min'>
      <b><?php echo $title;?>新增者</b>
      <span><?php echo $obj->user->name;?></span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>標題</b>
      <span><?php echo $obj->title;?></span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>金額</b>
      <span><?php echo number_format ($obj->money);?>元</span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>日期</b>
      <span><?php echo $obj->date ? $obj->date->format ('Y-m-d') : '';?></span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>有無發票</b>
      <span><?php echo Outcome::$typeNames[$obj->type];?></span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>備註</b>
      <span><?php echo $obj->memo;?></span>
    </div>

  </div>
</div>

