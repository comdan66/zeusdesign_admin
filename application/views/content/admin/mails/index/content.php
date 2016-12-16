<header>
  <div class='title'>
    <h1>郵件</h1>
    <p>郵件管理</p>
  </div>

  <form class='select'>
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
    <h2>郵件列表</h2>
  </header>

  <div class='content'>


    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='150'>標題</th>
          <th >內容</th>
          <th width='70' class='right'>發送數</th>
          <th width='70' class='right'>點閱數</th>
          <th width='70' class='right'>點閱率</th>
          <th width='50' class='right'>細節</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>
              <td><?php echo $obj->title;?></td>
              <td><?php echo $obj->mini_content ();?></td>
              <td class='right'><?php echo $obj->count_send;?></td>
              <td class='right'><?php echo $obj->count_open;?></td>
              <td class='right'><?php echo round (100 * $obj->count_open / $obj->count_send);?>%</td>
              <td class='right'>
                <a class='icon-y' href="<?php echo base_url ($uri_1, $obj->id, 'show');?>"></a>
              </td>
            </tr>
    <?php }
        } else { ?>
          <tr>
            <td colspan='7' class='no_data'>沒有任何資料。</td>
          </tr>
  <?php } ?>
      </tbody>
    </table>

    <div class='pagination'>
      <?php echo $pagination;?>
    </div>

  </div>
</div>

