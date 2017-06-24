<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>修改<?php echo $title;?></h1>
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
        <th width='150'>PM</th>
        <th >細項</th>
        <th width='100'>總金額</th>
        <th width='50'>檢視</th>
      </tr>
    </thead>
    <tbody>
<?php foreach (IncomeItem::find ('all', array ('include' => array ('user', 'pm', 'details'), 'conditions' => array ('income_id = ?', $obj->id))) as $item) {
        $money += $item->money ();?>
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
  <span class='unit'>合計總共 <b><?php echo number_format ($money);?></b> 元</span>

</div>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1, $obj->id);?>' method='post'>
    <input type='hidden' name='_method' value='put' />

    <div class='row'>
      <b class='need'>標題</b>
      <input type='text' name='title' value='<?php echo isset ($posts['title']) ? $posts['title'] : $obj->title;?>' placeholder='請輸入<?php echo $title;?>標題..' maxlength='200' pattern='.{1,200}' required title='輸入標題!' autofocus />
    </div>
    
    <div class='row'>
      <b class='need'>總金額</b>
      <input type='number' name='money' value='<?php echo isset ($posts['money']) ? $posts['money'] : $obj->money;?>' placeholder='請輸入總金額..' maxlength='200' pattern='.{1,200}' required title='輸入總金額標題!' <?php echo $obj->items ? 'readonly' : 'autofocus';?> />
    </div>

    <div class='row min'>
      <b class='need'>是否入帳</b>
      <label class='switch'>
        <input type='checkbox' name='status'<?php echo (isset ($posts['status']) ? $posts['status'] : $obj->status) == Income::STATUS_2 ? ' checked' : '';?> value='<?php echo Income::STATUS_2;?>' />
        <span></span>
      </label>
    </div>

    <div class='row'>
      <b data-title='若有勾選開發票，請填寫發票日期。'>發票日期</b>
      <input type='date' name='invoice_date' value='<?php echo isset ($posts['invoice_date']) ? $posts['invoice_date'] : ($obj->invoice_date ? $obj->invoice_date->format ('Y-m-d') : '');?>' placeholder='請輸入發票日期..' maxlength='200' title='輸入發票日期標題!' />
    </div>

    <div class='row'>
      <b>備註</b>
      <input type='text' name='memo' value='<?php echo isset ($posts['memo']) ? $posts['memo'] : $obj->memo;?>' placeholder='請輸入入帳單備註..' maxlength='200' title='輸入入帳單備註!' />
    </div>

    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
