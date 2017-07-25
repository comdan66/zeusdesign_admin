<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>修改<?php echo $title;?>資料</h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回個人頁</a>
</div>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ('admin', 'my', $obj->id);?>' method='post' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />

    <div class='row'>
      <b class='need'>載入動畫效果</b>
<?php foreach (UserSet::$aniNames as $key => $aniName) { ?>
        <label class='radio'>
          <input type='radio' name='ani' value='<?php echo $key;?>'<?php echo (isset ($posts['ani']) ? $posts['ani'] : $obj->set->ani) == $key ? ' checked' : '';?>>
          <span></span><?php echo $aniName;?>
        </label>
<?php } ?>
    </div>


    <div class='row'>
      <b class='need'><?php echo $title;?>名稱</b>
      <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入<?php echo $title;?>名稱..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>名稱!' autofocus />
    </div>
    

    <div class='row'>
      <b class='need' data-title='請填寫正確的 E-Mail。'><?php echo $title;?> E-Mail</b>
      <input type='text' name='email' value='<?php echo isset ($posts['email']) ? $posts['email'] : $obj->email;?>' placeholder='請輸入<?php echo $title;?> E-Mail..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?> E-Mail!' />
    </div>
    
    <div class='row'>
      <b data-title='預覽僅示意，未按比例。'><?php echo $title;?>Banner</b>
      <div class='drop_img'>
        <img src='<?php echo (string)$obj->set->banner ? $obj->set->banner->url () : '';?>' />
        <input type='file' name='banner' />
      </div>
    </div>




    <div class='row'>
      <b><?php echo $title;?>臉書網址</b>
      <input type='text' name='link_facebook' value='<?php echo isset ($posts['link_facebook']) ? $posts['link_facebook'] : $obj->set->link_facebook;?>' placeholder='請輸入<?php echo $title;?>臉書網址..' maxlength='200' title='輸入<?php echo $title;?>臉書網址!' />
    </div>
    <div class='row'>
      <b><?php echo $title;?>聯絡電話</b>
      <input type='text' name='phone' value='<?php echo isset ($posts['phone']) ? $posts['phone'] : $obj->set->phone;?>' placeholder='請輸入<?php echo $title;?>聯絡電話..' maxlength='200' title='輸入<?php echo $title;?>聯絡電話!' />
    </div>
    


    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回個人頁</a>
    </div>
  </form>
</div>
