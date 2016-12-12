<div class='panel'>
  <header>
    <h2>修改 薪資</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>

  <form class='form' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />
    
    <div class='row n2'>
      <label>* 受薪人員</label>
      <div>
        <select name='user_id'>
          <option value='' selected>請選擇 受薪人員</option>
    <?php if ($users = User::all (array ('select' => 'id, name'))) {
            foreach ($users as $user) { ?>
              <option value='<?php echo $user->id;?>'<?php echo (isset ($posts['user_id']) ? $posts['user_id'] : $obj->user_id) == $user->id ? ' selected': '';?>><?php echo $user->name;?></option>
      <?php }
          }?>
        </select>
      </div>
    </div>


    <div class='row n2'>
      <label>* 專案名稱</label>
      <div>
        <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入專案名稱..' maxlength='200' pattern='.{1,200}' required title='輸入專案名稱!' autofocus />
      </div>
    </div>

    <div class='row n2'>
      <label>* 金額</label>
      <div>
        <input type='number' name='money' id='money' value='<?php echo isset ($posts['money']) ? $posts['money'] : $obj->money;?>' placeholder='請輸入金額..' maxlength='200' pattern='.{1,200}' required title='輸入金額!' />
      </div>
    </div>

    <div class='row n2'>
      <label>* 是否已給付</label>
      <div>
        <label class='switch'>
          <input type='checkbox' name='is_finished'<?php echo (isset ($posts['is_finished']) ? $posts['is_finished'] : $obj->is_finished) ? ' checked' : '';?> />
          <span></span>
        </label>
      </div>
    </div>
    
    <div class='row n2'>
      <label>備註</label>
      <div>
        <input type='text' name='memo' value='<?php echo isset ($posts['memo']) ? $posts['memo'] : $obj->memo;?>' placeholder='請輸入備註..' maxlength='200' />
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
