<div class='panel'>
  <header>
    <h2>修改 FTP</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>

  <form class='form' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />
    
    <div class='row n2'>
      <label>* 專案名稱</label>
      <div>
        <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入專案名稱..' maxlength='200' pattern='.{1,200}' required title='輸入專案名稱!' autofocus />
      </div>
    </div>

    <div class='row n2'>
      <label>* 網站網址</label>
      <div>
        <input type='text' name='url' value='<?php echo isset ($posts['url']) ? $posts['url'] : $obj->url;?>' placeholder='請輸入網站網址..' maxlength='200' pattern='.{1,200}' required title='輸入網站網址!' />
      </div>
    </div>
    
    <div title='FTP 資訊' class='line'></div>
    <div class='row n2'>
      <label>主機</label>
      <div>
        <input type='text' name='ftp_url' value='<?php echo isset ($posts['ftp_url']) ? $posts['ftp_url'] : $obj->ftp_url;?>' placeholder='請輸入 FTP 主機..' maxlength='200' />
      </div>
    </div>

    <div class='row n2'>
      <label>帳號</label>
      <div>
        <input type='text' name='ftp_account' value='<?php echo isset ($posts['ftp_account']) ? $posts['ftp_account'] : $obj->ftp_account;?>' placeholder='請輸入 FTP 帳號..' maxlength='200' />
      </div>
    </div>

    <div class='row n2'>
      <label>密碼</label>
      <div>
        <input type='text' name='ftp_password' value='<?php echo isset ($posts['ftp_password']) ? $posts['ftp_password'] : $obj->ftp_password;?>' placeholder='請輸入 FTP 密碼..' maxlength='200' />
      </div>
    </div>
    
    <div title='管理頁資訊' class='line'></div>
    <div class='row n2'>
      <label>網址</label>
      <div>
        <input type='text' name='admin_url' value='<?php echo isset ($posts['admin_url']) ? $posts['admin_url'] : $obj->admin_url;?>' placeholder='請輸入管理頁網址..' maxlength='200' />
      </div>
    </div>

    <div class='row n2'>
      <label>帳號</label>
      <div>
        <input type='text' name='admin_account' value='<?php echo isset ($posts['admin_account']) ? $posts['admin_account'] : $obj->admin_account;?>' placeholder='請輸入管理頁帳號..' maxlength='200' />
      </div>
    </div>

    <div class='row n2'>
      <label>密碼</label>
      <div>
        <input type='text' name='admin_password' value='<?php echo isset ($posts['admin_password']) ? $posts['admin_password'] : $obj->admin_password;?>' placeholder='請輸入管理頁密碼..' maxlength='200' />
      </div>
    </div>

    <div title='其他資訊' class='line'></div>
    <div class='row n2'>
      <label>備註</label>
      <div>
        <textarea name='memo' placeholder='請輸入備註..' class='pure autosize'><?php echo isset ($posts['memo']) ? $posts['memo'] : $obj->memo;?></textarea>
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
