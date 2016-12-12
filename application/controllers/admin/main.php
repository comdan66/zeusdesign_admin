<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */
class Main extends Admin_controller {

  public function all_calendar () {
    return $this->add_param ('now_url', base_url ('admin', 'all-calendar'))
                ->load_view (array (
                  'id' => User::current ()->id
      ));
  }
  public function calendar () {
    return $this->add_param ('now_url', base_url ('admin', 'calendar'))
                ->load_view ();
  }
  public function index ($type = 'schedules', $offset = 0) {
    $pagination = '';
    $user_logs = $schedules = $columns = array ();
    $configs = array ('admin', 'my', $type, '%s');
    $conditions = conditions ($columns, $configs);

    if ($type == 'user_logs') {
      $limit = 5;
      OaModel::addConditions ($conditions, 'user_id = ?', User::current ()->id);
      
      $total = UserLog::count (array ('conditions' => $conditions));
      $logs = UserLog::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'conditions' => $conditions));
      $pagination = $this->_get_pagination ($limit, $total, $configs);

      foreach ($logs as $log)
        if (!isset ($user_logs[$log->created_at->format ('Y-m-d')])) $user_logs[$log->created_at->format ('Y-m-d')] = array ($log);
        else array_push ($user_logs[$log->created_at->format ('Y-m-d')], $log);
    } else {
      $limit = 10;
      OaModel::addConditions ($conditions, 'user_id = ?', User::current ()->id);
      OaModel::addConditions ($conditions, 'year = ?', date ('Y'));
      OaModel::addConditions ($conditions, 'month = ?', date ('m'));
      OaModel::addConditions ($conditions, 'day = ?', date ('d'));

      $total = Schedule::count (array ('conditions' => $conditions));
      $schedules = Schedule::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'include' => array ('tag'), 'order' => 'sort ASC, id DESC', 'conditions' => $conditions));
      $pagination = $this->_get_pagination ($limit, $total, $configs);
    }

    $logs = UserLog::find ('all', array (
      'select' => 'count(id) AS cnt, created_at, DATE(`created_at`) AS date',
      'limit' => 365,
      'group' => 'date',
      'order' => 'date DESC',
      'conditions' => array ('user_id = ?', User::current ()->id)));
    $logs = array_combine (column_array ($logs, 'date'), $logs);

    $chart = array ();
    for ($i = 0; $i < 12; $i++) $chart[$date = date ('Y-m-d', strtotime (date ('Y-m-d') . $i ? '-' . $i . ' day' : ''))] = isset ($logs[$date]) ? $logs[$date]->cnt : 0;
    $chart = array_reverse ($chart);

    return $this->add_param ('now_url', base_url ('admin', 'my'))
                ->load_view (array (
        'user' => User::current (),
        'chart' => $chart,
        'type' => $type,
        'logs' => $logs,
        'user_logs' => $user_logs,
        'schedules' => $schedules,
        'pagination' => $pagination
      ));
  }
}
