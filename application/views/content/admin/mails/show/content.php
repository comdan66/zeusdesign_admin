<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>檢視<?php echo $title;?></h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
</div>

<div class='panel'>
  <div class='show-type1 left'>

    <div class='row'>
      <b>狀態</b>
      <span style='color: <?php echo $obj->status == Mail::STATUS_1 ? 'rgba(234, 67, 53, 1.00)' : 'rgba(52, 168, 83, 1.00)';?>;'><?php echo Mail::$statusNames[$obj->status];?></span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>標題</b>
      <span><?php echo $obj->title;?></span>
    </div>

    <div class='row muti'>
      <b>收件者</b>
      <span class='list<?php echo !($to = explode (',', $obj->to)) ? ' e' : '';?>' data-cnt='1'>
  <?php foreach ($to as $t) { ?>
          <div><span><?php echo $t;?></span></div>
  <?php } ?>
      </span>
    </div>

    <div class='row muti'>
      <b>CC</b>
      <span class='list<?php echo !($cc = explode (',', $obj->cc)) ? ' e' : '';?>' data-cnt='1'>
  <?php foreach ($cc as $c) { ?>
          <div><span><?php echo $c;?></span></div>
  <?php } ?>
      </span>
    </div>

    <div class='row'>
      <b>跳址</b>
      <span><?php echo $obj->uri;?></span>
    </div>

    <div class='row'>
      <b>點擊數</b>
      <span><?php echo $obj->cnt_open;?></span>
    </div>

    <div class='row'>
      <b>發送數</b>
      <span><?php echo $obj->cnt_send;?></span>
    </div>

    <div class='row'>
      <b>點閱率</b>
      <span<?php echo ($t = round (100 * ($obj->cnt_send ? $obj->cnt_open / $obj->cnt_send : 0))) == 100 ? ' style="color: rgba(52, 168, 83, 1.00);"' : ($t ? '' : ' style="color: rgba(234, 67, 53, 1.00);font-weight: bold;"');?>><?php echo round (100 * ($obj->cnt_send ? $obj->cnt_open / $obj->cnt_send : 0));?>%</span>
    </div>

  </div>
</div>



<h2 class='_mailh'><?php echo $title;?>內容</h2>
<div class='_mail'>
  <?php echo $obj->content;?>
</div>
