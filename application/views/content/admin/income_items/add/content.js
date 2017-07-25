/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

(function (original) {
  jQuery.fn.clone = function () {
    var result           = original.apply(this, arguments),
        my_textareas     = this.find('textarea').add(this.filter('textarea')),
        result_textareas = result.find('textarea').add(result.filter('textarea')),
        my_selects       = this.find('select').add(this.filter('select')),
        result_selects   = result.find('select').add(result.filter('select'));

    for (var i = 0, l = my_textareas.length; i < l; ++i) $(result_textareas[i]).val($(my_textareas[i]).val());
    for (var j = 0, k = my_selects.length;   j < k; ++j) result_selects[j].selectedIndex = my_selects[j].selectedIndex;

    return result;
  };
}) (jQuery.fn.clone);

$(function () {
  var _mid = $('#_mid').val ();

  function mutiCol ($obj) {
    $obj.each (function () {
      var that = this,
          $row = $(this),
          $span = $row.find ('>span'),
          $b = $row.find ('>b');

      $row.data ('i', 0);

      that.fm = function (i, t) {
        return $('<div />').append (
          $('<div />').append (Array.apply (null, Array ($row.data ('attrs').length)).map (function (_, j) {
            return $('<a />').text ($row.data ('attrs')[j].need ? '＊' : '');
          }))).append ($('<div />').append (Array.apply (null, Array ($row.data ('attrs').length)).map (function (_, j) {
              if ($row.data ('attrs')[j].el == 'select') {
                return $('<select />').attr ('name', $row.data ('attrs')[j].name + '[' + i + ']' + ($row.data ('attrs')[j].key ? '[' + $row.data ('attrs')[j].key + ']' : ''))
                                     .attr ('class', $row.data ('attrs')[j].class ? $row.data ('attrs')[j].class : null).append (
                                      $row.data ('attrs')[j].options ? Array.apply (null, Array ($row.data ('attrs')[j].options.length)).map (function (_, k) {
                                        return $('<option />').attr ('value', $row.data ('attrs')[j].options[k].value)
                                                              .prop ('selected', (t ? $row.data ('attrs')[j].key && typeof t[$row.data ('attrs')[j].key] !== 'undefined' ? t[$row.data ('attrs')[j].key] : (typeof t === 'object' ? _mid : t) : _mid) == $row.data ('attrs')[j].options[k].value)
                                                              .text ($row.data ('attrs')[j].options[k].text);

                                      }) : null);
              } else if ($row.data ('attrs')[j].el == 'input') {
                var ty = $row.data ('attrs')[j].type ? $row.data ('attrs')[j].type : null;
                return $('<input />').attr ('type', $row.data ('attrs')[j].type ? $row.data ('attrs')[j].type : null)
                                     .attr ('name', $row.data ('attrs')[j].name + '[' + i + ']' + ($row.data ('attrs')[j].key ? '[' + $row.data ('attrs')[j].key + ']' : ''))
                                     .attr ('placeholder', $row.data ('attrs')[j].placeholder ? $row.data ('attrs')[j].placeholder : null)
                                     .attr ('class', $row.data ('attrs')[j].class ? $row.data ('attrs')[j].class : null)
                                     .val (t ? $row.data ('attrs')[j].key && typeof t[$row.data ('attrs')[j].key] !== 'undefined' ? t[$row.data ('attrs')[j].key] : (typeof t === 'object' ? '' : t) : '')
                ;
              } else {
                return null;
              }
            }))).append (
          $('<a />').click (function () { $(this).parent ().remove (); }));
      };

      if ($row.data ('vals'))
        $row.data ('vals').forEach (function (t) {
          that.fm ($row.data ('i'), t).insertBefore ($span);
          $row.data ('i', parseInt ($row.data ('i'), 10) + 1);
        });

      $span.find ('a').click (function () {
        var $t = that.fm ($row.data ('i')).insertBefore ($span);
        $row.data ('i', parseInt ($row.data ('i'), 10) + 1);
        setTimeout (function () { $t.find ('input').first ().focus (); }, 100);
      });

      if (!$row.find ('>div').length) {
        that.fm ($row.data ('i')).insertBefore ($span);
        $row.data ('i', parseInt ($row.data ('i'), 10) + 1);
      }
      
    });
  }

  mutiCol ($('form .row.muti2'));

  $('form .row.muti2').on ('keyup', '._q', function () {
    if (isNaN ($(this).val ()) || $(this).val () === '' || isNaN ($(this).next ().val ()) || $(this).next ().val () === '') return false;
    $(this).next ().next ().val ($(this).val () * $(this).next ().val ());
  });

  $('form .row.muti2').on ('keyup', '._s', function () {
    if (isNaN ($(this).val ()) || $(this).val () === '' || isNaN ($(this).prev ().val ()) || $(this).prev ().val () === '') return false;
    $(this).next ().val ($(this).val () * $(this).prev ().val ());
  });
});
