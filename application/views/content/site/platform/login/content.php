<div id='box'>
  <header>
    <div class='avatar'>
      <img src='<?php echo res_url('res', 'image', 'login_logo.png');?>' />
    </div>
    <div class='title'>
      <h1>宙思管理系統</h1>
      <p>Hello 宙思的小夥伴們快進來賺錢囉！</p>
    </div>
  </header>
  
  <span><?php echo Session::getData ('_fd', true);?></span>

  <a href='<?php echo Fb::loginUrl ('platform', 'fb_sign_in', 'admin');?>' class='facebook-login'>使用 Facebook 登入</a>

  <div class='or'>or</div>

  <form action='<?php echo base_url ('platform', 'ap_sign_in');?>' method='post'>
    <input type='text' name='account' placeholder='請輸入帳號' />
    <input type='text' name='password' placeholder='請輸入密碼' />
    <button type='submit'>使用帳密登入</button>
  </form>

  <footer>© <?php echo date ('Y');?> zeusdesign.com.tw</footer>
</div>