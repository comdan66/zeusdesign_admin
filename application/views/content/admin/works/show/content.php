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
      <span><?php echo Work::$statusNames[$obj->status];?></span>
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
      <b>其他照片</b>
      <div class='imgs<?php echo !$obj->images ? ' e' : '';?>'>
  <?php foreach ($obj->images as $image) { ?>
          <div class='img'><img src='<?php echo $image->name->url ();?>' /></div>
  <?php }?>
      </div>
    </div>
    
    <div class='row'>
      <b><?php echo $title;?>內容</b>
      <span><?php echo $obj->content;?></span>
    </div>

    <div class='row'>
      <b><?php echo $title;?>分類</b>
      <span class='tags2d<?php echo !$obj->tags ? ' e' : '';?>'>
  <?php if ($tags = WorkTag::find ('all', array ('include' => array ('tags'), 'conditions' => array ('work_tag_id = ?', 0)))) {
          foreach ($tags as $i => $tag) { ?>
            <div class='tag'>
              <label class='main<?php echo $tag_ids && in_array ($tag->id, $tag_ids) ? ' enb' : ' dis';?>'><?php echo $tag->name;?></label>
        <?php if ($tag->tags) {
                foreach ($tag->tags as $sub_tag) { ?>
                  <label class='sub<?php echo $tag_ids && in_array ($sub_tag->id, $tag_ids) ? ' enb' : ' dis';?>'><?php echo $sub_tag->name;?></label>
          <?php }
              } ?>
            </div>
    <?php }
        } ?>
      </span>
    </div>
<?php
    foreach (WorkItem::$typeNames as $type => $typeName) { ?>
      <div class='row muti'>
        <b><?php echo $title;?> <?php echo $typeName;?></b>
        <span class='list<?php echo !$obj->typeItems ($type) ? ' e' : '';?>' data-cnt='2'>
    <?php foreach ($obj->typeItems ($type) as $item) { ?>
            <div><span><?php echo $item->title;?></span><a href='<?php echo $item->href;?>' target='_blank'><?php echo $item->href;?></a></div>
    <?php } ?>
        </span>
      </div>
<?php
    } ?>
  </div>
</div>

