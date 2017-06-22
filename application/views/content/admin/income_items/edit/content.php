<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>修改<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1, $obj->id);?>' method='post' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />

    <div class='row'>
      <b class='need'><?php echo $title;?>標題</b>
      <input type='text' name='title' value='<?php echo isset ($posts['title']) ? $posts['title'] : $obj->title;?>' placeholder='請輸入<?php echo $title;?>標題..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>標題!' autofocus />
    </div>

    <div class='row'>
      <b class='need'><?php echo $title;?>負責人</b>
      <select name='user_id'>
  <?php if ($users = User::all (array ('select' => 'id, name'))) {
          foreach ($users as $user) { ?>
            <option value='<?php echo $user->id;?>'<?php echo (isset ($posts['user_id']) ? $posts['user_id'] : $obj->user_id) == $user->id ? ' selected': '';?>><?php echo $user->name;?></option>
    <?php }
        }?>
      </select>
    </div>

    <div class='row'>
      <b class='need'><?php echo $title;?>窗口PM</b>
      <select name='company_pm_id'>
          <option value='0' selected>請選擇聯絡人</option>
    <?php if ($companies = Company::all (array ('select' => 'id, name'))) {
            foreach ($companies as $company) { ?>
              <optgroup label='<?php echo $company->name;?>'>
          <?php if ($company->pms) {
                  foreach ($company->pms as $pm) { ?>
                    <option value='<?php echo $pm->id;?>'<?php echo (isset ($posts['company_pm_id']) ? $posts['company_pm_id'] : $obj->company_pm_id) == $pm->id ? ' selected': '';?>><?php echo $pm->name;?></option>
            <?php }
                } ?>
              </optgroup>
      <?php }
          } ?>
        </select>
    </div>

    <div class='row'>
      <b>相關圖片</b>
      <div class='drop_imgs'>
        
  <?php foreach ($obj->images as $image) { ?>
          <div class='drop_img'>
            <img src='<?php echo $image->name->url ();?>' />
            <input type='hidden' name='oldimg[]' value='<?php echo $image->id; ?>' />
            <input type='file' name='images[]' />
            <a class='icon-bin'></a>
          </div>
  <?php }?>

        <div class='drop_img'>
          <img src='' />
          <input type='file' name='images[]' />
          <a class='icon-bin'></a>
        </div>

      </div>
    </div>

    <div class='row'>
      <b class='need'>專案結束日期</b>
      <input type='date' name='close_date' value='<?php echo isset ($posts['close_date']) ? $posts['close_date'] : $obj->close_date ? $obj->close_date->format ('Y-m-d') : '';?>' placeholder='請輸入<?php echo $title;?>結束日期..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>結束日期!' />
    </div>
    
    <div class='row'>
      <b><?php echo $title;?>備註</b>
      <input type='text' name='memo' value='<?php echo isset ($posts['memo']) ? $posts['memo'] : $obj->memo;?>' placeholder='請輸入<?php echo $title;?>備註..' maxlength='200' title='輸入<?php echo $title;?>備註!' />
    </div>

    <div class='row muti2' data-vals='<?php echo json_encode ($details);?>' data-attrs='<?php echo json_encode ($row_muti);?>'>
      <b class='need'><?php echo $title;?>細項</b>
      <span><a></a></span>
    </div>

    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
