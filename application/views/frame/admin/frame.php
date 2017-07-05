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
      <div class='avatar' data-cntrole='notice' data-cnt='<?php echo $notice_cnt = Notice::count (array ('conditions' => array ('user_id = ? AND status = ?', User::current ()->id, Notice::STATUS_1)));?>'>
        <label for='user_ckb' class='_ic'>
          <img src='<?php echo User::current ()->avatar ();?>' />
        </label>
      </div>
    </header>

    <div id='main'>
      <div class='ani<?php echo User::current ()->set->ani;?>'>
        <?php if ($t = Session::getData ('_fi', true)) { ?><label class='alert type1'><?php echo $t;?></label><?php } ?>
        <?php if ($t = Session::getData ('_fd', true)) { ?><label class='alert type3'><?php echo $t;?></label><?php } ?>

        <?php echo isset ($content) ? $content : ''; ?>
      </div>
    </div>

    <div id='menu'>
      <header>
        <span>宙思</span>
        <span>管理系統</span>
      </header>

      <div class='group'>
        <span class='icon-u' data-cntrole='task' data-cnt='<?php echo ($task_cnt = Task::count (array ('joins' => 'LEFT JOIN (select user_id,task_id from task_user_mappings) as a ON(tasks.id = a.task_id)', 'conditions' => array ('status = ? AND a.user_id = ?', Task::STATUS_1, User::current ()->id))));?>'>個人管理</span>
        <div>
          <a class='icon-home<?php echo ($url = base_url ('admin', 'my', User::current ()->id)) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>個人頁面</a>
          <a class='icon-calendar2<?php echo ($url = base_url ('admin', 'my-calendar')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>我的行事曆</a>
          <a data-cntrole='task' data-cnt='<?php echo $task_cnt;?>' class='icon-shield<?php echo ($url = base_url ('admin', 'my-tasks')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>我的任務</a>
          <a class='icon-moneybag<?php echo ($url = base_url ('admin', 'my-zbs')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>我的宙思幣</a>
        </div>
      </div>

      <div class='group'>
        <span class='icon-user-secret'>後台管理</span>
        <div>
          <a class='icon-ua<?php echo ($url = base_url ('admin', 'users')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>人員管理</a>
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

      <div class='group'>
        <span class='icon-fs'>專案管理</span>
        <div>
          <a class='icon-sev<?php echo ($url = base_url ('admin', 'ftps')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>FTP 資料</a>
          <a class='icon-shield<?php echo ($url = base_url ('admin', 'tasks')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>任務列表</a>
        </div>
      </div>

      <footer>© <?php echo date ('Y');?> zeusdesign.com.tw</footer>
    </div><label class='icon-cross' for='menu_ckb'></label>

    <div id='user'>
      <div>
        <span>Hi, <b><?php echo User::current ()->name;?></b> 您好。</span>
        <?php if (User::current ()->set) {?>
          <span>目前登入次數：<b><?php echo number_format (User::current ()->set->login_count);?></b>次</span>
          <span>上次登入：<time datetime='<?php echo User::current ()->set->logined_at->format ('Y-m-d H:i:s');?>'><?php echo User::current ()->set->logined_at->format ('Y-m-d H:i:s');?></time></span>
        <?php }?>
        <a href='<?php echo base_url ('admin', 'my-notices');?>' class='icon-notifications_active' data-cntrole='notice' data-cnt='<?php echo $notice_cnt;?>'>檢視通知</a>
        <a href='<?php echo base_url ('logout');?>' class='icon-power'>登出</a>
      </div>
    </div><label for='user_ckb'></label>

    <div id='tip_texts'></div>

    <div id='loading'>
      <div><span>請稍後..</span></div>
    </div>

  </body>
</html>