<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Cli extends Oa_controller {

  public function __construct () {
    parent::__construct ();
    
    if (!$this->input->is_cli_request ()) {
      echo 'Request 錯誤！';
      exit ();
    }
  }
  public function clean_query () {
    $this->load->helper ('file');
    write_file (FCPATH . 'application/logs/query.log', '', FOPEN_READ_WRITE_CREATE_DESTRUCTIVE);
  }
  public function token () {
    $users = User::all ();
    foreach ($users as $user) {
      $user->token = token ($user->id);
      $user->save ();
    }
  }
  public function mail () {
    $this->load->library ('OAMail');

    $mail = OAMail::create ()->setSubject ('[宙思設計] 留言成功通知！')
                             ->setBody ("<div style='display: inline-block; margin: 0 auto; width: 600px; padding: 0; -moz-border-radius: 2px; -webkit-border-radius: 2px; border-radius: 2px; background-color: white;'><div style='display: block; color: #2a3f54; padding: 10px; border-bottom: 1px solid #e5e5e5;'><img src='https://cdn.zeusdesign.com.tw/mail/icons/logo_v1.png' style='display: inline-block; height: 40px; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px;'></div><div style='display: block; padding: 20px; color: #2e323b; border-bottom: 1px solid #e5e5e5; padding-bottom: 50px;'><p style='color: #222222; margin-top: 5px; line-height: 23px;'>您有一個來自於 <b>Teresa</b> 的新任務，以下是任務概述，詳細內容請您上 <a href='' style='display: inline-block; color: rgba(42, 90, 149, 0.7); font-weight: normal; text-decoration: none; padding: 0 2px; padding-bottom: 0; -moz-transition: all 0.3s; -o-transition: all 0.3s; -webkit-transition: all 0.3s; transition: all 0.3s;'>宙思後台<a> 查看詳細內容吧！</p><div style='border: 1px solid #bcbcbc; padding: 5px 15px; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; background-color: #f5f7fa;'><p style='color: #203041; margin: 5px 0; padding: 0 5px; line-height: 25px;'><b>任務名稱：</b><span>回家</span></p><p style='color: #203041; margin: 5px 0; margin-top: 10px; padding: 0 5px; padding-top: 10px; border-top: 1px dashed #e5e5e5; line-height: 25px;'><b>任務敘述：</b><span>回家回家回家回家回家，家回家回家回家，家回家回家回家，家回家回家回家。</span></p></div><a style='border: 1px solid rgba(255, 0, 0, 0.3); margin-top: 25px; padding: 10px; background-color: #ce5341; color: white; border: 1px solid #c33a2f; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; display: inline-block; text-decoration: inherit;' href=''>到 宙思後台 上查看</a></div><div style='text-align: right; font-size: 13px; display: block; padding-right: 15px; padding-top: 15px; color: #6d7780;padding-bottom: 15px;'>Copyright by ZEUS © 2016</div></div>")
                             ->addTo ('comdan66@gmail.com', 'OA');
    $mail->send ();
  }
  public function fcm () {

    $url = 'https://fcm.googleapis.com/fcm/send';

    $fields = array (
      'to' => '/topics/all',
      'notification' => array (
        "title" => 'abc',
        "body" => '123',
        'badge' => 1,
      ),
      'priority' => 10
    );
    $fields = json_encode ($fields);

    $headers = array (
      'Authorization: key=',
      'Content-Type: application/json'
    );

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_POST, true );
    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    // curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
    // curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

    $result = curl_exec ( $ch );
    var_dump ($result);
    var_dump (curl_error($ch));
    curl_close ( $ch );
  }
  public function index () {
    $banners = array_map (function ($banner) {
      return array (
          'id' => $banner->id,
          'title' => $banner->title,
          'content' => $banner->content,
          'cover' => $banner->cover->url ('800w'),
          'link' => $banner->link,
          'target' => $banner->target,
        );
    }, Banner::find ('all', array ('order' => 'sort DESC', 'conditions' => array ('is_enabled = ?', Banner::ENABLE_YES))));
    write_file (FCPATH . 'api/banners.json', json_encode ($banners));

    $promos = array_map (function ($promo) {
      return array (
          'id' => $promo->id,
          'title' => $promo->title,
          'content' => $promo->content,
          'cover' => $promo->cover->url ('500w'),
          'link' => $promo->link,
          'target' => $promo->target,
        );
    }, Promo::find ('all', array ('order' => 'sort DESC', 'conditions' => array ('is_enabled = ?', Promo::ENABLE_YES))));
    write_file (FCPATH . 'api/promos.json', json_encode ($promos));

    $articles = array_map (function ($article) {
        return $article->to_array ();
      }, Article::find ('all', array ('include' => array ('user', 'tags', 'sources'), 'order' => 'id DESC', 'conditions' => array ('is_enabled = ?', Article::ENABLE_YES))));
    write_file (FCPATH . 'api/articles.json', json_encode ($articles));
    
    $works = array_map (function ($work) {
      return $work->to_array ();
    }, Work::find ('all', array ('include' => array ('user', 'images', 'tags', 'blocks'), 'order' => 'id DESC', 'conditions' => array ('is_enabled = ?', Work::ENABLE_YES))));
    write_file (FCPATH . 'api/works.json', json_encode ($works));
  }
}



