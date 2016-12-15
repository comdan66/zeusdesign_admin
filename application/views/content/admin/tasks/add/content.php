<div class='panel'>
  <header>
    <h2>新增 任務</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>

  <form class='form full' method='post' action='<?php echo base_url ($uri_1);?>' enctype='multipart/form-data'>
    
    <div class='row n2'>
      <label>* 新增者</label>
      <div>
        <select name='user_id'>
    <?php if ($users = User::all (array ('select' => 'id, name'))) {
            foreach ($users as $user) { ?>
              <option value='<?php echo $user->id;?>'<?php echo (isset ($posts['user_id']) ? $posts['user_id'] : User::current ()->id) == $user->id ? ' selected': '';?>><?php echo $user->name;?></option>
      <?php }
          }?>
        </select>
      </div>
    </div>

<?php if ($users = User::find ('all', array ('conditions' => array ('id != ?', User::current ()->id)))) { ?>
        <div class='row n2'>
          <label>* 指派會員</label>
          <div>
      <?php foreach ($users as $user) { ?>
              <label class='checkbox tag'><input type='checkbox' name='user_ids[]' value='<?php echo $user->id;?>'<?php echo $user_ids && in_array ($user->id, $user_ids) ? ' checked' : '';?> /><span></span><?php echo $user->name;?></label>
      <?php } ?>
          </div>
        </div>
<?php }?>

    <div class='row n2'>
      <label>* 優先權</label>
      <div class='radios'>
  <?php foreach (Task::$levelNames as $key => $value) { ?>
          <label><input type='radio' name='level' value='<?php echo $key;?>' <?php echo (isset ($posts['level']) ? $posts['level'] : Task::LEVEL_4) == $key ? ' checked' : '';?> /><span></span><?php echo $value;?></label>
  <?php } ?>
      </div>
    </div>

    <div class='row n2'>
      <label>* 任務標題</label>
      <div>
        <input type='text' name='title' value='<?php echo isset ($posts['title']) ? $posts['title'] : '';?>' placeholder='請輸入任務標題..' maxlength='200' pattern='.{1,200}' required title='輸入任務標題!' autofocus />
      </div>
    </div>

    <div class='row n2'>
      <label>* 任務日期</label>
      <div>
        <input type='date' name='date_at' value='<?php echo isset ($posts['date_at']) ? $posts['date_at'] : date ('Y-m-d');?>' placeholder='請輸入任務日期..' maxlength='200' pattern='.{1,200}' required title='輸入任務日期!' />
      </div>
    </div>

    <div class='row n2'>
      <label>任務內容</label>
      <div>
        <textarea name='description' class='pure autosize cke' placeholder='請輸入內容..'><?php echo isset ($posts['description']) ? $posts['description'] : '';?></textarea>
      </div>
    </div>

    <div class='row n2'>
      <label>* 是否完成</label>
      <div>
        <label class='switch'>
          <input type='checkbox' name='finish'<?php echo isset ($posts['finish']) && ($posts['finish'] == Task::IS_FINISHED) ? ' checked' : '';?> />
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
