<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>檢視<?php echo $title;?></h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1);?>'>回列表頁</a>

  <a class='icon-bin' href='<?php echo base_url ($uri_1, $obj->id);?>' data-method='delete'>刪除</a>
  <a class='icon-pencil2' href='<?php echo base_url ($uri_1, $obj->id, 'edit');?>'>編輯</a>
</div>

<div class='panel'>
  <div class='show-type1'>

    <div class='row min'>
      <b>是否上架</b>
      <span><?php echo Article::$statusNames[$obj->status];?></span>
    </div>

    <div class='row min'>
      <b><?php echo $title;?>作者</b>
      <span><?php echo $obj->user->name;?></span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>標題</b>
      <span><?php echo $obj->title;?></span>
    </div>
    
    <div class='row'>
      <b data-title='預覽僅示意，未按比例。'><?php echo $title;?>封面</b>
      <div class='img'><img src='<?php echo $obj->cover->url ();?>' /></div>
    </div>

    
    <div class='row'>
      <b><?php echo $title;?>內容</b>
      <span><?php echo $obj->content;?></span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>分類</b>
      <span class='tags<?php echo !$obj->tags ? ' e' : '';?>'>
  <?php foreach ($obj->tags as $tag) { ?>
          <a class='tag'><?php echo $tag->name;?></a>
  <?php } ?>
      </span>
    </div>

    <div class='row muti'>
      <b><?php echo $title;?>參考</b>
      <span class='list<?php echo !$obj->sources ? ' e' : '';?>' data-cnt='2'>
  <?php foreach ($obj->sources as $source) { ?>
          <div><span><?php echo $source->title;?></span><a href='<?php echo $source->href;?>' target='_blank'><?php echo $source->href;?></a></div>
  <?php } ?>
      </span>
    </div>

  </div>
</div>

