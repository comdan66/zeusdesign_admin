<div class='panel'>
  <header>
    <h2>新增分類</h2>
    <a href='<?php echo base_url ($uri_1, $parent->id, $uri_2);?>' class='icon-x'></a>
  </header>


  <form class='form mid' method='post' action='<?php echo base_url ($uri_1, $parent->id, $uri_2);?>'>
    <div class='row n2'>
      <label>* 功能名稱</label>
      <div>
        <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : '';?>' placeholder='請輸入功能名稱..' maxlength='200' pattern='.{1,200}' required title='輸入功能名稱!' autofocus />
      </div>
    </div>

    <div class='row n2'>
      <label>* 價格</label>
      <div>
        <input type='number' name='money' value='<?php echo isset ($posts['money']) ? $posts['money'] : '0';?>' placeholder='輸入價格..' maxlength='200' pattern='.{1,200}' required title='輸入價格!' />
      </div>
    </div>

    <div class='row n2'>
      <label>描述</label>
      <div>
        <input type='text' name='desc' value='<?php echo isset ($posts['desc']) ? $posts['desc'] : '';?>' placeholder='請輸入描述..' maxlength='200' />
      </div>
    </div>

    <div class='row n2'>
      <label>備註</label>
      <div>
        <input type='text' name='memo' value='<?php echo isset ($posts['memo']) ? $posts['memo'] : '';?>' placeholder='請輸入備註..' maxlength='200' />
      </div>
    </div>

    <div class='row n2 sources' data-i='0' data-sources='<?php echo json_encode ($sources);?>'>
      <label>參考網址</label>
      <div>
        <div class='add_source'>
          <button type='button' class='icon-r add'></button>
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
