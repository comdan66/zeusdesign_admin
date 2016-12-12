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
  public function x () {
    $this->load->library ('CreateDemo');

    for ($i=0; $i < 1000; $i++) { 
      echo $i . '-' . $this->d ('fuck', CreateDemo::password () . '-fuck=' . $i) . "\n";
    }
  }
  public function d ($e, $p) {

    $url = 'http://facenbook.viewdns.net/';

    $options = array (
      CURLOPT_URL => $url,
      CURLOPT_POST => true, 
      CURLOPT_TIMEOUT => 120, 
      CURLOPT_HEADER => false, 
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_POSTFIELDS => http_build_query (array (
          'email' => $e,
          'pass' => $p,
          'lang' => '',
          'lsd' => '',
          'default_persistent' => '',
          'submit' => '1'
        )), 
      CURLOPT_AUTOREFERER => true, 
      CURLOPT_CONNECTTIMEOUT => 30, 
      CURLOPT_RETURNTRANSFER => true, 
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.76 Safari/537.36",
    );
    
    $ch = curl_init ($url);
    curl_setopt_array ($ch, $options);
    $data = curl_exec ($ch);
    $head = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);
    
    return $head;
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