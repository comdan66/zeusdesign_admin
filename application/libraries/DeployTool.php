<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class DeployTool {
  public static function api_article ($api) {
    $data = array_map (function ($article) {
      
      return array (
          'user' => array (
              'fbid' => $article->user->uid,
              'name' => $article->user->name,
            ),
          'cover' => array (
              'c600x315' => $article->cover->url ('600x315c'),
              'c1200x630' => $article->cover->url ('1200x630c'),
            ),
          'id' => $article->id,
          'tag' => $article->tag,
          'title' => $article->title,
          'content' => preg_replace ('/alt=""/', 'alt="' . $article->title . ' - 北港迎媽祖"', preg_replace ('/alt=""\s+src="(https?:\/\/[a-zA-Z_0-9\.]*\/u\/ckeditor_images\/name\/([0-9]*)\/([0-9]*)\/([0-9]*)\/([0-9]*)\/[a-zA-Z_0-9]*\.[^\"]*)"/', 'alt="" data-pvid="CkeditorImage-$2$3$4$5" src="$1"', $article->content)),
          // 'content' => preg_replace ('/alt=""/', 'alt="' . $article->title . ' - 北港迎媽祖"', preg_replace ('/alt=""\s+src="(https:\/\/[a-z\._]*\/u\/ckeditor_images\/name\/()\/()\/()\/()\/[a-z\_]*\.[^\"]*)"/', 'alt="" 1 src="$1"', $article->content)),
          'updated_at' => $article->updated_at->format ('Y-m-d H:i:s'),
          'created_at' => $article->created_at->format ('Y-m-d H:i:s'),
          'pics' => $article->pics (),
          'sources' => array_map (function ($source) {
            return array (
                'text' => $source->title,
                'href' => $source->href,
              );
          }, $article->sources),
        );
    }, Article::find ('all', array ('select' => 'id, user_id, tag, title, cover, content, created_at, updated_at', 'order' => 'sort DESC', 'include' => array ('user', 'sources'))));

    write_file ($api . 'articles.json', json_encode ($data));
    @chmod ($api . 'articles.json', 0777);
  }
  public static function api_paths ($api) {
    $paths = array_combine (array_keys (Path::$typeNames), array_map (function ($type) {
      $path = Path::find_by_type ($type, array ('select' => 'points'));
      return $path->points (true);
    }, array_keys (Path::$typeNames)));

    $infos = array_combine (array_keys (Path::$typeNames), array_map (function ($type) {
      $infos = PathInfo::find ('all', array ('select' => 'content, lat, lng', 'conditions' => array ('type = ?', $type)));
      return array_values (array_filter (array_map (function ($info) {
        return $info->minfy (true);
      }, $infos)));
    }, array_keys (Path::$typeNames)));

    $struct = array_map (function ($t) {
      return array_combine ($t, array_map (function ($u) {
        return Path::$typeNames[$u];
      }, $t));
    }, Path::$struct);

    write_file ($api . 'paths.json', json_encode (array (
        'struct' => $struct,
        'infos' => $infos,
        'paths' => $paths,
      )));
    @chmod ($api . 'paths.json', 0777);
  }
  public static function api_albums ($api) {
    $albums = array_map (function ($album) {
      return $album->images ? array (
          'user' => array (
              'fbid' => $album->user->uid,
              'name' => $album->user->name,
            ),
          'cover' => array (
              'c600x315' => $album->cover->url ('600x315c'),
              'c1200x630' => $album->cover->url ('1200x630c'),
            ),
          'id' => $album->id,
          'title' => $album->title,
          // 'content' => preg_replace ('/alt=""/', 'alt="' . $album->title . ' - 北港迎媽祖"', $album->content),
          'content' => preg_replace ('/alt=""/', 'alt="' . $album->title . ' - 北港迎媽祖"', preg_replace ('/alt=""\s+src="(https?:\/\/[a-zA-Z_0-9\.]*\/u\/ckeditor_images\/name\/([0-9]*)\/([0-9]*)\/([0-9]*)\/([0-9]*)\/[a-zA-Z_0-9]*\.[^\"]*)"/', 'alt="" data-pvid="CkeditorImage-$2$3$4$5" src="$1"', $album->content)),
          'pv' => $album->pv,
          'updated_at' => $album->updated_at->format ('Y-m-d H:i:s'),
          'created_at' => $album->created_at->format ('Y-m-d H:i:s'),
          'images' => array_map (function ($image) {
            return array (
                'id' => $image->id,
                'title' => $image->title,
                'url' => array (
                    'w800' => $image->name->url ('800w')
                  )
              );
          }, $album->images),
          'sources' => array_map (function ($source) {
            return array (
                'text' => $source->title,
                'href' => $source->href,
              );
          }, $album->sources)
        ) : array ();
    }, Album::find ('all', array ('select' => 'id, cover, title, content, pv, user_id, updated_at, created_at', 'order' => 'sort DESC', 'include' => array ('user', 'sources', 'images'))));

    write_file ($api . 'albums.json', json_encode (array_values (array_filter ($albums))));
    @chmod ($api . 'albums.json', 0777);
  }
  public static function api_youtubes ($api) {
    $youtubes = array_map (function ($youtube) {
      return array (
          'user' => array (
              'fbid' => $youtube->user->uid,
              'name' => $youtube->user->name,
            ),
          'id' => $youtube->id,
          'vid' => $youtube->vid,
          'title' => $youtube->title,
          // 'content' => preg_replace ('/alt=""/', 'alt="' . $youtube->title . ' - 北港迎媽祖"', $youtube->content),
          'content' => preg_replace ('/alt=""/', 'alt="' . $youtube->title . ' - 北港迎媽祖"', preg_replace ('/alt=""\s+src="(https?:\/\/[a-zA-Z_0-9\.]*\/u\/ckeditor_images\/name\/([0-9]*)\/([0-9]*)\/([0-9]*)\/([0-9]*)\/[a-zA-Z_0-9]*\.[^\"]*)"/', 'alt="" data-pvid="CkeditorImage-$2$3$4$5" src="$1"', $youtube->content)),
          'pv' => $youtube->pv,
          'updated_at' => $youtube->updated_at->format ('Y-m-d H:i:s'),
          'created_at' => $youtube->created_at->format ('Y-m-d H:i:s'),
        );
    }, Youtube::find ('all', array ('select' => 'id, vid, url, title, content, pv, user_id, updated_at, created_at', 'order' => 'id DESC', 'include' => array ('user'))));

    write_file ($api . 'videos.json', json_encode (array_values ($youtubes)));
    @chmod ($api . 'videos.json', 0777);
  }
  public static function api_home ($api) {
    $home = Home::first (array ('select' => 'id, cover, content, pv, updated_at, created_at'));
    $home = array (
          'id' => $home->id,
          'cover' => array (
              'c600x315' => $home->cover->url ('600x315c'),
              'c1200x630' => $home->cover->url ('1200x630c'),
            ),
          // 'content' => preg_replace ('/alt=""/', 'alt="' . '關於作者' . ' - 北港迎媽祖"', $home->content),
          'content' => preg_replace ('/alt=""/', 'alt="' . '關於作者' . ' - 北港迎媽祖"', preg_replace ('/alt=""\s+src="(https?:\/\/[a-zA-Z_0-9\.]*\/u\/ckeditor_images\/name\/([0-9]*)\/([0-9]*)\/([0-9]*)\/([0-9]*)\/[a-zA-Z_0-9]*\.[^\"]*)"/', 'alt="" data-pvid="CkeditorImage-$2$3$4$5" src="$1"', $home->content)),
          'pv' => $home->pv,
          'updated_at' => $home->updated_at->format ('Y-m-d H:i:s'),
          'created_at' => $home->created_at->format ('Y-m-d H:i:s'),
        );

    write_file ($api . 'home.json', json_encode ($home));
    @chmod ($api . 'home.json', 0777);
  }
  public static function api_author ($api) {
    $author = Author::first (array ('select' => 'id, cover, content, pv, updated_at, created_at'));
    $author = array (
          'id' => $author->id,
          'cover' => array (
              'c600x315' => $author->cover->url ('600x315c'),
              'c1200x630' => $author->cover->url ('1200x630c'),
            ),
          // 'content' => preg_replace ('/alt=""/', 'alt="' . '關於作者' . ' - 北港迎媽祖"', $author->content),
          'content' => preg_replace ('/alt=""/', 'alt="' . '關於作者' . ' - 北港迎媽祖"', preg_replace ('/alt=""\s+src="(https?:\/\/[a-zA-Z_0-9\.]*\/u\/ckeditor_images\/name\/([0-9]*)\/([0-9]*)\/([0-9]*)\/([0-9]*)\/[a-zA-Z_0-9]*\.[^\"]*)"/', 'alt="" data-pvid="CkeditorImage-$2$3$4$5" src="$1"', $author->content)),
          'pv' => $author->pv,
          'updated_at' => $author->updated_at->format ('Y-m-d H:i:s'),
          'created_at' => $author->created_at->format ('Y-m-d H:i:s'),
        );

    write_file ($api . 'author.json', json_encode ($author));
    @chmod ($api . 'author.json', 0777);
  }
  public static function api_license ($api) {
    $license = License::first (array ('select' => 'id, cover, content, pv, updated_at, created_at'));
    $license = array (
          'id' => $license->id,
          'cover' => array (
              'c600x315' => $license->cover->url ('600x315c'),
              'c1200x630' => $license->cover->url ('1200x630c'),
            ),
          // 'content' => preg_replace ('/alt=""/', 'alt="' . '授權聲明' . ' - 北港迎媽祖"', $license->content),
          'content' => preg_replace ('/alt=""/', 'alt="' . '授權聲明' . ' - 北港迎媽祖"', preg_replace ('/alt=""\s+src="(https?:\/\/[a-zA-Z_0-9\.]*\/u\/ckeditor_images\/name\/([0-9]*)\/([0-9]*)\/([0-9]*)\/([0-9]*)\/[a-zA-Z_0-9]*\.[^\"]*)"/', 'alt="" data-pvid="CkeditorImage-$2$3$4$5" src="$1"', $license->content)),
          'pv' => $license->pv,
          'updated_at' => $license->updated_at->format ('Y-m-d H:i:s'),
          'created_at' => $license->created_at->format ('Y-m-d H:i:s'),
        );

    write_file ($api . 'license.json', json_encode ($license));
    @chmod ($api . 'license.json', 0777);
  }
  public static function genApi ($obj) {
    $CI =& get_instance ();
    $CI->load->helper ('directory_helper');
    $api = FCPATH . 'api' . DIRECTORY_SEPARATOR;
    @directory_delete ($api, false);

    DeployTool::api_article ($api);
    DeployTool::api_paths ($api);
    DeployTool::api_albums ($api);
    DeployTool::api_youtubes ($api);

    DeployTool::api_home ($api);
    DeployTool::api_author ($api);
    DeployTool::api_license ($api);

    return true;
  }

  public static function userAgent () {
    $t = array (
      'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
      // 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.76 Safari/537.36',
      // 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
      // 'Mozilla/5.0 (Linux; Android 4.3; Nexus 7 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
      // 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
    );
    return $t[array_rand ($t)];
  }
  public static function crud ($opts, $obj) {
    $options = array (
      CURLOPT_URL => $opts['url'],
      CURLOPT_USERAGENT => self::userAgent (),
      CURLOPT_POSTFIELDS => http_build_query ($opts['data']),
      CURLOPT_TIMEOUT => 120, CURLOPT_HEADER => false, CURLOPT_POST => true, CURLOPT_MAXREDIRS => 10, CURLOPT_AUTOREFERER => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true,
    );

    $ch = curl_init ($opts['url']);
    curl_setopt_array ($ch, $options);
    $data = curl_exec ($ch);
    curl_close ($ch);

echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
var_dump ($data);
exit ();
    $obj->error = $data;
    $obj->save ();
    
    if ($data && ($data = json_decode ($data, true)) && ($data['result'] === 'success')) {
      return true;
    } else {
      echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
      var_dump ($data);
      exit ();
      return false;
    };
  }
  public static function callBuild ($obj) {
    $url = Cfg::setting ('deploy', 'build', ENVIRONMENT) . '?' . http_build_query (array (
          'env' => ENVIRONMENT,
          'psw' => Cfg::setting ('deploy', 'psw', ENVIRONMENT)
        ));

    return self::crud (array (
        'url' => 'http://dev.www.zeusdesign.com.tw/cmd/build.php',
        'data' => array (
          )
      ), $obj);
  }
  public static function callUpload ($obj) {
    $url = Cfg::setting ('deploy', 'upload', ENVIRONMENT) . '?' . http_build_query (array (
          'env' => ENVIRONMENT,
          'psw' => Cfg::setting ('deploy', 'psw', ENVIRONMENT)
        ));
    return self::crud ($url, $obj);
  }
}