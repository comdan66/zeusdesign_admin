<header>
  <div class='title'>
    <h1>請款</h1>
    <p>請款管理</p>
  </div>

  <form class='select'>
    <button type='button' id='export' href='<?php echo base_url ('admin', 'invoices', 'export');?>' class='icon-p'></button>
    <button type='submit' class='icon-s'></button>

<?php 
    if ($columns) { ?>
<?php foreach ($columns as $column) {
        if (isset ($column['select']) && $column['select']) { ?>
          <select name='<?php echo $column['key'];?>'>
            <option value=''>請選擇 <?php echo $column['title'];?>..</option>
      <?php $options = $column['select']; $groups = array ('' => array ()); foreach ($options as $option) if (!isset ($option['group'])) array_push ($groups[''], $option); else if (isset ($groups[$option['group']])) array_push ($groups[$option['group']], $option); else $groups[$option['group']] = array ($option);
            $optgroup = array_filter (array_keys ($groups)) ? true : false;

            foreach (array_reverse ($groups) as $label => $group) {
              if ($optgroup) { ?>
                <optgroup label='<?php echo $label === '' ? '其他' : $label;?>'>
        <?php } 
                foreach ($group as $option) { ?>
                  <option value='<?php echo $option['value'];?>'<?php echo (is_numeric ($column['value']) && ($column['value'] == $option['value'])) || ($option['value'] === $column['value']) ? ' selected' : '';?>><?php echo $option['text'];?></option>
          <?php }
              if ($optgroup) { ?>
                </optgroup>
        <?php }
            } ?>
          </select>
  <?php } else { ?>
          <label>
            <input type='text' name='<?php echo $column['key'];?>' value='<?php echo $column['value'];?>' placeholder='<?php echo $column['title'];?>搜尋..' />
            <i class='icon-s'></i>
          </label>
<?php   }
      }?>
<?php 
    } ?>

  </form>
</header>


<div class='panel'>
  <header>
    <h2>請款列表</h2>
    <a href='<?php echo base_url ($uri_1, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>


    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='90' class='center'>是否請款</th>
          <th width='120'>負責人</th>
          <th width='120'>聯絡人</th>
          <th width='150'>聯絡電話</th>
          <th>專案名稱</th>
          <th width='100'>總金額</th>
          <th width='80' class='right'>修改/刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>
              <td class='center'>
                <label class='switch' data-column='is_finished' data-url='<?php echo base_url ($uri_1, $obj->id);?>'>
                  <input type='checkbox' name='is_finished'<?php echo $obj->is_finished == Invoice::IS_FINISHED ? ' checked' : '';?> />
                  <span></span>
                </label>
              </td>
              <td><?php echo $obj->user->name;?></td>
              <td><?php echo $obj->customer ? $obj->customer->name . ($obj->customer->company ? '(' . $obj->customer->company->name . ')' : '') : '';?></td>
              <td><?php echo $obj->customer && $obj->customer->telephone ? $obj->customer->telephone . ' #' . trim ($obj->customer->extension, '#') : '';?></td>
              <td><?php echo $obj->name;?></td>
              <td><?php echo number_format ($obj->all_money);?></td>
              <td class='right'>
                <a class='icon-e' href="<?php echo base_url ($uri_1, $obj->id, 'edit');?>"></a>
                /
                <a class='icon-t' href="<?php echo base_url ($uri_1, $obj->id);?>" data-method='delete'></a>
              </td>
            </tr>
    <?php }
        } else { ?>
          <tr>
            <td colspan='6' class='no_data'>沒有任何資料。</td>
          </tr>
  <?php } ?>
      </tbody>
    </table>

    <div class='pagination'>
      <?php echo $pagination;?>
    </div>

  </div>
</div>

