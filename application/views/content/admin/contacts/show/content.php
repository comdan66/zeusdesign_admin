<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>檢視<?php echo $title;?></h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
</div>

<div class='panel'>
  <div class='show-type1'>

    <div class='row min'>
      <b>是否已讀</b>
      <span><?php echo Contact::$statusNames[$obj->status];?></span>
    </div>


    <div class='row'>
      <b><?php echo $title;?>稱呼</b>
      <span><?php echo $obj->name;?></span>
    </div>
    
    <div class='row'>
      <b><?php echo $title;?>E-Mail</b>
      <span><?php echo $obj->email;?></span>
    </div>
    
    <div class='row'>
      <b><?php echo $title;?>內容</b>
      <span><?php echo $obj->message;?></span>
    </div>

  </div>
</div>

