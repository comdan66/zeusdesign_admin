<input type='hidden' id='datas' data-api_month='<?php echo base_url ('admin', 'my_calendar', 'month');?>' data-api_daysort='<?php echo base_url ('admin', 'my_calendar', 'daysort');?>' data-api_day='<?php echo base_url ('admin', 'my_calendar', 'day');?>' data-api_create='<?php echo base_url ('admin', 'my-calendar');?>' data-tags='<?php echo json_encode (array_map (function ($user) { return array ('id' => $user->id, 'name' => $user->name, 'color' => $user->color); }, ScheduleTag::all (array ('select' => 'id, name, color'))));?>' data-users='<?php echo json_encode (array_map (function ($user) { return array ('id' => $user->id, 'name' => $user->name); }, User::all (array ('select' => 'id, name', 'conditions' => array ('id != ?', User::current ()->id)))));?>' />

<input type='checkbox' class='hckb' id='fix_pnl_ckb' />

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
  
  
  <div class='add s'>
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