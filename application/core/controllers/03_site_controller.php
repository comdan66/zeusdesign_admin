<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Site_controller extends Oa_controller {

  public function __construct () {
    parent::__construct ();

    $this
         ->set_componemt_path ('component', 'site')
         ->set_frame_path ('frame', 'site')
         ->set_content_path ('content', 'site')
         ->set_public_path ('public')

         ->set_title ('宙思設計')

         ->_add_meta ()
         ->_add_css ()
         ->_add_js ()
         ;

    if (file_exists ($path = FCPATH . implode (DIRECTORY_SEPARATOR, array_merge ($this->get_views_path (), $this->get_public_path (), array ('icon_site.css')))) && is_readable ($path))
      $this->add_css (base_url (implode ('/', array_merge ($this->get_views_path (), $this->get_public_path (), array ('icon_site.css')))));

  }

  private function _add_meta () {
    return $this->add_meta (array ('name' => 'robots', 'content' => 'noindex,nofollow'))
                ->add_meta (array ('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui'))
                ;
  }

  private function _add_css () {
    return $this;
  }

  private function _add_js () {
    return $this->add_js (base_url ('res', 'js', 'jquery_v1.10.2', 'jquery-1.10.2.min.js'))
                ->add_js (base_url ('res', 'js', 'jquery-rails_d2015_03_09', 'jquery_ujs.js'))
                ->add_js (base_url ('res', 'js', 'imgLiquid_v0.9.944', 'imgLiquid-min.js'))
                ;
  }
}