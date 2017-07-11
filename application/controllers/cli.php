<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Cli extends Oa_controller {

  public function __construct () {
    parent::__construct ();
    
    // if (!$this->input->is_cli_request ()) {
    //   echo 'Request 錯誤！';
    //   exit ();
    // }
  }
  public function o ($id = 0) {
    echo $id ? Mail::find_by_id ($id)->content : Mail::last ()->content;
  }
  public function x () {
    
    Mail::send ('test3', array (array ('oa', 'comdan66@gmail.com')), 'asd', function ($obj) {
      return array (
            array ('type' => 'section', 'title' => '', 'content' => Mail::renderUser ('吳政賢') . ' 已經將任務刪除囉，相關細節請至' . Mail::renderLink ('後台', base_url ('platform', 'mail', $obj->token)) . '看吧！'),
            array ('type' => 'ul', 'title' => '', 'li' => array (Mail::renderLi ('asdasdasd'), Mail::renderLi ('asdasdasdasdd'), Mail::renderLi ('asdasdasdas', Mail::renderLink ('asdasdasd', 'dasdasdasdasd')))),
            array ('type' => 'ol', 'title' => 'asd', 'li' => array (Mail::renderLi ('asdasdasd'), Mail::renderLi ('asdasdasdasdd'), Mail::renderLi ('asdasdasdas', Mail::renderLink ('asdasdasd', 'dasdasdasdasd')))),
        );
    }, '');


    // $this->load->view ('mail/test.php', array (
    //     'datas' =>  array (
    //       array ('type' => 'section', 'title' => '', 'content' => Mail::renderUser ('吳政賢') . ' 已經將任務刪除囉！'),
    //       array ('type' => 'ul', 'title' => '', 'li' => array (Mail::renderLi ('asdasdasd'), Mail::renderLi ('asdasdasdasdd'), Mail::renderLi ('asdasdasdas', Mail::renderLink ('asdasdasd', 'dasdasdasdasd')))),
    //       array ('type' => 'ol', 'title' => 'asd', 'li' => array (Mail::renderLi ('asdasdasd'), Mail::renderLi ('asdasdasdasdd'), Mail::renderLi ('asdasdasdas', Mail::renderLink ('asdasdasd', 'dasdasdasdasd')))),
    //   )));
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