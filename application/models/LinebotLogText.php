<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */
require FCPATH . 'vendor/autoload.php';
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;

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
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchIWantLook') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/我{0,1}(想|要)*找\s*(?P<c>.*)/';

    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;

    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $this->CI->load->library ('CreateDemo');

    if (!$datas = CreateDemo::pics (4, 5, $keys)) return $this->reply ($bot, new TextMessageBuilder ('哭哭，找不到你想要的 ' . implode (' ', $keys) . ' 耶..'));

    $builder = new TemplateMessageBuilder (mb_strimwidth (implode (',', $keys) . ' 來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder (array_map (function ($data) {
        return new CarouselColumnTemplateBuilder (
          mb_strimwidth ($data['title'], 0, 18 * 2, '…','UTF-8'),
          mb_strimwidth ($data['title'], 0, 28 * 2, '…','UTF-8'),
          $data['url'],
          array (new UriTemplateActionBuilder (mb_strimwidth ('我要看 ' . $data['title'], 0, 8 * 2, '…','UTF-8'), $data['page']))
      ); }, $datas)));

    return $this->reply ($bot, $builder);
  }
  public function searchIWantListen ($bot) {
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchIWantListen') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/我{0,1}(想|要)*(聽|看)\s*(?P<c>.*)/';
    

    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;

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
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchIWantEat') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/我{0,1}(想|要)*(吃)\s*(?P<c>.*)/';

    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;

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
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchRecommend') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/(?P<c>(吃什麼|吃啥|好吃的|啥好吃|要吃啥|什麼好吃))/';

    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;

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
  public function searchDont ($bot) {
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchDont') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/不\s*(?P<c>(想|要|可|準).*)/';
    $response = array ('為什麼？', '蛤～～', '所以？');
    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $builder = new TextMessageBuilder ($response[array_rand ($response)]);
    return $this->reply ($bot, $builder);
  }
  public function search3Q ($bot) {
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'search3Q') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/(?P<c>(謝|3Q|3q).*)/';
    $response = array ('不客氣啦！', 'OK 的啦！', '您客氣了：）');
    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $builder = new TextMessageBuilder ($response[array_rand ($response)]);
    return $this->reply ($bot, $builder);
  }
  public function searchSpeechles ($bot) {
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchSpeechles') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/(?P<c>(＝\s*＝|=\s*=|\.\.|3q).*)/';
    $response = array ('幹嘛！！？', '= =+', '哈哈哈哈..');
    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $builder = new TextMessageBuilder ($response[array_rand ($response)]);
    return $this->reply ($bot, $builder);
  }
  public function searchNotThing ($bot) {
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchNotThing') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/(?P<c>(沒事).*)/';
    $response = array ('真的嗎.. = =+', '說！剛剛要說啥', '嗯哼，快說喔！');
    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $builder = new TextMessageBuilder ($response[array_rand ($response)]);
    return $this->reply ($bot, $builder);
  }
  public function searchHaha ($bot) {
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchHaha') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/(?P<c>(XD|ＸＤ|ＸD|xＤ|好笑|哈哈*))/i';
    $response = array ('笑什麼笑！', '很好笑？', '呵呵呵', '笑屁喔！');
    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $builder = new TextMessageBuilder ($response[array_rand ($response)]);
    return $this->reply ($bot, $builder);
  }
  public function searchBot ($bot) {
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchBot') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/(?P<c>(機器人|Bot*))/i';
    $response = array ('幹嘛，找我？', '我不是機器人！', '人家很聰明的！！');
    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $builder = new TextMessageBuilder ($response[array_rand ($response)]);
    return $this->reply ($bot, $builder);
  }
  public function searchHello ($bot) {
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchHello') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/(?P<c>(妳好|你好|哈囉|Hello|嗨|Hi))/i';
    $response = array ('你好！', '嗨～～', '嗨，你好（揮手');
    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $builder = new TextMessageBuilder ($response[array_rand ($response)]);
    return $this->reply ($bot, $builder);
  }
  public function searchName ($bot) {
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchName') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/(?P<c>(你叫什|你是誰|妳叫什|妳是誰))/i';
    $response = array ('我叫 小～添～屎～，是大家的好朋友～～');
    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $builder = new TextMessageBuilder ($response[array_rand ($response)]);
    return $this->reply ($bot, $builder);
  }
  public function searchCallMe ($bot) {
    write_file (FCPATH . 'temp/input.json', ('==================1' . 'searchCallMe') . "\n", FOPEN_READ_WRITE_CREATE);
    $pattern = '/(天使|添屎)+(?P<c>.*)/';
    $response = array ('找我幹嘛？', '恩？', '怎麼了？', '我在！', implode (' ', $keys));
    if (!(isset ($this->text) && ($keys = LinebotLogText::regex ($pattern, $this->text)))) return false;
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $builder = new TextMessageBuilder ($response[array_rand ($response)]);
    return $this->reply ($bot, $builder);
  }
}