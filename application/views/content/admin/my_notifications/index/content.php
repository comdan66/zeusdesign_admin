<header>
  <div class='title'>
    <h1>通知</h1>
    <p>通知管理</p>
  </div>

  <form class='select'>
    <button type='submit' class='icon-s'></button>

<?php 
    if ($columns) { ?>
<?php foreach ($columns as $column) {
        if (isset ($column['select']) && $column['select']) { ?>
          <select name='<?php echo $column['key'];?>'>
            <option value=''>請選擇 <?php echo $column['title'];?>..</option>
      <?php foreach ($column['select'] as $option) { ?>
              <option value='<?php echo $option['value'];?>'<?php echo $option['value'] === $column['value'] ? ' selected' : '';?>><?php echo $option['text'];?></option>
      <?php } ?>
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
    <h2>通知列表</h2>
  </header>

  <div class='content'>

    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='80' class='center'>已讀</th>
          <th>內容敘述</th>
          <th width='95' class='right'>檢視</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>
              <td class='center'>
                <label class='switch' data-for_role='natification' data-column='is_read' data-url='<?php echo base_url ('admin/my_notifications', 'is_read', $obj->id);?>'>
                  <input type='checkbox' name='is_read'<?php echo $obj->is_read == Notification::READ_YES ? ' checked' : '';?> />
                  <span></span>
                </label>
              </td>
              <td><?php echo $obj->description;?></td>
              <td class='right'><?php if ($obj->link) {?><a class='icon-y' href="<?php echo $obj->link;?>"></a><?php }?></td>
            </tr>
    <?php }
        } else { ?>
          <tr>
            <td colspan='4' class='no_data'>沒有任何資料。</td>
          </tr>
  <?php } ?>
      </tbody>
    </table>

    <div class='pagination'>
      <?php echo $pagination;?>
    </div>

  </div>
</div>

