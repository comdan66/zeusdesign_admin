<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class My_calendar extends Admin_controller {
  private $uri_1 = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('member')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/my-calendar';
    $this->icon = 'icon-calendar2';
    $this->title = '行程';

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function status ($id = 0) {
    if (!($id && ($this->obj = ScheduleItem::find ('one', array ('conditions' => array ('id = ?', $id))))))
      return $this->output_error_json ('找不到該筆資料。');

    if ($this->obj->schedule->user_id != User::current ()->id)
      return $this->output_error_json ('您的權限不足。');

    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    $validation = function (&$posts) {
      return !(isset ($posts['status']) && is_string ($posts['status']) && is_numeric ($posts['status'] = trim ($posts['status'])) && ($posts['status'] = $posts['status'] ? ScheduleItem::STATUS_2 : ScheduleItem::STATUS_1) && in_array ($posts['status'], array_keys (ScheduleItem::$statusNames))) ? '「設定完成」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!ScheduleItem::transaction (function () use ($obj, $posts) {
      return $obj->save ();
    })) return $this->output_error_json ('更新失敗！');

    UserLog::logWrite (
      'icon-calendar2',
      ScheduleItem::$statusNames[$obj->status] . '一項' . $this->title,
      '將一筆' . $this->title . '調整為「' . ScheduleItem::$statusNames[$obj->status] . '」',
      array ($backup, $obj->backup (true)));

    return $this->output_json (array (
        'status' => $obj->status == ScheduleItem::STATUS_2,
        'finish' => count (array_filter ($obj->schedule->items, function ($item) { return $item->status == ScheduleItem::STATUS_2; })) == count ($obj->schedule->items),
      ));
  }
  public function task ($id = 0) {
    if (!($id && ($obj = Task::find ('one', array ('conditions' => array ('id = ?', $id))))))
      return $this->output_error_json ('找不到該筆資料。');

    if (!(($obj->user_id == User::current ()->id || (($t = column_array ($obj->user_mappings, 'user_id')) && in_array (User::current ()->id, $t)))))
      return $this->output_error_json ('您的權限不足，或者頁面不存在。');
    
    return $this->output_json (array (
        'edit' => $obj->user_id == User::current ()->id ? base_url ('admin', 'tasks', $obj->id, 'edit') : false,

        'id' => $obj->id,
        'title' => $obj->title,
        'content' => $obj->content,
        'href' => base_url ('admin', 'my-tasks', $obj->id, 'show'),
        'level' => Task::$levelNames[$obj->level],
        'levelc' => array_values (Task::$levelColors),
        'levelu' => $t = 100 / count (Task::$levelColors),
        'levelv' => $t * ($obj->level - 1),
        'users' => array_map (function ($user) {
          return array (
              'id' => $user->id,
              'name' => $user->name,
              'avatar' => $user->avatar (),
            );
        }, $obj->users),
        'commits' => array_map (function ($commit) {
          return array (
              'id' => $commit->id,
              'action' => $commit->action,
              'content' => !$commit->content && (string)$commit->file ? '上傳了一個檔案，名稱為「' . (string)$commit->file . '」。' : $commit->mini_content (),
              'avatar' => $commit->user->avatar (),
            );
        }, TaskCommit::find ('all', array ('include' => array ('user'), 'order' => 'id DESC', 'conditions' => array ('task_id = ?', $obj->id)))),
        'finish' => $obj->status == Task::STATUS_2 ? true : false,
      ));
  }
  public function show ($id = 0) {
    if (!($id && ($obj = Schedule::find ('one', array ('conditions' => array ('id = ?', $id))))))
      return $this->output_error_json ('找不到該筆資料。');

    if (!(User::current ()->in_roles (array ('schedule')) || ($obj->user_id == User::current ()->id || (($t = column_array ($obj->shares, 'user_id')) && in_array (User::current ()->id, $t)))))
      return $this->output_error_json ('您的權限不足，或者頁面不存在。');
    
    return $this->output_json (array (
        'type' => $obj->user_id == User::current ()->id ? 1 : 3,
        'id' => $obj->id,
        'title' => $obj->title,
        'memo' => $obj->memo,
        'tag' => $obj->schedule_tag_id && $obj->tag ? array (
            'id' => $obj->tag->id,
            'name' => $obj->tag->name,
            'color' => $obj->tag->color,
          ) : null,
        'items' => array_map (function ($item) {
          return array (
              'id' => $item->id,
              'content' => $item->content,
              'status' => $item->status == ScheduleItem::STATUS_2,
            );
        }, $obj->items),
        'users' => array_map (function ($user) {
          return array (
              'id' => $user->id,
              'name' => $user->name,
              'avatar' => $user->avatar (),
            );
        }, $obj->users),
        'finish' => count (array_filter ($obj->items, function ($item) { return $item->status == ScheduleItem::STATUS_2; })) == count ($obj->items),
      ));
  }
  public function month () {
    $gets = OAInput::get ();
    
    if (!(isset ($gets['date']) && is_string ($gets['date']) && ($gets['date'] = trim ($gets['date']))))
      return $this->output_error_json ('參數錯誤。');
    
    if (is_month ($gets['date'])) {
      $gets['date'] = $gets['date'] . '-01';

      $count = 42;
      $start = date ('Y-m-d', strtotime (($a = date ('Y-m-01', strtotime ($gets['date']))) . ' -' . date ('w', strtotime ($a)) . ' day'));
      $end = date ('Y-m-d', strtotime ($start . ' +' . $count . ' day'));
    } else if (is_date ($gets['date'])) {
      $count = 1;
      $start = $gets['date'];
      $end = $gets['date'];
    } else {
      return $this->output_error_json ('參數錯誤。');
    }

    $days = array ();

    for ($i = 0; $i < $count; $i++)
      $days[date ('Y-m-d', $key = strtotime ($start . ' +' . $i . ' day'))] = array ('y' => (int)date ('Y', $key), 'm' => (int)date ('m', $key), 'd' => (int)date ('d', $key), 'c' => array ());
    
    $objs = Task::find ('all', array ('order' => 'id DESC', 'joins' => 'LEFT JOIN (select user_id,task_id from task_user_mappings) as a ON(tasks.id = a.task_id)', 'conditions' => array ('date BETWEEN ? AND ? AND a.user_id = ?', $start, $end, User::current ()->id)));
    foreach ($objs as $obj)
      if (isset ($days[$obj->date->format ('Y-m-d')]['c']))
        array_push ($days[$obj->date->format ('Y-m-d')]['c'], array (
            'id' => $obj->id,
            'type' => 2,
            'color' => '',
            'text' => $obj->title,
            'img' => '',
            'finish' => $obj->status == Task::STATUS_2 ? true : false,
          ));

    if (User::current ()->in_roles (array ('schedule'))) {
      $objs = Schedule::find ('all', array ('include' => array ('tag', 'user', 'items'), 'order' => 'sort ASC', 'conditions' => array ('date BETWEEN ? AND ? AND schedules.user_id != ?', $start, $end, User::current ()->id)));
      foreach ($objs as $obj)
        if (isset ($days[$obj->date->format ('Y-m-d')]['c']) && $obj->user_id != User::current ()->id)
          array_push ($days[$obj->date->format ('Y-m-d')]['c'], array (
              'id' => $obj->id,
              'type' => 3,
              'color' => $obj->schedule_tag_id && $obj->tag ? $obj->tag->color : '',
              'text' => $obj->title,
              'img' => $obj->user->avatar (),
              'finish' => count (array_filter ($obj->items, function ($item) { return $item->status == ScheduleItem::STATUS_2; })) == count ($obj->items),
            ));
    } else {
      $objs = Schedule::find ('all', array ('include' => array ('tag', 'user', 'items'), 'order' => 'sort ASC', 'joins' => 'LEFT JOIN (select user_id,schedule_id from schedule_shares) as a ON(schedules.id = a.schedule_id)', 'conditions' => array ('date BETWEEN ? AND ? AND schedules.user_id != ? AND a.user_id = ?', $start, $end, User::current ()->id, User::current ()->id)));
      foreach ($objs as $obj)
        if (isset ($days[$obj->date->format ('Y-m-d')]['c']))
          array_push ($days[$obj->date->format ('Y-m-d')]['c'], array (
              'id' => $obj->id,
              'type' => 3,
              'color' => $obj->schedule_tag_id && $obj->tag ? $obj->tag->color : '',
              'text' => $obj->title,
              'img' => $obj->user->avatar (),
              'finish' => count (array_filter ($obj->items, function ($item) { return $item->status == ScheduleItem::STATUS_2; })) == count ($obj->items),
            ));
    }


    $objs = Schedule::find ('all', array ('include' => array ('tag', 'user', 'items'), 'order' => 'sort ASC', 'conditions' => array ('date BETWEEN ? AND ? AND schedules.user_id = ?', $start, $end, User::current ()->id)));
    foreach ($objs as $obj)
      if (isset ($days[$obj->date->format ('Y-m-d')]['c']) && $obj->user_id == User::current ()->id)
        array_push ($days[$obj->date->format ('Y-m-d')]['c'], array (
            'id' => $obj->id,
            'type' => 1,
            'color' => $obj->schedule_tag_id && $obj->tag ? $obj->tag->color : '',
            'text' => $obj->title,
            'img' => $obj->user->avatar (),
            'finish' => count (array_filter ($obj->items, function ($item) { return $item->status == ScheduleItem::STATUS_2; })) == count ($obj->items),
          ));

    // type 1 自己的
    // type 2 系統的
    // type 3 朋友的


    return $this->output_json (array_values ($days));
  }
  public function index () {
    $date = OAInput::get ('date');
    $id = OAInput::get ('id');
    $this->add_hidden (array (
      'id' => 'datas',
      'data-id' => $id ? $id : '',
      'data-date' => $date ? $date : '',
      'data-api_status' => base_url ('admin', 'my_calendar', 'status', '%d'),
      'data-api_content' => base_url ('admin', 'my-calendar', '%d', 'show'),
      'data-api_month' => base_url ('admin', 'my_calendar', 'month'),
      'data-api_daysort' => base_url ('admin', 'my_calendar', 'daysort'),
      'data-api_day' => base_url ('admin', 'my_calendar', 'day'),
      'data-api_delete' => base_url ('admin', 'my-calendar', '%d'),
      'data-api_update' => base_url ('admin', 'my-calendar', '%d'),
      'data-api_create' => base_url ('admin', 'my-calendar'),
      'data-api_task' => base_url ('admin', 'my_calendar', 'task', '%d'),
      'data-tags' => json_encode (array_map (function ($user) { return array ('id' => $user->id, 'name' => $user->name, 'color' => $user->color); }, ScheduleTag::all (array ('select' => 'id, name, color', 'conditions' => array ('user_id = ?', User::current ()->id))))),
      'data-users' => json_encode (array_map (function ($user) { return array ('id' => $user->id, 'name' => $user->name); }, User::all (array ('select' => 'id, name', 'conditions' => array ('id != ?', User::current ()->id))))),
      ))->load_view ();
  }
  public function daysort () {
    $posts = OAInput::post ();

    if (!isset ($posts['data'])) $posts['data'] = array ();

    if (!$datas = array_filter (array_map (function ($data) { if (!$schedule = Schedule::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $data['id'], User::current ()->id)))) return null; return array ('schedule' => $schedule, 'sort' => $data['sort'], 'date' => isset ($data['date']) ? $data['date'] : $schedule->date); }, $posts['data'])))
      return $this->output_json (true);

    $change = array ();
    if (!$datas = array_filter ($datas, function ($data) use (&$change) {
          foreach ($data as $key => $value)
            if ($key != 'schedule') { if ($data['schedule']->$key != $value) array_push ($change, array ('c' => $key, 'v' => array ('o' => $data['schedule']->$key, 'n' => $value))); $data['schedule']->$key = $value; }
          return Schedule::transaction (function () use ($data) { return $data['schedule']->save (); });
        }))
      return $this->output_error_json ('更新失敗！');

    UserLog::logWrite (
      $this->icon,
      '調整一項' . $this->title . '順序',
      '已經備份紀錄，細節可詢問工程師',
      $change);

    return $this->output_json (true);
  }
  public function day () {
    $gets = OAInput::get ();

    if (!(isset ($gets['date']) && is_string ($gets['date']) && is_date ($gets['date'] = trim ($gets['date']))))
      return $this->output_error_json ('參數錯誤。');

    $objs1 = Schedule::find ('all', array ('include' => array ('tag', 'items'), 'order' => 'sort ASC', 'conditions' => array ('user_id = ? AND date = ?', User::current ()->id, $gets['date'])));
    $objs1 = array_map (function ($obj) {
      return array (
        'img' => res_url ('res', 'image', 'draw.png'),
        'id' => $obj->id,
        'title' => $obj->title,
        'sub' => $obj->schedule_tag_id && $obj->tag ? $obj->tag->name : '',
        'note' => ($a = count (array_filter ($obj->items, function ($item) { return $item->status == ScheduleItem::STATUS_2; }))) . ' / ' . ($b = count ($obj->items)),
        'finish' => $a == $b,
        'type' => 1,
        );
    }, $objs1);

    $objs2 = Task::find ('all', array ('order' => 'id DESC', 'joins' => 'LEFT JOIN (select user_id,task_id from task_user_mappings) as a ON(tasks.id = a.task_id)', 'conditions' => array ('date = ? AND a.user_id = ?', $gets['date'], User::current ()->id)));
    $objs2 = array_map (function ($obj) {
      return array (
        'icon' => 'icon-shield',
        'id' => $obj->id,
        'title' => $obj->title,
        'sub' => $obj->mini_content (50),
        'note' => ($obj->status == Task::STATUS_2 ? '1' : '0') . ' / 1',
        'finish' => $obj->status == Task::STATUS_2 ? true : false,
        // 'href' => base_url ('admin', 'my-tasks', $obj->id)
        'type' => 2,
        );
    }, $objs2);

    if (User::current ()->in_roles (array ('schedule'))) {
      $objs3 = Schedule::find ('all', array ('include' => array ('tag', 'user', 'items'), 'order' => 'sort ASC', 'conditions' => array ('date = ? AND schedules.user_id != ?', $gets['date'], User::current ()->id)));
      $objs3 = array_map (function ($obj) {
        return array (
          'img' => $obj->user->avatar (),
          'id' => $obj->id,
          'title' => $obj->title,
          'sub' => $obj->schedule_tag_id && $obj->tag ? $obj->tag->name : '',
          'note' => ($a = count (array_filter ($obj->items, function ($item) { return $item->status == ScheduleItem::STATUS_2; }))) . ' / ' . ($b = count ($obj->items)),
          'finish' => $a == $b,
          'type' => 3,
          );
      }, $objs3);
    } else {
      $objs3 = Schedule::find ('all', array ('include' => array ('tag', 'user', 'items'), 'order' => 'sort ASC', 'joins' => 'LEFT JOIN (select user_id,schedule_id from schedule_shares) as a ON(schedules.id = a.schedule_id)', 'conditions' => array ('a.user_id = ? AND schedules.user_id != ? AND date = ?', User::current ()->id, User::current ()->id, $gets['date'])));
      $objs3 = array_map (function ($obj) {
        return array (
          'img' => $obj->user->avatar (),
          'id' => $obj->id,
          'title' => $obj->title,
          'sub' => $obj->schedule_tag_id && $obj->tag ? $obj->tag->name : '',
          'note' => ($a = count (array_filter ($obj->items, function ($item) { return $item->status == ScheduleItem::STATUS_2; }))) . ' / ' . ($b = count ($obj->items)),
          'finish' => $a == $b,
          'type' => 3,
          );
      }, $objs3);
    }
    
    $return = array ();
    if ($objs2) array_push ($return, array ('text' => '今日任務', 'objs' => $objs2, 'sort' => false));
    if ($objs1) array_push ($return, array ('text' => '個人事項', 'objs' => $objs1, 'sort' => true));
    if ($objs3) array_push ($return, array ('text' => '朋友事項', 'objs' => $objs3, 'sort' => false));

    return $this->output_json ($return);
  }
  public function create () {
    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();

    if (($msg = $this->_validation_create ($posts)) || (!Schedule::transaction (function () use (&$obj, $posts) {
      $posts['sort'] = (($posts['sort'] = Schedule::first (array ('select' => 'sort', 'order' => 'sort DESC', 'conditions' => array ('user_id = ? AND date = ?', User::current ()->id, $posts['date'])))) ? $posts['sort']->sort : 0) + 1;
      return verifyCreateOrm ($obj = Schedule::create (array_intersect_key ($posts, Schedule::table ()->columns)));
    }) && $msg = '新增失敗！')) return $this->output_error_json ($msg);


    if ($posts['items'])
      foreach ($posts['items'] as $i => $item)
        ScheduleItem::transaction (function () use ($i, $item, $obj) { return verifyCreateOrm (ScheduleItem::create (array_intersect_key (array_merge ($item, array ('schedule_id' => $obj->id)), ScheduleItem::table ()->columns))); });
    
    if ($posts['user_ids'])
      foreach ($posts['user_ids'] as $i => $user_id)
        ScheduleShare::transaction (function () use ($i, $user_id, $obj) { return verifyCreateOrm (ScheduleShare::create (array_intersect_key (array ('schedule_id' => $obj->id, 'user_id' => $user_id), ScheduleShare::table ()->columns))); });
    
    UserLog::logWrite (
      $this->icon,
      '新增一項' . $this->title . '',
      '名稱為：「' . $obj->title . '」',
      $obj->backup ());

    return $this->output_json (true);
  }
  public function update ($id) {
    if (!($id && ($obj = Schedule::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $id, User::current ()->id))))))
      return $this->output_error_json ('找不到該筆資料，或您的權限不足，或者頁面不存在。。');

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->backup (true);

    if ($msg = $this->_validation_update ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Schedule::transaction (function () use ($obj) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    if ($obj->items)
      foreach ($obj->items as $item)
        ScheduleItem::transaction (function () use ($item) { return $item->destroy (); });

    if ($posts['items'])
      foreach ($posts['items'] as $i => $item)
        ScheduleItem::transaction (function () use ($i, $item, $obj) { return verifyCreateOrm (ScheduleItem::create (array_intersect_key (array_merge ($item, array ('schedule_id' => $obj->id)), ScheduleItem::table ()->columns))); });
    
    if ($obj->shares)
      foreach ($obj->shares as $share)
        ScheduleShare::transaction (function () use ($share) { return $share->destroy (); });

    if ($posts['user_ids'])
      foreach ($posts['user_ids'] as $i => $user_id)
        ScheduleShare::transaction (function () use ($i, $user_id, $obj) { return verifyCreateOrm (ScheduleShare::create (array_intersect_key (array ('schedule_id' => $obj->id, 'user_id' => $user_id), ScheduleShare::table ()->columns))); });
    
    UserLog::logWrite (
      $this->icon,
      '修改一項' . $this->title,
      '名稱為：「' . $obj->title . '」',
      array ($backup, $obj->backup (true)));

    return $this->output_json (true);
  }
  public function destroy ($id) {
    if (!($id && ($obj = Schedule::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $id, User::current ()->id))))))
      return $this->output_error_json ('找不到該筆資料，或您的權限不足，或者頁面不存在。。');

    $backup = $obj->backup (true);

    if (!Schedule::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_fd' => '刪除失敗！'));

    UserLog::logWrite (
      $this->icon,
      '刪除一項' . $this->title,
      '已經備份了刪除紀錄，細節可詢問工程師',
      $backup);

    return $this->output_json (true);
  }

  private function _validation_create (&$posts) {
    if (!(isset ($posts['date']) && is_string ($posts['date']) && is_date ($posts['date'] = trim ($posts['date'])))) return '「日期」格式錯誤！';
    if (!(isset ($posts['title']) && is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '「標題」格式錯誤！';
    if (!(isset ($posts['user_id']) && is_string ($posts['user_id']) && is_numeric ($posts['user_id'] = trim ($posts['user_id'])) && User::find_by_id ($posts['user_id']))) $posts['user_id'] = User::current ()->id;
    if (isset ($posts['schedule_tag_id']) && !(is_string ($posts['schedule_tag_id']) && is_numeric ($posts['schedule_tag_id'] = trim ($posts['schedule_tag_id'])) && ScheduleTag::find_by_id ($posts['schedule_tag_id']))) $posts['schedule_tag_id'] = 0;
    if (isset ($posts['memo']) && !(is_string ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])))) $posts['memo'] = '';

    $posts['items'] = isset ($posts['items']) && is_array ($posts['items']) && $posts['items'] ? array_values (array_filter (array_map (function ($item) {
      if (!(isset ($item['status']) && is_string ($item['status']) && is_numeric ($item['status'] = trim ($item['status'])) && in_array ($item['status'], array_keys (ScheduleItem::$statusNames)))) $item['status'] = ScheduleItem::STATUS_1;
      if (!(isset ($item['content']) && is_string ($item['content']) && ($item['content'] = trim ($item['content'])))) $item['content'] = '';
      return $item;
    }, $posts['items']), function ($item) {
      return $item['content'];
    })) : array ();

    $posts['user_ids'] = isset ($posts['user_ids']) && is_array ($posts['user_ids']) && $posts['user_ids'] ? column_array (User::find ('all', array ('select' => 'id', 'conditions' => array ('id IN (?) AND id != ?', $posts['user_ids'], User::current ()->id))), 'id') : array ();

    return '';
  }
  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
