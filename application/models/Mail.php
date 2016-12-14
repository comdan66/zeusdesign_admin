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
  public static function send ($title, $content, $tos, $ccs) {
    if (!(is_string ($title = trim ($title)) && $title)) return false;
    if (!(is_string ($content = trim ($content)) && $content)) return false;
    if (!(is_array ($tos) && $tos)) return false;

    $CI =& get_instance ();
    $CI->load->library ('OAMail');

    $mail = OAMail::create ()->setSubject ($title)
                             ->setBody ($content);
    
    $to = $cc = '';
    if ($tos) foreach ($tos as $m => $n) if ($to .= ($n . '<' . $m . '>')) $mail->addTo ($m, $n);
    if ($ccs) foreach ($ccs as $m => $n) if ($cc .= ($n . '<' . $m . '>'))$mail->addCC ($m, $n);

    // if (!$mail->send ()) return false;
    $posts = array (
        'to' => $to,
        'cc' => $cc,
        'title' => $title,
        'content' => $content,
      );

    return Mail::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Mail::create (array_intersect_key ($posts, Mail::table ()->columns))); });
  }
}