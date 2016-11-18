<div class='panel'>
  <header>
    <h2>新增聯絡人</h2>
    <a href='<?php echo base_url ($uri_1, $parent->id, $uri_2);?>' class='icon-x'></a>
  </header>

  <form class='form' method='post' action='<?php echo base_url ($uri_1, $parent->id, $uri_2);?>'>
    
    <div class='row n2'>
      <label>* 聯絡人名稱</label>
      <div>
        <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : '';?>' placeholder='請輸入名稱..' maxlength='200' pattern='.{1,200}' required title='輸入名稱!' autofocus />
      </div>
    </div>

    <div class='row n2'>
      <label>公司電話</label>
      <div id='telephone'><?php echo $parent->telephone;?></div>
    </div>

    <div class='row n2'>
      <label>公司分機</label>
      <div>
        <input type='text' name='extension' value='<?php echo isset ($posts['extension']) ? $posts['extension'] : '';?>' placeholder='請輸入分機..' maxlength='10' />
      </div>
    </div>

    <div class='row n2'>
      <label>聯絡人手機</label>
      <div>
        <input type='text' name='cellphone' value='<?php echo isset ($posts['cellphone']) ? $posts['cellphone'] : '';?>' placeholder='請輸入手機..' maxlength='15' />
      </div>
    </div>

    <div class='row n2 emails' data-i='0' data-emails='<?php echo json_encode ($posts['emails']);?>'>
      <label>聯絡 E-Mail</label>
      <div>
        <div class='add_email'>
          <button type='button' class='icon-r add'></button>
        </div>
      </div>
    </div>

    <div class='row n2'>
      <label>聯絡人個性</label>
      <div>
        <input type='text' name='experience' value='<?php echo isset ($posts['experience']) ? $posts['experience'] : '';?>' placeholder='請輸入聯絡人個性..' maxlength='200' />
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
