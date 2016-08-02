/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

$(function () {
  window.vars.tags = $('#tags').data ('val');
  
  window.funs.schedulePrompt = function (tags, okCallback, title, inputText, textareaText, radioVal, noCallback) {
    window.vars.$schedulePrompt = $('.schedulePrompt');
    
    this.loading = function (title) {
      window.vars.$schedulePrompt.removeClass ('result').addClass ('loading').get (0).vars.$tip.attr ('title', title);
    }
    this.result = function (title) {
      window.vars.$schedulePrompt.removeClass ('loading').addClass ('result').get (0).vars.$tip.attr ('title', title);
    }

    this.okCallback = function (callback) {
      if (callback) window.vars.$schedulePrompt.get (0).vars.$ok.unbind ('click').click (callback.bind ($(this), window.vars.$schedulePrompt.get (0).vars.$input, window.vars.$schedulePrompt.get (0).vars.$textarea));
    }
    this.close = function () {
      window.vars.$schedulePrompt.removeClass ('show_animation').addClass ('hide_animation');

      window.vars.$schedulePrompt.get (0).vars.timer = setTimeout (function () {
        window.vars.$schedulePrompt.attr ('class', 'schedulePrompt');

        window.vars.$schedulePrompt.get (0).vars.$input.val ('');
        window.vars.$schedulePrompt.get (0).vars.$textarea.val ('');
        window.vars.$schedulePrompt.get (0).vars.$radios && window.vars.$schedulePrompt.get (0).vars.$radios.find ('input').prop ('checked', false);
        window.vars.$schedulePrompt.get (0).vars.timer = null;
      }, 500);
    };

    if (window.vars.$schedulePrompt.length < 1) {
      window.vars.$schedulePrompt = $('<div />').addClass ('schedulePrompt').appendTo (window.vars.$body);

      window.vars.$schedulePrompt.get (0).vars = {
        $title: $('<div />').addClass ('title'),
        $input: $('<input />').attr ('type', 'text').attr ('placeholder', '請輸入標題..'),
        $textarea: $('<textarea />').attr ('placeholder', '請輸入細節..'),
        $ok: $('<a />').addClass ('ok').text ('確定'),
        $no: $('<a />').addClass ('no').text ('取消'),
        $radios: tags.length ? $('<div />').addClass ('radios').append (tags.map (function (t) {
          return $('<label>').css ({'padding-bottom': '2px', 'border-bottom': '2px solid ' + (t.color ? t.color : '#000000')}).append (
            $('<input />').attr ('type', 'radio').attr ('name', 'tag').val (t.id)).append (
            $('<span />')).append (
            t.name)
        })) : null,
        $tip: $('<div />').addClass ('tip'),
        timer: null
      };


      window.vars.$schedulePrompt.append (
        $('<div />').addClass ('cover').click (this.close)).append (
        $('<div />').addClass ('wrapper').append (
          window.vars.$schedulePrompt.get (0).vars.$title).append (
          window.vars.$schedulePrompt.get (0).vars.$tip).append (
          $('<div />').addClass ('content').append (
            window.vars.$schedulePrompt.get (0).vars.$input).append (
            window.vars.$schedulePrompt.get (0).vars.$textarea).append (
            window.vars.$schedulePrompt.get (0).vars.$radios)).append (
          $('<div />').addClass ('btns').append (
            window.vars.$schedulePrompt.get (0).vars.$ok.click (this.close)).append (
            window.vars.$schedulePrompt.get (0).vars.$no.click (this.close))));
    }
    if (window.vars.$schedulePrompt.get (0).vars.timer) return false;

    if (title) window.vars.$schedulePrompt.get (0).vars.$title.text (title);
    if (inputText) window.vars.$schedulePrompt.get (0).vars.$input.val (inputText);
    if (textareaText) window.vars.$schedulePrompt.get (0).vars.$textarea.val (textareaText);
    if (radioVal) window.vars.$schedulePrompt.get (0).vars.$radios.find ('input[value="' + radioVal + '"]').prop ('checked', true);

    if (okCallback) window.vars.$schedulePrompt.get (0).vars.$ok.unbind ('click').click (okCallback.bind (
      $(this),
      window.vars.$schedulePrompt.get (0).vars.$input,
      window.vars.$schedulePrompt.get (0).vars.$textarea,
      window.vars.$schedulePrompt.get (0).vars.$radios));
    if (noCallback) window.vars.$schedulePrompt.get (0).vars.$no.unbind ('click').click (noCallback);
    window.vars.$schedulePrompt.addClass ('show').addClass ('show_animation');
    window.vars.$schedulePrompt.get (0).vars.$input.focus ();
  };

  function loadMonthSchedules ($obj) {
    var vars = $obj.get (0).vars;
    var $table = vars.$months.find ('table');
    var $month = $table.eq (1);

    vars.$months.attr ('class', 'months n' + $month.find ('tbody tr').length);
    vars.$title.text ($month.data ('y') + '年 · ' + $month.data ('m') + '月');

    $.ajax ({
      url: '/api/schedules/',
      data: {
        range: {
          year: $month.data ('y'),
          month: $month.data ('m'),
        }
      },
      async: true, cache: false, dataType: 'json', type: 'get',
      beforeSend: function () {
        $table.find ('td div').remove ();
      }
    })
    .done (function (result) {
      result.forEach (function (t, i) {
        $month.find ('td[data-y="' + t.year + '"][data-m="' + t.month + '"][data-d="' + t.day + '"]').append (
          $('<div />').data ('id', t.id).css ({'background-color': t.tag.color ? t.tag.color : '#000000'}).addClass (t.finish ? 'finished' : null).text (t.title));
      })
    })
    .fail (function (result) { ajaxError (result); })
    .complete (function (result) {});
  }
  function monthDayCount (y, m) {
    return (m == 1) ? ((y % 4) === 0) && ((y % 100) !== 0) || ((y % 400) === 0) ? 29 : 28 : [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][m];
  }
  function prevMonth (y, m) {
    m = isNaN (y) ? y.m : m;
    y = isNaN (y) ? y.y : y;
    return { y: m == 1 ? y - 1 : y, m: m == 1 ? 12 : m - 1 };
  }
  function nextMonth (y, m) {
    m = isNaN (y) ? y.m : m;
    y = isNaN (y) ? y.y : y;

    return { y: m == 12 ? y + 1 : y, m: m == 12 ? 1 : m + 1 };
  }
  function month ($obj, direction) {
      var $month = $obj.get (0).vars.$months.find ('table').eq (1);
      if (direction > 0) {
        var next = nextMonth (nextMonth ($month.data ('y'), $month.data ('m')));
        createMonth ($obj, next.y, next.m).appendTo ($obj.get (0).vars.$months);
        $obj.get (0).vars.$months.find ('table').first ().remove ();
      }
      if (direction < 0) {
        var prev = prevMonth (prevMonth ($month.data ('y'), $month.data ('m')));
        createMonth ($obj, prev.y, prev.m).prependTo ($obj.get (0).vars.$months);
        $obj.get (0).vars.$months.find ('table').last ().remove ();
      }
    loadMonthSchedules ($obj);
  }
  
  function createMonth ($obj, y, m) {
    var f = new Date(y, --m, 1).getDay ();
    var mc = monthDayCount (y, m);
    var wc = parseInt ((f + mc) / 7, 10) + (((f + mc) % 7) ? 1 : 0);

    var prev = prevMonth (y, m + 1);
    var next = nextMonth (y, m + 1);
    var pm = monthDayCount (prev.y, prev.m - 1);
    var nm = monthDayCount (next.y, next.m - 1);

    return $('<table />').attr ('data-y', y).attr ('data-m', m + 1).append ($('<thead />').append ($('<tr />').append (['日', '一', '二', '三', '四', '五', '六'].map (function (t) {
      return $('<th />').text (t);
    })))).append ($('<tbody />').append (Array.apply (null, Array (wc)).map (function (_, i) {
      return $('<tr />').append (Array.apply (null, Array (7)).map (function (_, j) {
        var vars = $obj.get (0).vars;
        var d = i * 7 + j;
        var nd = (d < f) || (d - f >= mc) ? (d < f) ? pm - (f - d - 1) : (d - f + 1) % mc : d - f + 1;

        return $('<td />')
        .addClass ((d < f) || d - f >= mc ? 'not' : null)
        .attr ('data-y', (d < f) ? prev.y : (d - f >= mc ? next.y : y))
        .attr ('data-m', ((d < f) ? prev.m : (d - f >= mc ? next.m : (m + 1))))
        .attr ('data-d', nd)
        .append ($('<span />').text (nd)).click (function () {
          vars.y = $(this).data ('y');
          vars.m = $(this).data ('m');
          vars.d = $(this).data ('d');

          var $header = $obj.parents ('.panel').find ('header');
          $header.append (
            $('<button />').attr ('type', 'button').addClass ('close_day').click (function () {
              $obj.removeClass ('day');
              $header.find ('h2').text ('行事曆');
              $header.find ('button').remove ();
              vars.$daySchedule.empty ();
              loadMonthSchedules ($obj);
            })).append (
            $('<button />').attr ('type', 'button').addClass ('add_schedule').data ('y', vars.y).data ('m', vars.m).data ('d', vars.d).click (createSchedule)).find ('h2').text (vars.y + '/' + vars.m + '/' + vars.d);
          
          $obj.addClass ('day');

          loadDaySchedules ({
              year: $obj.get (0).vars.y,
              month: $obj.get (0).vars.m,
              day: $obj.get (0).vars.d,
            }, $obj.get (0).vars.$daySchedule);
        });
      }));
    })));
  }

  function loadDaySchedules (data, $daySchedule) {
    $.ajax ({
      url: '/api/schedules/',
      data: data,
      async: true, cache: false, dataType: 'json', type: 'get',
      beforeSend: function () {
        $daySchedule.empty ()
      }
    })
    .done (function (result) {
      $daySchedule.append (result.map (initSchedule));
    }.bind ($(this)))
    .fail (function (result) { ajaxError (result); })
    .complete (function (result) {});
  }

  function createSchedule () {
    var $that = $(this);
    window.funs.schedulePrompt (window.vars.tags, function ($input, $textarea, $radios) {
      var title = $input.val ().trim ();
      var description = $textarea.val ().trim ();
      var prompt = $(this).get (0);
      var $radio = $radios ? $radios.find ('input:checked') : null;

      if (!title) return prompt.close ();

      $.ajax ({
        url: '/api/schedules/',
        data: {
          year: $that.data ('y'),
          month: $that.data ('m'),
          day: $that.data ('d'),
          title: title,
          description: description,
          tag_id: $radio ? $radio.val () : null
        },
        async: true, cache: false, dataType: 'json', type: 'POST',
        beforeSend: function () {
          prompt.loading ('讀取中..');
        }
      })
      .done (function (result) {
        prompt.result ('新增完成！');
        prompt.okCallback (function () {
          initSchedule (result).appendTo ($that.parents ('.panel').find ('.daySchedule'));
          prompt.close ();
        });
      })
      .fail (function (result) { ajaxError (result); })
      .complete (function (result) {});
    }, '新增工作');
  }
  function editSchedule () {
    var $that = $(this);
    var $schedule = $that.parents ('.schedule');
    
    var $h3 = $schedule.find ('h3');
    var $p = $schedule.find ('p');

    window.funs.schedulePrompt (window.vars.tags, function ($input, $textarea, $radios) {
      var title = $input.val ().trim ();
      var description = $textarea.val ().trim ();
      var prompt = $(this).get (0);
      var $radio = $radios ? $radios.find ('input:checked') : null;

      if (!title) return prompt.close ();

      $.ajax ({
        url: '/api/schedules/' + $that.data ('id'),
        data: {
          _method: 'put',
          title: title,
          description: description,
          tag_id: $radio ? $radio.val () : null
        },
        async: true, cache: false, dataType: 'json', type: 'POST',
        beforeSend: function () {
          prompt.loading ('讀取中..');
        }
      })
      .done (function (result) {
        prompt.result ('更新完成！');
        prompt.okCallback (function () {
          $h3.text (result.title);
          $p.text (result.description);
          $schedule.data ('tag_id', result.tag.id ? result.tag.id : 0).css ({'border-top': '5px solid ' + (result.tag.color ? result.tag.color : '#000000')});
          prompt.close ();
        });
      })
      .fail (function (result) { ajaxError (result); })
      .complete (function (result) {});
    }, '修改工作', $h3.text (), $p.text (), $schedule.data ('tag_id'));
  }
  function finishSchedule () {
    var $schedule = $(this).parents ('.schedule');
    $.ajax ({
      url: '/api/schedules/' + $(this).data ('id'),
      data: {
        _method: 'put',
        finish: $schedule.hasClass ('finished') ? 0 : 1
      },
      async: true, cache: false, dataType: 'json', type: 'post',
      beforeSend: function () {}
    })
    .done (function (result) {
      if (result.finish) $schedule.addClass ('finished');
      else $schedule.removeClass ('finished');
    })
    .fail (function (result) { ajaxError (result); })
    .complete (function (result) {});
  }
  function deleteSchedule () {
    var $schedule = $(this).parents ('.schedule');
    $.ajax ({
      url: '/api/schedules/' + $(this).data ('id'),
      async: true, cache: false, dataType: 'json', type: 'delete',
      beforeSend: function () {}
    })
    .done (function (result) {
      $schedule.remove ();
    })
    .fail (function (result) { ajaxError (result); })
    .complete (function (result) {});
  }
  function initSchedule (t) {
    return $('<div />').data ('id', t.id).data ('tag_id', t.tag.id ? t.tag.id : 0).css ({'border-top': '5px solid ' + (t.tag.color ? t.tag.color : '#000000')}).addClass ('schedule' + (t.finish ? ' finished' : '')).append (
      $('<div />').append (
        $('<div />').addClass ('controls').append (
          $('<a />').addClass ('move')).append (
          $('<a />').addClass ('finish').data ('id', t.id).click (finishSchedule)).append (
          $('<a />').addClass ('edit').data ('id', t.id).click (editSchedule)).append (
          $('<a />').addClass ('delete').data ('id', t.id).click (deleteSchedule))).append (
        $('<div />').addClass ('content').append (
          $('<h3 />').text (t.title)).append (
          $('<p />').text (t.description))));
  }
  function initMonth ($obj) {
    var vars = $obj.get (0).vars;

    var prev = prevMonth (vars.y, vars.m);
    var next = nextMonth (vars.y, vars.m);

    $obj.get (0).vars.$months.empty ()
        .append (createMonth ($obj, prev.y, prev.m))
        .append (createMonth ($obj, vars.y, vars.m))
        .append (createMonth ($obj, next.y, next.m))
        .find ('td[data-y="' + vars.y + '"][data-m="' + vars.m + '"][data-d="' + vars.d + '"]')
        .addClass ('today');
    
    loadMonthSchedules ($obj);
  }
  function updateSort (data) {
    if (!data.length) return;
    
    $.ajax ({
      url: '/api/schedules/sort/',
      data: { data: data },
      async: true, cache: false, dataType: 'json', type: 'post'
    });
  }


  $('.calendar').each (function () {
    var $that = $(this),
        time = new Date ();

    $(this).get (0).vars = {
      y: time.getFullYear (),
      m: time.getMonth () + 1,
      d: time.getDate (),
      $months: $(this).find ('> .months'),
      $title: $(this).find ('.title'),
      $daySchedule: $(this).find ('.daySchedule'),
    };

    initMonth ($(this));

    $(this).find ('> .year_months > a').click (function () {
      month ($that, $(this).hasClass ('icon-al') ? -1 : 1);
    });
  });

  $('.daySchedule').sortable ({
    handle: '.move',
    placeholder: 'schedule_highlight',
    update: function () {
      updateSort ($('.daySchedule .schedule').map (function (i) {
        return {id: $(this).data ('id'), sort: i};
      }).toArray ());
    }
  });

  $('.calendar td').sortable ({
    items: 'div',
    connectWith: 'td',
    update: function (e, ui) {
      var y = $(this).data ('y');
      var m = $(this).data ('m');
      var d = $(this).data ('d');
      
      updateSort ($(this).find ('div').map (function (i) {
          return {id: $(this).data ('id'), sort: i, year: y, month: m, day: d};
        }).toArray ());      
    }
  });
});