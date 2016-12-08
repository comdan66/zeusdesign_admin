<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Spends extends Api_controller {
  private $user = null;
  private $spend = null;

  public function __construct () {
    parent::__construct ();

    if (User::current ()) $this->user = User::current ();
    else $this->user = ($token = $this->input->get_request_header ('Token')) && ($user = User::find ('one', array ('conditions' => array ('token = ?', $token)))) ? $user : null;

    if (!$this->user)
        return $this->disable ($this->output_error_json ('Not found User!'));

    if (in_array ($this->uri->rsegments (2, 0), array ('finish', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->spend = Spend::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $id, $this->user->id))))))
        return $this->disable ($this->output_error_json ('Not found Data!'));
  }

  public function index () {
    $gets = OAInput::get ();
    OaModel::addConditions ($conditions, 'user_id = ?', $this->user->id);
    if (isset ($gets['date']) && $gets['date']) $end = date ('Y-m-d 23:59:59', strtotime ($gets['date'] . ' -1 day'));
    else $end = date ('Y-m-d 23:59:59');

    $start = date ('Y-m-d 00:00:00', strtotime ($end . ' -5 day'));
    OaModel::addConditions ($conditions, 'timed_at BETWEEN ? AND ?', $start, $end);
    
    $ws = array ();
    foreach (Spend::find ('all', array (
      'select' => 'id,number,address,cover,timed_at,DATE(timed_at) AS date',
      'order' => 'timed_at DESC',
      'include' => array ('items'),
      'conditions' => $conditions)) as $w)
      if ($d = array ('id' => $w->id, 'items' => $items = array_map (function ($item) { return $item->to_array (); }, $w->items), 'number' => $w->number, 'money' => $money = array_sum (column_array ($items, 'money')), 'address' => $w->address, 'money_srt' => number_format ($money), 'cover' => $w->cover->url ('100x100c'), 'timed_at' => time_unit ($w->timed_at->format ('H')) . ' ' . $w->timed_at->format ('g點 i分')))
        if (!isset ($ws[$w->date])) $ws[$w->date] = array ($d);
        else array_push ($ws[$w->date], $d);

    $spends = array ();
    foreach ($ws as $date => $w)
      array_push ($spends, array (
          'date' => array (
              'title' => date_unit ($date),
              'money' => number_format (array_sum (column_array ($w, 'money')))
            ),
          'spends' => $w,
        ));

    return $this->output_json ($spends);
  }

  public function create () {
    $posts = OAInput::post ();
    $cover = OAInput::file ('cover');
    
    if (!(isset ($posts['items']) && ($posts['items'] = array_filter ($posts['items'], function ($item) {
      return isset ($item['money']) && isset ($item['title']) && $item['money'] && $item['title'];
    })))) return $this->output_error_json ('沒有項目！');
    $items = $posts['items'];

    if (($msg = $this->_validation_must ($posts)) || ($msg = $this->_validation ($posts)))
      return $this->output_error_json ($msg);

    $posts['user_id'] = $this->user->id;

    $create = Spend::transaction (function () use (&$spend, $posts, $cover) {
      if (!verifyCreateOrm ($spend = Spend::create (array_intersect_key ($posts, Spend::table ()->columns))))
        return false;
      return !$cover || $spend->cover->put ($cover);
    });

    if ($items)
      foreach ($items as $item)
        SpendItem::transaction (function () use ($item, $spend) {
          return verifyCreateOrm (SpendItem::create (array_intersect_key (array_merge ($item, array (
            'user_id' => $spend->user_id,
            'spend_id' => $spend->id,
            )), SpendItem::table ()->columns)));
        });

    if (!$create) return $this->output_error_json ('新增失敗！');

    UserLog::create (array ('user_id' => $this->user->id, 'icon' => 'icon-wallet', 'content' => '新增一項花費記錄。', 'desc' => '在 ' . $spend->created_at->format ('Y-m-d H:i:s') . ' 新增了一項花費記錄。', 'backup' => json_encode ($spend->to_array ())));
    return $this->output_json ($spend->to_array ());
  }

  public function update ($id = 0) {
    $posts = OAInput::post ();
    $cover = OAInput::file ('cover');
    
    if (!(isset ($posts['items']) && ($posts['items'] = array_filter ($posts['items'], function ($item) {
      return isset ($item['money']) && isset ($item['title']) && $item['money'] && $item['title'];
    })))) return $this->output_error_json ('沒有項目！');
    $items = $posts['items'];

    if ($msg = $this->_validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $this->spend->table ()->columns))
      foreach ($columns as $column => $value)
        $this->spend->$column = $value;

    $spend = $this->spend;
    $update = Spend::transaction (function () use ($spend) { return $spend->save (); });

    if (!$update) return $this->output_error_json ('更新失敗！');


    if ($spend->items)
      foreach ($spend->items as $item)
        SpendItem::transaction (function () use ($item) {
          return $item->destroy ();
        });

    if ($items)
      foreach ($items as $item)
        SpendItem::transaction (function () use ($item, $spend) {
          return verifyCreateOrm (SpendItem::create (array_intersect_key (array_merge ($item, array (
            'user_id' => $spend->user_id,
            'spend_id' => $spend->id,
            )), SpendItem::table ()->columns)));
        });

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-wallet', 'content' => '修改一項花費紀錄。', 'desc' => '在 ' . $spend->updated_at->format ('Y-m-d H:i:s') . ' 修改了一項花費記錄。', 'backup' => json_encode ($spend->to_array ())));
    return $this->output_json ($spend->to_array ());
  }

  public function destroy () {
    $spend = $this->spend;
    $backup = json_encode ($spend->to_array ());
    $delete = Spend::transaction (function () use ($spend) { return $spend->destroy (); });

    if (!$delete) return $this->output_error_json ('刪除失敗！');

    UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-wallet', 'content' => '刪除一項花費紀錄。', 'desc' => '已經備份了刪除紀錄，細節可詢問工程師。', 'backup' => $backup));
    return $this->output_json (array ('message' => '刪除成功！'));
  }
  private function _validation (&$posts) {
    $keys = array ('number', 'money', 'timed_at', 'address', 'memo', 'lat', 'lng');

    $new_posts = array (); foreach ($posts as $key => $value) if (in_array ($key, $keys)) $new_posts[$key] = $value;
    $posts = $new_posts;

    if (isset ($posts['number']) && ($posts['number'] = trim ($posts['number'])) && !is_string ($posts['number'])) return '號碼格式錯誤！';
    if (isset ($posts['money']) && !is_numeric ($posts['money'] = trim ($posts['money']))) return '總金額格式錯誤！';
    if (isset ($posts['timed_at']) && !($posts['timed_at'] = trim ($posts['timed_at']))) return '時間格式錯誤或未填寫！';
    if (isset ($posts['address']) && ($posts['address'] = trim ($posts['address'])) && !is_string ($posts['address'])) return '地址格式錯誤！';
    if (isset ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) && !is_string ($posts['memo'])) return '備註格式錯誤！';
    
    if (isset ($posts['lat']) && !(is_numeric ($posts['lat'] = trim ($posts['lat'])) && ($posts['lat'] >= -90) && ($posts['lat'] <= 90))) return '緯度 格式錯誤！';
    if (isset ($posts['lng']) && !(is_numeric ($posts['lng'] = trim ($posts['lng'])) && ($posts['lng'] >= -180) && ($posts['lng'] <= 180))) return '經度 格式錯誤！';
    
    return '';
  }
  private function _validation_must (&$posts) {
    if (!isset ($posts['timed_at'])) return '沒有填寫 時間！';
    if (!isset ($posts['money'])) return '沒有填寫 總金額！';
    if (!isset ($posts['lat'])) return '沒有填寫 緯度！';
    if (!isset ($posts['lng'])) return '沒有填寫 經度！';

    return '';
  }
}
