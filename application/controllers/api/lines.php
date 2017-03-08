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
  public function test () {
    $this->load->library ('AlleyGet');

echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
var_dump ($this->searchDont ('我不想看電影 我等等'));
exit ();
  }
  private function searchIWantLook ($str) {
    preg_match_all ('/我{0,1}(想|要)*找\s*(?P<c>.*)/', $str, $result);
    if (!$result['c']) return array ();
    return preg_split ('/[\s,]+/', $result['c'][0]);
  }
  private function searchIWantListen ($str) {
    preg_match_all ('/我{0,1}(想|要)*(聽|看)\s*(?P<c>.*)/', $str, $result);
    if (!$result['c']) return array ();
    return preg_split ('/[\s,]+/', $result['c'][0]);
  }
  private function searchIWantEat ($str) {
    preg_match_all ('/我{0,1}(想|要)*(吃)\s*(?P<c>.*)/', $str, $result);
    if (!$result['c']) return array ();
    return preg_split ('/[\s,]+/', $result['c'][0]);
  }
  private function searchRecommend ($str) {
    preg_match_all ('/(?P<c>(吃什麼|吃啥|好吃的|啥好吃|要吃啥|什麼好吃))/', $str, $result);
    if (!$result['c']) return array ();
    return preg_split ('/[\s,]+/', $result['c'][0]);
  }
  private function searchDont ($str) {
    preg_match_all ('/不\s*(?P<c>(想|要|可|準).*)/', $str, $result);
    if (!$result['c']) return array ();
    return preg_split ('/[\s,]+/', $result['c'][0]);
  }
  private function search3Q ($str) {
    preg_match_all ('/(?P<c>(謝|3Q|3q).*)/', $str, $result);
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

          if ($keys = $this->searchIWantLook ($linebotLogText->text)) {
            $linebotLog->setStatus (LinebotLog::STATUS_MATCH);
            $this->load->library ('CreateDemo');
            if (($colums = CreateDemo::pics (4, 5, $keys)) && ($colums = array_map (function ($pic) use ($keys) {
                return new CarouselColumnTemplateBuilder (
                  mb_strimwidth ($pic['title'], 0, 18 * 2, '…','UTF-8'),
                  mb_strimwidth ($pic['title'], 0, 28 * 2, '…','UTF-8'),
                  $pic['url'],
                  array (new UriTemplateActionBuilder (mb_strimwidth ('我要看 ' . $pic['title'], 0, 8 * 2, '…','UTF-8'), $pic['page']))
                );
              }, $colums))) {

              $builder = new TemplateMessageBuilder (mb_strimwidth (implode (',', $keys) . ' 來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder ($colums));
              $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
              $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

              if (!$response->isSucceeded ()) return false;
              $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
              echo 'Succeeded!';
            } else {
              $builder = new TextMessageBuilder ('哭哭，找不到你想要的 ' . implode (' ', $keys) . ' 耶..');
              $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
              $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

              if (!$response->isSucceeded ()) return false;
              $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
              echo 'Succeeded!';
            }
          } else if ($keys = $this->searchIWantListen ($linebotLogText->text)) {
            $linebotLog->setStatus (LinebotLog::STATUS_MATCH);
            $this->load->library ('YoutubeGet');

            if (($colums = YoutubeGet::search (array ('q' => implode (' ', $keys), 'maxResults' => rand (3, 5)))) && ($colums = array_map (function ($youtube) use ($keys) {
                return new CarouselColumnTemplateBuilder (
                  mb_strimwidth ($youtube['title'], 0, 18 * 2, '…','UTF-8'),
                  mb_strimwidth ($youtube['title'], 0, 28 * 2, '…','UTF-8'),
                  $youtube['thumbnails'][count ($youtube['thumbnails']) - 1]['url'],
                  array (new UriTemplateActionBuilder (mb_strimwidth ('我要聽 ' . $youtube['title'], 0, 8 * 2, '…','UTF-8'), 
                    'https://www.youtube.com/watch?v=' . $youtube['id']))
                );
              }, $colums))) {

              $builder = new TemplateMessageBuilder (mb_strimwidth (implode (',', $keys) . ' 來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder ($colums));
              $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
              $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

              if (!$response->isSucceeded ()) return false;
              $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
              echo 'Succeeded!';
            } else {
              $builder = new TextMessageBuilder ('哭哭，找不到你想要的 ' . implode (' ', $keys) . ' 耶..');
              $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
              $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

              if (!$response->isSucceeded ()) return false;
              $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
              echo 'Succeeded!';
            }
          } else if ($keys = $this->searchIWantEat ($linebotLogText->text)) {
            $linebotLog->setStatus (LinebotLog::STATUS_MATCH);
            $this->load->library ('AlleyGet');

            if (($colums = AlleyGet::search (implode (' ', $keys))) && ($colums = array_map (function ($store) {
                return new CarouselColumnTemplateBuilder (
                  mb_strimwidth ($store['title'], 0, 18 * 2, '…','UTF-8'),
                  mb_strimwidth ($store['desc'], 0, 28 * 2, '…','UTF-8'),
                  $store['img'],
                  array (new UriTemplateActionBuilder (mb_strimwidth ('我要吃 ' . $store['title'], 0, 8 * 2, '…','UTF-8'), $store['url']))
                );
              }, $colums))) {

              $builder = new TemplateMessageBuilder (mb_strimwidth ('這幾家美食參考一下喔！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder ($colums));
              $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
              $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

              if (!$response->isSucceeded ()) return false;
              $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
              echo 'Succeeded!';
            } else {
              $builder = new TextMessageBuilder ('哭哭，找不到 ' . implode (' ', $keys) . ' 耶..');
              $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
              $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

              if (!$response->isSucceeded ()) return false;
              $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
              echo 'Succeeded!';
            }
          } else if ($keys = $this->searchRecommend ($linebotLogText->text)) {
            $linebotLog->setStatus (LinebotLog::STATUS_MATCH);
            $this->load->library ('AlleyGet');

            if (($colums = AlleyGet::recommend ()) && ($colums = array_map (function ($store) {
                return new CarouselColumnTemplateBuilder (
                  mb_strimwidth ($store['title'], 0, 18 * 2, '…','UTF-8'),
                  mb_strimwidth ($store['desc'], 0, 28 * 2, '…','UTF-8'),
                  $store['img'],
                  array (new UriTemplateActionBuilder (mb_strimwidth ('我要吃 ' . $store['title'], 0, 8 * 2, '…','UTF-8'), $store['url']))
                );
              }, $colums))) {

              $builder = new TemplateMessageBuilder (mb_strimwidth ('推薦的美食來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder ($colums));
              $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
              $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

              if (!$response->isSucceeded ()) return false;
              $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
              echo 'Succeeded!';
            } else {
              $builder = new TextMessageBuilder ('哭哭，這附近沒什麼美食耶..');
              $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
              $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

              if (!$response->isSucceeded ()) return false;
              $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
              echo 'Succeeded!';
            }
          } else if ($keys = $this->searchDont ($linebotLogText->text)) {
            $linebotLog->setStatus (LinebotLog::STATUS_MATCH);
            $builder = new TextMessageBuilder ('為什麼?');
            $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);

            $response = $bot->replyMessage ($linebotLog->reply_token, $builder);
            if (!$response->isSucceeded ()) return false;
            $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
            echo 'Succeeded!';
          } else if ($keys = $this->searchDont ($linebotLogText->text)) {
            $linebotLog->setStatus (LinebotLog::STATUS_MATCH);
            $text = array ('為什麼？', '所以？', '嗯哼，為什麼？');
            $builder = new TextMessageBuilder ($text[array_rand ($text)]);
            $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);

            $response = $bot->replyMessage ($linebotLog->reply_token, $builder);
            if (!$response->isSucceeded ()) return false;
            $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
            echo 'Succeeded!';
          } else if ($keys = $this->search3Q ($linebotLogText->text)) {
            $linebotLog->setStatus (LinebotLog::STATUS_MATCH);
            $text = array ('不客氣啦！', 'OK 的啦！', '您客氣了：）');
            $builder = new TextMessageBuilder ($text[array_rand ($text)]);
            $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);

            $response = $bot->replyMessage ($linebotLog->reply_token, $builder);
            if (!$response->isSucceeded ()) return false;
            $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
            echo 'Succeeded!';
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

          $linebotLog->setStatus (LinebotLog::STATUS_MATCH);
          $this->load->library ('AlleyGet');
          if (($colums = AlleyGet::products ($linebotLogLocation->latitude, $linebotLogLocation->longitude)) && ($colums = array_map (function ($store) {
              return new CarouselColumnTemplateBuilder (
                mb_strimwidth ($store['title'], 0, 18 * 2, '…','UTF-8'),
                mb_strimwidth ($store['desc'], 0, 28 * 2, '…','UTF-8'),
                $store['img'],
                array (new UriTemplateActionBuilder (mb_strimwidth ('我要吃 ' . $store['title'], 0, 8 * 2, '…','UTF-8'), $store['url']))
              );
            }, $colums))) {

            $builder = new TemplateMessageBuilder (mb_strimwidth ('附近好吃的美食來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder ($colums));
            $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
            $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

            if (!$response->isSucceeded ()) return false;
            $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
            echo 'Succeeded!';
          } else {
            $builder = new TextMessageBuilder ('哭哭，這附近沒什麼美食耶..');
            $linebotLog->setStatus (LinebotLog::STATUS_RESPONSE);
            $response = $bot->replyMessage ($linebotLog->reply_token, $builder);

            if (!$response->isSucceeded ()) return false;
            $linebotLog->setStatus (LinebotLog::STATUS_SUCCESS);
            echo 'Succeeded!';
          }
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
