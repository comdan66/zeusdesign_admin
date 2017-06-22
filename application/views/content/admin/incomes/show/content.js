/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

$(function () {

  $('.float .switch.ajax[data-column][data-url]').each (function () {
    var $that = $(this), column = $that.data ('column'), url = $that.data ('url'), $inp = $that.find ('input[type="checkbox"]');

    $inp.click (function () {
      var data = {};
      data[column] = $(this).prop ('checked') ? 1 : 0;

      $that.addClass ('loading');

      $.ajax ({
        url: url,
        data: data,
        async: true, cache: false, dataType: 'json', type: 'POST'
      })
      .done (function (result) {
        $that.removeClass ('loading');
        $(this).prop ('checked', result);
        window.fns.updateCounter ($that.data ('forcntrole'), result);

      }.bind ($(this)))
      .fail (function (result) {
        $that.removeClass ('loading');
        $(this).prop ('checked', !data[column]);
        if ((t = window.fns.IsJsonString (result.responseText)) !== null) window.fns.tipText ({title: '設定錯誤！', message: t.message});
        else window.fns.tipText ({title: '設定錯誤！', message: '※ 不明原因錯誤，請重新整理網頁確認。', error: result.responseText});
      }.bind ($(this)));
    });
  });


});