<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Mail extends OaModel {

  static $table_name = 'mails';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '尚未寄出',
    self::STATUS_2 => '已經寄出',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public static function renderLink ($text, $href = '') { if (is_array ($text)) { $href = isset ($text['href']) ? $text['href'] : (isset ($text[1]) ? $text[1] : ''); $text = isset ($text['text']) ? $text['text'] : (isset ($text[0]) ? $text[0] : ''); } return ($text = trim ($text)) ? '<a' . ($href ? ' href="' . $href . '"' : '') . ' style="color: #4285f4; font-weight: normal; text-decoration: none; cursor: pointer; border-bottom: 1px solid rgba(66, 133, 244, 0.3);">' . $text . '</a>' : ''; }
  public static function renderEmpty ($text = '') { return '<div style="display: block; text-align: center; color: #d7d7d7; font-size: 20px; font-weight: bold; margin: 42px 0;">無任何內容。</div>'; }
  public static function renderTitle ($text = '') { return ($text = trim ($text)) ? '<div style="display: block; color: #373737; padding: 8px 24px; margin-top: 8px; text-align: left;">' . $text . '</div>' : ''; }
  public static function renderUl ($li = array ()) { return is_array ($li) && $li ? '<ul style="display: block; padding: 16px 48px; text-align: left; margin: 0;">' . implode ('', $li) . '</ul>' : ''; }
  public static function renderOl ($li = array ()) { return is_array ($li) && $li ? '<ol style="display: block; padding: 16px 48px; text-align: left; margin: 0;">' . implode ('', $li) . '</ol>' : ''; }
  public static function renderLi ($text, $link = array ()) { return ($text = trim ($text)) || $link ? '<li style="color: #555555; word-break: break-all; margin-top: 0; margin-bottom: 12px; line-height: 20px; font-size: 14px;">' . $text . '' . ($link ? ' - ' . $link : '') . '</li>' : ''; }
  public static function renderB ($text) { return ($text = trim ($text)) ? '<b style="font-weight: bold; color: #373737;">' . $text . '</b>' : ''; }
  public static function renderP ($text) { return ($text = trim ($text)) ? '<p style="display: block; margin-top: 0; margin-bottom: 12px; line-height: 23px; font-size: 14px; color: #555555; word-break: break-all;">' . $text . '</p>' : ''; }
  public static function renderSection ($text) { return ($text = trim ($text)) ? '<div style="display: block; padding: 16px 32px; text-align: left;">' . $text . '</div>' : ''; }
  public static function renderUser ($name) { return Mail::renderB ($name); }


  public static function send ($title, $tos, $cb = null, $uri = '', $ccs = array ()) {
    if (!(is_string ($title) && ($title = trim ($title)))) return false;
    if (!($cb == null || is_callable ($cb))) return false;
    if (!is_array ($tos)) $tos = array ($tos);
    if (!is_array ($ccs)) $ccs = array ($ccs);

    if (!$tos = array_values (array_filter (array_map (function ($to) {
        if (is_object ($to) && ($to instanceof User)) return (isset ($to->name) && isset ($to->email)) || (isset ($to->id) && ($to = User::find ('one', array ('select' => 'name,email', 'conditions' => array ('id = ?', $to))))) ? array ('name' => $to->name, 'email' => $to->email) : false;
        else if (is_numeric ($to) && ($to = User::find ('one', array ('select' => 'name,email', 'conditions' => array ('id = ?', $to))))) return array ('name' => $to->name, 'email' => $to->email);
        else if (is_string ($to) && filter_var ($to, FILTER_VALIDATE_EMAIL) && count ($to = explode ('@', $to)) == 2) return array ('name' => $to[0], 'email' => $to[0] . '@' . $to[1]);
        else if (is_array ($to) && count ($to) >= 2) return isset ($to['name']) && isset ($to['email']) ? array ('name' => $to['name'], 'email' => $to['email']) : (isset ($to[0]) && isset ($to[1]) ? array ('name' => $to[0], 'email' => $to[1]) : false);
        else return false;
      }, $tos)))) return false;

    $ccs = array_values (array_filter (array_map (function ($cc) {
        if (is_object ($cc) && ($cc instanceof User)) return (isset ($cc->name) && isset ($cc->email)) || (isset ($cc->id) && ($cc = User::find ('one', array ('select' => 'name,email', 'conditions' => array ('id = ?', $cc))))) ? array ('name' => $cc->name, 'email' => $cc->email) : false;
        else if (is_numeric ($cc) && ($cc = User::find ('one', array ('select' => 'name,email', 'conditions' => array ('id = ?', $cc))))) return array ('name' => $cc->name, 'email' => $cc->email);
        else if (is_string ($cc) && filter_var ($cc, FILTER_VALIDATE_EMAIL) && count ($cc = explode ('@', $cc)) == 2) return array ('name' => $cc[0], 'email' => $cc[0] . '@' . $cc[1]);
        else if (is_array ($cc) && isset ($cc['name']) && isset ($cc['email'])) return array ('name' => $cc['name'], 'email' => $cc['email']);
        else return false;
      }, $ccs)));
    
    $posts = array (
        'title' => $title,
        'content' => '',
        'uri' => $uri = ($uri = trim ($uri)) && ($uri = trim ($uri, '/')) ? $uri : '',
        'to' => implode (',', array_map (function ($t) { return $t['name'] . '<' . $t['email'] . '>';}, $tos)),
        'cc' => implode (',', array_map (function ($t) { return $t['name'] . '<' . $t['email'] . '>';}, $ccs)),
        'cnt_send' => count ($tos) + count ($ccs),
        'cnt_open' => 0,
        'status' => Mail::STATUS_1,
        'token' => token ()
      );

    if (!Mail::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Mail::create (array_intersect_key ($posts, Mail::table ()->columns))); }))
      return false;

    $CI =& get_instance ();
    $CI->load->library ('OAMail');

    $obj->token = token ($obj->id);
    $obj->content = $CI->load->view ('mail/01.php', array ('datas' => $cb ? $cb ($obj) : array ()), true);
    $mail = OAMail::create ()->setSubject ($title)
                             ->setBody ($obj->content);

    $tmp = array ();
    foreach ($tos as $to)
      if (!in_array ($to['email'], $tmp) && array_push ($tmp, $to['email']))
        $mail->addTo ($to['email'], $to['name']);
    
    if ($ccs)
      foreach ($ccs as $cc)
        if (!in_array ($cc['email'], $tmp) && array_push ($tmp, $cc['email']))
          $mail->addCC ($to['email'], $to['name']);

    if (!$tmp) return false;
    if (ENVIRONMENT == 'production' && !$mail->send ()) return false;

    $obj->cnt_send = count ($tmp);
    $obj->status = Mail::STATUS_2;
    
    return Mail::transaction (function () use ($obj, $posts) { return $obj->save (); });
  }

  public function mini_content ($length = 100) {
    if (!isset ($this->content)) return '';
    return $length ? mb_strimwidth (remove_ckedit_tag ($this->content), 0, $length, '…','UTF-8') : remove_ckedit_tag ($this->content);
  }
}