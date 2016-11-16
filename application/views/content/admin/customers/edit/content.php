<div class='panel'>
  <header>
    <h2>修改聯絡人</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>


  <form class='form' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>'>
    <input type='hidden' name='_method' value='put' />
    <div class='row n2'>
      <label>* 公司</label>
      <div>
        <select name='company_id'>
          <option value='0' selected>請選擇公司</option>
    <?php if ($companies = Company::all (array ('select' => 'id, name'))) {
            foreach ($companies as $company) { ?>
              <option value='<?php echo $company->id;?>'<?php echo (isset ($posts['company_id']) ? $posts['company_id'] : $obj->company_id) == $company->id ? ' selected': '';?>><?php echo $company->name;?></option>
      <?php }
          }?>
        </select>
      </div>
    </div>

    <div class='row n2'>
      <label>名稱</label>
      <div>
        <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入名稱..' maxlength='200' pattern='.{1,200}' required title='輸入名稱!' autofocus />
      </div>
    </div>
    
    <div class='row n2'>
      <label>電子郵件</label>
      <div>
        <input type='text' name='email' value='<?php echo isset ($posts['email']) ? $posts['email'] : $obj->email;?>' placeholder='請輸入電子郵件..' maxlength='200' />
      </div>
    </div>

    <div class='row n2'>
      <label>電話</label>
      <div>
        <input type='text' name='telephone' value='<?php echo isset ($posts['telephone']) ? $posts['telephone'] : $obj->telephone;?>' placeholder='請輸入電話..' maxlength='20' />
      </div>
    </div>

    <div class='row n2'>
      <label>分機</label>
      <div>
        <input type='text' name='extension' value='<?php echo isset ($posts['extension']) ? $posts['extension'] : $obj->extension;?>' placeholder='請輸入分機..' maxlength='10' />
      </div>
    </div>

    <div class='row n2'>
      <label>手機</label>
      <div>
        <input type='text' name='cellphone' value='<?php echo isset ($posts['cellphone']) ? $posts['cellphone'] : $obj->cellphone;?>' placeholder='請輸入手機..' maxlength='10' />
      </div>
    </div>

    <div class='row n2'>
      <label>住址</label>
      <div>
        <input type='text' name='address' value='<?php echo isset ($posts['address']) ? $posts['address'] : $obj->address;?>' placeholder='請輸入住址..' maxlength='200' />
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
