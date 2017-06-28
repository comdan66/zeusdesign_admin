<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>修改<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1, $obj->id);?>' method='post' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />

    <div class='row min'>
      <b class='need'>是否完成</b>
      <label class='switch'>
        <input type='checkbox' name='status'<?php echo (isset ($posts['status']) ? $posts['status'] : $obj->status) == Task::STATUS_2 ? ' checked' : '';?> value='<?php echo Task::STATUS_2;?>' />
        <span></span>
      </label>
    </div>

    <div class='row'>
      <b class='need'><?php echo $title;?>擁有者</b>
      <select name='user_id'>
  <?php if ($users = User::all (array ('select' => 'id, name'))) {
          foreach ($users as $user) { ?>
            <option value='<?php echo $user->id;?>'<?php echo (isset ($posts['user_id']) ? $posts['user_id'] : $obj->user_id) == $user->id ? ' selected': '';?>><?php echo $user->name;?></option>
    <?php }
        }?>
      </select>
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
      <input type='text' name='title' value='<?php echo isset ($posts['title']) ? $posts['title'] : $obj->title;?>' placeholder='請輸入<?php echo $title;?>標題..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>標題!' autofocus />
    </div>

    <div class='row'>
      <b class='need'><?php echo $title;?>優先權</b>
<?php foreach (Task::$levelNames as $key => $levelName) { ?>
        <label class='radio'>
          <input type='radio' name='level' value='<?php echo $key;?>'<?php echo (isset ($posts['level']) ? $posts['level'] : $obj->level) == $key ? ' checked' : '';?>>
          <span></span><?php echo $levelName;?>
        </label>
<?php } ?>
    </div>

    <div class='row'>
      <b class='need'><?php echo $title;?>日期</b>
      <input type='date' name='date' value='<?php echo isset ($posts['date']) ? $posts['date'] : $obj->date->format ('Y-m-d');?>' placeholder='請輸入<?php echo $title;?>日期..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>日期!' />
    </div>
    
    <div class='row'>
      <b class='need'><?php echo $title;?>內容</b>
      <textarea class='cke' name='content' placeholder='請輸入<?php echo $title;?>內容..'><?php echo isset ($posts['content']) ? $posts['content'] : $obj->content;?></textarea>
    </div>

<?php if (count ($obj->attachments)) { ?>
        <div class='row icons'>
          <b class='need'><?php echo $title;?>附件</b>
          <div>
    <?php foreach ($obj->attachments as $attachment) { ?>
            <figure href='<?php echo $attachment->file_icon ();?>'>
              <input type='hidden' name='old_attachment_ids[]' value='<?php echo $attachment->id;?>' />
              <img src='<?php echo $attachment->file_icon ();?>' />
              <figcaption data-description='<?php echo $attachment->title;?>'><?php echo $attachment->title;?></figcaption>
              <a class='icon-cross'></a>
              <span><?php echo size_unit ($attachment->size);?></span>
            </figure>
    <?php }?>
          </div>
        </div>
<?php }?>

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
