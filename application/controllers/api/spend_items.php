<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Spend_items extends Api_controller {
  private $user = null;
  private $item = null;

  public function __construct () {
    parent::__construct ();

    if (User::current ()) $this->user = User::current ();
    else $this->user = ($token = $this->input->get_request_header ('Token')) && ($user = User::find ('one', array ('conditions' => array ('token = ?', $token)))) ? $user : null;

    if (!$this->user) return $this->disable ($this->output_error_json ('Not found User!'));

    if (in_array ($this->uri->rsegments (2, 0), array ('finish', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->item = Wallet::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $id, $this->user->id))))))
        return $this->disable ($this->output_error_json ('Not found Data!'));
  }
  public function index () {
    $gets = OAInput::get ();
    OaModel::addConditions ($conditions, 'user_id = ?', $this->user->id);
    $bool = isset ($gets['title']) && ($gets['title'] = trim ($gets['title']));
    if ($bool) OaModel::addConditions ($conditions, 'title LIKE ?', '%' . $gets['title'] . '%');
    $limit = isset ($gets['limit']) && is_numeric ($gets['limit'] = trim ($gets['limit'])) ? $gets['limit'] : 0;
    
    $items = SpendItem::find ('all', array (
        'select' => 'title, money, COUNT(id) AS cnt',
        'group' => 'CONCAT(title, money)',
        'order' => 'cnt DESC',
        'limit' => $limit,
        'conditions' => $conditions
      ));

    $items = array_map (function ($item) {
      return array (
          'title' => $item->title,
          'count' => $item->cnt,
          'money' => $item->money,
          'money_str' => number_format ($item->money),
        );
    }, $items);

    if ($bool && !in_array ($gets['title'], column_array ($items, 'title')))
      array_unshift ($items, array (
          'title' => $gets['title'],
          'count' => 0,
          'money' => 0,
          'money_str' => number_format (0),
        ));

    return $this->output_json ($limit ? array_slice ($items, 0, $limit) : $items);
  }























  // public function titles () {
  //   $gets = OAInput::get ();
  //   OaModel::addConditions ($conditions, 'user_id = ?', $this->user->id);
  //   if (isset ($gets['title']) && $gets['title']) OaModel::addConditions ($conditions, 'title LIKE ?', '%' . $gets['title'] . '%');

  //   $items = Wallet::find ('all', array (
  //         'select' => 'max(id) AS id, title, COUNT(id) AS cnt',
  //         'group' => 'title',
  //         'order' => 'cnt DESC',
  //         'conditions' => $conditions));

    

  //   $item_ids = column_array ($items, 'id');
  //   $item_ids = Wallet::find ('all', array ('select' => 'id, money', 'conditions' => array ('id IN (?)', $item_ids ? $item_ids : array (0))));
  //   $item_ids = array_combine (column_array ($item_ids, 'id'), column_array ($item_ids, 'money'));
    
  //   $items = array_map (function ($item) use ($item_ids) {
  //     return array (
  //         'title' => $item->title,
  //         'count' => $item->cnt,
  //         'money' => isset ($item_ids[$item->id]) ? $item_ids[$item->id] : 0,
  //       );
  //   }, $items);

  //   if (isset ($gets['title']) && $gets['title'] && !in_array ($gets['title'], column_array ($items, 'title')))
  //     array_unshift ($items, array (
  //         'title' => $gets['title'],
  //         'count' => 0,
  //         'money' => 0,
  //       ));

  //   return $this->output_json ($items);
  // }
  //   $gets = OAInput::get ();
  //   OaModel::addConditions ($conditions, 'user_id = ?', $this->user->id);
  //   if (isset ($gets['date']) && $gets['date']) $end = date ('Y-m-d 23:59:59', strtotime ($gets['date'] . ' -1 day'));
  //   else $end = date ('Y-m-d 23:59:59');

  //   $start = date ('Y-m-d 00:00:00', strtotime ($end . ' -5 day'));
  //   OaModel::addConditions ($conditions, 'timed_at BETWEEN ? AND ?', $start, $end);
    
  //   $ws = array ();
  //   foreach (Wallet::find ('all', array ('select' => 'id,title,money,address,cover,timed_at,DATE(timed_at) AS date', 'order' => 'timed_at DESC', 'conditions' => $conditions)) as $w)
  //     if ($d = array ('id' => $w->id, 'title' => $w->title, 'money' => $w->money, 'address' => $w->address, 'money_srt' => number_format ($w->money), 'cover' => $w->cover->url ('100x100c'), 'timed_at' => time_unit ($w->timed_at->format ('H')) . ' ' . $w->timed_at->format ('g點 i分')))
  //       if (!isset ($ws[$w->date])) $ws[$w->date] = array ($d);
  //       else array_push ($ws[$w->date], $d);

  //   $items = array ();
  //   foreach ($ws as $date => $w)
  //     array_push ($items, array (
  //         'date' => array (
  //             'title' => date_unit ($date),
  //             'money' => number_format (array_sum (column_array ($w, 'money')))
  //           ),
  //         'items' => $w,
  //       ));

  //   return $this->output_json ($items);
  // }
  // public function create () {

  //   $posts = OAInput::post ();
  //   $cover = OAInput::file ('cover');

  //   if (($msg = $this->_validation_must ($posts)) || ($msg = $this->_validation ($posts)))
  //     return $this->output_error_json ($msg);

  //   $posts['user_id'] = $this->user->id;

  //   $create = Wallet::transaction (function () use (&$item, $posts, $cover) {
  //     if (!verifyCreateOrm ($item = Wallet::create (array_intersect_key ($posts, Wallet::table ()->columns))))
  //       return false;
  //     return !$cover || $item->cover->put ($cover);
  //   });

  //   if (!$create) return $this->output_error_json ('新增失敗！');

  //   UserLog::create (array ('user_id' => $this->user->id, 'icon' => 'icon-item', 'content' => '新增一項花費記錄。', 'desc' => '在 ' . $item->created_at->format ('Y-m-d H:i:s') . ' 新增了一項花費記錄。', 'backup' => json_encode ($item->to_array ())));
  //   return $this->output_json ($item->to_array ());
  // }

  // public function update ($id = 0) {
  //   $posts = OAInput::post ();

  //   if ($msg = $this->_validation ($posts))
  //     return $this->output_error_json ($msg);

  //   if ($columns = array_intersect_key ($posts, $this->item->table ()->columns))
  //     foreach ($columns as $column => $value)
  //       $this->item->$column = $value;

  //   $item = $this->item;
  //   $update = Wallet::transaction (function () use ($item) { return $item->save (); });

  //   if (!$update) return $this->output_error_json ('更新失敗！');

  //   UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-item', 'content' => '修改一項花費紀錄。', 'desc' => '在 ' . $item->updated_at->format ('Y-m-d H:i:s') . ' 修改了一項花費記錄。', 'backup' => json_encode ($item->to_array ())));
  //   return $this->output_json ($item->to_array ());
  // }

  // public function destroy () {
  //   $item = $this->item;
  //   $backup = json_encode ($item->to_array ());
  //   $delete = Wallet::transaction (function () use ($item) { return $item->destroy (); });

  //   if (!$delete) return $this->output_error_json ('刪除失敗！');

  //   UserLog::create (array ('user_id' => User::current ()->id, 'icon' => 'icon-item', 'content' => '刪除一項花費紀錄。', 'desc' => '已經備份了刪除紀錄，細節可詢問工程師。', 'backup' => $backup));
  //   return $this->output_json (array ('message' => '刪除成功！'));
  // }
  // private function _validation (&$posts) {
  //   $keys = array ('title', 'money', 'address', 'memo', 'timed_at', 'lat', 'lng');

  //   $new_posts = array (); foreach ($posts as $key => $value) if (in_array ($key, $keys)) $new_posts[$key] = $value;
  //   $posts = $new_posts;

  //   if (isset ($posts['name']) && !($posts['name'] = trim ($posts['name']))) return '標題格式錯誤或未填寫！';
  //   if (isset ($posts['timed_at']) && !($posts['timed_at'] = trim ($posts['timed_at']))) return '時間格式錯誤或未填寫！';
  //   if (isset ($posts['money']) && !(is_numeric ($posts['money'] = trim ($posts['money'])) && $posts['money'] >= 0)) return '金額格式錯誤或未填寫！';
  //   if (isset ($posts['memo']) && ($posts['memo'] = trim ($posts['memo'])) && !is_string ($posts['memo'])) return '備註格式錯誤！';
  //   if (isset ($posts['address']) && ($posts['address'] = trim ($posts['address'])) && !is_string ($posts['address'])) return '地址格式錯誤！';
    
  //   if (isset ($posts['lat']) && !(is_numeric ($posts['lat'] = trim ($posts['lat'])) && ($posts['lat'] >= -90) && ($posts['lat'] <= 90))) return '緯度 格式錯誤！';
  //   if (isset ($posts['lng']) && !(is_numeric ($posts['lng'] = trim ($posts['lng'])) && ($posts['lng'] >= -180) && ($posts['lng'] <= 180))) return '經度 格式錯誤！';
    
  //   return '';
  // }
  // private function _validation_must (&$posts) {
  //   if (!isset ($posts['title'])) return '沒有填寫 標題！';
  //   if (!isset ($posts['money'])) return '沒有填寫 金額！';
  //   if (!isset ($posts['timed_at'])) return '沒有填寫 時間！';
    
  //   if (!isset ($posts['lat'])) return '沒有填寫 緯度！';
  //   if (!isset ($posts['lng'])) return '沒有填寫 經度！';
    
  //   return '';
  // }
}
