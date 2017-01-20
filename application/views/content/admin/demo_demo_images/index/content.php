<header>
  <div class='title'>
    <h1>提案系統</h1>
    <p>內容項目</p>
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
  <div class='edits'>
    <div>此提案名稱為：<b><?php echo $parent->name;?></b>，<span><?php echo Demo::$mobileNames[$parent->is_mobile];?></span> 的提案，目前狀態為：<span><?php echo Demo::$enableNames[$parent->is_enabled];?></span>，並且<span><?php echo $parent->password ? "有設定密碼，其密碼為：" . $parent->password : "沒有設定密碼";?></span><?php echo $parent->memo ? '，相關備註：' . $parent->memo : '。';?></div>
    <div>
      <a class='icon-t' href="<?php echo base_url ('admin', 'demos', $parent->id);?>" data-method='delete' data-alert='確定刪除？分類下的細項也會一併刪除喔！'></a>
      <a class='icon-e' href="<?php echo base_url ('admin', 'demos', $parent->id, 'edit');?>"></a>
      <a class='icon-new-tab' href="<?php echo $parent->demo_url ();?>" target='_blank'></a>
    </div>
  </div>
</div>

<div class='panel'>
  <header>
    <h2><?php echo $parent->name;?> 的內容</h2>
    <a href='<?php echo base_url ($uri_1, $parent->id, $uri_2, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>


    <table class='table'>
      <thead>
        <tr>
          <th width='80'>#</th>
          <th >封面</th>
          <th width='50' class='right'>排序</th>
          <th width='85' class='right'>修改/刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td><?php echo $obj->id;?></td>
              <td>
                <figure class='_i' href='<?php echo $obj->name->url ();?>'>
                  <img src='<?php echo $obj->name->url ();?>' />
                  <figcaption data-description=''></figcaption>
                </figure>
              </td>
              <td class='right sort_btns'>
                <a class='icon-tu' href='<?php echo base_url ($uri_1, $parent->id, $uri_2, $obj->id, 'sort', 'up');?>' data-method='post'></a>
                <a class='icon-td' href='<?php echo base_url ($uri_1, $parent->id, $uri_2, $obj->id, 'sort', 'down');?>' data-method='post'></a>
              </td>
              <td class='right'>
                <a class='icon-e' href="<?php echo base_url ($uri_1, $parent->id, $uri_2, $obj->id, 'edit');?>"></a>
                /
                <a class='icon-t' href="<?php echo base_url ($uri_1, $parent->id, $uri_2, $obj->id);?>" data-method='delete'></a>
              </td>
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

