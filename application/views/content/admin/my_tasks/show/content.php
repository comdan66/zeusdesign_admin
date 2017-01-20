<div class='panel main-task<?php echo $obj->finish == Task::IS_FINISHED ? ' finish' : '';?>'>
  <header>
    <h2><?php echo $obj->title;?></h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
<?php if ($obj->user->id == User::current ()->id) { ?>
        <a href='<?php echo base_url ('admin', 'tasks', $obj->id, 'edit');?>' class='icon-e'></a>
<?php } ?>
    <a id='to_commit' class='icon-d2'></a>
  </header>
  <div class='content'>
    <div class='info'>
      <div class='icon icon-shield'></div>
      <div class='users'>此任務是由 <div class='user owner'><img src='<?php echo $obj->user->avatar ();?>' /><span><?php echo $obj->user->name;?></span></div> 於 <time datetime='<?php echo $obj->created_at->format ('Y-m-d H:i:s');?>'><?php echo $obj->created_at->format ('Y-m-d H:i:s');?></time> 新增<?php echo $obj->created_at->format ('Y-m-d H:i:s') != $obj->updated_at->format ('Y-m-d H:i:s') ? '，並在 <time datetime="' . $obj->updated_at->format ('Y-m-d H:i:s') . '">' . $obj->updated_at->format ('Y-m-d H:i:s') . '</time>' . ' 更新' : '';?>。</div>
    </div>
    <div class='detail'>
      <div class='row'>
        <div class='l'>
          <div class='ll'>優先權</div>
          <div class='rr'>
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
        </div>
        <div class='r'>
          <div class='ll'>預計完成日期</div>
          <div class='rr date_at'><?php echo $obj->date_at->format ('Y-m-d');?> (<?php echo $quota_day > 0 ? '<span class="tomorrow">還有 ' . $quota_day . ' 天</span>' : ($quota_day < 0 ? '<span class="yesterday">已經過期 ' . abs ($quota_day) . ' 天</span>' : '<span class="today">就是今天</span>');?>)</div>
        </div>
      </div>
      <div class='row'>
        <div class='l2'>
          <div class='ll'>指派人員</div>
          <div class='rr users'><?php echo implode ('', array_map (function ($user) { return '<div class="user"><img src="' . $user->avatar () . '" /><span>' . $user->name . '</span></div>'; }, $users));?></div>
        </div>
      </div>
    </div>
    <div class='description'>
      <h4 title='內容如下'></h4>
      <div class='content'><?php echo $obj->description;?></div>
    </div>
<?php if (count ($obj->attachments)) { ?>
        <div class='description'>
          <h4 title='附件檔案'></h4>
          <div class='content attachments'>
      <?php foreach ($obj->attachments as $attachment) { ?>
              <a href='<?php echo $attachment->file->url ();?>' target='_blank'>
                <img src='<?php echo $attachment->file_icon ();?>' />
                <figcaption data-description='<?php echo $attachment->title;?>'><?php echo $attachment->title;?></figcaption>
                <div><?php echo size_unit ($attachment->size);?></div>
              </a>
      <?php }?>
          </div>
        </div>
<?php }?>
  </div>
</div>

<div class='panel commit-form'>
  <form class='commit' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />

    <div class='avatar'><img src='<?php echo User::current ()->avatar ();?>'></div>
    <div class='input'>
      <input type='text' id='commit_input_content' name='content' placeholder='請輸入留言、註解..' maxlength='200' >
      <input type='file' name='file' />
    </div>
    <button type='submit'>送出</button>
  </form>
</div>

<?php
    if ($commits = TaskCommit::find ('all', array ('order' => 'id DESC', 'include' => array ('user'), 'conditions' => array ('task_id = ?', $obj->id)))) { ?>
  <div class='panel commits'>
<?php foreach ($commits as $commit) { ?>
        <div class='commit'>
          <div class='users'><div class='user owner'><img src='<?php echo $commit->user->avatar ();?>' /><span><?php echo $commit->user->name;?></span></div> 於 <time datetime='<?php echo $commit->created_at->format ('Y-m-d H:i:s');?>'><?php echo $commit->created_at->format ('Y-m-d H:i:s');?></time> <?php echo $commit->action;?>。</div>
          <div class='content'><?php echo  $commit->content;?></div>
    <?php if ((string)$commit->file) {?>
            <div class='file'>
              <a href='<?php echo $commit->file->url ();?>' target='_blank'>
                <img src='<?php echo $commit->file_icon ();?>' />
                <div>
                  <span><?php echo (string)$commit->file;?></span>
                  <span><?php echo size_unit ($commit->size);?></span>
                </div>
              </a>
            </div>
    <?php } ?>
        </div>
<?php }?>
  </div>
<?php
    } ?>
