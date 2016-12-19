/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

$(function () {
  $('.icons a').click (function () {
    $(this).parents ('figure').remove ();
  });
  
  $('form.form').submit (function () {
    $('#loading .contant').text ('處理中，請稍候..');
    $('#loading').addClass ('s');
  });
});