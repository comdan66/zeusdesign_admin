<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>修改<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1, $obj->id);?>' method='post'>
    <input type='hidden' name='_method' value='put' />

    <div class='row'>
      <b class='need'><?php echo $title;?>名稱</b>
      <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入<?php echo $title;?>名稱..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>名稱!' autofocus />
    </div>

    <div class='row'>
      <b class='need'><?php echo $title;?> E-Mail</b>
      <input type='text' name='email' value='<?php echo isset ($posts['email']) ? $posts['email'] : $obj->email;?>' placeholder='請輸入<?php echo $title;?> E-Mail..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?> E-Mail!' />
    </div>
    
<?php 
    foreach (Cfg::setting ('role', 'group') as $group => $keys) { ?>
      <div class='row'>
          <b style='border-bottom: 1px solid rgba(220, 220, 220, 1);padding-bottom: 8px;margin-bottom: 8px;'><?php echo $group;?></b>
    <?php foreach ($keys as $key) {
            if (Cfg::setting ('role', 'role_names', $key)) { ?>
              <label class='checkbox'>
                <input type='checkbox' name='keys[]' value='<?php echo $key;?>'<?php echo $obj->in_roles (array ($key), true) ? ' checked' : '';?>>
                <span></span>
                <b><?php echo Cfg::setting ('role', 'role_names', $key, 'name');?></b><i><?php echo Cfg::setting ('role', 'role_names', $key, 'desc');?>。</i>
              </label>
      <?php } ?>
    <?php } ?>
        </div>
<?php } ?>
  
    <div class='row'>
      <b><?php echo $title;?> Facebook 鏈結</b>
      <input type='text' name='link_facebook' value='<?php echo isset ($posts['link_facebook']) ? $posts['link_facebook'] : $obj->set->link_facebook;?>' placeholder='請輸入<?php echo $title;?> Facebook 鏈結..' maxlength='200' title='輸入<?php echo $title;?> Facebook 鏈結!' />
    </div>
    
    <div class='row'>
      <b><?php echo $title;?> 聯絡電話</b>
      <input type='text' name='phone' value='<?php echo isset ($posts['phone']) ? $posts['phone'] : $obj->set->phone;?>' placeholder='請輸入<?php echo $title;?> 聯絡電話..' maxlength='200' title='輸入<?php echo $title;?> 聯絡電話!' />
    </div>
    
    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
