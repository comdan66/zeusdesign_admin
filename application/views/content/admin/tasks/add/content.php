<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>新增<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1);?>' method='post' enctype='multipart/form-data'>

    <div class='row min'>
      <b class='need'>是否完成</b>
      <label class='switch'>
        <input type='checkbox' name='status'<?php echo (isset ($posts['status']) ? $posts['status'] : Article::STATUS_1) == Article::STATUS_2 ? ' checked' : '';?> value='<?php echo Article::STATUS_2;?>' />
        <span></span>
      </label>
    </div>

<?php if ($users = User::all ()) { ?>
        <div class='row'>
          <b class='need'><?php echo $title;?>參與者</b>
    <?php foreach ($users as $user) {
            if (User::current ()->id != $user->id) {?>
              <label class='checkbox'>
                <input type='checkbox' name='user_ids[]' value='<?php echo $user->id;?>'<?php echo $user_ids && in_array ($user->id, $user_ids) ? ' checked' : '';?>>
                <span></span>
                <?php echo $user->name;?>
              </label>
    <?php   }
          } ?>
        </div>
<?php }?>

    <div class='row'>
      <b class='need'><?php echo $title;?>標題</b>
      <input type='text' name='title' value='<?php echo isset ($posts['title']) ? $posts['title'] : '';?>' placeholder='請輸入<?php echo $title;?>標題..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>標題!' autofocus />
    </div>

    <div class='row'>
      <b class='need'><?php echo $title;?>優先權</b>
<?php foreach (Task::$levelNames as $key => $levelName) { ?>
        <label class='radio'>
          <input type='radio' name='level' value='<?php echo $key;?>'<?php echo (isset ($posts['level']) ? $posts['level'] : Task::LEVEL_4) == $key ? ' checked' : '';?>>
          <span></span><?php echo $levelName;?>
        </label>
<?php } ?>
    </div>

    <div class='row'>
      <b class='need'><?php echo $title;?>預估完成日期</b>
      <input type='date' name='date' value='<?php echo isset ($posts['date']) ? $posts['date'] : '';?>' placeholder='請輸入<?php echo $title;?>預估完成日期..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>預估完成日期!' />
    </div>

    <div class='row'>
      <b><?php echo $title;?>內容</b>
      <textarea class='cke' name='content' placeholder='請輸入<?php echo $title;?>內容..'><?php echo isset ($posts['content']) ? $posts['content'] : '';?></textarea>
    </div>

    <div class='row muti' data-vals='<?php echo json_encode ($attachments);?>' data-cnt='<?php echo count ($row_muti);?>' data-attrs='<?php echo json_encode ($row_muti);?>'>
      <b><?php echo $title;?>附件</b>
      <span><a></a></span>
    </div>

    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
