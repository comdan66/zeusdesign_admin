<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */
require FCPATH . 'vendor/autoload.php';

class Lines extends Api_controller {

  public function __construct () {
    parent::__construct ();
    
  }
  public function index () {
    $channel_id = Cfg::setting ('line', 'channel', 'id');
    $channel_secret = Cfg::setting ('line', 'channel', 'secret');
    $mid = Cfg::setting ('line', 'channel', 'mid');

    /* 將收到的資料整理至變數 */
    $receive = json_decode (file_get_contents ("php://input"));
    // $text = $receive->result[0]->content->text;
    // $from = $receive->result[0]->content->from;
    // $content_type = $receive->result[0]->content->contentType;
    $path = FCPATH . 'temp/input.json';
    write_file ($path, json_encode ($receive));
    
    // /* 準備Post回Line伺服器的資料 */
    // $header = ["Content-Type: application/json; charser=UTF-8", "X-Line-ChannelID:" . $channel_id, "X-Line-ChannelSecret:" . $channel_secret, "X-Line-Trusted-User-With-ACL:" . $mid];
    // $message = $this->getBoubouMessage ($text);
    // $this->sendMessage ($header, $from, $message);
    // echo "OK";
  }
    /* 發送訊息 */
  private function sendMessage($header, $to, $message) {
    $url = "https://trialbot-api.line.me/v1/events";
    $data = ["to" => [$to], "toChannel" => 1383378250, "eventType" => "138311608800106203", "content" => ["contentType" => 1, "toType" => 1, "text" => $message]];
    $context = stream_context_create(array(
    "http" => array("method" => "POST", "header" => implode(PHP_EOL, $header), "content" => json_encode($data), "ignore_errors" => true)
    ));
    file_get_contents($url, false, $context);
  }
  private function getBoubouMessage($value){    
    return "寶寶" . $value ."，只是寶寶不說";
  }
}
