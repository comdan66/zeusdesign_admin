<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>確認<?php echo $title;?>，產生入帳單</h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>重新選擇</a>
</div>
<?php $all_money = 0; ?>

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
        <th width='70'>狀態</th>
        <th width='50'>檢視</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($objs as $obj) {
        $all_money += $obj->money (); ?>
        <tr>
          <td><?php echo $obj->id;?></td>
          <td><?php echo $obj->close_date ? $obj->close_date->format ('Y-m-d') : '';?></td>
          <td><?php echo $obj->title;?></td>
          <td><?php echo $obj->user->name;?></td>
          <td><div class='row'><?php echo $obj->pm->name;?></div><div class='row sub'><?php echo $obj->pm->company->name;?></div></td>
          <td><?php echo implode ('', array_map (function ($detail) {
            return '<div class="row">' . $detail->user->name . ' / ' . number_format ($detail->all_money) . '元</div>';
          }, $obj->details));?></td>
          <td><?php echo number_format ($obj->money ());?>元</td>
          <td style='color:<?php echo $obj->hasIncome () ? 'rgba(52, 168, 83, 1.00)' : 'rgba(234, 67, 53, 1.00)';?>;'><?php echo $obj->hasIncome () ? '已請款' : '未請款';?></td>
          <td><a class='icon-eye' href="<?php echo base_url ($uri_1, $obj->id, 'show');?>" target='_blank'></a></td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1);?>' method='post'>

<?php foreach ($objs as $obj) { ?>
        <input type='hidden' name='ids[]' value='<?php echo $obj->id;?>' />
<?php } ?>
    <div class='row min'>
      <b class='need'>全部筆數</b>
      <?php echo count ($objs);?> 筆
    </div>
    
    <div class='row'>
      <b class='need'>標題</b>
      <input type='text' name='title' value='<?php echo isset ($posts['title']) ? $posts['title'] : '';?>' placeholder='請輸入<?php echo $title;?>標題..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>標題!' autofocus />
    </div>
    
    <div class='row'>
      <b class='need'>總金額</b>
      <input type='number' name='money' value='<?php echo isset ($posts['money']) ? $posts['money'] : $all_money;?>' placeholder='請輸入總金額..' maxlength='200' pattern='.{1,200}' required title='輸入總金額標題!' readonly />
    </div>
    
<?php if (User::current ()->in_roles (array ('income_status'))) { ?>
        <div class='row min'>
          <b class='need'>是否入帳</b>
          <label class='switch'>
            <input type='checkbox' name='status'<?php echo (isset ($posts['status']) ? $posts['status'] : Income::STATUS_1) == Income::STATUS_2 ? ' checked' : '';?> value='<?php echo Income::STATUS_2;?>' />
            <span></span>
          </label>
        </div>
<?php } ?>

    <div class='row'>
      <b data-title='若有勾選開發票，請填寫發票日期。'>發票日期</b>
      <input type='date' name='invoice_date' value='<?php echo isset ($posts['invoice_date']) ? $posts['invoice_date'] : '';?>' placeholder='請輸入發票日期..' maxlength='200' title='輸入發票日期標題!' />
    </div>

    <div class='row'>
      <b>入帳單備註</b>
      <input type='text' name='memo' value='<?php echo isset ($posts['memo']) ? $posts['memo'] : '';?>' placeholder='請輸入入帳單備註..' maxlength='200' title='輸入入帳單備註!' />
    </div>

    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ('admin', 'income-items');?>'>回列表頁</a>
    </div>
  </form>
</div>