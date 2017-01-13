/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */
function numberFormat (n, d, p, t){
  d = d || 0;
  n = parseFloat (n);

  if (!p || !t) { p = '.'; t = ','; }
  var r = Math.round (Math.abs ( n ) * ('1e' + d)) + '', s = d ? r.slice (0, d * -1) : r, c = d ? r.slice (d * -1) : '', m = "";
  while (s.length > 3) { m += t + s.slice (-3); s = s.slice (0,-3); }

  return (n < 0 ? '-' : '') + s + m + (c ? (p + c) : '');
}

$(function () {

  var $leftFeatures = $('.left .features');
  var $leftFeature = $leftFeatures.find ('.feature');
  var $rightFeatures = $('.right .features');
  var $sumSpan = $('#sum span');

  function updatePrice () {
    var moneys = $rightFeatures.find ('.feature').map (function () { return $(this).data ('money'); }).toArray ();
    $sumSpan.text (numberFormat (moneys.length ? moneys.reduce (function (a, b) { return a + b; }) : 0));
  }


  $leftFeatures.sortable ({
    connectWith: ".features",
    remove: function (event, ui) {
        ui.item.clone (true).appendTo (ui.item.parent ());
        $(this).sortable ('cancel');
        updatePrice ();
      },
    placeholder: 'feature_highlight',
  });

  $rightFeatures.sortable ({
    connectWith: ".features",

    remove: function (event, ui) {
      ui.item.remove ();
      updatePrice ();
    },
    placeholder: 'feature_highlight',
  });

  $rightFeatures.on ('click', '.feature a', function () {
    $(this).parents ('.feature').remove ();
    updatePrice ();
  });

  var $types = $('#types');
  $types.change (function () {
    $leftFeatures.addClass ('no');
    var $tmp = $leftFeature.removeClass ('show').filter ('.type_' + $(this).val ()).addClass ('show');
    if ($tmp.length) $leftFeatures.removeClass ('no');
  });

  $('#export').click (function () {
    var ids = $rightFeatures.find ('.feature').map (function () { return $(this).data ('id'); }).toArray ();
    window.location.assign ($(this).data ('url') + '?ids=' + ids.join (','));
  });
});