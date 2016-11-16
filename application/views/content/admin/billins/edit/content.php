<div class='panel'>
  <header>
    <h2>修改 入帳</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>

  <form class='form' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />
    
    <div class='row n2'>
      <label>負責人</label>
      <div>
        <select name='user_id'>
          <option value='' selected>請選擇負責人</option>
    <?php if ($users = User::all (array ('select' => 'id, name'))) {
            foreach ($users as $user) { ?>
              <option value='<?php echo $user->id;?>'<?php echo (isset ($posts['user_id']) ? $posts['user_id'] : $obj->user->id) == $user->id ? ' selected': '';?>><?php echo $user->name;?></option>
      <?php }
          }?>
        </select>
      </div>
    </div>


    <div class='row n2'>
      <label>專案名稱</label>
      <div>
        <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入專案名稱..' maxlength='200' pattern='.{1,200}' required title='輸入專案名稱!' autofocus />
      </div>
    </div>

    <div class='row n2'>
      <label>總金額</label>
      <div>
        <input type='number' name='money' id='money' value='<?php echo isset ($posts['money']) ? $posts['money'] : $obj->money;?>' placeholder='請輸入總金額..' maxlength='200' pattern='.{1,200}' required title='輸入總金額!' />
      </div>
    </div>

    <div class='row n2'>
      <label>％數標題</label>
      <div id='billin_rate'>
        <div class='radios' data-val='<?php echo isset ($posts['rate_name']) ? $posts['rate_name'] : $obj->rate_name;?>'>
          <label>
            <input type='radio' name='rate_type' value='1' />
            <span></span>有發票
          </label>
          <label>
            <input type='radio' name='rate_type' value='2'  />
            <span></span>無發票
          </label>
          <label>
            <input type='radio' name='rate_type' value='3'  />
            <span></span>其他
          </label>
        </div>

        <div><input type='text' name='rate_name' id='rate_name' value='<?php echo isset ($posts['rate_name']) ? $posts['rate_name'] : $obj->rate_name;?>' placeholder='請輸入％數標題..' maxlength='200' pattern='.{1,200}' required title='輸入％數標題!' /></div>
      </div>
    </div>

    <div class='row n2'>
      <label>％數</label>
      <div>
        <input type='number' name='rate' id='rate' value='<?php echo isset ($posts['rate']) ? $posts['rate'] : $obj->rate;?>' placeholder='請輸入％數..' maxlength='200' pattern='.{1,200}' required title='輸入％數' />
      </div>
    </div>

    <div class='row n2'>
      <label>宙思＄</label>
      <div>
        <input type='number' name='zeus_money' id='zeus_money' value='<?php echo isset ($posts['zeus_money']) ? $posts['zeus_money'] : $obj->zeus_money;?>' placeholder='請輸入宙思＄..' maxlength='200' pattern='.{1,200}' required title='輸入宙思＄!' />
      </div>
    </div>

    <div class='row n2'>
      <label>日期</label>
      <div>
        <input type='date' name='date_at' value='<?php echo isset ($posts['date_at']) ? $posts['date_at'] : $obj->date_at->format ('Y-m-d');?>' placeholder='請輸入日期..' maxlength='200' pattern='.{1,200}' required title='輸入日期!' />
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
