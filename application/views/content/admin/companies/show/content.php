<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>檢視<?php echo $title;?></h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回列表頁</a>

  <a class='icon-bin' href='<?php echo base_url ($uri_1, $obj->id);?>' data-method='delete'>刪除</a>
  <a class='icon-pencil2' href='<?php echo base_url ($uri_1, $obj->id, 'edit');?>'>編輯</a>
</div>

<div class='panel'>
  <div class='show-type1'>
    <div class='row min'>
      <b><?php echo $title;?>名稱</b>
      <span><?php echo $obj->name;?></span>
    </div>
    <div class='row min'>
      <b><?php echo $title;?>統編</b>
      <span><?php echo $obj->tax_no;?></span>
    </div>
    <div class='row min'>
      <b><?php echo $title;?>電話</b>
      <span><?php echo $obj->phone;?></span>
    </div>
    <div class='row'>
      <b><?php echo $title;?>地址</b>
      <span><?php echo $obj->address;?></span>
    </div>
    <div class='row'>
      <b><?php echo $title;?>備註</b>
      <span><?php echo $obj->memo;?></span>
    </div>
  </div>
</div>

<h2><?php echo $title;?>的配合 PM、窗口</h2>

<div class='panel'>

  <div class='show-type1'>
    <div class='row min'>
      <b><?php echo $title;?>名稱</b>
      <span><?php echo $obj->name;?></span>
    </div>
    <div class='row min'>
      <b><?php echo $title;?>統編</b>
      <span><?php echo $obj->tax_no;?></span>
    </div>
    <div class='row min'>
      <b><?php echo $title;?>電話</b>
      <span><?php echo $obj->phone;?></span>
    </div>
    <div class='row'>
      <b><?php echo $title;?>地址</b>
      <span><?php echo $obj->address;?></span>
    </div>
    <div class='row'>
      <b><?php echo $title;?>備註</b>
      <span><?php echo $obj->memo;?></span>
    </div>
  </div>
</div>

