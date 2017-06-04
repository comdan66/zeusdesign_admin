<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Schedules extends Api_controller {
  private $user = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();

    if (User::current ()) $this->user = User::current ();
    else $this->user = User::setCurrent (($token = $this->input->get_request_header ('Token')) && ($user = User::find ('one', array ('conditions' => array ('token = ?', $token)))) ? $user : null);
    
    $this->icon = 'icon-ca';

    if (in_array ($this->uri->rsegments (2, 0), array ('finish', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Schedule::find ('one', array ('conditions' => array ('id = ? AND user_id = ? AND task_id = ?', $id, $this->user->id, 0))))))
        return $this->disable ($this->output_error_json ('Not found Data!'));
  }
  public function index () {
    $gets = OAInput::get ();

    if (User::current ()->in_roles (array ('project')) && isset ($gets['user_ids']) && is_array ($gets['user_ids']) && $gets['user_ids'])
      OaModel::addConditions ($conditions, 'user_id IN (?)', array_merge ($gets['user_ids'], array ($this->user->id)));
    else 
      OaModel::addConditions ($conditions, 'user_id = ?', $this->user->id);

    OaModel::addConditions ($conditions, '((user_id = ? && task_id != ?) || task_id = ?)', $this->user->id, 0, 0);


    if (!(isset ($gets['first']) && is_date ($gets['first'] = trim ($gets['first']))))
        return $this->output_error_json ('Parameters Error!');
      
    if (!(isset ($gets['type']) && in_array ($gets['type'] = trim ($gets['type']), array ('w', 'm'))))
        return $this->output_error_json ('Parameters Error!');
      
    $objs = array ();

    switch ($gets['type']) {
      case 'w':
        $gets['first'] = date ('Y-m-d', strtotime ($gets['first'] . ' -' . date ('w', strtotime ($gets['first'])) . ' day'));
        $gets['type'] = 7;
        break;
      
      default:
        $gets['first'] = date ('Y-m-d', strtotime (($a = date ('Y-m-01', strtotime ($gets['first']))) . ' -' . date ('w', strtotime ($a)) . ' day'));
        $gets['type'] = 42;
        break;
    }

    for ($i = 0; $i < $gets['type']; $i++) { 
      $objs[date ('Y-m-d', strtotime ($gets['first'] . ' +' . $i . ' day'))] = array ();
    }

    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    print_r ($objs);
    exit ();



    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // var_dump ( ($gets['first']));
    // exit ();

    // $a = date ('Y-m-d 00:00:00', strtotime ($gets['first'] . ' - ' . date ('w', strtotime ($gets['first'])) . ' day'));
    // $a = date ('Y-m-d H:i:s', strtotime ('first Monday of ' . date ('M', strtotime ($gets['first'])) . ' ' . date ('Y', strtotime ($gets['first'])) . ' -1 days'));
    // $b = date ('Y-m-d H:i:s', strtotime ('last Saturday of ' . date ('M', strtotime ($gets['first'])) . ' ' . date ('Y', strtotime ($gets['first']))));
    // for ($i = 0; $i < $w; $i++) { 
      
    // }

    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($a);
    exit ();
    // if (isset ($gets['year']) && $gets['year'] && is_numeric ($gets['year'])) OaModel::addConditions ($conditions, 'year = ?', $gets['year']);
    // if (isset ($gets['month']) && $gets['month'] && is_numeric ($gets['month'])) OaModel::addConditions ($conditions, 'month = ?', $gets['month']);
    // if (isset ($gets['day']) && $gets['day'] && is_numeric ($gets['day'])) OaModel::addConditions ($conditions, 'day = ?', $gets['day']);
    // if (isset ($gets['range']['year']) && isset ($gets['range']['month'])) OaModel::addConditions ($conditions, '((year = ? AND month = ?) OR (year = ? AND month = ?) OR (year = ? AND month = ?))', $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['year'] : $gets['range']['year'] : $gets['range']['year'] - 1, $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['month'] - 1 : 11 : 12, $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['year'] : $gets['range']['year'] : $gets['range']['year'], $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['month'] : 12 : 1, $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['year'] : $gets['range']['year'] + 1 : $gets['range']['year'], $gets['range']['month'] != 1 ? $gets['range']['month'] != 12 ? $gets['range']['month'] + 1 : 1 : 2);

    $objs = Schedule::find ('all', array ('order' => 'sort ASC, task_id DESC, id DESC', 'include' => array ('tag', 'user'), 'conditions' => $conditions));

    return $this->output_json (array_map (function ($obj) {
        return array (
            'id' => $obj->id,
            'task' => array (
                'id' => $obj->task_id,
                'href' => base_url ('admin', 'my-tasks', $obj->task_id, 'show')
              ),
            'year' => $obj->year,
            'month' => $obj->month,
            'day' => $obj->day,
            'finish' => $obj->finish == Schedule::IS_FINISHED,
            'title' => $obj->title,
            'description' => $obj->description,
            'tag' => $obj->tag (),
            'user' => array (
                'id' => $obj->user->id,
                'name' => $obj->user->name,
                'avatar' => $obj->user->avatar (),
              )
          );
      }, $objs));
  }
  public function create () {
    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $posts['description'] = OAInput::post ('description', false);

    if ($msg = $this->_validation_create ($posts))
      return $this->output_error_json ($msg);

    $posts['task_id'] = 0;
    $posts['user_id'] = $this->user->id;
    $posts['sort'] = ($tmp = Schedule::find ('one', array ('select' => 'sort', 'order' => 'sort DESC', 'conditions' => array ('year = ? AND month = ? AND day = ? AND user_id = ? AND task_id = ?', $posts['year'], $posts['month'], $posts['day'], User::current ()->id, 0)))) ? $tmp->sort + 1 : 0;

    if (!Schedule::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Schedule::create (array_intersect_key ($posts, Schedule::table ()->columns))); }))
      return $this->output_error_json ('新增失敗！');

    if (!$obj->task_id)
      UserLog::create (array (
        'user_id' => User::current ()->id,
        'icon' => $this->icon,
        'content' => '新增一項行程。',
        'desc' => '標題是 “' . $obj->mini_title () . '”' . ($obj->description ? '，內容是「' . $obj->mini_description () . '」' : '') . '。',
        'backup' => json_encode ($obj->columns_val ())));

    return $this->output_json (array (
        'id' => $obj->id,
        'title' => $obj->title,
        'task' => array (
            'id' => $obj->task_id,
            'href' => base_url ('admin', 'my-tasks', $obj->task_id, 'show')
          ),
        'finish' => $obj->finish == Schedule::IS_FINISHED,
        'description' => $obj->description,
        'tag' => $obj->tag ()
      ));
  }

  public function update ($id = 0) {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $posts['description'] = OAInput::post ('description', false);
    $backup = $obj->columns_val (true);

    if ($msg = $this->_validation_update ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Schedule::transaction (function () use ($obj) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    if (!$obj->task_id)
      UserLog::create (array (
        'user_id' => User::current ()->id,
        'icon' => $this->icon,
        'content' => '修改一項行程。',
        'desc' => '標題是 “' . $obj->mini_title () . '”' . ($obj->description ? '，內容是「' . $obj->mini_description () . '」' : '') . '。',
        'backup' => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));
    
    return $this->output_json (array (
        'title' => $obj->title,
        'description' => $obj->description,
        'tag' => $obj->tag ()
      ));
  }

  public function destroy () {
    $obj = $this->obj;
    $task_id = !$obj->task_id;
    $backup = $obj->columns_val (true);

    if (!Schedule::transaction (function () use ($obj) { return $obj->destroy (); }))
      return $this->output_error_json ('刪除失敗！');

    if ($task_id)
      UserLog::create (array (
        'user_id' => User::current ()->id,
        'icon' => $this->icon,
        'content' => '刪除一項行程。',
        'desc' => '已經備份了刪除紀錄，細節可詢問工程師。',
        'backup' => json_encode ($backup)));

    return $this->output_json (array ('message' => '刪除成功！'));
  }

  public function finish () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $backup = $obj->columns_val (true);
    
    $validation = function (&$posts) {
      if (!isset ($posts['finish'])) return '沒有選擇 是否完成！';
      if (!(is_numeric ($posts['finish'] = trim ($posts['finish'])) && in_array ($posts['finish'], array_keys (Schedule::$finishNames)))) return '是否完成 格式錯誤！';
      return '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Schedule::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('更新失敗！');

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '將一項行程標記成 ”' . Schedule::$finishNames[$obj->finish] . '“。',
      'desc' => '將行程 “' . $obj->mini_title () . '” 標記成 「' . Schedule::$finishNames[$obj->finish] . '」。',
      'backup'  => json_encode (array ('ori' => $backup, 'now' => $obj->columns_val (true)))));

    return $this->output_json ($obj->finish == Schedule::IS_FINISHED);
  }
  public function sort ($id = 0) {
    $user = $this->user;
    $posts = OAInput::post ();
    if (!(isset ($posts['data']) && $posts['data']))
      return $this->output_error_json ('更新失敗！');

    if (!$datas = array_filter (array_map (function ($data) use ($user) { return array ('schedule' => $schedule = Schedule::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $data['id'], $user->id))), 'sort' => $data['sort'], 'year' => isset ($data['year']) ? $data['year'] : $schedule->year, 'month' => isset ($data['month']) ? $data['month'] : $schedule->month, 'day' => isset ($data['day']) ? $data['day'] : $schedule->day); }, $posts['data']), function ($data) { return $data['schedule']; }))
      return $this->output_error_json ('更新失敗！');

    $change = array ();
    if (!$datas = array_filter ($datas, function ($data) use (&$change) {
          foreach ($data as $key => $value)
            if ($key != 'schedule') { if ($data['schedule']->$key != $value) array_push ($change, array ('column' => $key, 'value' => array ('old' => $data['schedule']->$key, 'new' => $value))); $data['schedule']->$key = $value; }
          return Schedule::transaction (function () use ($data) { return $data['schedule']->save (); });
        }))
      return $this->output_error_json ('更新失敗！');

    UserLog::create (array (
      'user_id' => User::current ()->id,
      'icon' => $this->icon,
      'content' => '調整一項行程順序。',
      'desc' => '已經備份紀錄，細節可詢問工程師。',
      'backup' => json_encode ($change)));
    
    return $this->output_json (array_map (function ($data) { return array ('id' => $data['schedule']->id, 'sort' => $data['schedule']->sort); }, $datas));
  }

  private function _validation_create (&$posts) {
    if (!isset ($posts['title'])) return '沒有填寫 標題！';
    if (!isset ($posts['year'])) return '沒有填寫 年份！';
    if (!isset ($posts['month'])) return '沒有填寫 月份！';
    if (!isset ($posts['day'])) return '沒有填寫 日期！';

    if (!(is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '標題 格式錯誤！';
    if (!(is_numeric ($posts['year'] = trim ($posts['year'])) && ($posts['year'] >= 1))) return '年份 格式錯誤！';
    if (!(is_numeric ($posts['month'] = trim ($posts['month'])) && ($posts['month'] >= 1) && ($posts['month'] <= 12))) return '月份 格式錯誤！';
    if (!(is_numeric ($posts['day'] = trim ($posts['day'])) && ($posts['day'] >= 1) && ($posts['day'] <= 31))) return '日期 格式錯誤！';

    $posts['description'] = isset ($posts['description']) && is_string ($posts['description']) && ($posts['description'] = trim ($posts['description'])) ? $posts['description'] : '';
    $posts['schedule_tag_id'] = isset ($posts['schedule_tag_id']) && is_numeric ($posts['schedule_tag_id'] = trim ($posts['schedule_tag_id'])) && (ScheduleTag::find ('all', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['schedule_tag_id'])))) ? $posts['schedule_tag_id'] : '';
    return '';
  }
  private function _validation_update (&$posts) {
    if (!isset ($posts['title'])) return '沒有填寫 標題！';
    if (!(is_string ($posts['title']) && ($posts['title'] = trim ($posts['title'])))) return '標題 格式錯誤！';
    $posts['description'] = isset ($posts['description']) && is_string ($posts['description']) && ($posts['description'] = trim ($posts['description'])) ? $posts['description'] : '';
    $posts['schedule_tag_id'] = isset ($posts['schedule_tag_id']) && is_numeric ($posts['schedule_tag_id'] = trim ($posts['schedule_tag_id'])) && (ScheduleTag::find ('all', array ('select' => 'id', 'conditions' => array ('id = ?', $posts['schedule_tag_id'])))) ? $posts['schedule_tag_id'] : '';
    return '';
  }
}
