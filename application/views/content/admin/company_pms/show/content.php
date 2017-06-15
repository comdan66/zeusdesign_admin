<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>檢視<?php echo $title;?></h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo base_url ($uri_1, $parent->id, $uri_2);?>'>回列表頁</a>

  <a class='icon-bin' href='<?php echo base_url ($uri_1, $parent->id, $uri_2, $obj->id);?>' data-method='delete'>刪除</a>
  <a class='icon-pencil2' href='<?php echo base_url ($uri_1, $parent->id, $uri_2, $obj->id, 'edit');?>'>編輯</a>
</div>

<div class='panel'>
  <div class='show-type1 left'>


    <div class='row'>
      <b>名稱</b>
      <span><?php echo $obj->name;?></span>
    </div>

    <div class='row'>
      <b>分機</b>
      <span><?php echo $obj->extension;?></span>
    </div>

<?php
    foreach (CompanyPmItem::$typeNames as $type => $typeName) { ?>
      <div class='row muti'>
        <b><?php echo $typeName;?></b>
        <span class='list<?php echo !$obj->typeItems ($type) ? ' e' : '';?>' data-cnt='2'>
    <?php foreach ($obj->typeItems ($type) as $item) { ?>
            <div><span><?php echo $item->content;?></span></div>
    <?php } ?>
        </span>
      </div>
<?php
    } ?>

    <div class='row'>
      <b>個性、合作心得</b>
      <span><?php echo $obj->experience;?></span>
    </div>

    <div class='row'>
      <b>備註</b>
      <span><?php echo $obj->memo;?></span>
    </div>
    

  </div>
</div>

