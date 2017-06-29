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
  <table class='table-list w1200'>
    <thead>
      <tr>
        <th width='70'>完成</th>
        <th width='100'>擁有者</th>
        <th width='150'>標題<?php echo listSort ($uri_1, 'title');?></th>
        <th width='110'>優先權<?php echo listSort ($uri_1, 'level');?></th>
        <th >內容</th>

        <th width='80'>留言數</th>
        <th width='50'>檢視</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($objs as $obj) { ?>
        <tr>
          <td>
      <?php if ($obj->user_id == User::current ()->id && User::current ()->in_roles (array ('task'))) {?>
              <label class='switch ajax' data-forcntrole='task' data-column='status' data-url='<?php echo base_url ('admin', 'tasks', 'status', $obj->id);?>'>
                <input type='checkbox'<?php echo $obj->status == Task::STATUS_2 ? ' checked' : '';?> />
                <span></span>
              </label>
      <?php } else {
              echo Task::$statusNames[$obj->status];
            }?>
          </td>
          <td><?php echo $obj->user->name;?></td>
          <td><?php echo $obj->title;?></td>
          <td><div class='color' style='background-color: <?php echo isset (Task::$levelColors[$obj->level]) ? Task::$levelColors[$obj->level] : '#ffffff';?>;'></div><?php echo Task::$levelNames[$obj->level];?></td>
          <td><?php echo $obj->mini_content (50);?></td>
          <td><?php echo number_format (count ($obj->commits));?> 則</td>

          <td>
            <a class='icon-eye' href="<?php echo base_url ($uri_1, $obj->id, 'show');?>"></a>
          </td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>

<div class='pagination'><?php echo $pagination;?></div>
