<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>檢視<?php echo $title;?></h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回列表頁</a>

  <?php
  if (!$obj->hasIncome ()) { ?>

    <a class='icon-bin' href='<?php echo base_url ($uri_1, $obj->id);?>' data-method='delete'>刪除</a>
    <a class='icon-pencil2' href='<?php echo base_url ($uri_1, $obj->id, 'edit');?>'>編輯</a>
  <?php
  } else { ?>
    <a class='icon-bil' href='<?php echo base_url ('admin', 'incomes', $obj->income->id, 'show');?>'>入帳單</a>
  <?php
  }?>
</div>

<div class='panel'>
  <div class='show-type1'>

    <div class='row min'>
      <b>是否請款</b>
      <span><?php echo $obj->hasIncome () ? '已請款' : '未請款';?></span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>負責人</b>
      <span><?php echo $obj->user->name;?></span>
    </div>
    
    <div class='row'>
      <b><?php echo $title;?>公司</b>
      <span><?php echo $obj->pm->company->name;?></span>
    </div>
    
    <div class='row'>
      <b><?php echo $title;?>聯絡窗口 PM</b>
      <span><?php echo $obj->pm->name;?></span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>標題</b>
      <span><?php echo $obj->title;?></span>
    </div> 

    <div class='row'>
      <b>相關圖片</b>
      <div class='imgs<?php echo !$obj->images ? ' e' : '';?>'>
  <?php foreach ($obj->images as $image) { ?>
          <div class='img'><img src='<?php echo $image->name->url ();?>' /></div>
  <?php }?>
      </div>
    </div>
    
    <div class='row'>
      <b>專案結束日期</b>
      <span><?php echo $obj->close_date ? $obj->close_date->format ('Y-m-d') : '';?></span>
    </div> 

    <div class='row'>
      <b>總金額</b>
      <span><?php echo number_format ($obj->money ());?>元</span>
    </div>

    <div class='row'>
      <b>備註</b>
      <span><?php echo $obj->memo;?></span>
    </div>

  </div>
</div>

<div class='panel'>
  <h2>請款細項</h2>

  <table class='table-list w1200'>
    <thead>
      <tr>
        <th width='120'>製作者</th>
        <th >標題</th>
        <th width='100'>數量</th>
        <th width='100'>單價</th>
        <th width='100'>金額</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($obj->details as $detail) { ?>
        <tr>
          <td><?php echo $detail->user->name;?></td>
          <td><?php echo $detail->title;?></td>
          <td><?php echo number_format ($detail->quantity);?>筆</td>
          <td><?php echo number_format ($detail->sgl_money);?>元</td>
          <td><?php echo number_format ($detail->all_money);?>元</td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>

