<header>
  <div class='title'>
    <h1>任務</h1>
    <p>任務 管理</p>
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
    <h2>任務 列表</h2>
  </header>

  <div class='content'>
    <table class='table'>
      <thead>
        <tr>
          <th width='60' class='center'>#</th>
          <th width='90' class='center'>已完成</th>
          <th width='120'>新增者</th>
          <th width='150'>任務名稱</th>
          <th width='120'>優先權</th>
          <th >任務敘述</th>
          <th width='60' class='right'>留言數</th>
          <th width='50' class='right'>細節</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>
              
              <td class='center'>
          <?php if ($obj->user_id == User::current ()->id) { ?>
                  <label class='switch' data-column='finish' data-url='<?php echo base_url ('admin/my_tasks', 'finish', $obj->id);?>'>
                    <input type='checkbox' name='finish'<?php echo $obj->finish == Task::IS_FINISHED ? ' checked' : '';?> />
                    <span></span>
                  </label>
          <?php } else {
                  echo '<font color="' . ($obj->finish == Task::NO_FINISHED ? 'red' : '') . '">' . Task::$finishNames[$obj->finish] . '</font>';
                } ?>
              </td>

              <td><?php echo $obj->user->name;?></td>
              <td><?php echo $obj->title;?></td>
              <td>
                <div class='color' style='background-color: <?php echo isset (Task::$levelColors[$obj->level]) ? Task::$levelColors[$obj->level] : '#ffffff';?>;'></div>
                <?php echo isset (Task::$levelNames[$obj->level]) ? Task::$levelNames[$obj->level] : '無';?>
              </td>
              <td><?php echo $obj->mini_description ();?></td>
              
              <td class='right'><?php echo count ($obj->commits);?></td>
              <td class='right'>
                <a class='icon-y' href="<?php echo base_url ($uri_1, $obj->id, 'show');?>"></a>
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

