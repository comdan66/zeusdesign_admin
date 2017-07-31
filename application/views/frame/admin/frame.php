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

<?php if (User::current ()->in_roles (array ('member'))) { ?>
        <div class='group'>
          <span class='icon-u' data-cntrole='task' data-cnt='<?php echo ($task_cnt = Task::count (array ('joins' => 'LEFT JOIN (select user_id,task_id from task_user_mappings) as a ON(tasks.id = a.task_id)', 'conditions' => array ('status = ? AND a.user_id = ?', Task::STATUS_1, User::current ()->id))));?>'>個人管理</span>
          <div>
            <a class='icon-home<?php echo ($url = base_url ('admin', 'my', User::current ()->id)) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>個人頁面</a>
            <a class='icon-calendar2<?php echo ($url = base_url ('admin', 'my-calendar')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>我的行事曆</a>
            <a data-cntrole='task' data-cnt='<?php echo $task_cnt;?>' class='icon-shield<?php echo ($url = base_url ('admin', 'my-tasks')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>我的任務</a>
            <a class='icon-moneybag<?php echo ($url = base_url ('admin', 'my-zbs')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>我的宙思幣</a>
          </div>
        </div>
<?php } ?>

<?php if (User::current ()->in_roles (array ('user', 'email'))) { ?>
        <div class='group'>
          <span class='icon-user-secret' data-cnt='<?php echo ($backup_cnt = User::current ()->in_roles (array ('backup')) ? Backup::count (array ('conditions' => array ('status = ?', Backup::STATUS_1))) : 0) + ($cronjob_cnt = User::current ()->in_roles (array ('cronjob')) ? Cronjob::count (array ('conditions' => array ('status = ?', Cronjob::STATUS_1))) : 0);?>'>後台管理</span>
          <div>
      <?php if (User::current ()->in_roles (array ('user'))) { ?>
              <a class='icon-ua<?php echo ($url = base_url ('admin', 'users')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>人員管理</a>
      <?php }
            if (User::current ()->in_roles (array ('backup'))) { ?>
              <a data-cnt='<?php echo $backup_cnt;?>' class='icon-backup<?php echo ($url = base_url ('admin', 'backups')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>備份紀錄</a>
      <?php }
            if (User::current ()->in_roles (array ('cronjob'))) { ?>
              <a data-cnt='<?php echo $cronjob_cnt;?>' class='icon-clipboard<?php echo ($url = base_url ('admin', 'cronjobs')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>排程紀錄</a>
      <?php }
            if (User::current ()->in_roles (array ('email'))) { ?>
              <a class='icon-em<?php echo ($url = base_url ('admin', 'mails')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>E-Mail 管理</a>
      <?php } ?>
          </div>
        </div>
<?php } ?>

<?php if (User::current ()->in_roles (array ('banner', 'promo', 'contact', 'deploy'))) { ?>
        <div class='group'>
          <span class='icon-ea' data-cntrole='contact' data-cnt='<?php echo ($contact_cnt = User::current ()->in_roles (array ('contact')) ? Contact::count (array ('conditions' => array ('status = ?', Contact::STATUS_1))) : 0);?>'>官網管理</span>
          <div>
      <?php if (User::current ()->in_roles (array ('banner'))) { ?>
              <a class='icon-im<?php echo ($url = base_url ('admin', 'banners')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>旗幟管理</a>
      <?php }
            if (User::current ()->in_roles (array ('promo'))) { ?>
              <a class='icon-im<?php echo ($url = base_url ('admin', 'promos')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>促銷管理</a>
      <?php }
            if (User::current ()->in_roles (array ('contact'))) { ?>
              <a data-cntrole='contact' data-cnt='<?php echo $contact_cnt;?>' class='icon-em<?php echo ($url = base_url ('admin', 'contacts')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>聯絡宙思</a>
      <?php }
            if (User::current ()->in_roles (array ('deploy'))) { ?>
              <a class='icon-loop2<?php echo ($url = base_url ('admin', 'deploys')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>部署紀錄</a>
      <?php } ?>
          </div>
        </div>
<?php } ?>

<?php if (User::current ()->in_roles (array ('article_tag', 'article'))) { ?>
        <div class='group'>
          <span class='icon-file-text2'>文章管理</span>
          <div>
      <?php if (User::current ()->in_roles (array ('article_tag'))) { ?>
              <a class='icon-price-tags<?php echo ($url = base_url ('admin', 'article-tags')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>文章分類</a>
      <?php }
            if (User::current ()->in_roles (array ('article'))) { ?>
              <a class='icon-list<?php echo ($url = base_url ('admin', 'articles')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>文章列表</a>
      <?php } ?>
          </div>
        </div>
<?php } ?>

<?php if (User::current ()->in_roles (array ('work_tag', 'work'))) { ?>
        <div class='group'>
          <span class='icon-g'>作品管理</span>
          <div>
      <?php if (User::current ()->in_roles (array ('work_tag'))) { ?>
              <a class='icon-price-tags<?php echo ($url = base_url ('admin', 'work-tags')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>作品分類</a>
      <?php }
            if (User::current ()->in_roles (array ('work'))) { ?>
              <a class='icon-list<?php echo ($url = base_url ('admin', 'works')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>作品列表</a>
      <?php } ?>
          </div>
        </div>
<?php } ?>

<?php if (User::current ()->in_roles (array ('company', 'income_item', 'income', 'outcome', 'surplus'))) { ?>
        <div class='group'>
          <span class='icon-bil'>帳務管理</span>
          <div>
      <?php if (User::current ()->in_roles (array ('company'))) { ?>
              <a class='icon-b<?php echo ($url = base_url ('admin', 'companies')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>配合廠商</a>
      <?php }
            if (User::current ()->in_roles (array ('income_tags'))) { ?>
              <a class='icon-price-tags<?php echo ($url = base_url ('admin', 'income-tags')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>請款分類</a>
      <?php }
            if (User::current ()->in_roles (array ('income_item'))) { ?>
              <a class='icon-ti<?php echo ($url = base_url ('admin', 'income-items')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>請款列表</a>
      <?php }
            if (User::current ()->in_roles (array ('income'))) { ?>
              <a class='icon-ib<?php echo ($url = base_url ('admin', 'incomes')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>入帳列表</a>
      <?php }
            if (User::current ()->in_roles (array ('outcome'))) { ?>
              <a class='icon-ob<?php echo ($url = base_url ('admin', 'outcomes')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>出帳列表</a>
      <?php }
            if (User::current ()->in_roles (array ('surplus'))) { ?>
              <a class='icon-moneybag<?php echo ($url = base_url ('admin', 'surplus')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>宙思盈餘</a>
      <?php } ?>
          </div>
        </div>
<?php } ?>

<?php if (User::current ()->in_roles (array ('ftp', 'task'))) { ?>
        <div class='group'>
          <span class='icon-fs'>專案管理</span>
          <div>
      <?php if (User::current ()->in_roles (array ('ftp'))) { ?>
              <a class='icon-sev<?php echo ($url = base_url ('admin', 'ftps')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>FTP 列表</a>
      <?php }
            if (User::current ()->in_roles (array ('task'))) { ?>
              <a class='icon-shield<?php echo ($url = base_url ('admin', 'tasks')) && isset ($_url) && ($url == $_url) ? ' show' : '';?>' href='<?php echo $url;?>'>任務列表</a>
      <?php } ?>
          </div>
        </div>
<?php } ?>

      <footer>© 2013 - <?php echo date ('Y');?> ZEUS Design CO., Ltd.</footer>
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