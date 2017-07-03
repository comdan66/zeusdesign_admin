<header style='background-image:url("<?php echo $obj->banner ('1200x630c');?>")'>
  <div class='avatar _ic'>
    <img src='<?php echo $obj->avatar ();?>' />
  </div>

  <div class='info'>
    <h1><?php echo $obj->name;?></h1>
    <div>
      <a href="mailto:<?php echo $obj->email;?>" class='icon-em' target='_blank'></a>
      <?php if ($obj->set && $obj->set->link_facebook) { ?><a href="<?php echo $obj->set->link_facebook;?>" class='icon-facebook' target='_blank'></a><?php }?>
      <?php if ($obj->set && $obj->set->link_line) { ?><a href="<?php echo $obj->set->link_line;?>" class='icon-brand' target='_blank'></a><?php }?>
      <?php if ($obj->set && $obj->set->link_google) { ?><a href="<?php echo $obj->set->link_google;?>" class='icon-google-plus' target='_blank'></a><?php }?>
    </div>
  </div>

  <div class='bottom' data-title='擁有權限：'>
<?php
    if ($obj->roles) {
      foreach ($obj->roles as $role) { ?>
        <span><?php echo $role->name ();?></span>
<?php }
    } else { ?>
      <span>無任何權限</span> 
<?php
    } ?>
  </div>
  <a class='setting icon-se' href=''></a>
</header>

<div class='block1'>
  <div>
    <span class='title'><span>各個數據</span></span>
    <div class='units'>
      <a href='' class='icon-shield'>
        <span class='count-up' data-unit='個'><?php echo Task::count (array ('conditions' => array ('user_id = ? AND status = ?', $obj->id, Task::STATUS_2)));?></span>
        <span>未完成任務</span>
      </a>
      <a href='' class='icon-calendar2'>
        <span class='count-up' data-unit='件'><?php echo Schedule::count (array ('conditions' => array ('user_id = ?', $obj->id)));?></span>
        <span>今日行程</span>
      </a>
      <a href='' class='icon-moneybag'>
        <span class='count-up' data-unit='元'><?php echo array_sum (array_map (function ($zb) { return $zb->pay (); }, Zb::find ('all', array ('select' => 'id, income_id, percentage', 'conditions' => array ('user_id = ?', $obj->id)))));?></span>
        <span>累積宙思幣</span>
      </a>
      <a href='' class='icon-enter'>
        <span class='count-up' data-unit='次'><?php echo $obj->login_count;?></span>
        <span>登入次數</span>
      </a>
      <a href='' class='icon-file-text2'>
        <span class='count-up' data-unit='篇'><?php echo Article::count (array ('conditions' => array ('user_id = ?', $obj->id)));?></span>
        <span>我的文章數</span>
      </a>
      <a href='' class='icon-g'>
        <span class='count-up' data-unit='件'><?php echo Work::count (array ('conditions' => array ('user_id = ?', $obj->id)));?></span>
        <span>我的作品數</span>
      </a>
    </div>
    <span class='title'><span>後台操作紀錄</span></span>
    <div class='calendar'>
<?php for ($i = 52; $i > 0; $i--) { ?>
        <div>
    <?php for ($j = 7; $j > 0; $j--) {
            $day = ($i - 1) * 7 + ($j - 1) - (6 - date ('w'));
            $y = date ('Y', $t = strtotime (date ('Y-m-d') . ' -' . $day . ' days'));
            $d = date ('d', $t);
            $m = date ('m', $t);
            $log = isset ($logs[$y . '-' . $m . '-' . $d]) ? $logs[$y . '-' . $m . '-' . $d] : false?>
            <div<?php echo $log ? ' class="' . $log['s'] . '"' : '';?> data-month='<?php echo $m;?>' data-day='<?php echo $d;?>' title='<?php echo $y . '.' . $m . '.' . $d;?> 操作了 <?php echo $log ? $log['cnt'] : 0;?> 次' data-cnt="<?php echo $log ? $log['cnt'] : 0;?>"></div>
    <?php } ?>
        </div>
<?php } ?>
    </div>
  </div>

  <div>
    <span class='title'><span>PTT 大事</span><a href="">更多..</a></span>
    <div class='ptt'>
      <!-- <a href="" class='icon-se'>立馬訂閱</a> -->
      <a href="">
        <span class='b'>爆</span>
        <span>[相助] 有意想不到的貴人相助</span>
        <span>ASddsfsSDFSdf</span>
      </a>
      <a href="">
        <span class='s'>噓</span>
        <span>[相助] 有意想不到的貴人相助</span>
      </a>
      <a href="">
        <span>12</span>
        <span>[相助] 有意想不到的貴人相助有意想不到的貴人相助有意想不到的貴人相助有意想不到的貴人相助</span>
        <span>ASddsfsSDFSdf</span>
      </a>
      <a href="">
        <span>12</span>
        <span>[相助] 有意想不到的貴人相助</span>
        <span>ASddsfsSDFSdf</span>
      </a>
      <a href="">
        <span>12</span>
        <span>[相助] 有意想不到的貴人相助</span>
        <span>ASddsfsSDFSdf</span>
      </a>
      <a href="">
        <span>12</span>
        <span>[相助] 有意想不到的貴人相助</span>
      </a>
      <a href="">
        <span>12</span>
        <span>[相助] 有意想不到的貴人相助</span>
      </a>
      <a href="">
        <span>12</span>
        <span>[相助] 有意想不到的貴人相助</span>
      </a>

    </div>
  </div>
