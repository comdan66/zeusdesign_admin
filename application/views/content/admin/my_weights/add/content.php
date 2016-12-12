<div class='panel'>
  <header>
    <h2>新增 入帳</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>

  <form class='form' method='post' action='<?php echo base_url ($uri_1);?>' enctype='multipart/form-data'>

    <div class='row n2'>
      <label>日期</label>
      <div>
        <?php echo date ('Y-m-d');?>
      </div>
    </div>

    <div class='row n2'>
      <label>名稱</label>
      <div>
        <?php echo User::current ()->name;?>
      </div>
    </div>

    <div class='row n2'>
      <label>* 自拍一張</label>
      <div class='img_row'>
        <div class='drop_img no_cchoice'>
          <img src='' />
          <input type='file' name='cover' />
        </div>
      </div>
    </div>

    <div class='row n2'>
      <label>目前體重</label>
      <div>
        <input type='number' name='weight' id='weight' value='<?php echo isset ($posts['weight']) ? $posts['weight'] : '0';?>' placeholder='請輸入目前體重..' maxlength='200' pattern='.{1,200}' required title='輸入目前體重!' autofocus />
      </div>
    </div>

    <div class='row n2'>
      <label>目前體脂率</label>
      <div>
        <input type='number' name='rate' id='rate' value='<?php echo isset ($posts['rate']) ? $posts['rate'] : '0';?>' placeholder='請輸入目前體脂率..' maxlength='200' pattern='.{1,200}' required title='輸入目前體脂率!' />
      </div>
    </div>

    <div class='row n2'>
      <label>運動消耗卡路里</label>
      <div>
        <input type='number' name='calorie' id='calorie' value='<?php echo isset ($posts['calorie']) ? $posts['calorie'] : '0';?>' placeholder='請輸入這次運動消耗卡路里..' maxlength='200' pattern='.{1,200}' required title='輸入這次運動消耗卡路里!' />
      </div>
    </div>

    <div class='row n2'>
      <label>備註</label>
      <div>
        <input type='text' name='memo' value='<?php echo isset ($posts['memo']) ? $posts['memo'] : '';?>' placeholder='請輸入備註..' maxlength='200' />
      </div>
    </div>


    <div class='btns'>
      <div class='row n2'>
        <label></label>
        <div>
          <button type='reset'>取消</button>
          <button type='submit'>送出</button>
        </div>
      </div>
    </div>
  </form>
</div>
