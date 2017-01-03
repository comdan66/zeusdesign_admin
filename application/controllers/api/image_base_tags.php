<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Image_base_tags extends Api_controller {
  private $user = null;
  private $tag = null;

  public function __construct () {
    parent::__construct ();

    if (User::current ()) $this->user = User::current ();
    else $this->user = ($token = $this->input->get_request_header ('Token')) && ($user = User::find ('one', array ('conditions' => array ('token = ?', $token)))) ? $user : null;

    if (!$this->user)
        return $this->disable ($this->output_error_json ('Not found User!'));

    if (in_array ($this->uri->rsegments (2, 0), array ('finish', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->tag = ImageBaseTag::find ('one', array ('conditions' => array ('id = ? AND user_id = ?', $id, $this->user->id))))))
        return $this->disable ($this->output_error_json ('Not found Data!'));
  }
  public function index () {
    $tags = ImageBaseTag::find ('all', array (
      'order' => 'id DESC',
      'conditions' => array ('user_id = ?', $this->user->id)));

    return $this->output_json (array_map (function ($tag) {
      return array (
          'id' => $tag->id,
          'name' => $tag->name,
        );
    }, $tags));
  }
}