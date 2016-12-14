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

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public static function renderContent ($path, $params = array ()) {
    $CI =& get_instance ();
    return $CI->load->view ($path, $params, true);
  }
  public static function send ($title, $content, $tos, $ccs = array ()) {
    if (!(is_string ($title = trim ($title)) && $title)) return false;
    if (!(is_string ($content = trim ($content)) && $content)) return false;
    if (!(is_array ($tos) && $tos)) return false;

    $CI =& get_instance ();
    $CI->load->library ('OAMail');

    $mail = OAMail::create ()->setSubject ($title)
                             ->setBody ($content);
    
    $to_str = $cc_str = array ();
    
    if ($tos)
      foreach ($tos as $to)
        if (is_object ($to) && ($to instanceof User) && isset ($to->email) && isset ($to->name))
          if ($to->id == User::current ()->id)
            array_push ($ccs, $to);
          else
            array_push ($to_str, ($to->name . '<' . $to->email . '>')) && $mail->addTo ($to->email, $to->name);

    if ($ccs)
      foreach ($ccs as $cc)
        if (is_object ($cc) && ($cc instanceof User) && isset ($cc->email) && isset ($cc->name))
          array_push ($cc_str, ($cc->name . '<' . $cc->email . '>')) && $mail->addCC ($cc->email, $cc->name);

    if (!$to_str) return false;
    if (ENVIRONMENT == 'production' && !$mail->send ()) return false;

    $posts = array (
        'to' => implode (', ', $to_str),
        'cc' => implode (', ', $cc_str),
        'title' => $title,
        'content' => $content,
      );

    return Mail::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Mail::create (array_intersect_key ($posts, Mail::table ()->columns))); });
  }
}