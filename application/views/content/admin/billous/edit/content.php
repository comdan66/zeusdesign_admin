<div class='panel'>
  <header>
    <h2>修改 出帳</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>

  <form class='form' method='post' action='<?php echo base_url ($uri_1);?>' enctype='multipart/form-data'>
    
    <div class='row n2'>
      <label>新增者</label>
      <div>
        <select name='user_id'>
          <option value='' selected>請選擇新增者</option>
    <?php if ($users = User::all (array ('select' => 'id, name'))) {
            foreach ($users as $user) { ?>
              <option value='<?php echo $user->id;?>'<?php echo (isset ($posts['user_id']) ? $posts['user_id'] : $obj->user_id) == $user->id ? ' selected': '';?>><?php echo $user->name;?></option>
      <?php }
          }?>
        </select>
      </div>
    </div>


    <div class='row n2'>
      <label>項目名稱</label>
      <div>
        <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入項目名稱..' maxlength='200' pattern='.{1,200}' required title='輸入項目名稱!' autofocus />
      </div>
    </div>

    <div class='row n2'>
      <label>金額</label>
      <div>
        <input type='number' name='money' id='money' value='<?php echo isset ($posts['money']) ? $posts['money'] : $obj->money;?>' placeholder='請輸入金額..' maxlength='200' pattern='.{1,200}' required title='輸入金額!' />
      </div>
    </div>

    <div class='row n2'>
      <label>日期</label>
      <div>
        <input type='date' name='date_at' value='<?php echo isset ($posts['date_at']) ? $posts['date_at'] : $obj->date_at->format ('Y-m-d');?>' placeholder='請輸入日期..' maxlength='200' pattern='.{1,200}' required title='輸入日期!' />
      </div>
    </div>

    <div class='row n2'>
      <label>是否有開發票</label>
      <div>
        <div class='radios'>
    <?php foreach (Billou::$invoiceNames as $key => $val) { ?>
            <label>
              <input type='radio' name='is_invoice' value='<?php echo $key;?>'<?php echo (isset ($posts['is_invoice']) ? $posts['is_invoice'] : $obj->is_invoice) == $key ? ' checked' : '';?> />
              <span></span><?php echo $val;?>
            </label>
    <?php } ?>
        </div>
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
