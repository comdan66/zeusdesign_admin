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
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

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
      
      $messageBuilder = null;
      if ($event instanceof TextMessage && preg_match ('/GPS/i', $replyText)) {

        $buttonTemplateBuilder = new ButtonTemplateBuilder ('2017 白沙屯媽祖 GPS', '2017 白沙屯媽祖 GPS 即時定位，歲次丁酉年，苗栗通霄白沙屯拱天宮媽祖南下北港朝天宮進香 GPS 系統。', 'https://baishatun.godroad.tw/img/og/index.png', array (
            new UriTemplateActionBuilder ('開啟 GPS 定位', 'https://baishatun.godroad.tw'),
          ));
        $messageBuilder = new TemplateMessageBuilder ('2017 白沙屯媽祖 GPS', $buttonTemplateBuilder);
        
      }
      if ($event instanceof TextMessage && preg_match ('/媽祖位置|媽祖在哪/i', $replyText)) {
        $latLng = json_decode (file_get_contents ('https://api.baishatun.godroad.tw/gps.json'));
        $messageBuilder = new LocationMessageBuilder ('媽祖現在的位置', Get_Address_From_Google_Maps ($latLng[0], $latLng[1]), $latLng[0], $latLng[1]);
      }


      if (!$messageBuilder) return;

      write_file ($path, 'Data OK3..' . "\n", FOPEN_READ_WRITE_CREATE);
      $resp = $bot->replyMessage ($event->getReplyToken (), $messageBuilder);
      
      if ($response->isSucceeded ()) {
          write_file ($path, 'Succeeded!' . "\n", FOPEN_READ_WRITE_CREATE);
          echo 'Succeeded!';
          return;
      } else {
          write_file ($path, $response->getHTTPStatus . ' ' . $response->getRawBody () . "\n", FOPEN_READ_WRITE_CREATE);
          return;
      }
    }
  }


  private function Get_Address_From_Google_Maps ($lat, $lng) {

  $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&sensor=false';

  $data = @file_get_contents($url);
  $jsondata = json_decode($data,true);

  if (!$this->check_status ($jsondata)) return '';

  // $address = array(
  //     'country' => google_getCountry($jsondata),
  //     'province' => google_getProvince($jsondata),
  //     'city' => google_getCity($jsondata),
  //     'street' => google_getStreet($jsondata),
  //     'postal_code' => google_getPostalCode($jsondata),
  //     'country_code' => google_getCountryCode($jsondata),
  //     'formatted_address' => google_getAddress($jsondata),
  // );

  return $this->google_getAddress ($jsondata);
  }
  private function check_status ($jsondata) {
      if ($jsondata["status"] == "OK") return true;
      return false;
  }
  private function google_getAddress ($jsondata) {
      return $jsondata["results"][0]["formatted_address"];
  }
}
