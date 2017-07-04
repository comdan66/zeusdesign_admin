<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Cli extends Oa_controller {

  public function __construct () {
    parent::__construct ();
    
    if (!$this->input->is_cli_request ()) {
      echo 'Request 錯誤！';
      exit ();
    }
  }
  public function x () {
    // $data = '7/02';
    // $d = DateTime::createFromFormat ('m/d', $data);
    // var_dump ($d->format ('Y-m-d'));
    // exit ();
    $a = date ('Y-m-d', strtotime (date ('Y-m-d') . ' -' . 3 . ' day'));
    $b = date ('Y-m-d', strtotime (date ('Y-m-d') . ' -' . 2 . ' day'));

    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($a, $b, $a > $b);
    exit ();
  }

  public function ptt () {
    if (!$tags = PttTag::find ('all', array ('select' => 'id, uri', 'conditions' => array ('uri != ?', ''))))
      return ;

    $this->load->library ('PttGeter');

    foreach ($tags as $tag) {
      $uri = $tag->uri;

      for ($i = 0; $i < 100; $i++) { 

        $gets = PttGeter::getListAndPrevNextUri ($uri, $tag->id);
        if (!$gets['list']) break;
        $uri = $gets['prev'];

        foreach ($gets['list'] as $article) {
          if (!$obj = Ptt::find ('one', array ('select' => 'id, pid, cnt, updated_at', 'conditions' => array ('pid = ?', $article['pid'])))) {
            Ptt::create ($article);
          } else if ($obj->cnt != $article['cnt']) {
            $obj->cnt = $article['cnt'];
            $obj->save ();
          }
        }
        
        echo "$i \n";
      }
    }
  }
  public function token () {
  }
}