
<div class='search'>

  <div class='left'>
    <h2 class='icon-calendar2'> 我的行事曆</h2>
  </div>
  <div class='right'>
    <a class='icon-price-tags' href='<?php echo base_url ('admin', 'my-schedule-tags');?>'> 分類管理</a>
  </div>
</div>

<input type='checkbox' class='hckb' id='fix_pnl_ckb'  />

<div class='panel'>
  
  <div class='calendar' data-url='<?php echo base_url ('admin', 'my_calendar', 'ajax');?>'>
    <div class='title'>
      <div class='now'></div>
      <div class='arr'><a></a><a></a><a></a></div>
    </div>
    <div class='months'></div>
  </div>

</div>

<div id='fix-panel'>
  <header><a class='icon-keyboard_arrow_left'></a><span>本日活動</span><a class='icon-r'></a></header>
  
  
  <div class='content'></div>
  <div class='content'>
      
      <span>標題</span>
      <div class='row'>
        asd
      </div>

      <span>待辦事項</span>
      <div class='row mtckbs'>
          
        <div>
          <label class='checkbox'>
            <input type='checkbox'>
            <span></span>
          </label>
          <span>asddassd</span>
        </div>

        <div>
          <label class='checkbox'>
            <input type='checkbox'>
            <span></span>
          </label>
          <span>asddassd</span>
        </div>
        <div>
          <label class='checkbox'>
            <input type='checkbox'>
            <span></span>
          </label>
          <span>asddassd</span>
        </div>
        <div>
          <label class='checkbox'>
            <input type='checkbox'>
            <span></span>
          </label>
          <span>asddassd</span>
        </div>
        <div>
          <label class='checkbox'>
            <input type='checkbox'>
            <span></span>
          </label>
          <span>asddassd</span>
        </div>
        <div>
          <label class='checkbox'>
            <input type='checkbox'>
            <span></span>
          </label>
          <span>asddassd</span>
        </div>
        <div>
          <label class='checkbox'>
            <input type='checkbox'>
            <span></span>
          </label>
          <span>asddassd</span>
        </div>
        <div>
          <label class='checkbox'>
            <input type='checkbox'>
            <span></span>
          </label>
          <span>asddassd</span>
        </div>
        <div>
          <label class='checkbox'>
            <input type='checkbox'>
            <span></span>
          </label>
          <span>asddassd</span>
        </div>

      </div>

      <span>行程分類</span>
      <div class='row tag'>
        asad
      </div>

      <span>分享對象</span>
      <div class='row users'>
        <div><img src='https://graph.facebook.com/1222557214424285/picture?width=100&height=100'><span>OAOAOAOAOAOAOAOAOAOAOA</span></div>
        <div><img src='https://graph.facebook.com/1222557214424285/picture?width=100&height=100'><span>OA</span></div>
        <div><img src='https://graph.facebook.com/1222557214424285/picture?width=100&height=100'><span>OA</span></div>
        <div><img src='https://graph.facebook.com/1222557214424285/picture?width=100&height=100'><span>OA</span></div>
      </div>

      <span>備註</span>
      <div class='row'>
        asad
      </div>
  </div>
  
  <div class='add'>
    <form>
      <div class='note'></div>
      <div class='row'>
        <b class='need'>行程標題</b>
        <input type='text' name='title' value='' placeholder='請輸入行程標題..' maxlength='200' pattern='.{1,200}' required title='輸入行程標題!' />
      </div>

      <div class='row'>
        <b>待辦事項</b>
        
        <div class='mtckbs'>
          
          <div>
            <label class='checkbox'>
              <input type='checkbox'>
              <span></span>
            </label>

            <input type='text' name='title' value='' placeholder='請輸入待辦事項..' maxlength='200' pattern='.{1,200}' required title='輸入標題!' />

            <a></a>
          </div>

          <span>
            <a></a>
          </span>

        </div>

      </div>

      <div class='row'>
        <b>行程分類</b>
        <div class='rdos'>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
          <label class='radio'>
            <input type='radio' name='tag'>
            <span></span>asd
          </label>
        </div>
      </div>

      <div class='row'>
        <b data-title='他們只能看，不能修改。'>分享對象</b>
        <div class='ckbs'>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
          <label class='checkbox'>
            <input type='checkbox' name='tag'>
            <span></span>asd
          </label>
        </div>
      </div>

      <div class='row'>
        <b>備註</b>
        <textarea class='pure' name='content' placeholder='請輸入備註..'></textarea>
      </div>
    </form>
  </div>
</div>
<label></label>