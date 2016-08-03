<header>
  <div class='title'>
    <h1>使用者</h1>
    <p>使用者權限管理</p>
  </div>

  <form class='select'>
    <button type='submit'>尋找</button>

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
    <h2>使用者 列表</h2>
  </header>

  <div class='content'>


    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='70' class='center'>照片</th>
          <th width='150'>名稱</th>
          <th>郵件</th>
          <th width='50' class='center'>設定</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($users) {
          foreach ($users as $user) { ?>
            <tr>
              <td class='center'><?php echo $user->id;?></td>
              
              <td class='center'>
                <figure class='_i' href='<?php echo $user->avatar (200, 200);?>'>
                  <img src='<?php echo $user->avatar (200, 200);?>' />
                  <figcaption data-description='<?php echo $user->name;?>'><?php echo $user->name;?></figcaption>
                </figure>
              </td>
              <td><?php echo $user->name;?></td>
              <td><?php echo $user->email;?></td>


              <td class='center'>
                <a class='icon-se' href="<?php echo base_url ($uri_1, $user->id, 'show');?>"></a>
              </td>

            </tr>
    <?php }
        } else { ?>
          <tr>
            <td colspan='8' class='no_data'>沒有任何資料。</td>
          </tr>
  <?php } ?>
      </tbody>
    </table>

    <div class='pagination'>
      <?php echo $pagination;?>
    </div>

  </div>
</div>

