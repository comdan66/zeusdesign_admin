<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Platform extends Site_controller {

  public function __construct () {
    parent::__construct ();
    $this->load->library ('fb');
  }
  public function mail ($token = '') {
    if (!$mail = Mail::find ('one', array ('select' => 'id, uri, cnt_open, updated_at', 'conditions' => array ('token = ?', $token))))
      return redirect_message (array ('login'), array ('_fd' => ''));

    $mail->cnt_open = $mail->cnt_open + 1;
    $mail->save ();

    if (User::current () && User::current ()->is_login ()) 
      return redirect_message (explode ('/', $mail->uri), array ('_fd' => ''));

    return redirect (forward_static_call_array (array ('Fb', 'loginUrl'), array_merge (array ('platform', 'fb_sign_in'), explode ('/', $mail->uri))));
  }
  public function login () {
    if (User::current () && User::current ()->is_login ()) return redirect_message (array ('admin', 'my'), array ());
    else $this->set_frame_path ('frame', 'pure')
              ->set_title ('登入')
              ->load_view (array (
                  'posts' => Session::getData ('posts', true)
                ));
  }

  public function ap_sign_in () {
    $posts = OAInput::post ();

    if (!(($account = $posts['account']) && ($password = $posts['password']) && is_string ($account) && ($account = trim ($account)) && is_string ($password) && ($password = trim ($password)) && ($password = password ($password))))
      return redirect_message (array ('login'), array ('_fd' => '帳密 登入錯誤，資訊錯誤!(1)', 'posts' => $posts));

    if (!($user = User::find ('one', array ('conditions' => array ('account = ?', $account)))))
      // if (!User::transaction (function () use (&$user, $account, $password) {
      //   return verifyCreateOrm ($user = User::create (array_intersect_key (array (
      //       'fid' => '',
      //       'account' => $account,
      //       'password' => $password,
      //       'token' => token ($account),
      //       'name' => $account,
      //       'email' => '',
      //     ), User::table ()->columns))) && $user->create_set ();
      //   })) return redirect_message (array ('login'), array ('_fd' => '帳密 登入錯誤，請通知程式設計人員!(2)', 'posts' => $posts));
    
    if (!(isset ($user) && $user->password === $password))
      return redirect_message (array ('login'), array ('_fd' => '帳密 登入錯誤，密碼或帳號有誤！', 'posts' => $posts));
    
    if (!$user->set) $user->create_set ();

    $user->set->login_count += 1;
    $user->set->logined_at = date ('Y-m-d H:i:s');

    if (!User::transaction (function () use ($user) { return $user->save () && $user->set->save (); }))
      return redirect_message (array ('login'), array ('_fd' => 'Facebook 登入錯誤，請通知程式設計人員!(3)', 'posts' => $posts));

    Session::setData ('user_token', $user->token);
    return redirect_message (func_get_args (), array ('_fi' => '使用 Facebook 登入成功!'));
  }
  
  public function fb_sign_in () {
    if (!(Fb::login () && ($me = Fb::me ()) && ((isset ($me['name']) && ($name = $me['name'])) && (isset ($me['id']) && ($fid = $me['id'])))))
      return redirect_message (array ('login'), array ('_fd' => 'Facebook 登入錯誤，請通知程式設計人員!(1)'));
    
    $email = isset ($me['email']) ? $me['email'] : '';

    if (!($user = User::find ('one', array ('conditions' => array ('fid = ?', $fid)))))
      if (!User::transaction (function () use (&$user, $fid, $name, $email) {
        return verifyCreateOrm ($user = User::create (array_intersect_key (array (
          'fid' => $fid,
          'account' => '',
          'password' => '',
          'token' => token ($fid),
          'name' => $name,
          'email' => $email,
        ), User::table ()->columns))) && $user->create_set ();
      }))
        return redirect_message (array ('login'), array ('_fd' => 'Facebook 登入錯誤，請通知程式設計人員!(2)'));

    // $user->name = $name;
    // $user->email = $email;
    
    if (!$user->set) $user->create_set ();

    $user->set->login_count += 1;
    $user->set->logined_at = date ('Y-m-d H:i:s');

    if (!User::transaction (function () use ($user) { return $user->save () && $user->set->save (); }))
      return redirect_message (array ('login'), array ('_fd' => 'Facebook 登入錯誤，請通知程式設計人員!(3)'));

    Session::setData ('user_token', $user->token);
    return redirect_message (func_get_args (), array ('_fi' => '使用 Facebook 登入成功!'));
  }

  public function logout () {
    Session::setData ('user_token', '');
    return redirect_message ('login', array ('_fi' => '登出成功!'));
  }
}
