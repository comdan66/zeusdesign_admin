<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>><?php echo $title;?>列表</h1>

<?php 
  foreach ($objs as $obj) { ?>
    <div class='panel'>
      <h2><?php echo $obj['y'];?>年 <?php echo $title;?> 列表</h2>
      
      <table class='table-list'>
        <thead>
          <tr>
            <th width='100'>月份</th>
            <th width='90'>檢視細項</th>
            <th width='115'>含稅入帳</th>
            <th width='115'>宙思收入</th>
            <th width='115'>宙思支出</th>
            <th >宙思盈餘</th>
          </tr>
        </thead>
        <tbody>
    <?php $all = $zeus = $out = $sur = 0;
          foreach ($obj['months'] as $month) {
            $all += $month['all']; $zeus += $month['zeus']; $out += $month['out']; $sur += $month['sur']; ?>
            <tr>
              <td><?php echo $month['m1'] . '月 - ' . $month['m2'] . '月';?></td>
              <td class='edit'>
                <a title='入帳' target='_balnk' class='icon-ib' href="<?php echo base_url ('admin', 'incomes', '?year%5B%5D=' . $obj['y'] . '&date%5B%5D=' . $month['m1'] . '&date%5B%5D=' . $month['m2'] . '&status=' . Income::STATUS_2);?>"></a>
                <a title='出帳' target='_balnk' class='icon-ob' href="<?php echo base_url ('admin', 'outcomes', '?year%5B%5D=' . $obj['y'] . '&date%5B%5D=' . $month['m1'] . '&date%5B%5D=' . $month['m2'] . '&status=' . Outcome::STATUS_2);?>"></a>
              </td>
              <td><?php echo number_format ($month['all']);?>元</a></td>
              <td><?php echo number_format ($month['zeus']);?>元</td>
              <td><?php echo number_format ($month['out']);?>元</td>
              <td><?php echo number_format ($month['sur']);?>元</td>
            </tr>
    <?php } ?>
          <tr style='font-weight: bold;'>
            <td>總和</td>
            <td> </td>
            <td style='color: <?php echo $all < 0 ? 'rgba(234, 67, 53, 1.00)' : 'rgba(52, 168, 83, 1.00)';?>'><?php echo number_format ($all);?>元</td>
            <td style='color: <?php echo $zeus < 0 ? 'rgba(234, 67, 53, 1.00)' : 'rgba(52, 168, 83, 1.00)';?>'><?php echo number_format ($zeus);?>元</td>
            <td style='color: <?php echo $out < 0 ? 'rgba(234, 67, 53, 1.00)' : 'rgba(52, 168, 83, 1.00)';?>'><?php echo number_format ($out);?>元</td>
            <td style='color: <?php echo $sur < 0 ? 'rgba(234, 67, 53, 1.00)' : 'rgba(52, 168, 83, 1.00)';?>'><?php echo number_format ($sur);?>元</td>
          </tr>
        </tbody>
      </table>
    </div>

<?php 
  }
?>