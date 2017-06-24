<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>><?php echo $title;?>列表</h1>

<div class='search'>
  <input type='checkbox' id='search_conditions' class='hckb'<?php echo $isSearch = array_filter (column_array ($searches, 'value'), function ($t) { return $t !== null; }) ? ' checked' : '';?> />
  
  <div class='left'>
    <label class='icon-search' for='search_conditions'></label>
    <span><b>搜尋條件：</b><?php echo $isSearch ? implode (',', array_filter (array_map (function ($search) { return $search['value'] !== null ? $search['text'] : null; }, $searches), function ($t) { return $t !== null; })) : '無';?>，共 <b><?php echo number_format ($total);?></b> 筆。</span>
  </div>

  <div class='right'></div>

  <form class='conditions'>
<?php
    foreach ($searches as $name => $search) {
      if ($search['el'] == 'input') { ?>
        <input type='<?php echo isset ($search['type']) ? $search['type'] : 'text';?>' name='<?php echo $name;?>' placeholder='依照<?php echo $search['text'];?>搜尋..' value='<?php echo $search['value'] === null ? '' : $search['value'];?>'>
<?php }
      if ($search['el'] == 'select' && $search['items']) { ?>
        <select name='<?php echo $name;?>'>
          <option value=''<?php echo $search['value'] === null ? '' : ' selected';?>>依照<?php echo $search['text'];?>搜尋</option>
    <?php foreach ($search['items'] as $item) { ?>
            <option value='<?php echo $item['value'];?>'<?php echo ($search['value'] !== null) && ($item['value'] == $search['value']) ? ' selected' : '';?>><?php echo $item['text'];?></option>
    <?php } ?>
        </select>
<?php }
      if ($search['el'] == 'checkbox' && $search['items']) { ?>
        <div class='checkboxs' title='依照<?php echo $search['text'];?>搜尋'>
<?php     foreach ($search['items'] as $item) { ?>
            <label class='checkbox'>
              <input type='checkbox' name='<?php echo $name;?>' value='<?php echo $item['value'];?>'<?php echo ($search['value'] !== null) && (!is_array ($search['value']) ? $item['value'] == $search['value'] : in_array ($item['value'], $search['value'])) ? ' checked' : '';?>>
              <span></span>
              <?php echo $item['text'];?>
            </label>
<?php     } ?>
        </div>
<?php }
    } ?>

    <div class='btns'>
      <button type='submit'>搜尋</button>
      <a href='<?php echo base_url ($uri_1);?>'>取消</a>
    </div>

  </form>
</div>

<div class='panel'>
  <span class='unit'>未領總金額：<b class='n'>NT$</b> <b><?php echo number_format ($status1->a);?></b> <b class='n'>元</b> / 已領總金額：<b class='n'>NT$</b> <b><?php echo number_format ($status2->a);?></b> <b class='n'>元</b>。</span>
</div>

<div class='panel'>
  <table class='table-list'>
    <thead>
      <tr>
        <th width='60'>#<?php echo listSort ($uri_1, 'id');?></th>
        <th width='150'>入帳標題</th>
        <th >細項</th>
        <th width='100'>未稅金額<?php echo listSort ($uri_1, 'money');?></th>
        <th width='100'>實領金額</th>
        <th width='80'>有無發票</th>
        <th width='60'>狀態</th>
        <th width='50'>檢視</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($objs as $obj) { ?>
        <tr>
          <td><?php echo $obj->id;?></td>
          <td><?php echo $obj->income ? $obj->income->title : '';?></td>
          <td><?php echo $obj->details ? implode ('', array_map (function ($detail) {
            return '<div class="row">' . $detail->item->title . ' / ' . ($detail->title ? $detail->title . ' / ' : '') . number_format ($detail->all_money) . '元</div>';
          }, IncomeItemDetail::find ('all', array ('include' => array ('item'), 'conditions' => array ('zb_id = ?', $obj->id))))) : '';?></td>
          <td><?php echo number_format ($obj->money);?>元</td>
          <td><?php echo number_format ($obj->pay ());?>元</td>
          <td><?php echo $obj->income->has_tax () ? '有開' : '沒開';?>發票</td>
          <td style='color: <?php echo $obj->status == Zb::STATUS_1 ? 'rgba(234, 67, 53, 1.00)': 'rgba(52, 168, 83, 1.00)';?>;'><?php echo Zb::$statusNames[$obj->status];?></td>
          <td>
            <a class='icon-eye' href="<?php echo base_url ($uri_1, $obj->id, 'show');?>"></a>
          </td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>

<div class='pagination'><?php echo $pagination;?></div>
