<header>
  <div class='title'>
    <h1>盈餘</h1>
    <p>盈餘 管理</p>
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



<?php
    if ($objs) {
      foreach ($objs as $year => $range) {
        $in_monry = $in_z_monry = $ou_monry = 0; ?>
        <div class='panel'>
          <header>
            <h2><?php echo $year;?> 年列表</h2>
          </header>

          <div class='content'>
            <table class='table'>
              <thead>
                <tr>
                  <th class='center' width='120'>月份</th>
                  <th class='right' width='150'>專案入帳</th>
                  <th class='right' width='150'>宙思入帳</th>
                  <th class='right' width='150'>宙思出帳</th>
                  <th class='right'>宙思盈餘</th>
                </tr>
              </thead>
              <tbody>
          <?php if ($range) {
                  foreach ($range as $obj) { 
                    $in_monry += $obj['in_monry'];
                    $in_z_monry += $obj['in_z_monry'];
                    $ou_monry += $obj['ou_monry']; ?>
                    <tr>
                      <td class='center'><?php echo $obj['range'];?></td>
                      <td class='right'>NT$ <?php echo number_format ($obj['in_monry']);?>元</td>
                      <td class='right'>NT$ <?php echo number_format ($obj['in_z_monry']);?>元</td>
                      <td class='right'>NT$ <?php echo number_format ($obj['ou_monry']);?>元</td>
                      <td class='right'>NT$ <?php echo number_format ($obj['in_z_monry'] - $obj['ou_monry']);?>元</td>
                    </tr>
            <?php }
                } else { ?>
                  <tr>
                    <td colspan='13' class='no_data'>沒有任何資料。</td>
                  </tr>
          <?php } ?>
              </tbody>
            </table>

            <table class='table s'>
              <tbody>
                <tr>
                  <td class='center' width='120'><b>總合</b></td>
                  <td class='right' width='150'>NT$ <?php echo number_format ($in_monry);?>元</td>
                  <td class='right' width='150'>NT$ <?php echo number_format ($in_z_monry);?>元</td>
                  <td class='right' width='150'>NT$ <?php echo number_format ($ou_monry);?>元</td>
                  <td class='right'>NT$ <?php echo number_format ($in_z_monry - $ou_monry);?>元</td>
                </tr>
              </tbody>
            </table>

          </div>
        </div>
    <?php
      }
    } else { ?>
      沒有任何資料。
<?php
    }?>