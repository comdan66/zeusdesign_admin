/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

$(function () {
  // $('.panel > .table-list');
  $('.panel .table-list.header .cik').click (function () {

    $(this).parents ('.panel').toggleClass ('s').siblings ().removeClass ('s');
  });
});
