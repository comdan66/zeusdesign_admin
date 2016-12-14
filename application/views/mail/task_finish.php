  <div style='display: inline-block; margin: 0 auto; width: 600px; padding: 0; -moz-border-radius: 2px; -webkit-border-radius: 2px; border-radius: 2px; background-color: white;'>
    <div style='display: block; color: #2a3f54; padding: 10px; border-bottom: 1px solid #e5e5e5;'>
      <img src='https://cdn.zeusdesign.com.tw/mail/icons/logo_v1.png' style='display: inline-block; height: 40px; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px;'>
    </div>
    <div style='display: block; padding: 20px; color: #2e323b; border-bottom: 1px solid #e5e5e5; padding-bottom: 50px;'>
      <p style='color: #222222; margin-top: 5px; line-height: 23px;'><b><?php echo $user;?></b> 已經調整了任務狀態，以下是任務概述，調整的詳細內容請您上 <a href='<?php echo $url;?>' style='display: inline-block; color: rgba(42, 90, 149, 0.7); font-weight: normal; text-decoration: none; padding: 0 2px; padding-bottom: 0; -moz-transition: all 0.3s; -o-transition: all 0.3s; -webkit-transition: all 0.3s; transition: all 0.3s;'>宙思後台<a> 查看吧！</p>

<?php if (isset ($detail) && is_array ($detail) && $detail) { ?>
        <div style='border: 1px solid #bcbcbc; padding: 5px 15px; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; background-color: #f5f7fa;'>
    <?php foreach ($detail as $i => $value) {
            if (!$i) { ?>
              <p style='color: #203041; margin: 5px 0; padding: 0 5px; line-height: 25px;'><b><?php echo $value['title'];?></b><span><?php echo $value['value'];?></span></p>
      <?php } else { ?>
              <p style='color: #203041; margin: 5px 0; margin-top: 10px; padding: 0 5px; padding-top: 10px; border-top: 1px dashed #e5e5e5; line-height: 25px;'><b><?php echo $value['title'];?></b><span><?php echo $value['value'];?></span></p>
      <?php }
          }?>
        </div>
<?php } ?>

      <a href='<?php echo $url;?>' style='border: 1px solid rgba(255, 0, 0, 0.3); margin-top: 25px; padding: 10px; background-color: #ce5341; color: white; border: 1px solid #c33a2f; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; display: inline-block; text-decoration: inherit;'>到 宙思後台 上查看</a>
    </div>
    <div style='text-align: right; font-size: 13px; display: block; padding-right: 15px; padding-top: 15px; color: #6d7780;padding-bottom: 15px;'>
      Copyright by <a href='https://www.zeusdesign.com.tw/' style='display: inline-block; color: rgba(42, 90, 149, 0.7); font-weight: normal; text-decoration: none; padding: 0 2px; padding-bottom: 0; -moz-transition: all 0.3s; -o-transition: all 0.3s; -webkit-transition: all 0.3s; transition: all 0.3s;'>ZEUS</a> © 2016
    </div>
  </div>