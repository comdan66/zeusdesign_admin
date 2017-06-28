/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a(jQuery)}(function(a){a.fn.extend({OAdropUploadImg:function(b){var c={},d=function(b){e(b),a(this).attr("data-loading","讀取中..").removeClass("no")},e=function(b){a(this).removeAttr("data-loading").addClass("no"),b.attr("src","")},f=function(a,b,c){var d=new Image;d.src=a.target.result,d.onload=function(){_vmxw=1024;var a=document.createElement("canvas");6==c||8==c?(a.height=d.width,a.width=d.height):(a.width=d.width,a.height=d.height),Math.max(a.width,a.height)>_vmxw&&(a.width>a.height?(a.height=_vmxw/a.width*a.height,a.width=_vmxw):(a.width=_vmxw/a.height*a.width,a.height=_vmxw)),3==c?(a.getContext("2d").transform(-1,0,0,-1,a.width,a.height),a.getContext("2d").drawImage(d,0,0,a.width,a.height)):6==c?(a.getContext("2d").transform(0,1,-1,0,a.width,0),a.getContext("2d").drawImage(d,0,0,a.height,a.width)):8==c?(a.getContext("2d").transform(0,-1,1,0,0,a.height),a.getContext("2d").drawImage(d,0,0,a.height,a.width)):a.getContext("2d").drawImage(d,0,0,a.width,a.height),b(a)}},g=function(a,b){var c=new FileReader;c.onload=function(a){var c=new DataView(a.target.result);if(65496!=c.getUint16(0,!1))return f(this,b,-2);for(var d=c.byteLength,e=2;e<d;){var g=c.getUint16(e,!1);if(e+=2,65505==g){if(1165519206!=c.getUint32(e+=2,!1))return f(this,b,-1);var h=18761==c.getUint16(e+=6,!1);e+=c.getUint32(e+4,h);var i=c.getUint16(e,h);e+=2;for(var j=0;j<i;j++)if(274==c.getUint16(e+12*j,h))return f(this,b,c.getUint16(e+12*j+8,h))}else{if(65280!=(65280&g))break;e+=c.getUint16(e,!1)}}return f(this,b,-1)}.bind(this),c.readAsArrayBuffer(a.slice(0,65536))},h=function(b,c){var d=a(this),e=new FileReader;e.onload=function(a){g.bind(a,c,function(a){b.attr("src",a.toDataURL()).load(function(){d.removeAttr("data-loading")})})()},e.readAsDataURL(c)},i=function(b){var c=a(this),f=c.find("img"),g=c.find('input[type="file"]').change(function(){d.bind(c,f)(),a(this).val().length&&a(this).get(0).files&&a(this).get(0).files[0]?h.bind(c,f,a(this).get(0).files[0])():e.bind(c,f)(),a(this).css({top:0,left:0})});f.attr("src").length||c.addClass("no"),c.bind("dragover",function(b){b.stopPropagation(),b.preventDefault(),a(this).addClass("ho"),g.offset({top:b.originalEvent.pageY-15,left:b.originalEvent.pageX-10})}).bind("dragleave",function(b){b.stopPropagation(),b.preventDefault(),a(this).removeClass("ho")}).bind("drop",function(b){a(this).removeClass("ho")})};return a(this).each(function(){i.bind(a(this))(a.extend(!0,c,b))}),a(this)}})});


