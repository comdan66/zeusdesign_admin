<div class='panel'>
  <header>
    <h2>修改 FTP</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>

  <form class='form full' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>' enctype='multipart/form-data'>
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

    <div class='row n2'>
      <label>* FTP 主機</label>
      <div>
        <input type='text' name='host' value='<?php echo isset ($posts['host']) ? $posts['host'] : $obj->host;?>' placeholder='請輸入FTP 主機..' maxlength='200' pattern='.{1,200}' required title='輸入FTP 主機!' />
      </div>
    </div>

    <div class='row n2'>
      <label>FTP 帳號</label>
      <div>
        <input type='text' name='account' value='<?php echo isset ($posts['account']) ? $posts['account'] : $obj->account;?>' placeholder='請輸入FTP 帳號..' maxlength='200' />
      </div>
    </div>

    <div class='row n2'>
      <label>FTP 密碼</label>
      <div>
        <input type='text' name='password' value='<?php echo isset ($posts['password']) ? $posts['password'] : $obj->password;?>' placeholder='請輸入FTP 密碼..' maxlength='200' />
      </div>
    </div>

    <div class='row n2'>
      <label>Server 管理頁網址</label>
      <div>
        <input type='text' name='admin_url' value='<?php echo isset ($posts['admin_url']) ? $posts['admin_url'] : $obj->admin_url;?>' placeholder='請輸入Server 管理頁網址..' maxlength='200' />
      </div>
    </div>

    <div class='row n2'>
      <label>備註</label>
      <div>
        <textarea name='memo' placeholder='請輸入備註..'><?php echo isset ($posts['memo']) ? $posts['memo'] : $obj->memo;?></textarea>
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
