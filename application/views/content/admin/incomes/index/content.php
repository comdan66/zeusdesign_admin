<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>><?php echo $title;?>列表</h1>

<div class='search'>
  <input type='checkbox' id='search_conditions' class='hckb'<?php echo $isSearch = array_filter (column_array ($searches, 'value'), function ($t) { return $t !== null; }) ? ' checked' : '';?> />
  
  <div class='left'>
    <label class='icon-search' for='search_conditions'></label>
    <span><b>搜尋條件：</b><?php echo $isSearch ? implode (',', array_filter (array_map (function ($search) { return $search['value'] !== null ? $search['text'] : null; }, $searches), function ($t) { return $t !== null; })) : '無';?>，共 <b><?php echo number_format ($total);?></b> 筆。</span>
  </div>

  <div class='right'>
    <!-- <a class='icon-r' href='<?php echo base_url ($uri_1, 'add');?>'>新增</a> -->
  </div>

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
  <table class='table-list w1200'>
    <thead>
      <tr>
        <th width='60'>#<?php echo listSort ($uri_1, 'id');?></th>
        <th width='70'>是否入帳</th>
        <th width='90'>發票日期</th>
        <th >請款項目</th>
        <th >人員薪資</th>
        <th width='80'>放款進度</th>
        <th width='90'>金額<?php echo listSort ($uri_1, 'money');?></th>
        <th width='130'>備註</th>
        <th width='90'>編輯</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($objs as $obj) { ?>
        <tr>
          <td><?php echo $obj->id;?></td>
          <td class='center'>
            <label class='switch ajax' data-column='status' data-url='<?php echo base_url ($uri_1, 'status', $obj->id);?>'><input type='checkbox'<?php echo $obj->status == Income::STATUS_2 ? ' checked' : '';?> /><span></span></label>
          </td>
          <td><?php echo $obj->invoice_date ? $obj->invoice_date->format ('Y-m-d') : '';?></td>
          <td><?php echo $obj->items ? implode ('', array_map (function ($t) {
            return '<div class="row">' . $t->title . ' / ' . number_format ($t->money ()) . '元</div>';
          }, $obj->items)) : '';?></td>
          <td><?php echo $obj->zbs ? implode ('', array_map (function ($zb) {
            return '<div class="row' . ($zb->status == Zb::STATUS_2 ? ' finish' : '') . '">' . $zb->user->name . ' / ' . number_format ($zb->money) . '元</div>';
          }, $obj->zbs)) : '';?></td>
          <td style='color: <?php echo $obj->progress () < 100 ? 'rgba(234, 67, 53, 1.00)': 'rgba(52, 168, 83, 1.00)';?>;'><?php echo $obj->progress ()?>%</td>
          <td><?php echo number_format ($obj->money);?>元</td>
          <td><?php echo $obj->memo;?></td>
          <td>
            <a class='icon-eye' href="<?php echo base_url ($uri_1, $obj->id, 'show');?>"></a>
            /
            <a class='icon-pencil2' href="<?php echo base_url ($uri_1, $obj->id, 'edit');?>"></a>
            /
            <a class='icon-bin' href="<?php echo base_url ($uri_1, $obj->id);?>" data-method='delete'></a>
          </td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>

<div class='pagination'><?php echo $pagination;?></div>
