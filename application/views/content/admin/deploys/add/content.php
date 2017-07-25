<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>新增<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1);?>' method='post'>

    <div class='row'>
      <b class='need'>類型</b>
<?php foreach (Deploy::$typeNames as $key => $typeName) { ?>
        <label class='radio'>
          <input type='radio' name='type' value='<?php echo $key;?>'<?php echo (isset ($posts['type']) ? $posts['type'] : Deploy::TYPE_1) == $key ? ' checked' : '';?>>
          <span></span><?php echo $typeName;?>
        </label>
<?php } ?>
    </div>

    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
