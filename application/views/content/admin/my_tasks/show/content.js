/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
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