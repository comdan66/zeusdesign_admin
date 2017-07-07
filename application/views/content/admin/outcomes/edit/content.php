<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>修改<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1, $obj->id);?>' method='post'>
    <input type='hidden' name='_method' value='put' />

    <div class='row min'>
      <b class='need'>是否入帳</b>
      <label class='switch'>
        <input type='checkbox' name='status'<?php echo (isset ($posts['status']) ? $posts['status'] : $obj->status) == Outcome::STATUS_2 ? ' checked' : '';?> value='<?php echo Outcome::STATUS_2;?>' />
        <span></span>
      </label>
    </div>
    
    <div class='row'>
      <b class='need'><?php echo $title;?>標題</b>
      <input type='text' name='title' value='<?php echo isset ($posts['title']) ? $posts['title'] : $obj->title;?>' placeholder='請輸入<?php echo $title;?>標題..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>標題!' autofocus />
    </div>

    <div class='row'>
      <b class='need'><?php echo $title;?>新增者</b>
      <select name='user_id'>
  <?php if ($users = User::all (array ('select' => 'id, name'))) {
          foreach ($users as $user) { ?>
            <option value='<?php echo $user->id;?>'<?php echo (isset ($posts['user_id']) ? $posts['user_id'] : $obj->user_id) == $user->id ? ' selected': '';?>><?php echo $user->name;?></option>
    <?php }
        }?>
      </select>
    </div>

    <div class='row'>
      <b class='need'><?php echo $title;?>金額</b>
      <input type='number' name='money' value='<?php echo isset ($posts['money']) ? $posts['money'] : $obj->money;?>' placeholder='請輸入<?php echo $title;?>金額..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>金額!' />
    </div>

    <div class='row'>
      <b class='need'>是否有開發票</b>
<?php foreach (Outcome::$typeNames as $key => $typeNames) { ?>
        <label class='radio'>
          <input type='radio' name='type' value='<?php echo $key;?>'<?php echo (isset ($posts['type']) ? $posts['type'] : $obj->type) == $key ? ' checked' : '';?>>
          <span></span><?php echo $typeNames;?>
        </label>
<?php } ?>
    </div>

    <div class='row'>
      <b>備註</b>
      <input type='text' name='memo' value='<?php echo isset ($posts['memo']) ? $posts['memo'] : $obj->memo;?>' placeholder='請輸入備註..' maxlength='200' title='輸入備註!' />
    </div>
    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
