/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

$(function () {
  $('form.form').submit (function () {
    $('#loading .contant').text ('編譯中，請稍候..');
    $('#loading').addClass ('s');
  });
});