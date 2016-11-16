<header>
  <div class='title'>
    <h1>聯絡人</h1>
    <p>聯絡人管理</p>
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
    <h2>聯絡人列表</h2>
    <a href='<?php echo base_url ($uri_1, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>


    <table class='table'>
      <thead>
        <tr>
          <th width='60'>#</th>
          <th width='100'>公司</th>
          <th width='110'>名稱</th>
          <th width='200'>電子郵件</th>
          <th width='110'>電話</th>
          <th width='60'>分機</th>
          <th width='110'>手機</th>
          <th width='130'>住址</th>
          <th >備註</th>
          <th width='85' class='right'>修改/刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td><?php echo $obj->id;?></td>
              <td><?php echo $obj->company ? $obj->company->name : '-';?></td>
              <td><?php echo $obj->name;?></td>
              <td><?php echo $obj->email ? $obj->email : '-';?></td>
              <td><?php echo $obj->telephone ? $obj->telephone : '-';?></td>
              <td><?php echo $obj->extension ? '#' . $obj->extension : '-';?></td>
              <td><?php echo $obj->cellphone ? $obj->cellphone : '-';?></td>
              <td><?php echo $obj->address ? $obj->address : '-';?></td>
              <td><?php echo $obj->memo ? $obj->memo : '-';?></td>
              <td class='right'>
                <a class='icon-e' href="<?php echo base_url ($uri_1, $obj->id, 'edit');?>"></a>
                /
                <a class='icon-t' href="<?php echo base_url ($uri_1, $obj->id);?>" data-method='delete'></a>
              </td>
            </tr>
    <?php }
        } else { ?>
          <tr>
            <td colspan='10' class='no_data'>沒有任何資料。</td>
          </tr>
  <?php } ?>
      </tbody>
    </table>

    <div class='pagination'>
      <?php echo $pagination;?>
    </div>

  </div>
</div>

