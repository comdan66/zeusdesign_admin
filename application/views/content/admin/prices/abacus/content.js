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
  $('.left .features').sortable ({
    connectWith: ".features",
    remove: function (event, ui) {
        ui.item.clone (true).appendTo (ui.item.parent ());
        $(this).sortable ('cancel');
        updatePrice ();
      },
    placeholder: 'feature_highlight',
  });
  $('.right .features').sortable ({
    connectWith: ".features",

    remove: function (event, ui) {
      ui.item.remove ();
      updatePrice ();
    },
    placeholder: 'feature_highlight',
  });

  $('.right .features').on ('click', '.feature a', function () {
    $(this).parents ('.feature').remove ();
    updatePrice ();
  });

  function updatePrice () {
    var moneys = $('.right .features .feature').map (function () {
      return $(this).data ('money');
    }).toArray ();


    var price = moneys.length ? moneys.reduce (function (a, b) { return a + b; }) : 0;
    $('#sum span').text (numberFormat (price));
    
  }
  // $('.calendar td').sortable ({
  //   items: 'div.edited',
  //   connectWith: 'td',
  //   update: function (e, ui) {
  //     var y = $(this).data ('y');
  //     var m = $(this).data ('m');
  //     var d = $(this).data ('d');
      
  //     updateSort ($(this).find ('div.edited').map (function (i) {
  //         return {id: $(this).data ('id'), sort: i, year: y, month: m, day: d};
  //       }).toArray ());
  //   }
  // });
});