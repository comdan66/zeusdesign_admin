<header>
  <div class='title'>
    <h1>帳務</h1>
    <p>出帳 上稿管理</p>
  </div>

  <form class='select'>
    <button type='button' class='icon-p' id='export' href='<?php echo base_url ('admin', 'billous', 'export');?>'></button>
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
    <h2>出帳 列表</h2>
    <a href='<?php echo base_url ($uri_1, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>
    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='100'>新增者</th>
          <th width='150'>項目名稱</th>
          <th width='100'>金額</th>
          <th width='100'>是否有發票</th>
          <th>備註</th>
          <th width='100'>日期</th>
          <th width='85' class='center'>修改/刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>
             
              <td><?php echo $obj->user->name;?></td>
              <td><?php echo $obj->name;?></td>
              <td><?php echo number_format ($obj->money);?></td>
              <td><?php echo Billou::$invoiceNames[$obj->is_invoice];?></td>
              <td><?php echo $obj->memo;?></td>
              <td><?php echo $obj->date_at->format ('Y-m-d');?></td>

              <td class='center'>
                <a class='icon-e' href="<?php echo base_url ($uri_1, $obj->id, 'edit');?>"></a>
                /
                <a class='icon-t' href="<?php echo base_url ($uri_1, $obj->id);?>" data-method='delete'></a>
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

