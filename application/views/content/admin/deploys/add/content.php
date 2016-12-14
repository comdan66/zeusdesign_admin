<div class='panel'>
  <header>
    <h2>執行部署網站</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>

  <form class='form' method='post' action='<?php echo base_url ($uri_1);?>' enctype='multipart/form-data'>

    <div class='row n2'>
      <label>執行者</label>
      <div>
  <?php echo User::current ()->name;?>
      </div>
    </div>

    <div class='row n2'>
      <label>類型</label>
      <div>
        <div class='radios'>
    <?php foreach (Deploy::$typeNames as $key => $val) { ?>
            <label>
              <input type='radio' name='type' value='<?php echo $key;?>'<?php echo (isset ($posts['type']) ? $posts['type'] : Deploy::TYPE_BUILD) == $key ? ' checked' : '';?> />
              <span></span><?php echo $val;?>
            </label>
    <?php } ?>
        </div>
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
