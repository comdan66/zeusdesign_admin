<div class='panel'>
  <header>
    <h2>修改提案</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>


  <form class='form' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>'>
    <input type='hidden' name='_method' value='put' />

    <div class='row n2'>
      <label>* 是否公開</label>
      <div>
        <label class='switch'>
          <input type='checkbox' name='is_enabled'<?php echo (isset ($posts['is_enabled']) ? $posts['is_enabled'] : $obj->is_enabled) == Demo::ENABLE_YES ? ' checked' : '';?> />
          <span></span>
        </label>
      </div>
    </div>
    <div class='row n2'>
      <label>* 是否為手機版</label>
      <div>
        <label class='switch'>
          <input type='checkbox' name='is_mobile'<?php echo (isset ($posts['is_mobile']) ? $posts['is_mobile'] : $obj->is_mobile) == Demo::MOBILE_YES ? ' checked' : '';?> />
          <span></span>
        </label>
      </div>
    </div>

    <div class='row n2'>
      <label>* 名稱</label>
      <div>
        <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入名稱..' maxlength='200' pattern='.{1,200}' required title='輸入名稱!' autofocus />
      </div>
    </div>
    <div class='row n2'>
      <label>密碼</label>
      <div>
        <input type='text' name='password' value='<?php echo isset ($posts['password']) ? $posts['password'] : $obj->password;?>' placeholder='請輸入密碼..' maxlength='200' />
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
