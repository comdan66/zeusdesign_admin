/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

$(function () {
  // var copyTextareaBtn = document.querySelector('.js-textareacopybtn');

  // copyTextareaBtn.addEventListener('click', function(event) {
  //   
  // });
  $('#copy').click (function () {
    var copyTextarea = document.querySelector('#url');
    copyTextarea.select();

    try {
      var successful = document.execCommand ('copy');
      var msg = successful ? 'successful' : 'unsuccessful';
      window.fns.tipText ({title: '複製成功', message: '※ 已經成功複製網址！'});
    } catch (err) {
    }

  });
});