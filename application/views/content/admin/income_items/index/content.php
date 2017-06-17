<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>><?php echo $title;?>列表</h1>

<div class='search'>
  <input type='checkbox' id='search_conditions' class='hckb'<?php echo $isSearch = array_filter (column_array ($searches, 'value'), function ($t) { return $t !== null; }) ? ' checked' : '';?> />
  
  <div class='left'>
    <label class='icon-search' for='search_conditions'></label>
    <span><b>搜尋條件：</b><?php echo $isSearch ? implode (',', array_filter (array_map (function ($search) { return $search['value'] !== null ? isset ($search['text']) ? $search['text'] : (isset ($search['text1']) ? $search['text1'] : (isset ($search['text2']) ? $search['text2'] : null)) : null; }, $searches), function ($t) { return $t !== null; })) : '無';?>，共 <b><?php echo number_format ($total);?></b> 筆。</span>
  </div>

  <div class='right'>
    <a class='icon-r' href='<?php echo base_url ($uri_1, 'add');?>'>新增<?php echo $title;?></a>
  </div>

  <form class='conditions'>
<?php
    foreach ($searches as $name => $search) {
      if ($search['el'] == 'input') { ?>
        <input type='text' name='<?php echo $name;?>' placeholder='依照<?php echo $search['text'];?>搜尋..' value='<?php echo $search['value'] === null ? '' : $search['value'];?>'>
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
      if ($search['el'] == 'dysltckb' && $search['items1'] && $search['items2']) { ?>
        <div class='dysltckb'>
          <select data-name='<?php echo $name;?>' data-ckbs='<?php echo json_encode ($search['items2']);?>'<?php echo $search['value'] !== null ? " data-val='" . (is_array ($search['value']) ? json_encode ($search['value']) : '') . "'" : '';?>>
            <option value=''>依照<?php echo $search['text1'];?>挑選<?php echo $search['text2'];?>搜尋</option>
      <?php foreach ($search['items1'] as $item) { ?>
              <option value='<?php echo $item['value'];?>'><?php echo $item['text'];?></option>
      <?php } ?>
          </select>
          <div class='checkboxs' title='依照<?php echo $search['text2'];?>搜尋'></div>
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
        <th width='90'>結束日期</th>
        <th width='180'>標題<?php echo listSort ($uri_1, 'title');?></th>
        <th width='120'>負責人</th>
        <th >細項</th>
        <th width='100'>總金額</th>
        <th width='70'>狀態</th>
        <th width='100'>編輯</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($objs as $obj) { ?>
        <tr>
          <td><?php echo $obj->close_date ? $obj->close_date->format ('Y-m-d') : '';?></td>
          <td><?php echo $obj->title;?></td>
          <td><?php echo $obj->user->name;?></td>
          <td><?php echo implode ('', array_map (function ($detail) {
            return '<div class="row">' . $detail->user->name . ' / ' . number_format ($detail->all_money) . '元</div>';
          }, $obj->details));?></td>
          <td><?php echo number_format ($obj->money ()) . '';?>元</td>
          <td style='color:<?php echo $obj->income ? 'rgba(52, 168, 83, 1.00);' : 'rgba(234, 67, 53, 1.00)';?>'><?php echo $obj->income ? '已請款' : '未請款';?></td>
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
