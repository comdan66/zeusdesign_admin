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
  <table class='table-list'>
    <thead>
      <tr>
        <th width='60'>#<?php echo listSort ($uri_1, 'id');?></th>
        <th width='60' class='center'>已讀</th>
        <th width='120'>稱呼<?php echo listSort ($uri_1, 'name');?></th>
        <th width='220'>E-Mail<?php echo listSort ($uri_1, 'email');?></th>
        <th >內容</th>
        <th width='90'>新增日期</th>
        <th width='45' class='center'>檢視</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($objs as $obj) { ?>
        <tr>
          <td><?php echo $obj->id;?></td>
          <td class='center'>
            <label class='switch ajax' data-forcntrole='contact' data-column='status' data-url='<?php echo base_url ($uri_1, 'status', $obj->id);?>'>
              <input type='checkbox'<?php echo $obj->status == Contact::STATUS_2 ? ' checked' : '';?> />
              <span></span>
            </label>
          </td>
          <td><?php echo $obj->name;?></td>
          <td><?php echo $obj->email;?></td>
          <td><?php echo $obj->mini_message (50);?></td>
          <td><?php echo $obj->created_at->format ('Y-m-d');?></td>
          <td class='center'>
            <a class='icon-eye' href="<?php echo base_url ($uri_1, $obj->id, 'show');?>"></a>
            <a class='icon-bin' href="<?php echo base_url ($uri_1, $obj->id);?>" data-method='delete'></a>
          </td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>

<div class='pagination'><?php echo $pagination;?></div>
