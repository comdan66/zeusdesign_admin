<div class='panel'>
  <header>
    <h2>網站基本設定</h2>
  </header>


  <form class='form' method='post' action='<?php echo base_url ($uri_1);?>'>

    <div class='row n2'>
      <label>* 網站名稱</label>
      <div>
        <input type='text' name='site_title' value='<?php echo isset ($posts['site_title']) ? $posts['site_title'] : Siteconf::getVal ('site_title');?>' placeholder='請輸入網站名稱..' maxlength='200' pattern='.{1,200}' required title='輸入網站名稱!' autofocus />
      </div>
    </div>

    <div class='row n2'>
      <label>* 網站關鍵字</label>
      <div>
        <input type='text' name='site_keyword' value='<?php echo isset ($posts['site_keyword']) ? $posts['site_keyword'] : Siteconf::getVal ('site_keyword');?>' placeholder='請輸入網站關鍵字..' maxlength='200' pattern='.{1,200}' required title='輸入網站關鍵字!' autofocus />
      </div>
    </div>

    <div class='row n2'>
      <label>* 網站敘述</label>
      <div>
        <textarea name='site_desc' placeholder='請輸入網站敘述(約 150~200 個字)..' maxlength='200'><?php echo isset ($posts['site_desc']) ? $posts['site_desc'] : Siteconf::getVal ('site_desc');?></textarea>
      </div>
    </div>

    <div class='btns'>
      <div class='row n2'>
        <label></label>
        <div>
          <button type='submit'>儲存</button>
        </div>
      </div>
    </div>
  </form>
</div>
