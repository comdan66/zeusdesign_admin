<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>修改<?php echo $title;?></h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回列表頁</a>


  <a class='icon-bin' href='<?php echo base_url ($uri_1, $obj->id);?>' data-method='delete'>刪除</a>
  <a class='icon-pencil2' href='<?php echo base_url ($uri_1, $obj->id, 'edit');?>'>編輯</a>
</div>

<?php $money = 0; ?>
<div class='panel'>
  <h2>請款項目</h2>

  <table class='table-list w1200'>
    <thead>
      <tr>
        <th width='50'>ID</th>
        <th width='90'>結束日期</th>
        <th width='180'>標題</th>
        <th width='120'>負責人</th>
        <th width='130'>PM</th>
        <th >細項</th>
        <th width='100'>總金額</th>
        <th width='50'>檢視</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($obj->items as $item) {
        $money += $item->money (); ?>
        <tr>
          <td><?php echo $item->id;?></td>
          <td><?php echo $item->close_date ? $item->close_date->format ('Y-m-d') : '';?></td>
          <td><?php echo $item->title;?></td>
          <td><?php echo $item->user->name;?></td>
          <td><div class='row'><?php echo $item->pm->name;?></div><div class='row sub'><?php echo $item->pm->company->name;?></div></td>
          <td><?php echo implode ('', array_map (function ($detail) { return '<div class="row">' . $detail->user->name . ' / ' . number_format ($detail->all_money) . '元</div>'; }, $item->details));?></td>
          <td><?php echo number_format ($item->money ());?>元</td>
          <td><a class='icon-eye' href="<?php echo base_url ('admin', 'income-items', $item->id, 'show');?>" target='_blank'></a></td>
        </tr>
<?php } ?>
    </tbody>
  </table>
    <span class='right'>合計總共 <b><?php echo number_format ($money);?></b> 元</span>

</div>

<div class='panel'>
  <div class='show-type1'>

    <div class='row min'>
      <b>是否入帳</b>
      <span><?php echo Income::$statusNames[$obj->status];?></span>
    </div>
    
    <div class='row'>
      <b>是否開發票</b>
      <span><?php echo $obj->has_tax () ? '有開發票' : '未開發票';?></span>
    </div>
    
    <div class='row'>
      <b>發票日期</b>
      <span><?php echo $obj->invoice_date ? $obj->invoice_date->format ('Y-m-d') : '';?></span>
    </div>

    <div class='row'>
      <b>總金額(未稅)</b>
      <span><?php echo number_format ($obj->money);?>元</span>
    </div>

    <div class='row'>
      <b data-title='因為「<?php echo $obj->has_tax () ? '有開' : '未開'; ?>」發票，所以是「未稅金額」x <?php echo $obj->has_tax () ? '1.05' : '1';?>'>總金額(含稅)</b>
      <span><?php echo number_format ($obj->tax_money ());?>元</span>
    </div>

    <div class='row'>
      <b data-title='因為「<?php echo $obj->has_tax () ? '有開' : '未開'; ?>」發票，所以是「含稅金額」x <?php echo $obj->has_tax () ? '20%' : '10%';?>'>宙思收入</b>
      <span><?php echo number_format ($obj->zeus_money ());?>元</span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>備註</b>
      <span><?php echo $obj->memo;?></span>
    </div> 

  </div>
</div>


<?php
foreach ($users as $user) {
  $money = 0;?>
  <div class='panel'>
    <h2>給付「<?php echo $user['user']->name;?>」</h2>
    <table class='table-list'>
      <thead>
        <tr>
          <th width='60'>#</th>
          <th >專案名稱</th>
          <th width='100'>數量</th>
          <th width='110'>單價</th>
          <th width='120'>金額</th>
        </tr>
      </thead>
      <tbody>
  <?php foreach ($user['details'] as $detail) {
          $money += $detail->all_money;?>
          <tr>
            <td></td>
            <td><?php echo $detail->item->title . ($detail->title ? ' - ' . $detail->title : '');?></td>
            <td><?php echo number_format ($detail->quantity);?>筆</td>
            <td><?php echo number_format ($detail->sgl_money);?>元</td>
            <td><?php echo number_format ($detail->all_money);?>元</td>
          </tr>
  <?php }?>
      </tbody>
    </table>
    <span class='right'>「未稅金額」：<b><?php echo number_format ($money);?></b> 元，「含稅金額」：<b><?php echo number_format ($a = round ($money * $obj->tax_rate ()));?></b> 元，「宙思收入」：<b><?php echo number_format ($b = round ($a * $obj->zeus_rate ()));?></b> 元。</span>
    <span class='right'>「放款金額」為 <b><?php echo number_format ($a - $b);?></b> 元。</span>

  </div>
  <?php
} ?>
