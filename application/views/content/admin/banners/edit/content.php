<div class='panel'>
  <header>
    <h2>修改 Banner</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>


  <form class='form' method='post' action='<?php echo base_url ($uri_1, $banner->id);?>' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />
    <div class='row n2'>
      <label>標題</label>
      <div>
        <input type='text' name='title' value='<?php echo isset ($posts['title']) ? $posts['title'] : $banner->title;?>' placeholder='請輸入標題..' maxlength='200' pattern='.{1,200}' required title='輸入名稱!' autofocus />
      </div>
    </div>
    <div class='row n2'>
      <label>內容</label>
      <div>
        <textarea name='content' class='pure autosize' placeholder='請輸入內容..'><?php echo isset ($posts['content']) ? $posts['content'] : $banner->content;?></textarea>
      </div>
    </div>
    <div class='row n2'>
      <label>鏈結</label>
      <div>
        <input type='text' name='link' value='<?php echo isset ($posts['link']) ? $posts['link'] : $banner->link;?>' placeholder='請輸入鏈結..' maxlength='200' pattern='.{1,200}' required title='輸入名稱!' />
      </div>
    </div>

    <div class='row n2'>
      <label>封面</label>
      <div class='img'>

        <div class='h'><img src='<?php echo $banner->cover->url ();?>' /></div>

        <div>
          <input type='file' name='cover' />
          <button type='button' class='file'>選擇檔案</button>
        </div>

      </div>
    </div>
    <div class='row n2'>
      <label>開啟方式</label>
      <div class='radios'>
        <label>
          <input type='radio' name='target' value='<?php echo Banner::TARGET_SELF;?>' <?php echo (isset ($posts['target']) ? $posts['target'] : $banner->target) == Banner::TARGET_SELF ? ' checked' : '';?> />
          <span></span>
          本頁
        </label>
        <label>
          <input type='radio' name='target' value='<?php echo Banner::TARGET_BLANK;?>' <?php echo (isset ($posts['target']) ? $posts['target'] : $banner->target) == Banner::TARGET_BLANK ? ' checked' : '';?>/>
          <span></span>
          分頁
        </label>
      </div>
    </div>

    <div class='row n2'>
      <label>上、下架</label>
      <div>
        <label class='switch'>
          <input type='checkbox' name='is_enabled'<?php echo (isset ($posts['is_enabled']) ? $posts['is_enabled'] : $banner->is_enabled) ? ' checked' : '';?> />
          <span></span>
        </label>
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
