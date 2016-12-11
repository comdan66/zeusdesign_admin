<div class='panel'>
  <header>
    <h2>修改聯絡人公司</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>


  <form class='form' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>'>
    <input type='hidden' name='_method' value='put' />

    <div class='row n2'>
      <label>* 公司名稱</label>
      <div>
        <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入公司名稱..' maxlength='200' pattern='.{1,200}' required title='輸入公司名稱!' autofocus />
      </div>
    </div>

    
    <div class='row n2'>
      <label>公司統編</label>
      <div>
        <input type='text' name='business_no' value='<?php echo isset ($posts['business_no']) ? $posts['business_no'] : $obj->business_no;?>' placeholder='請輸入公司統編..' maxlength='25' />
      </div>
    </div>

    <div class='row n2'>
      <label>公司電話</label>
      <div>
        <input type='text' name='telephone' value='<?php echo isset ($posts['telephone']) ? $posts['telephone'] : $obj->telephone;?>' placeholder='請輸入公司電話..' maxlength='15' />
      </div>
    </div>
    
    <div class='row n2'>
      <label>公司地址</label>
      <div>
        <input type='text' name='address' value='<?php echo isset ($posts['address']) ? $posts['address'] : $obj->address;?>' placeholder='請輸入公司住址..' maxlength='200' />
      </div>
    </div>

    <div class='row n2'>
      <label>備註</label>
      <div>
        <input type='text' name='memo' value='<?php echo isset ($posts['memo']) ? $posts['memo'] : $obj->memo;?>' placeholder='請輸入備註..' maxlength='200' />
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
