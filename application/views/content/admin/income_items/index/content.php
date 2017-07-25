<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>><?php echo $title;?>列表</h1>

<div class='search'>
  <input type='checkbox' id='search_conditions' class='hckb' checked />
  
  <div class='left'>
    <label class='icon-search' for='search_conditions'></label>
    <span><b>條件搜尋</b>。共 <b id='_tl'>0</b> 筆。</span>
  </div>

  <div class='right'>
    <a class='icon-r' href='<?php echo base_url ($uri_1, 'add');?>'>新增<?php echo $title;?></a>
  </div>

  <div class='conditions'>
    <input type='text' name='title' placeholder='依照標題搜尋..' value=''>

    <select name='status'>
      <option value=''>依照狀態搜尋</option>
      <option value='0'>未請款</option>
      <option value='1'>已請款</option>
    </select>
    
    <div class='checkboxs' title='依照負責人搜尋'>
<?php foreach (User::all () as $user) { ?>
        <label class='checkbox'>
          <input type='checkbox' name='user_ids' value='<?php echo $user->id;?>'<?php echo User::current ()->id == $user->id ? ' checked' : '';?>>
          <span></span>
          <?php echo $user->name;?>
        </label>
<?php }?>
    </div>

    <div class='dysltckb'>
      <select data-name='pms' data-ckbs='<?php echo json_encode (array_map (function ($t) { return array ('text' => $t->name, 'value' => $t->id, 'parent_id' => $t->company_id);}, CompanyPm::all ()));?>'>
        <option value=''>依照公司挑選窗口 PM</option>
  <?php foreach (Company::all () as $company) { ?>
          <option value='<?php echo $company->id;?>'><?php echo $company->name;?></option>
  <?php }?>
      </select>
      <div class='checkboxs' title='依照窗口 PM 搜尋'></div>
    </div>

    <div class='btns'>
      <button type='submit' data-url='<?php echo base_url ('admin', 'income_items', 'ajax');?>'>搜尋</button>
    </div>
  </div>
</div>

<form action='<?php echo base_url ('admin', 'incomes', 'add');?>' class='panel note' id='_no' method='post'>
  <span>已經勾選 <b>0</b>筆。</span>
  <button type='submit' class='icon-fa'>合併入帳單</button>
</form>

<div class='panel'>
  <table class='table-list w1200 dy'>
    <thead>
      <tr>
        <th width='45' class='center'>勾選</th>
        <th width='55' class='center'>狀態</th>
        <th width='70' class='center'>圖片</th>
        <th width='160' class='left'>標題</th>
        <th width='110' class='left'>負責人</th>
        <th width='140' class='left'>窗口 PM / 公司</th>
        <th >細項</th>
        <th width='70'>總金額</th>
        <th width='85'>結束日期</th>
        <th width='90'>編輯</th>
      </tr>
    </thead>
    <tbody id='_tb'></tbody>
  </table>

</div>
<div class='pagination' id='_pgn'></div>
