<!DOCTYPE html>
<html lang="zh">
  <head>
    <?php echo isset ($meta_list) ? $meta_list : ''; ?>

    <title><?php echo isset ($title) ? $title : ''; ?></title>

<?php echo isset ($css_list) ? $css_list : ''; ?>

<?php echo isset ($js_list) ? $js_list : ''; ?>

  </head>
  <body lang="zh-tw">
    <?php echo isset ($hidden_list) ? $hidden_list : ''; ?>

    <input type='checkbox' class='hckb' id='menu_ckb' />
    <input type='checkbox' class='hckb' id='user_ckb' />

    <header id='header'>
      <div class='logo'>
        <span><i>Ｚ</i></span>
        <span>Zeus Design Studio!</span>
      </div>
      <div class='midle'>
        <label class='icon-menu' for='menu_ckb'></label>
      </div>
      <div class='avatar' news>
        <label for='user_ckb' class='_ic'>
          <img src='<?php echo User::current ()->avatar ();?>' />
        </label>
      </div>
    </header>

    <div id='main'>
      <div>
        <label class='alert type1'><?php echo Session::getData ('_fi', true);?></label>
        <label class='alert type3'><?php echo Session::getData ('_fd', true);?></label>

        <?php echo isset ($content) ? $content : ''; ?>
      </div>
    </div>

    <div id='menu'>
      <header>
        <span>宙思</span>
        <span>管理系統</span>
      </header>

      <div class='group'>
        <span class='icon-u'>個人管理</span>
        <div>
          <a class='icon-home' href=''>個人頁面</a>
          <a class='icon-calendar2'>我的行事曆</a>
          <a class='icon-shield'>我的任務</a>
          <a class='icon-moneybag'>我的宙思幣</a>
        </div>
      </div>

      <div class='group'>
        <span class='icon-ea' data-cntrole='contact' data-cnt='<?php echo ($contact_cnt = Contact::count (array ('conditions' => array ('status = ?', Contact::STATUS_1))));?>'>官網管理</span>
        <div>
          <a class='icon-im<?php echo ($url = base_url ('admin', 'banners')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>旗幟管理</a>
          <a class='icon-im<?php echo ($url = base_url ('admin', 'promos')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>促銷管理</a>
          <a data-cntrole='contact' data-cnt='<?php echo $contact_cnt;?>' class='icon-em<?php echo ($url = base_url ('admin', 'contacts')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>聯絡宙思</a>
          <a class='icon-loop2<?php echo ($url = base_url ('admin', 'deploys')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>部署紀錄</a>
        </div>
      </div>

      <div class='group'>
        <span class='icon-file-text2'>文章管理</span>
        <div>
          <a class='icon-price-tags<?php echo ($url = base_url ('admin', 'article-tags')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>文章分類</a>
          <a class='icon-list<?php echo ($url = base_url ('admin', 'articles')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>文章列表</a>
        </div>
      </div>

      <div class='group'>
        <span class='icon-g'>作品管理</span>
        <div>
          <a class='icon-price-tags<?php echo ($url = base_url ('admin', 'work-tags')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>作品分類</a>
          <a class='icon-list<?php echo ($url = base_url ('admin', 'works')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>作品列表</a>
        </div>
      </div>

      <div class='group'>
        <span class='icon-bil'>帳務管理</span>
        <div>
          <a class='icon-b<?php echo ($url = base_url ('admin', 'companies')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>配合廠商</a>
          <a class='icon-ti<?php echo ($url = base_url ('admin', 'income-items')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>請款列表</a>
          <a class='icon-bil<?php echo ($url = base_url ('admin', 'incomes')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>入帳列表</a>
        </div>
      </div>

      <footer>© <?php echo date ('Y');?> zeusdesign.com.tw</footer>
    </div><label class='icon-cross' for='menu_ckb'></label>

    <div id='user'>
      <div>
        <span>Hi, <b><?php echo User::current ()->name;?></b> 您好。</span>
        <span>目前登入次數：<b><?php echo number_format (User::current ()->login_count);?></b>次</span>
        <span>上次登入：<time datetime='<?php echo User::current ()->logined_at->format ('Y-m-d H:i:s');?>'><?php echo User::current ()->logined_at->format ('Y-m-d H:i:s');?></time></span>
        <a href='' class='icon-notifications_active' data-count='10,000'>您有未讀訊息</a>
        <a href='<?php echo base_url ('logout');?>' class='icon-power'>登出</a>
      </div>
    </div><label for='user_ckb'></label>

    <div id='tip_texts'></div>

    <div id='loading'>
      <div><span>請稍後..</span></div>
    </div>

  </body>
</html>