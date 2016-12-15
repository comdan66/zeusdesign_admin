<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Mail extends OaModel {

  static $table_name = 'mails';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );
  const FINISH_NO  = 0;
  const FINISH_YES = 1;

  static $finishNames = array(
    self::FINISH_NO  => '未寄出',
    self::FINISH_YES => '已寄出',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public static function renderContent ($path, $params = array ()) {
    $CI =& get_instance ();
    return $CI->load->view ($path, $params, true);
  }
  public static function send ($title, $path, $params, $tos, $ccs = array ()) {
    if (!(is_string ($title = trim ($title)) && $title)) return false;
    if (!(is_string ($path = trim ($path)) && $path)) return false;
    if (!is_array ($params)) return false;
    if (!(is_array ($tos = array_filter ($tos, function ($to) { return is_object ($to) && ($to instanceof User) && isset ($to->email) && isset ($to->name); })) && $tos)) return false;
    $ccs = array_filter ($ccs, function ($cc) { return is_object ($cc) && ($cc instanceof User) && isset ($cc->email) && isset ($cc->name); });

    $posts = array (
        'title' => $title,
        'content' => '',
        'to' => '',
        'cc' => '',
        'count_send' => 0,
        'count_open' => 0,
        'finish' => Mail::FINISH_NO,
      );

    if (!Mail::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Mail::create (array_intersect_key ($posts, Mail::table ()->columns))); }))
      return false;

    $CI =& get_instance ();
    $CI->load->library ('OAMail');

    if (isset ($params['url']))
      $params['url'] = $params['url'] . '?id=' . $obj->id;

    $content = Mail::renderContent ($path, $params);

    $mail = OAMail::create ()->setSubject ($title)
                             ->setBody ($content);

    $to_str = $cc_str = $tmp = array ();
    if ($tos)
      foreach ($tos as $to)
        if ($to->id == User::current ()->id)
          array_push ($ccs, $to);
        else if (!in_array ($to->id, $tmp) && array_push ($tmp, $to->id))
          array_push ($to_str, ($to->name . '<' . $to->email . '>')) && $mail->addTo ($to->email, $to->name);
    
    if ($ccs)
      foreach ($ccs as $cc)
        if (!in_array ($cc->id, $tmp) && array_push ($tmp, $cc->id))
          array_push ($cc_str, ($cc->name . '<' . $cc->email . '>')) && $mail->addCC ($cc->email, $cc->name);

    if (!$to_str) return false;
    if (ENVIRONMENT == 'production' && !$mail->send ()) return false;

    $posts = array (
        'to' => implode (', ', $to_str),
        'cc' => implode (', ', $cc_str),
        'content' => $content,
        'count_send' => count ($tmp),
        'finish' => Mail::FINISH_YES,
      );

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    return Mail::transaction (function () use ($obj, $posts) { return $obj->save (); });
  }
}