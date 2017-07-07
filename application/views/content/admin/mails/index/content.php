<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>><?php echo $title;?>列表</h1>

<div class='search'>
  <input type='checkbox' id='search_conditions' class='hckb'<?php echo $isSearch = array_filter (column_array ($searches, 'value'), function ($t) { return $t !== null; }) ? ' checked' : '';?> />
  
  <div class='left'>
    <label class='icon-search' for='search_conditions'></label>
    <span><b>搜尋條件：</b><?php echo $isSearch ? implode (',', array_filter (array_map (function ($search) { return $search['value'] !== null ? $search['text'] : null; }, $searches), function ($t) { return $t !== null; })) : '無';?>，共 <b><?php echo number_format ($total);?></b> 筆。</span>
  </div>

  <div class='right'>
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
  <table class='table-list'>
    <thead>
      <tr>
        <th width='70'>狀態</th>
        <th >標題<?php echo listSort ($uri_1, 'title');?></th>
        <th width='180'>跳址</th>
        <th width='80'>開啟率</th>
        <th width='80'>點擊數<?php echo listSort ($uri_1, 'cnt_open');?></th>
        <th width='80'>發送數<?php echo listSort ($uri_1, 'cnt_send');?></th>
        <th width='50'>檢視</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($objs as $obj) { ?>
        <tr>
          
          <td style='color: <?php echo $obj->status == Mail::STATUS_1 ? 'rgba(234, 67, 53, 1.00)': 'rgba(52, 168, 83, 1.00)';?>;'><?php echo Mail::$statusNames[$obj->status];?></td>
          <td><?php echo $obj->title;?></td>
          <td><?php echo $obj->uri;?></td>
          <td<?php echo ($t = round (100 * ($obj->cnt_send ? $obj->cnt_open / $obj->cnt_send : 0))) >= 100 ? ' style="color: rgba(52, 168, 83, 1.00);"': ($t ? '' : ' style="color: rgba(234, 67, 53, 1.00);font-weight: bold;"');?>><?php echo $t;?>%</td>
          <td><?php echo $obj->cnt_open;?></td>
          <td><?php echo $obj->cnt_send;?></td>
          <td>
            <a class='icon-eye' href="<?php echo base_url ($uri_1, $obj->id, 'show');?>"></a>
          </td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>

<div class='pagination'><?php echo $pagination;?></div>