$(function () {
  window.fns = {};

  var $tipTexts = $('#tip_texts');
  var $loading = $('#loading');
  var $menuCkb = $('#menu_ckb');
  $('._ic').imgLiquid ({verticalAlign: 'center'});
  $('time[datetime]').timeago ();

  var $group = { div: $('.group > div'), span: $('.group > span') };
  $group.div.find (' > a.show').each (function () { $(this).parent ().prev ().addClass ('show'); });
  $group.div.each (function () { $(this).addClass ('n' + $(this).find ('> a').length); });
  $group.span.click (function () { $(this).toggleClass ('show'); });
  setTimeout (function () { $group.span.addClass ('t'); }, 500);

  $('.drop_img').OAdropUploadImg ();
  window.fns.IsJsonString = function (str) { try { return JSON.parse (str); } catch (e) { return null; } };

  window.fns._fsg = function (key) { return ((typeof (Storage) !== 'undefined') && (value = localStorage.getItem (key)) && (value = JSON.parse (value))) ? value : undefined; };
  window.fns._fss = function (key, data) { try { if (typeof (Storage) !== 'undefined') { localStorage.setItem (key, JSON.stringify (data)); return true; } return false; } catch (err) { console.error ('Set storage failure.', error); return false; } };
  
  if ($(window).width () > 750) $menuCkb.prop ('checked', window.fns._fsg ('zeus.menu') ? true : false);
  $menuCkb.click (function () { if ($(window).width () > 750) window.fns._fss ('zeus.menu', $(this).prop ('checked')); });
  setTimeout (function () { $('body').addClass ('ani'); }, 300);
  
  window.fns.mutiImg = function ($obj) {
    if ($obj.length <= 0) return;
    $obj.on ('click', '.drop_img > a', function () {
      var $parent = $(this).parent ();
      $parent.remove ();
    });

    $obj.on ('change', '.drop_img > input[type="file"]', function () {
      if (!$(this).val ().length) return;

      var $parent = $(this).parent ();
      $parent.find ('input[type="hidden"]').remove ();

      if ($obj.find ('>.drop_img').last ().hasClass ('no')) return;
      var $n = $parent.clone ().removeAttr ('data-loading').addClass ('no');
      $n.find ('img').attr ('src', '');
      $n.find ('input').val ('');
      $n.OAdropUploadImg ().insertAfter ($parent);
    });
  };
  window.fns.mutiImg ($('.drop_imgs'));

  
  $('textarea.cke').ckeditor ({
    filebrowserUploadUrl: $('#filebrowserUploadUrl').val (),
    filebrowserImageBrowseUrl: $('#filebrowserImageBrowseUrl').val (),
    skin: 'oa',
    height: 300,
    resize_enabled: false,
    removePlugins: 'elementspath',
    toolbarGroups: [{ name: '1', groups: [ 'mode', 'tools', 'links', 'basicstyles', 'colors', 'insert', 'list' ] }],
    removeButtons: 'Strike,Underline,Italic,Table,HorizontalRule,Smiley,Subscript,Superscript,Forms,Save,NewPage,Print,Preview,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Form,RemoveFormat,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Flash,PageBreak,Iframe,About,Styles'
  });
  
  $('.table-list:not(.dy)').each (function () { if (!$(this).find ('tbody > tr').length) $(this).find ('tbody').append ($('<tr />').append ($('<td />').attr ('colspan', $(this).find ('thead th').length).text ('沒有任何資料。'))); });
  $('.table-list.dy').each (function () { if (!$(this).find ('tbody > tr').length) $(this).find ('tbody').append ($('<tr />').append ($('<td />').attr ('colspan', $(this).find ('thead th').length).text ('讀取中，請稍候..'))); });


  window.fns.mutiCol = function ($obj) {
    $obj.each (function () {
      var that = this,
          $row = $(this),
          $span = $row.find ('>span'),
          $b = $row.find ('>b');

      $row.data ('i', 0);

      that.fm = function (i, t) {
        return $('<div />').append (
          $('<div />').append (
            $('<a />').click (function () {
              var $p = $(this).parent ().parent ();
              $p.clone (true).insertBefore ($p.index () == 1 ? $span : $p.prev ());
              $p.remove ();
            })).append (
            $('<a />').click (function () {
              var $p = $(this).parent ().parent (), $x = $p.next (), $n = $p.clone (true);
              if ($x.is ('span')) $n.insertAfter ($b); else $n.insertAfter ($x);
              $p.remove ();
            }))).append (Array.apply (null, Array ($row.data ('cnt'))).map (function (_, j) {

              return $('<input />').attr ('type', $row.data ('attrs')[j].type ? $row.data ('attrs')[j].type : null)
                                   .attr ('name', $row.data ('attrs')[j].name + '[' + i + ']' + ($row.data ('attrs')[j].key ? '[' + $row.data ('attrs')[j].key + ']' : ''))
                                   .attr ('placeholder', $row.data ('attrs')[j].placeholder ? $row.data ('attrs')[j].placeholder : null)
                                   .attr ('accept', $row.data ('attrs')[j].accept ? $row.data ('attrs')[j].accept : null)
                                   .attr ('class', $row.data ('attrs')[j].class ? $row.data ('attrs')[j].class : null)
                                   .val (t ? $row.data ('attrs')[j].key && typeof t[$row.data ('attrs')[j].key] !== 'undefined' ? t[$row.data ('attrs')[j].key] : (typeof t === 'object' ? '' : t) : '');
            })).append (
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

      that.fm ($row.data ('i')).insertBefore ($span);
      $row.data ('i', parseInt ($row.data ('i'), 10) + 1);
    });
  };

  window.fns.mutiCol ($('form .row.muti'));

  window.fns.tipText = function (obj) {
    var $a = $('<a />').click (function () { $(this).parent ().fadeOut (550, function () { $(this).remove (); }); });
    var $t = $('<div />').append (
      $('<span />').text (obj.title)).append (
      $('<span />').text (obj.message)).append (
      typeof obj.error !== 'undefined' ? $('<span />').html ('※ 錯誤原因如下，您可以截圖給工程人員。').add ($('<div />').text (obj.error)) : null).append (
      $a);
    setTimeout (function () { $a.click (); }, 5500);
    return $tipTexts.prepend ($t.fadeIn ());
  };

  window.fns.updateCounter = function (key, result) {
    if (typeof key === 'undefined') return;
    $('*[data-cntrole*="' + key + '"]').each (function () { $(this).attr ('data-cnt', (result ? -1 : 1) + parseInt ($(this).attr ('data-cnt'), 10)); });
  };

  window.fns.ajaxFail = function (r) {
    if ((t = window.fns.IsJsonString (r.responseText)) !== null) window.fns.tipText ({title: '設定錯誤！', message: t.message});
    else window.fns.tipText ({title: '設定錯誤！', message: '※ 不明原因錯誤，請重新整理網頁確認。', error: r.responseText});
  };

  $('.table-list .switch.ajax[data-column][data-url]').each (function () {
    var $that = $(this), column = $that.data ('column'), url = $that.data ('url'), $inp = $that.find ('input[type="checkbox"]');

    $inp.click (function () {
      if ($that.hasClass ('loading')) return;

      var data = {};
      data[column] = $(this).prop ('checked') ? 1 : 0;

      $that.addClass ('loading');

      $.ajax ({
        url: url,
        data: data,
        async: true, cache: false, dataType: 'json', type: 'POST'
      })
      .done (function (result) {
        $(this).prop ('checked', result);
        
        $that.removeClass ('loading');
        window.fns.updateCounter ($that.data ('forcntrole'), result);

      }.bind ($(this)))
      .fail (function (result) {
        $that.removeClass ('loading');
        $(this).prop ('checked', !data[column]);

        window.fns.ajaxFail (result);
      }.bind ($(this)));
    });
  });

  $('a[data-method="delete"]').click (function () {
    var title = $(this).data ('alert') ? $(this).data ('alert') : '確定要刪除？';
    if (!confirm (title)) return false;
    else return true;
  });

  $('form.form-type2 .sorts').sortable ({
    items: 'div.sort',
    placeholder: 'sort_highlight',
    update: function () {
      $(this).find ('.sort').each (function (i) {
        $(this).attr ('data-i', i + 1);
      });
    }
  });

  window.fns.showLoading = function (str) {
    $loading.get (0)._t = setTimeout (function () {
      $loading.addClass ('s').find ('span').text (typeof str === 'undefined' ? '請稍候..' : str);
    }, 300);
  };

  window.fns.closeLoading = function () {
    clearTimeout ($loading.get (0)._t);
    $loading.removeClass ('s');
  };

  $('form.loading[method="post"]').submit (function () {
    if ($(this).data ('is_submit') === true) return false;
    $(this).data ('is_submit', true);

    var $m = $(this).find ('input[type="hidden"][name="_method"]');

    return window.fns.showLoading (($m.length && $m.val () == 'put' ? '更新中，' : '新增中，') + '請稍候..');
  });


  $('.search .dysltckb').each (function () {
    var $that = $(this);
    var $select = $that.find ('select');
    var name = $select.attr ('data-name');
    var $checkboxs = $that.find ('.checkboxs');
    var val = (typeof $select.data ('val') === 'undefined' ? [] : $select.data ('val')).map (function (t) {
      return parseInt (t, 10);
    });


    $select.change (function () {
      var ckbs = $(this).data ('ckbs').filter (function (t) {
        return t.parent_id == $(this).val ();
      }.bind ($(this)));

      $checkboxs.empty ().append (ckbs.map (function (t) {
        return $('<label />').addClass ('checkbox').append (
          $('<input />').attr ('type', 'checkbox').attr ('name', name).val (t.value).prop ('checked', $.inArray (t.value, val) != -1)).append (
          $('<span />')).append (
          t.text);
      }));
    });

    if (val.length) {
      var ckbs = $select.data ('ckbs').filter (function (t) { return $.inArray (t.value, val) != -1; }.bind ($(this))).map (function (t) { return t.parent_id; });
      $select.find ('option').each (function () {
        $(this).prop ('selected', $.inArray (parseInt ($(this).val (), 10), ckbs) != -1);
      });
      $select.change ();
    }
  });
});