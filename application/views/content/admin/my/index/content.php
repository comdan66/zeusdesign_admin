<header style='background-image:url("<?php echo $obj->banner ('1200x630c');?>")'>
  <div class='avatar _ic'>
    <img src='<?php echo $obj->avatar ();?>' />
  </div>

  <div class='info'>
    <h1><?php echo $obj->name;?></h1>
    <div>
      <a href="mailto:<?php echo $obj->email;?>" class='icon-em' target='_blank'></a>
      <?php if ($obj->set && $obj->set->link_facebook) { ?><a href="<?php echo $obj->set->link_facebook;?>" class='icon-facebook' target='_blank'></a><?php }?>
      <?php if ($obj->set && $obj->set->link_line) { ?><a href="<?php echo $obj->set->link_line;?>" class='icon-brand' target='_blank'></a><?php }?>
      <?php if ($obj->set && $obj->set->link_google) { ?><a href="<?php echo $obj->set->link_google;?>" class='icon-google-plus' target='_blank'></a><?php }?>
    </div>
  </div>

  <div class='bottom' data-title='擁有權限：'>
<?php
    if ($obj->roles) {
      foreach ($obj->roles as $role) { ?>
        <span><?php echo $role->name ();?></span>
<?php }
    } else { ?>
      <span>無任何權限</span> 
<?php
    } ?>
  </div>
  <a class='setting icon-se' href=''></a>
</header>

<span class='title'><span>各個數據</span></span>

<div class='units'>
  <a href='' class='icon-shield'>
    <span class='count-up' data-unit='個'><?php echo Task::count (array ('conditions' => array ('user_id = ? AND status = ?', $obj->id, Task::STATUS_2)));?></span>
    <span>未完成任務</span>
  </a>
  <a href='' class='icon-calendar2'>
    <span class='count-up' data-unit='件'><?php echo Schedule::count (array ('conditions' => array ('user_id = ?', $obj->id)));?></span>
    <span>今日行程</span>
  </a>
  <a href='' class='icon-moneybag'>
    <span class='count-up' data-unit='元'><?php echo array_sum (array_map (function ($zb) { return $zb->pay (); }, Zb::find ('all', array ('select' => 'id, income_id, percentage', 'conditions' => array ('user_id = ?', $obj->id)))));?></span>
    <span>累積宙思幣</span>
  </a>
  <a href='' class='icon-enter'>
    <span class='count-up' data-unit='次'><?php echo $obj->login_count;?></span>
    <span>登入次數</span>
  </a>
  <a href='' class='icon-file-text2'>
    <span class='count-up' data-unit='篇'><?php echo Article::count (array ('conditions' => array ('user_id = ?', $obj->id)));?></span>
    <span>我的文章數</span>
  </a>
  <a href='' class='icon-g'>
    <span class='count-up' data-unit='件'><?php echo Work::count (array ('conditions' => array ('user_id = ?', $obj->id)));?></span>
    <span>我的作品數</span>
  </a>
</div>
<span class='title'><span>後台操作紀錄</span></span>

<div class='calendar'>
<?php for ($i = 52; $i > 0; $i--) { ?>
    <div>
<?php for ($j = 7; $j > 0; $j--) {
        $day = ($i - 1) * 7 + ($j - 1) - (6 - date ('w'));
        $ymd = date ('Y-m-d', strtotime (date ('Y-m-d') . ' -' . $day . ' days'));
        $log = isset ($logs[$ymd]) ? $logs[$ymd] : false?>
        <div<?php echo $log ? ' class="' . $log['s'] . '"' : '';?> data-cnt="<?php echo $log ? $log['cnt'] : 0;?>">
          <span><?php echo $ymd;?> 操作了 <?php echo $log ? $log['cnt'] : 0;?> 次</span>
        </div>
<?php } ?>
    </div>
<?php } ?>
</div>

<div class='block2'>
  <div>
    <span class='title'><span>今日任務</span></span>
    
    <div class='list<?php echo !$tasks ? ' e' : '';?>' data-e='今日沒有任何任務。'>
<?php foreach ($tasks as $task) { ?>
        <a href="">
          <i class='icon-shield'></i>
          <span data-sub='<?php echo $task->mini_content (50);?>'><?php echo $task->title;?></span>
        </a>
<?php }?>
    </div>
    <span class='title'><span>今日行程</span></span>
    
    <div class='list<?php echo !($schedules1 && $schedules3) ? ' e' : '';?>' data-e='今日沒有任何行程。'>
<?php foreach ($schedules1 as $schedule) { ?>
        <a href="">
          <img src=<?php echo $schedule->user->avatar ();?> />
          <span data-sub='<?php echo $schedule->schedule_tag_id && $schedule->tag ? $schedule->tag->name : '';?>'><?php echo $schedule->title;?></span>
        </a>
<?php }?>
      <?php foreach ($schedules3 as $schedule) { ?>
        <a href="">
          <img src=<?php echo $schedule->user->avatar ();?> />
          <span data-sub='<?php echo $schedule->schedule_tag_id && $schedule->tag ? $schedule->tag->name : '';?>'><?php echo $schedule->title;?></span>
        </a>
<?php }?>

    </div>
  </div>
  <div>
    <span class='title'><span>操作記錄</span><a href="">更多..</a></span>
    
    <div class='logs'>
      <h3>今天</h3>

      <div class='icon-home'>今天今天今天今天</div>
      <span>asdasdasdasddsasdasdasdasddsasdasdasdasddsasdasdasdasddsasdasdasdasddsasdasdasdasddsasdasdasdasdds</span>
      <span>asdasdasdasdds</span>
      <div class='icon-home'>asdasdasdas</div>
      <div class='icon-home'>asdasdasdas</div>
      <span>asdasdasdasdds</span>
      <div class='icon-home'>asdasdasdas</div>

    </div>

  </div>
</div>
