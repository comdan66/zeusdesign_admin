<header>
  <div class='title'>
    <h1>FTP</h1>
    <p>FTP 管理</p>
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
    <h2>FTP 列表</h2>
    <a href='<?php echo base_url ($uri_1, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>
    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='100'>專案名稱</th>
          <th>網站網址</th>
          <th width='250'>FTP</th>
          <th width='250'>管理頁</th>
          <th width='90' class='center'>修改/刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>
             
              <td><?php echo $obj->name;?></td>
              <td><?php echo $obj->url;?></td>
              
              <td><div class='munit'><?php echo $obj->ftp_url;?></div>
                  <div class='munit'><?php echo $obj->ftp_account;?></div>
                  <div class='munit'><?php echo $obj->ftp_password;?></div></td>

              <td><div class='munit'><?php echo $obj->admin_url;?></div>
                  <div class='munit'><?php echo $obj->admin_account;?></div>
                  <div class='munit'><?php echo $obj->admin_password;?></div></td>

              <td class='center'>
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

