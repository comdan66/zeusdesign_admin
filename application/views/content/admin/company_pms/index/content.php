<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>><?php echo $title;?>列表</h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_b);?>'>回列表頁</a>

  <a class='icon-bin' href='<?php echo base_url ($uri_b, $parent->id);?>' data-method='delete'>刪除</a>
  <a class='icon-pencil2' href='<?php echo base_url ($uri_b, $parent->id, 'edit');?>'>編輯</a>

  <div class='show-type1 left' style='width:100%;border-top: 1px solid rgba(220, 220, 220, 1);padding-top: 20px;'>
    <div class='row min'>
      <b>名稱</b>
      <span><?php echo $parent->name;?></span>
    </div>
    <div class='row min'>
      <b>統編</b>
      <span><?php echo $parent->tax_no;?></span>
    </div>
    <div class='row min'>
      <b>電話</b>
      <span><?php echo $parent->phone;?></span>
    </div>
    <div class='row min'>
      <b>地址</b>
      <span><?php echo $parent->address;?></span>
    </div>
    <div class='row min'>
      <b>備註</b>
      <span><?php echo $parent->memo;?></span>
    </div>
  </div>
</div>


<div class='search'>
  <input type='checkbox' id='search_conditions' class='hckb'<?php echo $isSearch = array_filter (column_array ($searches, 'value'), function ($t) { return $t !== null; }) ? ' checked' : '';?> />
  
  <div class='left'>
    <label class='icon-search' for='search_conditions'></label>
    <span><b>搜尋條件：</b><?php echo $isSearch ? implode (',', array_filter (array_map (function ($search) { return $search['value'] !== null ? $search['text'] : null; }, $searches), function ($t) { return $t !== null; })) : '無';?>，共 <b><?php echo number_format ($total);?></b> 筆。</span>
  </div>

  <div class='right'>
    <a class='icon-r' href='<?php echo base_url ($uri_1, $parent->id,  $uri_2, 'add');?>'>新增聯絡人</a>
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
      <a href='<?php echo base_url ($uri_1, $parent->id,  $uri_2);?>'>取消</a>
    </div>

  </form>
</div>

<div class='panel'>
  <table class='table-list w1100'>
    <thead>
      <tr>
        <th width='60'>#<?php echo listSort (array ($uri_1, $parent->id,  $uri_2), 'id');?></th>
        <th width='140'>名稱<?php echo listSort (array ($uri_1, $parent->id,  $uri_2), 'name');?></th>
        <th width='90'>分機<?php echo listSort (array ($uri_1, $parent->id,  $uri_2), 'extension');?></th>
        <th width='125'>手機</th>
        <th >E-Mail</th>
        <th width='140'>備註</th>
        <th width='100'>編輯</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($objs as $obj) { ?>
        <tr>
          <td><?php echo $obj->id;?></td>
          <td><?php echo $obj->name;?></td>
          <td><?php echo $obj->extension;?></td>
          <td><?php echo implode ('', column_array ($obj->phones, 'content', function ($t) {
            return '<div class="row">' . $t . '</div>';
          }));?></td>
          <td><?php echo implode ('', column_array ($obj->emails, 'content', function ($t) {
            return '<div class="row">' . $t . '</div>';
          }));?></td>
          <td><?php echo $obj->memo;?></td>
          <td class='edit'>
            <a class='icon-eye' href="<?php echo base_url ($uri_1, $parent->id,  $uri_2, $obj->id, 'show');?>"></a>
            <a class='icon-pencil2' href="<?php echo base_url ($uri_1, $parent->id,  $uri_2, $obj->id, 'edit');?>"></a>
            <a class='icon-bin' href="<?php echo base_url ($uri_1, $parent->id,  $uri_2, $obj->id);?>" data-method='delete'></a>
          </td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>

<div class='pagination'><?php echo $pagination;?></div>

