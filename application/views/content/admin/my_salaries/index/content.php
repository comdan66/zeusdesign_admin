<header>
  <div class='title'>
    <h1>宙思幣</h1>
    <p>宙思幣 管理</p>
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
    <h2>宙思幣 列表</h2>
    <div>累計總金額：<span>NT$ <?php echo number_format ($money);?></span> 元</div>
  </header>

  <div class='content'>
    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='120' class='center'>是否已給付</th>
          <th width='150'>專案名稱</th>
          <th width='100'>金額</th>
          <th >備註</th>
          <th width='100' class='right'>疑問</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>

              <td class='center'><?php echo Salary::$finishNames[$obj->is_finished];?></td>
              <td><?php echo $obj->name;?></td>
              <td><?php echo number_format ($obj->money);?></td>
              <td><?php echo $obj->memo;?></td>
              <td class='right'>
                <a class='icon-help_outline' href='mailto:teresa@zeusdesign.com.tw?subject=[宙思後台] 關於專案 “<?php echo $obj->name;?>” 的問題&body=Hi 管理員,%0D%0A%0D%0A 針對專案 <?php echo $obj->name;?> 我有一下問題..'></a>
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

