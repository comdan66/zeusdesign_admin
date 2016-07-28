<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Schedules extends Api_controller {
  private $user = null;
  private $schedule = null;

  public function __construct () {
    parent::__construct ();

    if (User::current ()) $this->user = User::current ();
    else $this->user = ($token = $this->input->get_request_header ('Token')) && ($user = User::find ('one', array ('conditions' => array ('token = ?', $token)))) ? $user : null;

    if (in_array ($this->uri->rsegments (2, 0), array ('finish', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->schedule = Schedule::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $id, $this->user->id))))))
        return $this->disable ($this->output_error_json ('Not found Data!'));
  }
  public function index () {
    $gets = OAInput::get ();

    OaModel::addConditions ($conditions, 'user_id = ?', $this->user->id);

    if (isset ($gets['year']) && $gets['year'] && is_numeric ($gets['year'])) OaModel::addConditions ($conditions, 'year = ?', $gets['year']);
    if (isset ($gets['month']) && $gets['month'] && is_numeric ($gets['month'])) OaModel::addConditions ($conditions, 'month = ?', $gets['month']);
    if (isset ($gets['day']) && $gets['day'] && is_numeric ($gets['day'])) OaModel::addConditions ($conditions, 'day = ?', $gets['day']);
    if (isset ($gets['range']['year']) && isset ($gets['range']['month'])) OaModel::addConditions ($conditions, '((year = ? AND month = ?) OR (year = ? AND month = ?) OR (year = ? AND month = ?))', $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['year'] : $gets['range']['year'] : $gets['range']['year'] - 1, $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['month'] - 1 : 11 : 12, $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['year'] : $gets['range']['year'] : $gets['range']['year'], $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['month'] : 12 : 1, $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['year'] : $gets['range']['year'] + 1 : $gets['range']['year'], $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['month'] + 1 : 1 : 2);

    $schedules = Schedule::find ('all', array (
      'order' => 'sort ASC',
      'include' => array ('tag'),
      'conditions' => $conditions));

    $schedules = array_map (function ($schedule) {
      return $schedule->to_array ();
    }, $schedules);

    return $this->output_json ($schedules);
  }
  public function create () {

    $posts = OAInput::post ();

    if ($msg = $this->_validation_create ($posts))
      return $this->output_error_json ($msg);

    $posts['user_id'] = $this->user->id;
    $create = Schedule::transaction (function () use (&$schedule, $posts) {
      return verifyCreateOrm ($schedule = Schedule::create (array_intersect_key ($posts, Schedule::table ()->columns)));
    });

    if (!$create) return $this->output_error_json ('新增失敗！');
    
    UserLog::transaction (function () use ($schedule) {
      UserLog::create (array (
          'user_id' => $schedule->user_id,
          'content' => '[行程]新增一項行程，標題是 “' . $schedule->title . '”。'
        ));
    });

    return $this->output_json ($schedule->to_array ());
  }

  public function update ($id = 0) {
    $posts = OAInput::post ();

    if ($msg = $this->_validation_update ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $this->schedule->table ()->columns))
      foreach ($columns as $column => $value)
        $this->schedule->$column = $value;

    $schedule = $this->schedule;
    $update = Schedule::transaction (function () use ($schedule) { return $schedule->save (); });

    if (!$update) return $this->output_error_json ('更新失敗！');

    UserLog::transaction (function () use ($schedule) {
      UserLog::create (array (
          'user_id' => $schedule->user_id,
          'content' => '[行程]更新 “' . $schedule->title . '” 行程。'
        ));
    });

    return $this->output_json ($schedule->to_array ());
  }

  public function destroy () {
    $schedule = $this->schedule;
    $delete = Schedule::transaction (function () use ($schedule) { return $schedule->destroy (); });

    if (!$delete) return $this->output_error_json ('刪除失敗！');

    UserLog::transaction (function () use ($schedule) {
      UserLog::create (array (
          'user_id' => $schedule->user_id,
          'content' => '[行程]將 “' . $schedule->title . '” 行程刪除了。'
        ));
    });

    return $this->output_json (array ('message' => '刪除成功！'));
  }
  public function sort ($id = 0) {
    $user = $this->user;
    $posts = OAInput::post ();
    if (!(isset ($posts['data']) && $posts['data']))
      return $this->output_error_json ('更新失敗！');

    if (!$datas = array_filter (array_map (function ($data) use ($user) {
            return array (
                'schedule' => $schedule = Schedule::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $data['id'], $user->id))),
                'sort' => $data['sort'],
                'year' => isset ($data['year']) ? $data['year'] : $schedule->year,
                'month' => isset ($data['month']) ? $data['month'] : $schedule->month,
                'day' => isset ($data['day']) ? $data['day'] : $schedule->day,
              );
          }, $posts['data']), function ($data) {
            return $data['schedule'];
          }))
      return $this->output_error_json ('更新失敗！');

    if (!$datas = array_filter ($datas, function ($data) {
          foreach ($data as $key => $value)
            if ($key != 'schedule')
              $data['schedule']->$key = $value;

          return Schedule::transaction (function () use ($data) { return $data['schedule']->save (); });
        }))
      return $this->output_error_json ('更新失敗！');


    UserLog::transaction (function () use ($user) {
      UserLog::create (array (
          'user_id' => $user->id,
          'content' => '[行程]調整行程順序。'
        ));
    });

    return $this->output_json (array_map (function ($data) {
        return array (
            'id' => $data['schedule']->id,
            'sort' => $data['schedule']->sort
          );
      }, $datas));
  }
  private function _validation_update (&$posts) {
    $keys = array ('title', 'description', 'year', 'month', 'day', 'finish', 'sort', 'tag_id');
    $new_posts = array ();
    foreach ($posts as $key => $value)
      if (in_array ($key, $keys))
        $new_posts[$key] = $value;

    $posts = $new_posts;
    if (isset ($posts['title']) && !($posts['title'] = trim ($posts['title']))) return '標題錯誤！';
    if (!(isset ($posts['description']) && is_string ($posts['description'] = trim ($posts['description'])))) $posts['description'] = '';
    if (isset ($posts['year']) && !(($posts['year'] = trim ($posts['year'])) && is_numeric ($posts['year']))) return '年份錯誤！';
    if (isset ($posts['month']) && !(($posts['month'] = trim ($posts['month'])) && is_numeric ($posts['month']))) return '月份錯誤！';
    if (isset ($posts['day']) && !(($posts['day'] = trim ($posts['day'])) && is_numeric ($posts['day']))) return '日期錯誤！';
    if (isset ($posts['finish']) && !(is_numeric ($posts['finish']) && in_array ($posts['finish'], array_keys (Schedule::$finishNames)))) return '參數錯誤！';
    
    if (!(isset ($posts['tag_id']) && is_numeric ($posts['tag_id'] = trim ($posts['tag_id'])) && (ScheduleTag::find ('one', array ('conditions' => array ('id = ?', $posts['tag_id'])))))) $posts['tag_id'] = NULL;
    else $posts['schedule_tag_id'] = $posts['tag_id'];

    return '';
  }
  private function _validation_create (&$posts) {
    $keys = array ('title', 'description', 'year', 'month', 'day', 'finish', 'sort', 'tag_id');
    $new_posts = array ();
    foreach ($posts as $key => $value)
      if (in_array ($key, $keys))
        $new_posts[$key] = $value;

    $posts = $new_posts;
    if (!(isset ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '標題錯誤！';
    if (!(isset ($posts['year']) && ($posts['year'] = trim ($posts['year'])) && is_numeric ($posts['year']))) return '年份錯誤！';
    if (!(isset ($posts['month']) && ($posts['month'] = trim ($posts['month'])) && is_numeric ($posts['month']))) return '月份錯誤！';
    if (!(isset ($posts['day']) && ($posts['day'] = trim ($posts['day'])) && is_numeric ($posts['day']))) return '日期錯誤！';
    
    if (!(isset ($posts['description']) && is_string ($posts['description'] = trim ($posts['description'])))) $posts['description'] = '';
    if (isset ($posts['finish']) && !(is_numeric ($posts['finish']) && in_array ($posts['finish'], array_keys (Schedule::$finishNames)))) return '參數錯誤！';
    
    if (!(isset ($posts['tag_id']) && is_numeric ($posts['tag_id'] = trim ($posts['tag_id'])) && (ScheduleTag::find ('one', array ('conditions' => array ('id = ?', $posts['tag_id'])))))) $posts['tag_id'] = NULL;
    else $posts['schedule_tag_id'] = $posts['tag_id'];

    return '';
  }
}
