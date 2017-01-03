<header>
  <div class='title'>
    <h1>體重</h1>
    <p>體重系統</p>
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
    <h2>記錄 列表</h2>
    <a href='<?php echo base_url ($uri_1, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>


    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='70' class='center'>封面</th>
          <th width='135'>體重</th>
          <th width='135'>體脂率</th>
          <th width='155'>運動卡路里</th>
          <th width='85'>日期</th>
          <th class='right'>備註</th>
          <th width='85' class='right'>修改/刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>
              <td class='center'>
          <?php if ((string)$obj->cover) { ?>
                  <figure class='_i' href='<?php echo $obj->cover->url ('500w');?>'>
                    <img src='<?php echo $obj->cover->url ('500w');?>' />
                    <figcaption data-description='<?php echo $obj->date_at->format ('Y-m-d');?> <?php echo $obj->weight;?>Kg (<?php echo $obj->rate;?>%) <?php echo $obj->calorie;?>Kcal'><?php echo $obj->date_at->format ('Y-m-d');?></figcaption>
                  </figure>
          <?php } ?>
              </td>
              
              <td><?php echo $obj->weight;?></td>
              <td><?php echo $obj->rate;?></td>
              <td><?php echo $obj->calorie;?></td>
              <td><?php echo $obj->date_at->format ('Y-m-d');?></td>
              <td class='right'><?php echo $obj->memo;?></td>

              <td class='right'>
          <?php if (!($obj->date_at->format ('Y-m-d') < date ('Y-m-d', strtotime (date ('Y-m-d') . ' ' . $range)))) { ?>
                  <a class='icon-e' href="<?php echo base_url ($uri_1, $obj->id, 'edit');?>"></a>
                  /
                  <a class='icon-t' href="<?php echo base_url ($uri_1, $obj->id);?>" data-method='delete'></a>
          <?php }?>
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

