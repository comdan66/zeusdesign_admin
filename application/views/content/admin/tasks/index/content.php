<header>
  <div class='title'>
    <h1>專案</h1>
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
    <a href='<?php echo base_url ($uri_1, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>
    <table class='table'>
      <thead>
        <tr>
          <th width='90' class='center'>已經完成</th>
          <th width='100'>新增者</th>
          <th width='140'>標題</th>
          <th width='120'>優先權</th>
          <th >內容</th>
          <th width='150' class='right'>指派人員</th>
          <th width='100' class='right'>日期</th>
          <th width='85' class='right'>修改/刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
             
              <td class='center'>
                <label class='switch' data-column='finish' data-url='<?php echo base_url ($uri_1, 'finish', $obj->id);?>'>
                  <input type='checkbox' name='finish'<?php echo $obj->finish == Task::IS_FINISHED ? ' checked' : '';?> />
                  <span></span>
                </label>
              </td>

              <td><?php echo $obj->user->name;?></td>
              <td><?php echo $obj->title;?></td>
              <td>
                <div class='color' style='background-color: <?php echo isset (Task::$levelColors[$obj->level]) ? Task::$levelColors[$obj->level] : '#ffffff';?>;'></div>
                <?php echo isset (Task::$levelNames[$obj->level]) ? Task::$levelNames[$obj->level] : '無';?>
              </td>
              <td><?php echo $obj->mini_description ();?></td>
              <td class='right'><?php echo implode ('', array_map (function ($u) { return '<div class="munit" style="text-align:right;">' . $u->name . '</div>'; }, $obj->users));?></td>
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

