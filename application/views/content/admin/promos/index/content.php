<header>
  <div class='title'>
    <h1>促銷</h1>
    <p>Promo 上稿管理</p>
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
    <h2>Promo 列表</h2>
    <a href='<?php echo base_url ($uri_1, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>


    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='80' class='center'>上、下架</th>
          <th width='70' class='center'>封面</th>
          <th width='150'>標題</th>
          <th>內容</th>
          <th width='150'>鏈結</th>
          <th width='50' class='center'>排序</th>
          <th width='80' class='center'>修改/刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($promos) {
          foreach ($promos as $promo) { ?>
            <tr>
              <td class='center'><?php echo $promo->id;?></td>
              <td class='center'>
                <label class='switch' data-column='is_enabled' data-url='<?php echo base_url ($uri_1, $promo->id);?>'>
                  <input type='checkbox' name='is_enabled'<?php echo $promo->is_enabled == Promo::ENABLE_YES ? ' checked' : '';?> />
                  <span></span>
                </label>
              </td>
              <td class='center'>
                <figure class='_i' href='<?php echo $promo->cover->url ('500w');?>'>
                  <img src='<?php echo $promo->cover->url ('500w');?>' />
                  <figcaption data-description='<?php echo $promo->mini_content (0);?>'><?php echo $promo->title;?></figcaption>
                </figure>
              </td>
              <td><?php echo $promo->title;?></td>
              <td><?php echo $promo->mini_content (50);?></td>
              <td><?php echo mini_link ($promo->link, 25);?></td>


              <td class='center'>
                <a class='icon-tu' href='<?php echo base_url ('promos', $promo->id, 'sort', 'up');?>' data-method='post'></a>
                <a class='icon-td' href='<?php echo base_url ('promos', $promo->id, 'sort', 'down');?>' data-method='post'></a>
              </td>
              <td class='center'>
                <a class='icon-e' href="<?php echo base_url ($uri_1, $promo->id, 'edit');?>"></a>
                /
                <a class='icon-t' href="<?php echo base_url ($uri_1, $promo->id);?>" data-method='delete'></a>
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

