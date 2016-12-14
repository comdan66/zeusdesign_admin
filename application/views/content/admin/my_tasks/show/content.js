/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

$(function () {
  var $cursor = $('#cursor');
  var $commitInputContent = $('#commit_input_content');

  setTimeout (function () { $cursor.css ({left: $cursor.data ('val')}); }, 500);

  $('#to_commit').click (function () {
    window.vars.$body.animate ({ scrollTop: $commitInputContent.offset ().top }, 'slow', function () {
      $commitInputContent.focus ();
    });
  });
});