<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>><?php echo $title;?>列表</h1>

<div class='search'>
  <input type='checkbox' id='search_conditions' class='hckb'<?php echo $isSearch = array_filter (column_array ($searches, 'value'), function ($t) { return $t !== null; }) ? ' checked' : '';?> />
  
  <div class='left'>
    <label class='icon-search' for='search_conditions'></label>
    <span><b>搜尋條件：</b><?php echo $isSearch ? implode (',', array_filter (array_map (function ($search) { return $search['value'] !== null ? $search['text'] : null; }, $searches), function ($t) { return $t !== null; })) : '無';?>，共 <b><?php echo number_format ($total);?></b> 筆。</span>
  </div>

  <div class='right'>
    <a class='icon-r' href='<?php echo base_url ($uri_1, 'add');?>'>新增<?php echo $title;?></a>
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

<?php
foreach ($objs as $obj) { ?>
  <div class='panel'>
    <header>
      <table class='table-list header'>
        <tbody>
            <tr>
              <td class='left mobile'><b><?php echo $obj->name;?></b></td>
              <td width='140' data-before='電話：'><?php echo $obj->phone;?></td>
              <td width='140' data-before='統編：'><?php echo $obj->tax_no;?></td>

              <td class='mobile' width='120'>
                <a class='icon-r' href="<?php echo base_url ('admin/company', $obj->id, 'pms', 'add');?>"></a>
                /
                <a class='icon-eye' href="<?php echo base_url ('admin/company', $obj->id, 'pms');?>"></a>
                /
                <a class='icon-pencil2' href="<?php echo base_url ($uri_1, $obj->id, 'edit');?>"></a>
                /
                <a class='icon-bin' href="<?php echo base_url ($uri_1, $obj->id);?>" data-method='delete'></a>
              </td>
            </tr>
        </tbody>
      </table>
    </header>


    <table class='table-list'>
      <thead>
        <tr>
          <th width='150' class='left'>PM 名稱</th>
          <th width='100'>分機</th>
          <th ><?php echo CompanyPmItem::$typeNames[CompanyPmItem::TYPE_1];?></th>
          <th width='100'><?php echo CompanyPmItem::$typeNames[CompanyPmItem::TYPE_2];?></th>
          <th width='120'>備註</th>
        </tr>
      </thead>
      <tbody>
  <?php foreach ($obj->pms as $pm) { ?>
          <tr>
            <td class='left'><?php echo $pm->name;?></td>
            <td><?php echo $pm->extension;?></td>
            <td><?php echo implode (', ', column_array ($pm->emails, 'content'));?></td>
            <td><?php echo implode (', ', column_array ($pm->phones, 'content'));?></td>
            <td><?php echo $pm->memo;?></td>
          </tr>
  <?php }?>
          
      </tbody>
    </table>
  </div>
<?php
} ?>


<div class='pagination'><?php echo $pagination;?></div>
