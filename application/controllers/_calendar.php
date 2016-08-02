<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Calendar extends Admin_controller {

  public function __construct () {
    parent::__construct ();
    $this->add_param ('now_url', base_url ('calendar'));
  }
  
  public function index () {

    $tags = ScheduleTag::find ('all', array ('select' => 'id, name, color', 'conditions' => array ('user_id = ?', User::current ()->id)));
    $tags = array_map (function ($tag) {
      return array (
          'id' => $tag->id,
          'name' => $tag->name,
          'color' => $tag->color ()
        );
    }, $tags);

    $this->load_view (array (
        'tags' => $tags
      ));
  }
}
