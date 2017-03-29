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

    <div id='container' class=''>
      <div id='main_row'>
        <div id='left_side'>
          
          <header>
            <a href='<?php echo base_url ();?>'>Ｚ</a>
            <span>Zeus Design Studio!</span>
          </header>

          <div id='login_user'>
            <figure class='_i'>
              <img src="<?php echo User::current ()->avatar ();?>">
            </figure>
            <div>
              <span>Hi, 您好!</span>
              <span><?php echo User::current ()->name;?></span>
            </div>
          </div>

          <ul id='main_menu'>
      <?php if (User::current ()->in_roles (array ('member'))) { ?>
              <li>
                <label data-role='schedule' data-cnt='<?php echo ($task_cnt = Task::count (array ('conditions' => array ('finish = ? AND (user_id = ? || (id IN (?)))', Task::NO_FINISHED, User::current ()->id, ($task_ids = column_array (TaskUserMapping::find ('all', array ('select' => 'task_id', 'conditions' => array ('user_id = ?', User::current ()->id))), 'task_id')) ? $task_ids : array (0))))) + ($schedule_cnt = Schedule::count (array ('conditions' => array ('user_id = ? AND finish = ? AND year = ? AND month = ? AND day = ?', User::current ()->id, Schedule::NO_FINISHED, date ('Y'), date ('m'), date ('d')))));?>'>
                  <input type='checkbox' />
                  <span class='icon-u'>個人管理</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'my');?>" class='icon-bo<?php echo $now_url == $url ? ' active' : '';?>'>基本資料</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'schedule-tags');?>" class='icon-ta<?php echo $now_url == $url ? ' active' : '';?>'>行程分類</a></li>
                    <li data-role='schedule' data-cnt='<?php echo $schedule_cnt;?>'><a href="<?php echo $url = base_url ('admin', 'calendar');?>" class='icon-calendar<?php echo $now_url == $url ? ' active' : '';?>'>我的行程</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'my-weights');?>" class='icon-balance-scale<?php echo $now_url == $url ? ' active' : '';?>'>體重記錄</a></li>
                    <li data-role='task' data-cnt='<?php echo $task_cnt;?>'><a href="<?php echo $url = base_url ('admin', 'my-tasks');?>" class='icon-shield<?php echo $now_url == $url ? ' active' : '';?>'>我的任務</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'my-image-base-tags');?>" class='icon-ta<?php echo $now_url == $url ? ' active' : '';?>'>圖庫分類</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'my-image-bases');?>" class='icon-cs<?php echo $now_url == $url ? ' active' : '';?>'>我的圖庫</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'my-salaries');?>" class='icon-moneybag<?php echo $now_url == $url ? ' active' : '';?>'>我的宙思幣</a></li>
                  </ul>
                </label>
              </li>
      <?php }
            if (User::current ()->in_roles (array ('admin'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-se'>後台系統</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'users');?>" class='icon-ua<?php echo $now_url == $url ? ' active' : '';?>'>權限設定</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'mails');?>" class='icon-em<?php echo $now_url == $url ? ' active' : '';?>'>郵件列表</a></li>
                  </ul>
                </label>
              </li>
      <?php }
            if (User::current ()->in_roles (array ('site'))) { ?>
              <li>
                <label data-role='contact' data-cnt='<?php echo ($contact_cnt = Contact::count (array ('conditions' => array ('is_readed = ?', Contact::READ_NO))));?>'>
                  <input type='checkbox' />
                  <span class='icon-ea'>前台系統</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'banners');?>" class='icon-im<?php echo $now_url == $url ? ' active' : '';?>'>旗幟管理</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'promos');?>" class='icon-im<?php echo $now_url == $url ? ' active' : '';?>'>促銷管理</a></li>
                    <li data-role='contact' data-cnt='<?php echo $contact_cnt;?>'><a href="<?php echo $url = base_url ('admin', 'contacts');?>" class='icon-em<?php echo $now_url == $url ? ' active' : '';?>'>聯絡我們</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'deploys');?>" class='icon-pi<?php echo $now_url == $url ? ' active' : '';?>'>部署紀錄</a></li>
                  </ul>
                </label>
              </li>
      <?php }
            if (User::current ()->in_roles (array ('article'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-f'>文章系統</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'article-tags');?>" class='icon-ta<?php echo $now_url == $url ? ' active' : '';?>'>文章分類</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'articles');?>" class='icon-fa<?php echo $now_url == $url ? ' active' : '';?>'>文章管理</a></li>
                  </ul>
                </label>
              </li>
      <?php }
            if (User::current ()->in_roles (array ('work'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-g'>作品系統</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'work-tags');?>" class='icon-ta<?php echo $now_url == $url ? ' active' : '';?>'>作品分類</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'works');?>" class='icon-g<?php echo $now_url == $url ? ' active' : '';?>'>作品管理</a></li>
                  </ul>
                </label>
              </li>
      <?php }
            if (User::current ()->in_roles (array ('demo'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-layout'>提案系統</span>
                  <ul>
              <?php foreach (Demo::find ('all', array ('order' => 'id DESC')) as $demo) { ?>
                      <li class='item n1'>
                        <a href="<?php echo $url = base_url ('admin', 'demo', $demo->id, 'images');?>" class='icon-br<?php echo $now_url == $url ? ' active' : '';?>'><?php echo $demo->name?></a>
                        <a class='icon-new-tab' href="<?php echo $demo->demo_url ();?>" target='_blank'></a>
                      </li>
              <?php }?>
                    
                    <li><a href="<?php echo $url = base_url ('admin', 'demos', 'add');?>" class='icon-r<?php echo $now_url ==  base_url ('admin', 'demos') ? ' active' : '';?>'>新增提案</a></li>
                  </ul>
                </label>
              </li>
      <?php }
            if (User::current ()->in_roles (array ('price'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-clipboard'>報價系統</span>
                  <ul>
              <?php foreach (PriceType::find ('all', array ('order' => 'id DESC')) as $type) { ?>
                      <li class='item'>
                        <a href="<?php echo $url = base_url ('admin', 'type', $type->id, 'prices');?>" class='icon-ta<?php echo $now_url == $url ? ' active' : '';?>'><?php echo $type->name?></a>
                        <a class='icon-e' href="<?php echo base_url ('admin', 'price-types', $type->id, 'edit');?>"></a>
                        <a class='icon-t' href="<?php echo base_url ('admin', 'price-types', $type->id);?>" data-method='delete' data-alert='確定刪除？分類下的細項也會一併刪除喔！'></a>
                      </li>
              <?php }?>
                    
                    <li><a href="<?php echo $url = base_url ('admin', 'price-types', 'add');?>" class='icon-r<?php echo $now_url ==  base_url ('admin', 'price-types') ? ' active' : '';?>'>新增分類</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'prices', 'abacus');?>" class='icon-abacus<?php echo $now_url ==  $url ? ' active' : '';?>'>報價計算機</a></li>
                  </ul>
                </label>
              </li>
      <?php }
            if (User::current ()->in_roles (array ('image'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-ims'>圖庫系統</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'image-bases');?>" class='icon-cs<?php echo $now_url == $url ? ' active' : '';?>'>宙思圖庫</a></li>
                  </ul>
                </label>
              </li>
      <?php }
            if (User::current ()->in_roles (array ('customer'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-b'>聯絡人系統</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'customer-companies');?>" class='icon-br<?php echo $now_url == $url ? ' active' : '';?>'>聯絡人公司</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'customers');?>" class='icon-ab<?php echo $now_url == $url ? ' active' : '';?>'>聯絡人管理</a></li>
                  </ul>
                </label>
              </li>
      <?php } 
            if (User::current ()->in_roles (array ('invoice'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-ti'>請款系統</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'invoice-tags');?>" class='icon-ta<?php echo $now_url == $url ? ' active' : '';?>'>請款分類</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'invoices');?>" class='icon-ti<?php echo $now_url == $url ? ' active' : '';?>'>請款管理</a></li>
                  </ul>
                </label>
              </li>
      <?php } 
            if (User::current ()->in_roles (array ('bills'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-bil'>帳務系統</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'billins');?>" class='icon-ib<?php echo $now_url == $url ? ' active' : '';?>'>入帳管理</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'billous');?>" class='icon-ob<?php echo $now_url == $url ? ' active' : '';?>'>出帳管理</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'bills');?>" class='icon-moneybag<?php echo $now_url == $url ? ' active' : '';?>'>盈餘管理</a></li>
                  </ul>
                </label>
              </li>
      <?php }
            if (User::current ()->in_roles (array ('project'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-fs'>專案系統</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'ftps');?>" class='icon-sev<?php echo $now_url == $url ? ' active' : '';?>'>FTP 管理</a></li>
                    <li><a href="<?php echo $url = base_url ('admin', 'tasks');?>" class='icon-shield<?php echo $now_url == $url ? ' active' : '';?>'>任務 管理</a></li>
                  </ul>
                </label>
              </li>
      <?php }
            if (User::current ()->in_roles (array ('salary'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-moneybag'>薪資系統</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'salaries');?>" class='icon-moneybag<?php echo $now_url == $url ? ' active' : '';?>'>薪資管理</a></li>
                  </ul>
                </label>
              </li>
      <?php } ?>

          </ul>

        </div>
        <div id='right_side'>
          <div id='top_side'>
            <button type='button' id='hamburger' class='icon-m'></button>
            <span>
              <a data-role='natification' data-cnt='<?php echo Notification::count (array ('conditions' => array ('is_read = ? AND user_id = ?', Notification::READ_NO, User::current ()->id)));?>' href='<?php echo $url = base_url ('admin', 'my-notifications');?>' class='icon-no_a<?php echo $now_url == $url ? ' active' : '';?>'></a>
              <a href='<?php echo base_url ('logout');?>' class='icon-o'></a>
            </span>
          </div>
          <div id='main'>
      <?php if ($_flash_danger = Session::getData ('_flash_danger', true)) { ?>
              <div id='_flash_danger'><?php echo $_flash_danger;?></div>
      <?php } else if ($_flash_info = Session::getData ('_flash_info', true)) { ?>
              <div id='_flash_info'><?php echo $_flash_info;?></div>
      <?php }?>
      <?php echo isset ($content) ? $content : ''; ?>
          </div>
          <div id='bottom_side'>
            後台版型設計 by 宙思 <a href='http://www.ioa.tw/' target='_blank'>OA Wu</a>
          </div>
        </div>
      </div>
    </div>
    
    <div id='loading'>
      <div class='cover'></div>
      <div class='contant'>編譯中，請稍候..</div>
    </div>
  </body>
</html>