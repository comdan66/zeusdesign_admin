<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class DeployTool {
  public static function genApi ($is_test = false) {
    $CI =& get_instance ();
    $CI->load->helper ('directory_helper');
    $api = FCPATH . 'api' . DIRECTORY_SEPARATOR;
    @directory_delete ($api, false);

    $banners = array_map (function ($banner) { return $banner->to_array (); }, Banner::find ('all', array ('order' => 'sort DESC', 'conditions' => array ('is_enabled = ?', Banner::ENABLE_YES))));
    write_file ($api . 'banners.json', json_encode ($banners));
    @chmod ($api . 'banners.json', 0777);

    $promos = array_map (function ($promo) { return $promo->to_array (); }, Promo::find ('all', array ('order' => 'sort DESC', 'conditions' => array ('is_enabled = ?', Promo::ENABLE_YES))));
    write_file ($api . 'promos.json', json_encode ($promos));
    @chmod ($api . 'promos.json', 0777);

    $articles = array_map (function ($article) { return $article->to_array (); }, Article::find ('all', array ('include' => array ('user', 'tags', 'sources'), 'order' => 'id DESC', 'conditions' => $is_test ? array () : array ('is_enabled = ?', Article::ENABLE_YES))));
    write_file ($api . 'articles.json', json_encode ($articles));
    @chmod ($api . 'articles.json', 0777);

    $works = array_map (function ($work) { return $work->to_array (); }, Work::find ('all', array ('include' => array ('user', 'images', 'tags', 'blocks'), 'order' => 'id DESC', 'conditions' => $is_test ? array () : array ('is_enabled = ?', Work::ENABLE_YES))));
    write_file ($api . 'works.json', json_encode ($works));
    @chmod ($api . 'works.json', 0777);

    return true;
  }
  public static function crud ($url) {
    $options = array (
      CURLOPT_URL => $url, CURLOPT_TIMEOUT => 120, CURLOPT_HEADER => false, CURLOPT_MAXREDIRS => 10,
      CURLOPT_AUTOREFERER => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.76 Safari/537.36",
    );

    $ch = curl_init ($url);
    curl_setopt_array ($ch, $options);
    $data = curl_exec ($ch);
    curl_close ($ch);

    if ($data && ($data = json_decode ($data, true)) && ($data['result'] === 'success')) return true;
    else false;
  }
  public static function callBuild () {
    $url = Cfg::setting ('deploy', 'build', ENVIRONMENT) . '?' . http_build_query (array (
          'env' => ENVIRONMENT,
          'psw' => Cfg::setting ('deploy', 'psw', ENVIRONMENT)
        ));
    return self::crud ($url);
  }
  public static function callUpload () {
    $url = Cfg::setting ('deploy', 'upload', ENVIRONMENT) . '?' . http_build_query (array (
          'env' => ENVIRONMENT,
          'psw' => Cfg::setting ('deploy', 'psw', ENVIRONMENT)
        ));
    return self::crud ($url);
  }
}