// curl -X POST --header "Authorization: key=AAAAaR1d2Tk:APA91bGpyK-D6G_qxw3RLwlmQ7PTG33wpD-KBjMKCGFOOI7PtvFICGpHgDu1il5snwYobBNv0fA4X0nJqi4PFETYs2NZno7cpu__HEnpLLBnO3a1PvqB2DT-KZOldt5eg4j_bIBT815830E0zkC2h8qbPoBnF8nS9g" --Header "Content-Type: application/json" https://fcm.googleapis.com/fcm/send -d "{\"to\":\"e852fBr20_I:APA91bG1rFNNKbsqaz9aA9riqCypwCdfN3jZ2XdSRNxC0OHLzuZncMzpwvZHYziTOjdj50XW6bO084AOOnxA4kLq5ibNtBiscZarygUbA0QLjDYpYiT1fZ4xiMYaepCfvzERA7fgEoG2\",\"notification\":{\"body\":\"Yellow\"},\"priority\":10}"



// curl -X POST --header "Authorization: key={ Key }" --Header "Content-Type: application/json" https://fcm.googleapis.com/fcm/send -d "{\"to\": \"{ Registration ID }\",\"notification\":{\"body\": \"Yellow\"},\"priority\":10}"

// [[NSNotificationCenter defaultCenter] addObserver:self
//                                          selector:@selector(tokenRefreshNotification:)
//                                              name:kFIRInstanceIDTokenRefreshNotification
//                                            object:nil];

// - (void)tokenRefreshNotification:(NSNotification *)notification {
//     NSString *refreshedToken = [[FIRInstanceID instanceID] token];
// }
    

    



// curl -X POST --header "Authorization: key=AAAAaR1d2Tk:APA91bGpyK-D6G_qxw3RLwlmQ7PTG33wpD-KBjMKCGFOOI7PtvFICGpHgDu1il5snwYobBNv0fA4X0nJqi4PFETYs2NZno7cpu__HEnpLLBnO3a1PvqB2DT-KZOldt5eg4j_bIBT815830E0zkC2h8qbPoBnF8nS9g" --Header "Content-Type: application/json" https://fcm.googleapis.com/fcm/send -d '{ "to": "/topics/test1", "notification":{"body":"Red"}, "priority": 10}'


// display: inline-block;
// width: 120px;
// height: 120px;
// line-height: 114px;
// font-size: 80px;
// border: 1px solid red;
// text-align: center;

// margin: 5px;
// color: rgba(183, 183, 188, 1.00);