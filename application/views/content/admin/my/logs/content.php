<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>><?php echo $title;?>的操作列表</h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回<?php echo $self ? '個人' : '列表';?>頁</a>
</div>

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
      <a href='<?php echo base_url ('admin/my/logs/' . $obj->id);?>'>取消</a>
    </div>

  </form>
</div>

<div class='panel'>
  <table class='table-list'>
    <thead>
      <tr>
        <th width='60'>圖示</th>
        <th width='170' class='left'>標題</th>
        <th class='left'>內容</th>
        <th width='80'>操作類型</th>
        <th width='90'>時間</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($objs as $obj) { ?>
        <tr>
          <td><i class='<?php echo $obj->icon;?>'></i></td>
          <td class='left'><?php echo $obj->title;?></td>
          <td class='left'><?php echo $obj->content;?></td>
          <td><?php echo UserLog::$statusNames[$obj->status];?></td>
          <td><?php echo $obj->created_at->format ('Y-m-d H:i:s');?></td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>

<div class='pagination'><?php echo $pagination;?></div>