</div>

<span class='title'><span>星座運勢</span></span>
<div class='constellation'>
  <!-- <a href="" class='icon-se'>設定生日後就有星座運勢囉！</a> -->
  <article>
    <p><span>巨蟹座</span>2017年會有意想不到的貴人相助，也應該主動對別人伸出援手。無聊時打開電視，會發現找很久的訊息。今年最好的方位在北方，尤其是對於財運的影響更大。今年的處女座，最幸運的顏色是銀色，尤其是銀色的雜誌能為你帶來好運。今年事業上會有好的變化。今年好運令你驚訝？確實現在是你運氣最旺的時候。盡可能從事你有把握的事情，才能夠發揮你的好運，至於無法掌握的事情就別碰了，留給別人去操心吧！處女座的你，身體反應了你的情緒，不適合喝酒，遠離酒精對健康更好。</p>
  </article>
  <time>- 2017.01.02</time>
</div>

<div class='block2'>
  <div>
    <span class='title'><span>今日行程</span></span>
    
    <div class='list'>
      <a href="">
        <img src="<?php echo $obj->avatar ();?>">
        <span data-sub='有意想不到的貴有意想不到的貴'>有意想不到的貴人相助，有意想不到的貴人相助，</span>
      </a>
      <a href="">
        <img src="<?php echo $obj->avatar ();?>">
        <span data-sub='asd'>asdsadsadsa</span>
      </a>
      <a href="">
        <img src="<?php echo $obj->avatar ();?>">
        <span>asdsadsadsa</span>
      </a>
      <a href="">
        <img src="<?php echo $obj->avatar ();?>">
        <span data-sub='asd'>asdsadsadsa</span>
      </a>
    </div>
  </div>
  <div>
    <span class='title'><span>操作記錄</span><a href="">更多..</a></span>
    
    <div class='logs'>
      <h3>今天</h3>

      <div class='icon-home'>今天今天今天今天</div>
      <span>asdasdasdasddsasdasdasdasddsasdasdasdasddsasdasdasdasddsasdasdasdasddsasdasdasdasddsasdasdasdasdds</span>
      <span>asdasdasdasdds</span>
      <div class='icon-home'>asdasdasdas</div>
      <div class='icon-home'>asdasdasdas</div>
      <span>asdasdasdasdds</span>
      <div class='icon-home'>asdasdasdas</div>

    </div>

  </div>
</div>

<!-- 

      <article>
        <p>2017年會有意想不到的貴人相助，也應該主動對別人伸出援手。無聊時打開電視，會發現找很久的訊息。今年最好的方位在北方，尤其是對於財運的影響更大。今年的處女座，最幸運的顏色是銀色，尤其是銀色的雜誌能為你帶來好運。今年事業上會有好的變化。今年好運令你驚訝？確實現在是你運氣最旺的時候。盡可能從事你有把握的事情，才能夠發揮你的好運，至於無法掌握的事情就別碰了，留給別人去操心吧！處女座的你，身體反應了你的情緒，不適合喝酒，遠離酒精對健康更好。</p>
        <p>2017年會有意想不到的貴人相助，也應該主動對別人伸出援手。無聊時打開電視，會發現找很久的訊息。今年最好的方位在北方，尤其是對於財運的影響更大。今年的處女座，最幸運的顏色是銀色，尤其是銀色的雜誌能為你帶來好運。今年事業上會有好的變化。今年好運令你驚訝？確實現在是你運氣最旺的時候。盡可能從事你有把握的事情，才能夠發揮你的好運，至於無法掌握的事情就別碰了，留給別人去操心吧！處女座的你，身體反應了你的情緒，不適合喝酒，遠離酒精對健康更好。</p>
        <p>2017年會有意想不到的貴人相助，也應該主動對別人伸出援手。無聊時打開電視，會發現找很久的訊息。今年最好的方位在北方，尤其是對於財運的影響更大。今年的處女座，最幸運的顏色是銀色，尤其是銀色的雜誌能為你帶來好運。今年事業上會有好的變化。今年好運令你驚訝？確實現在是你運氣最旺的時候。盡可能從事你有把握的事情，才能夠發揮你的好運，至於無法掌握的事情就別碰了，留給別人去操心吧！處女座的你，身體反應了你的情緒，不適合喝酒，遠離酒精對健康更好。</p>
      </article>
