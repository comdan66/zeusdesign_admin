<header>
  <div class='title'>
    <h1>圖庫</h1>
    <p>圖庫 管理</p>
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
    <h2>圖庫 列表</h2>
  </header>

  <div class='content'>
    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='70'>圖片</th>
          <th width='300'>網頁網址</th>
          <th width='300'>圖片網址</th>
          <th >備註</th>
          <th width='95' class='right'>原始檔/刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>

              <td class='center'>
                <figure class='_i' href='<?php echo $obj->name->url ('500w');?>'>
                  <img src='<?php echo $obj->name->url ('500w');?>' />
                  <figcaption data-description=''></figcaption>
                </figure>
              </td>
              <td><?php echo mini_link ($obj->from_url, 40);?></td>
              <td><?php echo mini_link ($obj->image_url, 40);?></td>
              <td><?php echo $obj->memo;?></td>
              <td class='right'>
                <a class='icon-y' href='<?php echo $obj->name->url ();?>' target='_blank'></a>
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

