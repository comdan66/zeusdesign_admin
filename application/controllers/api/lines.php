<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */
require FCPATH . 'vendor/autoload.php';
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Constant\EventSourceType;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\VideoMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\Event\MessageEvent\AudioMessage;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;

class Lines extends Api_controller {

  public function __construct () {
    parent::__construct ();
    
  }
  public function test ($str) {
echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
var_dump ($this->searchIWant ($str));
exit ();
  }
  private function searchIWant ($str) {
    preg_match_all ('/我(想|要)*看{0,1}\s*(?P<c>.*)/', $str, $result);
    if (!$result['c']) return array ();
    return preg_split ('/[\s,]+/', $result['c'][0]);
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

    foreach ($events as $event) {
      $instanceof = '';

      if ($event instanceof TextMessage) $instanceof = 'TextMessage';
      if ($event instanceof LocationMessage) $instanceof = 'LocationMessage';
      
      if ($event instanceof VideoMessage) $instanceof = 'VideoMessage';
      if ($event instanceof StickerMessage) $instanceof = 'StickerMessage';
      if ($event instanceof ImageMessage) $instanceof = 'ImageMessage';
      if ($event instanceof AudioMessage) $instanceof = 'AudioMessage';

      $params = array (
          'type' => $event->getType (),
          'instanceof' => $instanceof,
          'reply_token' => $event->getReplyToken (),
          'source_id' => $event->getEventSourceId (),
          'source_type' => $event->isUserEvent() ? EventSourceType::USER : ($event->isGroupEvent () ? EventSourceType::GROUP : EventSourceType::ROOM),
          'timestamp' => $event->getTimestamp (),
          'message_type' => $event->getMessageType (),
          'message_id' => $event->getMessageId (),
          'status' => LinebotLog::STATUS_INIT,
        );
      if (!LinebotLog::transaction (function () use (&$linebotLog, $params) { return verifyCreateOrm ($linebotLog = LinebotLog::create ( array_intersect_key ($params, LinebotLog::table ()->columns))); })) return false;


      switch ($linebotLog->instanceof) {
        case 'TextMessage':
          $params = array (
              'linebot_log_id' => $linebotLog->id,
              'text' => $event->getText (),
            );
          if (!LinebotLogText::transaction (function () use (&$linebotLogText, $params) { return verifyCreateOrm ($linebotLogText = LinebotLogText::create ( array_intersect_key ($params, LinebotLogText::table ()->columns))); })) return false;
          $linebotLog->setStatus (LinebotLog::STATUS_CONTENT);

          if ($keys = $this->searchIWant ($linebotLogText->text)) {
            $linebotLog->setStatus (LinebotLog::STATUS_MATCH);
          
            $this->load->library ('CreateDemo');
            if ($colums = array_map (function ($pic) use ($keys) {
              return new CarouselColumnTemplateBuilder (
                $pic['title'], $pic['title'], $pic['url'],
                array (new UriTemplateActionBuilder ('我要看 ' . $keys[0], $pic['page']))
              );
            }, CreateDemo::pics (3, 8, $keys))) {

              $builder = new TemplateMessageBuilder (implode (',', $keys) . ' 來囉！', new CarouselTemplateBuilder ($colums));
              $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
              $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

              if (!$response->isSucceeded ()) return false;
              $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
              echo 'Succeeded!';
            } else {
              $builder = new TextMessageBuilder ('哭哭，找不到你想要的 ' . $linebotLogText->text . ' 耶..');
              $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
              $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

              if (!$response->isSucceeded ()) return false;
              $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
              echo 'Succeeded!';
            }
          } 


          break;
        case 'LocationMessage':
          $params = array (
              'linebot_log_id' => $linebotLog->id,
              'title' => $event->getTitle (),
              'address' => $event->getAddress (),
              'latitude' => $event->getLatitude (),
              'longitude' => $event->getLongitude (),
            );
          if (!LinebotLogLocation::transaction (function () use (&$linebotLogLocation, $params) { return verifyCreateOrm ($linebotLogLocation = LinebotLogLocation::create ( array_intersect_key ($params, LinebotLogLocation::table ()->columns))); })) return false;
          $linebotLog->setStatus (LinebotLog::STATUS_CONTENT);

          break;
        case 'StickerMessage':
          $params = array (
              'linebot_log_id' => $linebotLog->id,
              'package_id' => $event->getPackageId (),
              'sticker_id' => $event->getStickerId (),
            );
          if (!LinebotLogSticker::transaction (function () use (&$linebotLogSticker, $params) { return verifyCreateOrm ($linebotLogSticker = LinebotLogSticker::create ( array_intersect_key ($params, LinebotLogSticker::table ()->columns))); })) return false;
          $linebotLog->setStatus (LinebotLog::STATUS_CONTENT);
          break;

        case 'VideoMessage': $params = array ('linebot_log_id' => $linebotLog->id,); if (!LinebotLogVideo::transaction (function () use (&$linebotLogText, $params) { return verifyCreateOrm ($linebotLogText = LinebotLogVideo::create ( array_intersect_key ($params, LinebotLogVideo::table ()->columns))); })) return false; $linebotLog->setStatus (LinebotLog::STATUS_CONTENT); break;
        case 'ImageMessage': $params = array ('linebot_log_id' => $linebotLog->id,); if (!LinebotLogImage::transaction (function () use (&$linebotLogText, $params) { return verifyCreateOrm ($linebotLogText = LinebotLogImage::create ( array_intersect_key ($params, LinebotLogImage::table ()->columns))); })) return false; $linebotLog->setStatus (LinebotLog::STATUS_CONTENT); break;
        case 'AudioMessage': $params = array ('linebot_log_id' => $linebotLog->id,); if (!LinebotLogAudio::transaction (function () use (&$linebotLogText, $params) { return verifyCreateOrm ($linebotLogText = LinebotLogAudio::create ( array_intersect_key ($params, LinebotLogAudio::table ()->columns))); })) return false; $linebotLog->setStatus (LinebotLog::STATUS_CONTENT); break;
        default:
          break;
      }

      // if () {


        // $messageBuilder = null;
        // if (preg_match ('/GPS/i', $log->message_text)) {
        //   $buttonTemplateBuilder = new ButtonTemplateBuilder ('2017 白沙屯媽祖 GPS', '2017 白沙屯媽祖 GPS 即時定位，歲次丁酉年，苗栗通霄白沙屯拱天宮媽祖南下北港朝天宮進香 GPS 系統。', 'https://baishatun.godroad.tw/img/og/index.png', array (new UriTemplateActionBuilder ('開啟 GPS 定位', 'https://baishatun.godroad.tw')));
        //   $messageBuilder = new TemplateMessageBuilder ('2017 白沙屯媽祖 GPS', $buttonTemplateBuilder);
        // } else if (preg_match ('/媽祖位置|媽祖在哪|媽祖婆在哪|媽祖在哪裡|媽祖在那|媽祖現在在那|媽祖現在在哪/i', $log->message_text)) {
        //   $this->load->library ('Point');
        //   $cfg = AdminConfig::getVal ('master_point');
        //   $last = $cfg::find ('one', array ('select' => 'lat,lng,lat2,lng2', 'order' => 'id DESC', 'conditions' => array ('enable = ?', Point::IS_ENABLED)));
        //   $latLng = json_decode (file_get_contents ('https://api.baishatun.godroad.tw/gps.json'));
        //   $messageBuilder = new LocationMessageBuilder ('白沙屯媽祖現在的位置', $this->Get_Address_From_Google_Maps ($last->lat2 ? $last->lat2 : $last->lat, $last->lng2 ? $last->lng2 : $last->lng), $last->lat2 ? $last->lat2 : $last->lat, $last->lng2 ? $last->lng2 : $last->lng);
        // } else if ($log->source_type == EventSourceType::USER && preg_match ('/感恩|謝謝/i', $log->message_text)) {
        //   $messageBuilder = new TextMessageBuilder ('不客氣喔：）');
        // } else if ($log->source_type == EventSourceType::USER) {
        //   $messageBuilder = new TextMessageBuilder ('目前我只能接受 "媽祖在哪" 與 "GPS" 的詢問喔～');
        // } else if (($log->message_text == '狀態回報' || $log->message_text == '回報狀態') && $log->source_type == EventSourceType::GROUP && ($log->source_id == 'Cc6be8ee87731e621d54489b430aed9d5' || $log->source_id == 'Ceacec98c68fcd15e66e93216955d0cd6')) {
        //   $this->load->library ('Point');
        //   $cfg = AdminConfig::getVal ('master_point');
        //   $last = $cfg::find ('one', array ('select' => 'time_at', 'order' => 'id DESC', 'conditions' => array ('enable = ?', Point::IS_ENABLED)));
        //   $messageBuilder = new TextMessageBuilder ('目前ＧＰＳ已經 ' . (AdminConfig::getVal ('cron') ? '開啟' : '關閉') . " 接收。\n上一次訊號紀錄是 " . $last->time_at->format ('Y-m-d H:i:s'));
        // } else if (preg_match ('/機器人/i', $log->message_text) && $log->source_type == EventSourceType::GROUP && $log->source_id == 'C8ad2243d1f92cdc01d0dfd6b492efd88') {
        //   $messageBuilder = new TextMessageBuilder (preg_replace ('/機器人/i', '', $log->message_text));
        // }
        // if (!$messageBuilder) return;

        // $response = $bot->replyMessage ($log->reply_token, $messageBuilder);

        // if ($response->isSucceeded ()) {
        //   $log->ok = LinebotLog::IS_ECHO;
        //   $log->save ();

        //   echo 'Succeeded!';
        //   return;
        // }
      // }
    }
  }

  private function Get_Address_From_Google_Maps ($lat, $lng) {

  $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&sensor=false&language=zh-TW';

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
