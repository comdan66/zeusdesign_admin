<div class='panel'>
  <header>
    <h2>修改 任務</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>

  <form class='form full' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />
    
    <div class='row n2'>
      <label>新增者</label>
      <div>
        <select name='user_id'>
    <?php if ($users = User::all (array ('select' => 'id, name'))) {
            foreach ($users as $user) { ?>
              <option value='<?php echo $user->id;?>'<?php echo (isset ($posts['user_id']) ? $posts['user_id'] : $obj->user_id) == $user->id ? ' selected': '';?>><?php echo $user->name;?></option>
      <?php }
          }?>
        </select>
      </div>
    </div>

<?php if ($users = User::all ()) { ?>
        <div class='row n2'>
          <label>* 指派會員</label>
          <div>
      <?php $tag_ids = isset ($posts['tag_ids']) ? $posts['tag_ids'] : column_array ($obj->task_mappings, 'user_id');
            foreach ($users as $user) { ?>
              <label class='checkbox tag'><input type='checkbox' name='tag_ids[]' value='<?php echo $user->id;?>'<?php echo $tag_ids && in_array ($user->id, $tag_ids) ? ' checked' : '';?> /><span></span><?php echo $user->name;?></label>
      <?php } ?>
          </div>
        </div>
<?php }?>

    <div class='row n2'>
      <label>* 標題</label>
      <div>
        <input type='text' name='title' value='<?php echo isset ($posts['title']) ? $posts['title'] : $obj->title;?>' placeholder='請輸入標題..' maxlength='200' pattern='.{1,200}' required title='輸入標題!' autofocus />
      </div>
    </div>

    <div class='row n2'>
      <label>* 日期</label>
      <div>
        <input type='date' name='date_at' value='<?php echo isset ($posts['date_at']) ? $posts['date_at'] : $obj->date_at->format ('Y-m-d');?>' placeholder='請輸入日期..' maxlength='200' pattern='.{1,200}' required title='輸入日期!' />
      </div>
    </div>

    <div class='row n2'>
      <label>* 內容</label>
      <div>
        <textarea name='description' class='pure autosize cke' placeholder='請輸入內容..'><?php echo isset ($posts['description']) ? $posts['description'] : $obj->description;?></textarea>
      </div>
    </div>

    <div class='row n2'>
      <label>是否完成</label>
      <div>
        <label class='switch'>
          <input type='checkbox' name='finish'<?php echo (isset ($posts['finish']) ? $posts['finish'] : $obj->finish) == Task::IS_FINISHED ? ' checked' : '';?> />
          <span></span>
        </label>
      </div>
    </div>


    <div class='btns'>
      <div class='row n2'>
        <label></label>
        <div>
          <button type='reset'>取消</button>
          <button type='submit'>送出</button>
        </div>
      </div>
    </div>
  </form>
</div>
