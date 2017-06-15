<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>新增<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1);?>' method='post'>

    <div class='row'>
      <b class='need'><?php echo $title;?>名稱</b>
      <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : '';?>' placeholder='請輸入<?php echo $title;?>名稱..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>名稱!' autofocus />
    </div>
    
    <div class='row'>
      <b><?php echo $title;?>電話</b>
      <input type='text' name='phone' value='<?php echo isset ($posts['phone']) ? $posts['phone'] : '';?>' placeholder='請輸入<?php echo $title;?>電話..' maxlength='200' title='輸入<?php echo $title;?>電話!' />
    </div>

    <div class='row'>
      <b><?php echo $title;?>統一編號</b>
      <input type='text' name='tax_no' value='<?php echo isset ($posts['tax_no']) ? $posts['tax_no'] : '';?>' placeholder='請輸入<?php echo $title;?>統一編號..' maxlength='200' title='輸入<?php echo $title;?>統一編號!' />
    </div>

    <div class='row'>
      <b><?php echo $title;?>地址</b>
      <input type='text' name='address' value='<?php echo isset ($posts['address']) ? $posts['address'] : '';?>' placeholder='請輸入<?php echo $title;?>地址..' maxlength='200' title='輸入<?php echo $title;?>地址!' />
    </div>

    <div class='row'>
      <b><?php echo $title;?>備註</b>
      <input type='text' name='memo' value='<?php echo isset ($posts['memo']) ? $posts['memo'] : '';?>' placeholder='請輸入<?php echo $title;?>備註..' maxlength='200' title='輸入<?php echo $title;?>備註!' />
    </div>


    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
