<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */
require FCPATH . 'vendor/autoload.php';
use LINE\LINEBot\MessageBuilder;

class LinebotLogText extends OaModel {

  static $table_name = 'linebot_log_texts';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('log', 'class_name' => 'LinebotLog'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public static function regex ($pattern, $str) {
    preg_match_all ($pattern, $str, $result);
    if (!$result['c']) return array ();
    return preg_split ('/[\s,]+/', $result['c'][0]);
  }
  public function reply ($bot, MessageBuilder $build) {
    $this->log->setStatus (LinebotLog::STATUS_RESPONSE);
    $response = $bot->replyMessage ($this->log->reply_token, $build);

    if (!$response->isSucceeded ()) return false;
    $this->log->setStatus (LinebotLog::STATUS_SUCCESS);
    return true;
  }
  public function searchIWantLook ($bot) {
    $pattern = '/我{0,1}(想|要)*找\s*(?P<c>.*)/';
write_file (FCPATH . 'temp/input.json', ('==================2') . "\n", FOPEN_READ_WRITE_CREATE);

    if (!(isset ($this->text) && $keys = LinebotLogText::regex ($pattern, $this->text))) return false;
write_file (FCPATH . 'temp/input.json', ('==================2.1') . "\n", FOPEN_READ_WRITE_CREATE);

    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $this->CI->load->library ('CreateDemo');
write_file (FCPATH . 'temp/input.json', ('==================3') . "\n", FOPEN_READ_WRITE_CREATE);

    if (!$datas = CreateDemo::pics (4, 5, $keys)) return $this->reply ($bot, new TextMessageBuilder ('哭哭，找不到你想要的 ' . implode (' ', $keys) . ' 耶..'));
write_file (FCPATH . 'temp/input.json', ('==================4') . "\n", FOPEN_READ_WRITE_CREATE);

    $builder = new TemplateMessageBuilder (mb_strimwidth (implode (',', $keys) . ' 來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder (array_map (function ($data) {
        return new CarouselColumnTemplateBuilder (
          mb_strimwidth ($data['title'], 0, 18 * 2, '…','UTF-8'),
          mb_strimwidth ($data['title'], 0, 28 * 2, '…','UTF-8'),
          $data['url'],
          array (new UriTemplateActionBuilder (mb_strimwidth ('我要看 ' . $data['title'], 0, 8 * 2, '…','UTF-8'), $data['page']))
      ); }, $datas)));
write_file (FCPATH . 'temp/input.json', ('==================5') . "\n", FOPEN_READ_WRITE_CREATE);

    return $this->reply ($bot, $builder);
  }
  public function searchIWantListen ($bot) {
    $pattern = '/我{0,1}(想|要)*(聽|看)\s*(?P<c>.*)/';
    

    if (!(isset ($this->text) && $keys = LinebotLogText::regex ($pattern, $this->text))) return false;

    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $this->CI->load->library ('YoutubeGet');

    if (!$datas = YoutubeGet::search (array ('q' => implode (' ', $keys), 'maxResults' => rand (3, 5)))) return $this->reply ($bot, new TextMessageBuilder ('哭哭，找不到你想要的 ' . implode (' ', $keys) . ' 耶..'));

    $builder = new TemplateMessageBuilder (mb_strimwidth (implode (',', $keys) . ' 來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder (array_map (function ($data) {
      return new CarouselColumnTemplateBuilder (
        mb_strimwidth ($data['title'], 0, 18 * 2, '…','UTF-8'),
        mb_strimwidth ($data['title'], 0, 28 * 2, '…','UTF-8'),
        $data['thumbnails'][count ($data['thumbnails']) - 1]['url'],
        array (new UriTemplateActionBuilder (mb_strimwidth ('我要聽 ' . $data['title'], 0, 8 * 2, '…','UTF-8'), 'https://www.youtube.com/watch?v=' . $data['id']))
      );
    }, $datas)));

    return $this->reply ($bot, $builder);
  }
  public function searchIWantEat ($bot) {
    $pattern = '/我{0,1}(想|要)*(吃)\s*(?P<c>.*)/';

    if (!(isset ($this->text) && $keys = LinebotLogText::regex ($pattern, $this->text))) return false;

    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $this->CI->load->library ('AlleyGet');

    if (!$datas = AlleyGet::search (implode (' ', $keys))) return $this->reply ($bot, new TextMessageBuilder ('哭哭，找不到你想要的 ' . implode (' ', $keys) . ' 耶..'));

    $builder = new TemplateMessageBuilder (mb_strimwidth (implode (',', $keys) . ' 來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder (array_map (function ($data) {
      return new CarouselColumnTemplateBuilder (
        mb_strimwidth ($data['title'], 0, 18 * 2, '…','UTF-8'),
        mb_strimwidth ($data['desc'], 0, 28 * 2, '…','UTF-8'),
        $data['img'],
        array (new UriTemplateActionBuilder (mb_strimwidth ('我要吃 ' . $data['title'], 0, 8 * 2, '…','UTF-8'), $data['url']))
      );
    }, $datas)));

    return $this->reply ($bot, $builder);
  }
  public function searchRecommend ($bot) {
    $pattern = '/我{0,1}(想|要)*(吃)\s*(?P<c>.*)/';

    if (!(isset ($this->text) && $keys = LinebotLogText::regex ($pattern, $this->text))) return false;

    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $this->CI->load->library ('AlleyGet');

    if (!$datas = AlleyGet::recommend ()) return $this->reply ($bot, new TextMessageBuilder ('哭哭，這附近沒什麼美食耶..'));

    $builder = new TemplateMessageBuilder (mb_strimwidth (implode (',', $keys) . ' 來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder (array_map (function ($data) {
      return new CarouselColumnTemplateBuilder (
        mb_strimwidth ($data['title'], 0, 18 * 2, '…','UTF-8'),
        mb_strimwidth ($data['desc'], 0, 28 * 2, '…','UTF-8'),
        $data['img'],
        array (new UriTemplateActionBuilder (mb_strimwidth ('我要吃 ' . $data['title'], 0, 8 * 2, '…','UTF-8'), $data['url']))
      );
    }, $datas)));

    return $this->reply ($bot, $builder);
  }
}