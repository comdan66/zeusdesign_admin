<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */
require FCPATH . 'vendor/autoload.php';
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\ButtonTemplateBuilder;

class Lines extends Api_controller {

  public function __construct () {
    parent::__construct ();
    
  }
  public function index () {
    $path = FCPATH . 'temp/input.json';
    $channel_id = Cfg::setting ('line', 'channel', 'id');
    $channel_secret = Cfg::setting ('line', 'channel', 'secret');
    $token = Cfg::setting ('line', 'channel', 'token');

    if (!isset ($_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE])) {
      write_file ($path, '===> Error, Header Error!' . "\n", FOPEN_READ_WRITE_CREATE);
      exit ();
    }

    $httpClient = new CurlHTTPClient ($token);
    $bot = new LINEBot ($httpClient, ['channelSecret' => $channel_secret]);

    $signature = $_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE];
    $body = file_get_contents ("php://input");

    
    try {
      $events = $bot->parseEventRequest ($body, $signature);
    } catch (Exception $e) {
      write_file ($path, '===> Error, Events Error! Msg:' . $e->getMessage () . "\n", FOPEN_READ_WRITE_CREATE);
      exit ();
    }
    write_file ($path, 'Data OK..' . "\n", FOPEN_READ_WRITE_CREATE);

    foreach ($events as $event) {
      if ($event instanceof MessageEvent) {
        write_file ($path, '===> Log:' . 'message event has come' . "\n", FOPEN_READ_WRITE_CREATE);
      }
      if ($event instanceof TextMessage) {
        write_file ($path, '===> Log:' . 'text message has come' . "\n", FOPEN_READ_WRITE_CREATE);
      }
      if ($event instanceof LocationMessage) {
        write_file ($path, '===> Log:' . 'location message has come' . "\n", FOPEN_READ_WRITE_CREATE);
      }
      $replyText = $event->getText ();
      
      if (!($event instanceof TextMessage && $replyText == '表演'))
        continue;

      // $resp = $bot->replyText ($event->getReplyToken (), array (
      //   'type' => 'location',
      //   'title' => 'my location',
      //   'address' => '〒150-0002 東京都渋谷区渋谷２丁目２１−１',
      //   'latitude' => 35.65910807942215,
      //   'longitude' => 139.70372892916203,
      //   ));


      // $messageBuilder = new LocationMessageBuilder ('my location', '〒150-0002 東京都渋谷区渋谷２丁目２１−１', 35.65910807942215, 139.70372892916203);
      $messageBuilder = new ButtonTemplateBuilder ('你好', '測試一下', 'http://pic.mazu.ioa.tw/upload/pictures/name/0/0/4/32/2048w_1470185053_5700966ba81a8.jpg', array (
            "type" => "uri",
            "label" => "View detail",
            "uri" => "http://example.com/page/123"
        ));
      $resp = $bot->replyMessage ($event->getReplyToken (), $messageBuilder);
    }
    echo "OK";
  }
}
