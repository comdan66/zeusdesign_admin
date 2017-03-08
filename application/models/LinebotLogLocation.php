<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class LinebotLogLocation extends OaLineModel {

  static $table_name = 'linebot_log_locations';

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
  public function reply ($bot, MessageBuilder $build) {
    $this->log->setStatus (LinebotLog::STATUS_RESPONSE);
    $response = $bot->replyMessage ($this->log->reply_token, $build);

    if (!$response->isSucceeded ()) return false;
    $this->log->setStatus (LinebotLog::STATUS_SUCCESS);
    return true;
  }
  public function searchProducts2 ($bot) {
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $this->CI->load->library ('AlleyGet');

    if (!$datas = AlleyGet::products ($this->latitude, $this->longitude)) return $this->reply ($bot, new TextMessageBuilder ('哭哭，這附近沒什麼美食耶..'));

    $builder = new TemplateMessageBuilder (mb_strimwidth ('附近好吃的美食來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder (array_map (function ($store) {
        return new CarouselColumnTemplateBuilder (
          mb_strimwidth ($store['title'], 0, 18 * 2, '…','UTF-8'),
          mb_strimwidth ($store['desc'], 0, 28 * 2, '…','UTF-8'),
          $store['img'],
          array (new UriTemplateActionBuilder (mb_strimwidth ('我要吃 ' . $store['title'], 0, 8 * 2, '…','UTF-8'), $store['url']))
        );
      }, $datas)));

    return $this->reply ($bot, $builder);
  }

  public function searchProducts ($bot) {
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $builder = new TemplateMessageBuilder ('請問您是要知道這附近的？', new ConfirmTemplateBuilder ('有個問題需要被解答！', array (new MessageTemplateActionBuilder ('店家美食？', '我想知道這附近的美食(' . $this->latitude . ', ' . $this->longitude . ')'), new MessageTemplateActionBuilder ('天氣概況(' . $this->latitude . ', ' . $this->longitude . ')', ''))));
    return $this->reply ($bot, $builder);
  }
}