/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

$(function () {
  var _ids = [];
  var $_tl = $('#_tl');
  var $_tb = $('#_tb');
  var $_pgn = $('#_pgn');
  var $_no = $('#_no');
  var $_fm = $('#_fm');

  function loadData (url) {
    
    $.ajax ({
      url: url,
      data: {
        title: $('.search .conditions input[name="title"]').val (),
        status: $('.search .conditions select[name="status"]').val (),
        user_ids: $('.search .conditions input[name="user_ids"]:checked').map (function () { return $(this).val (); }).toArray (),
        pms: $('.search .conditions input[name="pms"]:checked').map (function () { return $(this).val (); }).toArray (),
      },
      async: true, cache: false, dataType: 'json', type: 'POST'
    })
    .done (function (r) {
      var $pgn = $(r.pagination);
      $pgn.find ('a[href^="http"]').click (function () {
        loadData ($(this).attr ('href'));
        return false;
      });
      $_pgn.empty ().append ($pgn);

      $_tl.text (r.total);
      
      $_tb.empty ().append (r.objs.length ? r.objs.map (function (t) {
        return $('<tr />').append (
          $('<td />').addClass ('center').append (
            !t.status ? $('<label />').addClass ('checkbox').append (
            $('<input />').attr ('type', 'checkbox').attr ('name', 'ids').val (t.id).prop ('checked', $.inArray ('' + t.id, _ids) !== -1).change (function () {
              if ($(this).prop ('checked')) _ids.push ($(this).val ());
              else _ids = _ids.filter (function (t) { return t != $(this).val (); }.bind ($(this)));

              if (_ids.length) $_no.addClass ('s').find ('b').text (_ids.length);
              else $_no.removeClass ('s');
            })).append (
            $('<span />'))
            : null)).append (
          $('<td />').addClass ('center').text (t.status ? '已請款' : '未請款').css ({'color': t.status ? 'rgba(52, 168, 83, 1.00)' : 'rgba(234, 67, 53, 1.00)'})).append (
          $('<td />').addClass ('center').append (t.srcs.length ? $('<div />').addClass ('oaips').append (t.srcs.map (function (s) {
            return $('<div />').addClass ('oaip').data ('data-src', s.ori).append ($('<img />').attr ('src', s.w800)).imgLiquid ({verticalAlign: 'center'});
          })) : null)).append (
          $('<td />').addClass ('left').text (t.title)).append (
          $('<td />').addClass ('left').text (t.user)).append (
          $('<td />').addClass ('left').append ($('<div />').addClass ('row').text (t.pm)).append ($('<div />').addClass ('row').addClass ('sub').text (t.company))).append (
          $('<td />').append (t.detail.map (function (u) {
            return $('<div />').addClass ('row').addClass (u.status ? 'finish' : '').text (u.user + ' / ' + u.money + '元');
          }))).append (
          $('<td />').text (t.money + '元')).append (
          $('<td />').text (t.close_date)).append (
          $('<td />').addClass ('edit')
            .append ($('<a />').addClass ('icon-eye').attr ('href', t.links.show))
            .append (t.status ? $('<a />').addClass ('icon-bil').attr ('href', t.links.income).attr ('target', '_blank') : null)
            .append (!t.status ? $('<a />').addClass ('icon-pencil2').attr ('href', t.links.edit) : null)
            .append (!t.status ? $('<a />').addClass ('icon-bin').attr ('href', t.links.delete).attr ('data-method', 'delete').click (function () {
              if (!confirm ('確定要刪除？')) return false;
            }) : null));
      }) : $('<tr />').append ($('<td />').attr ('colspan', 10).text ('沒有任何資料。')));

      $('#_tb .oaips').each (function () {
        if ($(this).find ('>*').length > 1)
          $(this).attr ('data-cnt', $(this).find ('>*').length);
      });
      window.fns.oaips ('#_tb .oaips', '.oaip');
    })
    .fail (window.fns.ajaxFail)
    .complete (function () {
    });
  }

  $('.search .btns button').click (function () {
    _ids = [];
    if (_ids.length) $_no.addClass ('s').find ('b').text (_ids.length);
    else $_no.removeClass ('s');
    loadData ($(this).data ('url'));
  }).click ();

  $_no.submit (function () {
    $(this).append (_ids.map (function (t) {
      return $('<input />').attr ('type', 'hidden').attr ('name', 'ids[]').val (t);
    }));
  });

  $('label.all').click (function () {
    $_tb.find ('label.checkbox input').prop ('checked', $(this).find ('input').prop ('checked'));
  });
});
