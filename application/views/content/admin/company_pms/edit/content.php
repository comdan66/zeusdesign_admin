<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>修改<?php echo $title;?></h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_b);?>'>回表頁</a>
</div>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1, $parent->id, $uri_2, $obj->id);?>' method='post'>
    <input type='hidden' name='_method' value='put' />

    <div class='row'>
      <b class='need'><?php echo $title;?>名稱</b>
      <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入<?php echo $title;?>名稱..' maxlength='200' pattern='.{1,200}' required title='輸入<?php echo $title;?>名稱!' autofocus />
    </div>

    <div class='row'>
      <b>分機</b>
      <input type='text' name='extension' value='<?php echo isset ($posts['extension']) ? $posts['extension'] : $obj->extension;?>' placeholder='請輸入<?php echo $title;?>分機..' maxlength='200' title='輸入<?php echo $title;?>分機!' />
    </div>

    
<?php
    foreach (CompanyPmItem::$typeNames as $type => $typeName) { ?>
      <div class='row muti' data-vals='<?php echo json_encode ($items[$type]);?>' data-cnt='<?php echo count ($row_muti[$type]);?>' data-attrs='<?php echo json_encode ($row_muti[$type]);?>'>
        <b><?php echo $title;?> <?php echo $typeName;?></b>
        <span><a></a></span>
      </div>
<?php
    } ?>

    <div class='row'>
      <b>個性、合作心得</b>
      <input type='text' name='experience' value='<?php echo isset ($posts['experience']) ? $posts['experience'] : $obj->experience;?>' placeholder='請輸入<?php echo $title;?>個性、合作心得..' maxlength='200' title='輸入<?php echo $title;?>個性、合作心得!' />
    </div>

    <div class='row'>
      <b>備註</b>
      <input type='text' name='memo' value='<?php echo isset ($posts['memo']) ? $posts['memo'] : $obj->memo;?>' placeholder='請輸入<?php echo $title;?>備註..' maxlength='200' title='輸入<?php echo $title;?>備註!' />
    </div>


    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_b);?>'>回列表頁</a>
    </div>
  </form>
</div>
