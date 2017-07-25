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
  <table class='table-list w1100'>
    <thead>
      <tr>
        <th width='80' class='conter'>是否成功</th>
        <th width='120' class='left'>標題</th>
        <th width='200' class='left'>規則</th>
        <th class='left'>吐出訊息</th>
        <th width='90'>花費時間</th>
        <th width='150'>執行時間</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($objs as $obj) { ?>
        <tr>
          <td class='conter' style='color: <?php echo $obj->status == Backup::STATUS_2 ? 'rgba(34, 164, 136, 1.00)' : 'rgba(195, 55, 42, 1.00)';?>;'><?php echo Backup::$statusNames[$obj->status];?></td>
          <td class='left'><?php echo $obj->title;?></td>
          <td class='left'><?php echo $obj->rule;?></td>
          <td class='left'><?php echo $obj->mini_content (50);?></td>
          <td><?php echo round ($obj->end_at - $obj->start_at, 4);?> 秒</td>
          <td><?php echo $obj->created_at->format ('Y-m-d H:i:s');?></td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>

<div class='pagination'><?php echo $pagination;?></div>
