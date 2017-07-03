<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class My extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('member')))
      return redirect_message (array ('admin'), array ('_fd' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/my';
    $this->icon = 'icon-home';
    $this->title = '個人首頁';

    if (!$this->obj = User::find ('one', array ('conditions' => array ('id = ?', User::current ()->id))))
      return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }

  public function index () {
    $obj = $this->obj;


    $logs = UserLog::find ('all', array (
      'select' => 'count(id) AS cnt, DATE(`created_at`) AS date',
      'limit' => 365,
      'group' => 'date',
      'order' => 'date DESC',
      'conditions' => array ('user_id = ? AND status = ?', $obj->id, UserLog::STATUS_2)));
    $logs = array_combine (column_array ($logs, 'date'), column_array ($logs, 'cnt', function ($t) { return (int) $t;}));

    $tls = array_filter ($logs);
    arsort ($tls);
    $u = floor (count ($tls = array_values ($tls)) / 5);
    $s = array (0, $tls[$u * 4], $tls[$u * 3], $tls[$u * 2], $tls[$u * 1]);
    $logs = array_map (function ($t) use ($s) { return array ('cnt' => $t, 's' => $t <= $s[4] ? $t <= $s[3] ? $t <= $s[2] ? $t <= $s[1] ? 's0' : 's1' : 's2' : 's3' : 's4');}, $logs);

    $this->load_view (array (
        'obj' => $obj,
        'logs' => $logs,
      ));
  }
}
