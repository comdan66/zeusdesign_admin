<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>檢視<?php echo $title;?></h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
  
  <a class='icon-d2 to_commit'>留言</a>
  <?php
  if ($obj->user_id == User::current ()->id && User::current ()->in_roles (array ('task'))) { ?>
    <a class='icon-pencil2' href='<?php echo base_url ('admin', 'tasks', $obj->id, 'edit');?>'>編輯</a>
  <?php
  }?>
</div>


<div class='panel<?php echo $obj->status == Task::STATUS_2 ? ' finish' : '';?>'>
  <h2><?php echo $title;?>：<?php echo $obj->title;?></h2>
  <span class='info'>此任務是由 <b><?php echo $obj->user->name;?></b> 於 <time datetime='<?php echo $obj->created_at->format ('Y-m-d H:i:s');?>'><?php echo $obj->created_at->format ('Y-m-d H:i:s');?></time> 新增<?php echo $obj->created_at->format ('Y-m-d H:i:s') != $obj->updated_at->format ('Y-m-d H:i:s') ? '，並在 <time datetime="' . $obj->updated_at->format ('Y-m-d H:i:s') . '">' . $obj->updated_at->format ('Y-m-d H:i:s') . '</time>' . ' 更新' : '';?>。</span>

  <div class='box'>
    <div>
      <h3>任務優先權</h3>
      
      <div class='colors'>
  <?php $cnt = count (Task::$levelColors);
        $unit = 100 / $cnt;
        $val = $unit * ($obj->level - 1);
        foreach (Task::$levelColors as $key => $color) { ?>
          <span style='width:<?php echo $unit;?>%;background-color:<?php echo $color;?>;'></span>
  <?php }?>
        <span id='cursor' data-val='<?php echo $val;?>%' style='width:<?php echo $unit;?>%;'></span>
      </div>
      
      <span><?php echo isset (Task::$levelNames[$obj->level]) ? Task::$levelNames[$obj->level] : '無';?></span>

    </div>
    <div>
      <h3>預估完成日期</h3>
      <div class='date'><?php echo $obj->date->format ('Y-m-d');?> (<?php echo $quota_day > 0 ? '<span class="tomorrow">還有 ' . $quota_day . ' 天</span>' : ($quota_day < 0 ? '<span class="yesterday">已經過期 ' . abs ($quota_day) . ' 天</span>' : '<span class="today">就是今天</span>');?>)</div>
    </div>
    <div>
      <h3>參與人員</h3>
      <div class='users'>
  <?php foreach ($obj->users as $user) { ?>
          <div class='user'><img src='<?php echo $user->avatar ();?>'><span><?php echo $user->name;?></span></div>
  <?php }?>
      </div>
    </div>
  </div>

  

</div>

<div class='panel'>
  <h2><?php echo $obj->title;?> 的內容</h2>
  <span class='info'>以下是任務「<b><?php echo $obj->title;?></b>」的工作內容，若有疑問請善用下方 <a class='to_commit'>留言系統</a>。</span>
  <article><?php echo $obj->content;?></article>


</div>

<div class='panel'>
  <h2>相關附件</h2>
  <span class='info'>以下是任務「<b><?php echo $obj->title;?></b>」的附件檔案，可以點擊檔案下載。</span>
  <div class='attachments'>
<?php 
    foreach ($obj->attachments as $attachment) { ?>
      <a href='<?php echo $attachment->file->url ();?>' target='_blank'>
        <img src='<?php echo $attachment->file_icon ();?>' />
        <figcaption data-description='<?php echo $attachment->title;?>'><?php echo $attachment->title;?></figcaption>
        <div><?php echo size_unit ($attachment->size);?></div>
      </a>
<?php
    }?>
  </div>
</div>

<h3 class='h'>針對任務「<?php echo $obj->title;?>」留言</h3>
<div class='panel commit-form'>
  <form class='commit' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />

    <img src='<?php echo User::current ()->avatar ();?>'>

    <input type='text' id='commit_input_content' name='content' placeholder='請輸入留言、註解..' maxlength='200' >
    <input type='file' name='file' />

    <button type='submit'>送出</button>
  </form>
</div>

<div class='panel commits'>
  <?php
  foreach ($obj->commits as $commit) { ?>
    <div class='commit'>
      <div>
        <div class='user'>
          <img src='<?php echo $commit->user->avatar ();?>' />
          <span><?php echo $commit->user->name;?></span>
        </div>
        <span>於</span>
        <time datetime='<?php echo $commit->created_at->format ('Y-m-d H:i:s');?>'><?php echo $commit->created_at->format ('Y-m-d H:i:s');?></time>
        <span><?php echo $commit->action;?>。</span>
      </div>
      <div><?php echo $commit->content;?></div>

<?php if ((string)$commit->file) {?>
        <div>
          <a href='<?php echo $commit->file->url ();?>' target='_blank'>
            <img src='<?php echo $commit->file_icon ();?>' />
            <span><?php echo (string)$commit->file;?></span>
            <span><?php echo size_unit ($commit->size);?></span>
          </a>
        </div>
<?php } ?>
    </div>
  <?php
  } ?>
</div>
