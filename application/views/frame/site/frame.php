<!DOCTYPE html>
<html lang="zh">
  <head>
    <?php echo isset ($meta_list) ? $meta_list : ''; ?>

    <link rel="manifest" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'manifest.json');?>">
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'apple-icon-57x57.png');?>" />
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'apple-icon-60x60.png');?>" />
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'apple-icon-72x72.png');?>" />
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'apple-icon-76x76.png');?>" />
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'apple-icon-114x114.png');?>" />
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'apple-icon-120x120.png');?>" />
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'apple-icon-144x144.png');?>" />
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'apple-icon-152x152.png');?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'apple-icon-180x180.png');?>" />
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'android-icon-192x192.png');?>" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'favicon-32x32.png');?>" />
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'favicon-96x96.png');?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo res_url ('res', 'image', 'favicon', 'v3', 'favicon-16x16.png');?>" />

    <title><?php echo isset ($title) ? $title : ''; ?></title>

<?php echo isset ($css_list) ? $css_list : ''; ?>

<?php echo isset ($js_list) ? $js_list : ''; ?>

  </head>
  <body lang="zh-tw">
    <?php echo isset ($hidden_list) ? $hidden_list : ''; ?>

    <?php echo isset ($content) ? $content : ''; ?>
  </body>
</html>