<div class='block b1'>
  <div>
    <span class='icon-home'></span>
    <span class='count-up'>1234</span>
    <span>專案數目</span>
  </div>
  <div> 
    <span class3#='icon-home'></span>
    <span class='count-up'>1234</span>
    <span>專案數目</span>
  </div>
  <div>
    <span class='icon-home'></span>
    <span class='count-up'>1234</span>
    <span>專案數目</span>
  </div>
  <div>
    <span class='icon-home'></span>
    <span class='count-up'>1234</span>
    <span>專案數目</span>
  </div>
</div>

<div class='block b4'>
  <div>
    <h2>後台紀錄</h2>
    <div class='calendar2'>
      <div><div class='s0'></div><div class='s1'></div><div class='s2'></div><div class='s3'></div><div class='s4'></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      <div><div></div><div></div><div></div><div></div><div></div><div class='s3' data-month='5' data-day='4'></div><div class='s2' data-month='5' data-day='5'></div></div>
      <div><div class='s4' data-month='5' data-day='6'></div><div class='s0' data-month='5' data-day='7'></div><div class='s3' data-month='5' data-day='8'></div><div class='s1' data-month='5' data-day='9'></div><div class='s2' data-month='5' data-day='10'></div><div class='s3' data-month='5' data-day='11'></div><div class='s4' data-month='5' data-day='12'></div></div>
    </div>
    <div class='desc'>
      <i>較低</i>
      <span class='s0'></span>
      <span class='s1'></span>
      <span class='s2'></span>
      <span class='s3'></span>
      <span class='s4'></span>
      <i>較高</i>
    </div>
  </div>
</div>
<div class='block b2'>
  <div>
    <h2>星座運勢</h2>
    <p>2017年會有意想不到的貴人相助，也應該主動對別人伸出援手。無聊時打開電視，會發現找很久的訊息。今年最好的方位在北方，尤其是對於財運的影響更大。今年的處女座，最幸運的顏色是銀色，尤其是銀色的雜誌能為你帶來好運。今年事業上會有好的變化。今年好運令你驚訝？確實現在是你運氣最旺的時候。盡可能從事你有把握的事情，才能夠發揮你的好運，至於無法掌握的事情就別碰了，留給別人去操心吧！處女座的你，身體反應了你的情緒，不適合喝酒，遠離酒精對健康更好。72017年會有意想不到的貴人相助，也應該主動對別人伸出援手。無聊時打開電視，會發現找很久的訊息。今年最好的方位在北方，尤其是對於財運的影響更大。今年的處女座，最幸運的顏色是銀色，尤其是銀色的雜誌能為你帶來好運。今年事業上會有好的變化。今年好運令你驚訝？確實現在是你運氣最旺的時候。盡可能從事你有把握的事情，才能夠發揮你的好運，至於無法掌握的事情就別碰了，留給別人去操心吧！處女座的你，身體反應了你的情緒，不適合喝酒，遠離酒精對健康更好。7 這個數字會給你帶來好運。說到大家都關心的財運，對於處女座的你而言，最近投資可能會有好收穫，但還是理性為上，不要僥倖投機。</p>
    <a href="">閱讀更多..</a>
  </div>

  <div>
    <h2>Ptt 大事</h2>

    <div>
      <span>12</span>
      <span>[相助] 有意想不到的貴人相助</span>
      <a href='' class='icon-home'></a>
    </div>
    <div>
      <span>12</span>
      <span>[相助] 有意想不到的貴人相助</span>
      <a href='' class='icon-home'></a>
    </div>
    <div>
      <span>12</span>
      <span>[相助] 有意想不到的貴人相助</span>
      <a href='' class='icon-home'></a>
    </div>
    <div>
      <span>12</span>
      <span>[相助] 有意想不到的貴人相助</span>
      <a href='' class='icon-home'></a>
    </div>
    <div>
      <span>12</span>
      <span>[相助] 有意想不到的貴人相助</span>
      <a href='' class='icon-home'></a>
    </div>
    <div>
      <span>12</span>
      <span>[相助] 有意想不到的貴人相助</span>
      <a href='' class='icon-home'></a>
    </div>
    <a href="">閱讀更多..</a>
  </div>
</div>

<div class='block b3'>
  <div>
    <h2>表格資料</h2>
    <table class='table-list'>
      <thead>
        <tr>
          <th width='70'>#</th>
          <th >標題 4</th>
          <th width='100'>編輯</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1234</td>
          <td>44444444444444444</td>
          <td>
            <a href="" class='icon-pencil2'></a>
            /
            <a href="" class='icon-bin'></a>
          </td>
        </tr>
        
        <tr>
          <td>1234</td>
          <td>44444444444444444</td>
          <td>
            <a href="" class='icon-pencil2'></a>
            /
            <a href="" class='icon-bin'></a>
          </td>
        </tr>
        
        <tr>
          <td>1234</td>
          <td>44444444444444444</td>
          <td>
            <a href="" class='icon-pencil2'></a>
            /
            <a href="" class='icon-bin'></a>
          </td>
        </tr>
        
        
        
      </tbody>
    </table>

  </div>
</div>

 -->