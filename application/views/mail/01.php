<div style="display: inline-block; width: 100%; background-color: #ebebeb; text-align: center; padding: 16px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;">
  <div style="display: inline-block; max-width: 600px; width: 100%; margin: 0; background-color: white; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; text-align: left; background-color: white; border: 1px solid #d7d7d7; border-bottom: 1px solid #bebebe; overflow: hidden;">
    <div style="display: block; color: #2a3f54; border-bottom: 1px solid gainsboro; padding: 16px; margin-bottom: 16px; overflow: hidden; -moz-border-radius-topleft: 3px; -webkit-border-top-left-radius: 3px; border-top-left-radius: 3px; -moz-border-radius-topright: 3px; -webkit-border-top-right-radius: 3px; border-top-right-radius: 3px;"><img style="display: block; height: 40px; margin: 0;" src="https://cdn.zeusdesign.com.tw/mail/icons/logo_v1.png"></div>
    
<?php if (!(isset ($datas) && $datas)) Mail::renderEmpty ();?>
<?php foreach ($datas as $data) {
        if (!isset ($data['type'])) continue;
        if (isset ($data['title']) && $data['title']) echo Mail::renderTitle ($data['title']);

        switch ($data['type']) {
          case 'section':
            if (isset ($data['content']) && $data['content']) echo Mail::renderSection ($data['content']);
            break;
          
          case 'ul':
            if (isset ($data['li']) && is_array ($data['li'])) echo Mail::renderUl ($data['li']);
            break;
          
          case 'ol':
            if (isset ($data['li']) && is_array ($data['li'])) echo Mail::renderOl ($data['li']);
            break;
          
          default:
            break;
        }
      }?>

    <div style="display: block; border-top: 1px solid gainsboro; padding: 16px; height: 40px; line-height: 40px; text-align: right; font-size: 13px; color: #6d7780; margin-top: 16px; -moz-border-radius-bottomleft: 3px; -webkit-border-bottom-left-radius: 3px; border-bottom-left-radius: 3px; -moz-border-radius-bottomright: 3px; -webkit-border-bottom-right-radius: 3px; border-bottom-right-radius: 3px;">Copyright by <?php echo Mail::renderLink ('ZEUS', 'https://www.zeusdesign.com.tw/');?> © 2017</div>
  </div>
</div>
