<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
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
}
