<header>
  <div class='title'>
    <h1>聯絡</h1>
    <p>新客戶聯絡管理</p>
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
    <h2>聯絡列表</h2>
    <a href='<?php echo base_url ($uri_1, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>

    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='80' class='center'>已讀</th>
          <th width='150'>名稱</th>
          <th width='200'>E-Mail</th>
          <th>內容</th>
          <th width='100'>IP</th>
          <th width='65' class='right'>刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>
              <td class='center'>
                <label class='switch' data-column='is_readed' data-url='<?php echo base_url ($uri_1, $obj->id);?>'>
                  <input type='checkbox' name='is_readed'<?php echo $obj->is_readed == Contact::READ_YES ? ' checked' : '';?> />
                  <span></span>
                </label>
              </td>
              <td><?php echo $obj->name;?></td>
              <td><?php echo $obj->email;?></td>
              <td><?php echo $obj->message;?></td>
              <td><?php echo $obj->ip;?></td>

              <td class='right'>
                <a class='icon-t' href="<?php echo base_url ($uri_1, $obj->id);?>" data-method='delete'></a>
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

