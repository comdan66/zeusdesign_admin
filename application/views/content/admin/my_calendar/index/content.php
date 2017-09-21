
<div class='search'>

  <div class='left'>
    <h2 class='icon-calendar2'> 我的行事曆</h2>
  </div>
  <div class='right'>
    <a class='icon-price-tags' href='<?php echo base_url ('admin', 'my-schedule-tags');?>'> 分類管理</a>
  </div>
</div>

<input type='checkbox' class='hckb' id='fix_pnl_ckb' />

<div class='panel'>
  
  <div class='calendar' data-url='<?php echo base_url ('admin', 'my_calendar', 'ajax');?>'>
    <div class='title'>
      <div class='now'></div>
      <div class='arr'><a></a><a></a><a></a></div>
    </div>
    <div class='months'></div>
  </div>

</div>

<div id='fix-panel'></div>
<label></label>