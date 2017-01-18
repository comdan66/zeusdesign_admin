<header>
  <div class='title'>
    <h1>入帳</h1>
    <p>管理系統</p>
  </div>

  <form class='select'>
    <button type='button' class='icon-p' id='export' href='<?php echo base_url ($uri_1, 'export');?>'></button>
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
    <h2>入帳 列表</h2>
    <a href='<?php echo base_url ($uri_1, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>


    <table class='table'>
      <thead>
        <tr>
          <th width='80' class='center'>已入帳</th>
          <th width='80' class='center'>已支付</th>
          <th width='85'>負責人</th>
          <th width='110'>專案名稱</th>
          <th width='75'>總金額</th>
          <th width='105'>類型</th>
          <th width='70'>宙思獲得</th>
          <th width='90'>小計</th>
          <th>備註</th>
          <th width='85' class='right'>日期</th>
          <th width='85' class='right'>修改/刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'>
                <label class='switch' data-column='is_finished' data-url='<?php echo base_url ($uri_1, 'is_finished', $obj->id);?>'>
                  <input type='checkbox' name='is_finished'<?php echo $obj->is_finished == Billin::IS_FINISHED ? ' checked' : '';?> />
                  <span></span>
                </label>
              </td>
              <td class='center'>
                <label class='switch blue' data-column='is_pay' data-url='<?php echo base_url ($uri_1, 'is_pay', $obj->id);?>'>
                  <input type='checkbox' name='is_pay'<?php echo $obj->is_pay == Billin::IS_PAY ? ' checked' : '';?> />
                  <span></span>
                </label>
              </td>
              
              <td><?php echo $obj->user->name;?></td>
              <td><?php echo $obj->name;?></td>
              <td><?php echo number_format ($obj->money);?></td>
              <td><?php echo $obj->rate_name . '(' . ($obj->rate) . '%)';?></td>
              <td><?php echo number_format ($obj->zeus_money);?></td>
              <td><?php echo number_format ($obj->money - $obj->zeus_money);?></td>
              <td><?php echo $obj->memo;?></td>
              <td class='right'><?php echo $obj->date_at->format ('Y-m-d');?></td>

              <td class='right'>
                <a class='icon-e' href="<?php echo base_url ($uri_1, $obj->id, 'edit');?>"></a>
                /
                <a class='icon-t' href="<?php echo base_url ($uri_1, $obj->id);?>" data-method='delete'></a>
              </td>

            </tr>
    <?php }
        } else { ?>
          <tr>
            <td colspan='12' class='no_data'>沒有任何資料。</td>
          </tr>
  <?php } ?>
      </tbody>
    </table>

    <div class='pagination'>
      <?php echo $pagination;?>
    </div>

  </div>
</div>

