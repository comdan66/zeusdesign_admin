<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>新增<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1);?>' method='post'>

    <div class='row'>
      <b class='need'><?php echo $title;?>專案名稱</b>
      <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : '';?>' placeholder='請輸入<?php echo $title;?>專案名稱..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>專案名稱!' autofocus />
    </div>
    
    <div class='row'>
      <b ><?php echo $title;?>網站網址</b>
      <input type='text' name='link' value='<?php echo isset ($posts['link']) ? $posts['link'] : '';?>' placeholder='請輸入<?php echo $title;?>網站網址..' maxlength='200' title='輸入<?php echo $title;?>網站網址!' />
    </div>
    
    <div class='row'>
      <b><?php echo $title;?>備註</b>
      <textarea class='cke' name='content' placeholder='請輸入<?php echo $title;?>內容..'><?php echo isset ($posts['content']) ? $posts['content'] : '';?></textarea>
    </div>
    
    <div class='row'>
      <b><?php echo $title;?>備註</b>
      <textarea class='pure' name='memo' placeholder='請輸入<?php echo $title;?>備註..'><?php echo isset ($posts['memo']) ? $posts['memo'] : '';?></textarea>
    </div>

    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
