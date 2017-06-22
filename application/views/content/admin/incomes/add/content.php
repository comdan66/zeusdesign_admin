<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>新增<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1);?>' method='post'>

    <div class='row'>
      <b class='need'>總金額</b>
      <input type='number' name='money' value='<?php echo isset ($posts['money']) ? $posts['money'] : 0;?>' placeholder='請輸入總金額..' maxlength='200' pattern='.{1,200}' required title='輸入總金額標題!' autofocus />
    </div>

    <div class='row min'>
      <b class='need'>是否入帳</b>
      <label class='switch'>
        <input type='checkbox' name='status'<?php echo (isset ($posts['status']) ? $posts['status'] : Income::STATUS_1) == Income::STATUS_2 ? ' checked' : '';?> value='<?php echo Income::STATUS_2;?>' />
        <span></span>
      </label>
    </div>

    <div class='row'>
      <b data-title='若有勾選開發票，請填寫發票日期。'>發票日期</b>
      <input type='date' name='invoice_date' value='<?php echo isset ($posts['invoice_date']) ? $posts['invoice_date'] : '';?>' placeholder='請輸入發票日期..' maxlength='200' title='輸入發票日期標題!' />
    </div>

    <div class='row'>
      <b>備註</b>
      <input type='text' name='memo' value='<?php echo isset ($posts['memo']) ? $posts['memo'] : '';?>' placeholder='請輸入入帳單備註..' maxlength='200' title='輸入入帳單備註!' />
    </div>

    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
