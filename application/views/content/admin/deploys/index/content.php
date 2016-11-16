<header>
  <div class='title'>
    <h1>部署</h1>
    <p>編譯、上傳管理</p>
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
    <h2>部署紀錄列表</h2>
    <a href='<?php echo base_url ($uri_1, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>
    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='150'>執行者</th>
          <th width='150'>類型</th>
          <th >狀態</th>
          <th width='70' class='right'>檢視</th>
          <th width='140' class='right'>執行時間</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>
              <td><?php echo $obj->user->name;?></td>
              <td><?php echo Deploy::$typeNames[$obj->type];?></td>
              <td<?php echo $obj->is_success == Deploy::SUCCESS_NO ? ' class="red"' : ' class="green"';?>><?php echo Deploy::$successNames[$obj->is_success];?></td>
              <td class='right'>
                <a class='icon-y' href='<?php echo Cfg::setting ('deploy', 'view', ENVIRONMENT);?>' target='_blank'></a>
              </td>
              <td class='right'><?php echo $obj->created_at->format ('Y-m-d H:i:s');?></td>
            </tr>
    <?php }
        } else { ?>
          <tr>
            <td colspan='5' class='no_data'>沒有任何資料。</td>
          </tr>
  <?php } ?>
      </tbody>
    </table>

    <div class='pagination'>
      <?php echo $pagination;?>
    </div>

  </div>
</div>
