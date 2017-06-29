/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

$(function () {
  var $cursor = $('#cursor');
  var $commitInputContent = $('#commit_input_content');

  setTimeout (function () { $cursor.css ({left: $cursor.data ('val')}); }, 500);

  $('.to_commit').click (function () {
    $('body').animate ({ scrollTop: $commitInputContent.offset ().top - 88 }, 'slow', function () {
      $commitInputContent.focus ();
    });
  });
  $('form.commit').submit (function () {
    $('#loading .contant').text ('新增中，請稍候..');
    $('#loading').addClass ('s');
  });
});