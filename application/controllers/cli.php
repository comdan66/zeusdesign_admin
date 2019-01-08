<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Cli extends Oa_controller {

  public function __construct () {
    parent::__construct ();
    
    if (!$this->input->is_cli_request ()) {
      echo 'Request 錯誤！';
      exit ();
    }

    ini_set ('memory_limit', '2048M');
    ini_set ('set_time_limit', 60 * 60);
    ob_start ();
  }
  
  private function _save_cronjob ($cronjob, $title = '') {
    $ob = ob_get_contents ();
    @ob_end_clean ();
    $cronjob->content = ($title ? $title . ($ob ? ' - ' . $ob : '') : '') . $ob;

    $cronjob->end_at = microtime (true);
    $cronjob->save ();
    return true;
  }
  public function x () {
    
    Mail::send (
      User::find_by_id(1),
      '[聯絡宙思] 宙思官網有新的留言（' . date('Y-m-d H:i:s') . '）',
      'admin/contacts/10/show',
      function ($o) {
        return [[
          'type' => 'ol',
          'title' => 'Hi 管理者，宙思官網有新的留言，詳細內容請至' . Mail::renderLink ('宙思後台', base_url ('platform', 'mail', $o->token)) . '查看，以下是細節：',
          'li' => array_map(function($change) {
            return Mail::renderLi($change);
          }, ['稱呼：Tina', 'E-Mail：tina.yin@micro-ip.com', '內容：公司目前現有官網需要更新，希望更方便使用者使用；<br>另需製作一個形象頁面，可置於原有官網之上層，使使用者更能快速了解我司之平台服務。<br>想知道貴司報價及能配合的方式，且網頁希望能以買斷方式合作。'])
        ]];
    });
  }
  public function backup_2 () {
    if (!(Cronjob::transaction (function () use (&$cronjob) {
      return verifyCreateOrm ($cronjob = Cronjob::create (array (
          'title' => '備份 ' . Backup::$typeNames[Backup::TYPE_2] . '',
          'rule' => '每日上午 04點00分 開始',
          'content' => '',
          'start_at' => microtime (true),
          'end_at' => 0,
          'status' => Cronjob::STATUS_1
        )));
    }) && $cronjob && Backup::transaction (function () use (&$backup) {
      return verifyCreateOrm ($backup = Backup::create (array (
          'file' => '',
          'size' => '',
          'type' => Backup::TYPE_2,
          'status' => Backup::STATUS_1,
        )));
    }) && $backup))
      return $cronjob ? $this->_save_cronjob ($cronjob, '初始化錯誤！') : '';

    if (!(write_file ($t2 = FCPATH . 'temp' . DIRECTORY_SEPARATOR . 'backup_t' . Backup::TYPE_2 . '_' . date ('Ymd_His') . '.json', read_file ($t1 = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'query.log')) && write_file ($t1, '')))
      return $this->_save_cronjob ($cronjob, '寫入檔案失敗！');

    $backup->size = filesize ($t2);
    $backup->status = Backup::STATUS_2;
    if (!$backup->file->put ($t2))
      return $this->_save_cronjob ($cronjob, '上傳檔案失敗！');

    $cronjob->status = Cronjob::STATUS_2;
    return $this->_save_cronjob ($cronjob);
  }
  public function backup_1 () {
    $this->load->helper ('directory');

    if (!(Cronjob::transaction (function () use (&$cronjob) {
      return verifyCreateOrm ($cronjob = Cronjob::create (array (
          'title' => '備份 ' . Backup::$typeNames[Backup::TYPE_1] . '',
          'rule' => '每日上午 04點30分 開始',
          'content' => '',
          'start_at' => microtime (true),
          'end_at' => 0,
          'status' => Cronjob::STATUS_1
        )));
    }) && $cronjob && Backup::transaction (function () use (&$backup) {
      return verifyCreateOrm ($backup = Backup::create (array (
          'file' => '',
          'size' => '',
          'type' => Backup::TYPE_1,
          'status' => Backup::STATUS_1,
        )));
    }) && $backup))
      return $cronjob ? $this->_save_cronjob ($cronjob, '初始化錯誤！') : '';

    if (!write_file ($t = FCPATH . 'temp' . DIRECTORY_SEPARATOR . 'backup_t' . Backup::TYPE_1 . '_' . date ('Ymd_His') . '.json', json_encode (array_combine ($models = array_map (function ($m) { return pathinfo ($m, PATHINFO_FILENAME); }, array_filter (directory_map (FCPATH . 'application' . DIRECTORY_SEPARATOR . 'models', 1), function ($t) { return !(strpos ($t, '_') === 0) && pathinfo ($t, PATHINFO_EXTENSION) === 'php'; })), array_map (function ($m) {
      return array_map (function ($t) {
        return $t->backup ();
      }, $m::all ());
    }, $models)))))
      return $this->_save_cronjob ($cronjob, '寫入檔案失敗！');

    $backup->size = filesize ($t);
    $backup->status = Backup::STATUS_2;
    if (!$backup->file->put ($t))
      return $this->_save_cronjob ($cronjob, '上傳檔案失敗！');

    $cronjob->status = Cronjob::STATUS_2;
    return $this->_save_cronjob ($cronjob);
  }
  public function backup_log () {
    if (!(Cronjob::transaction (function () use (&$cronjob) {
      return verifyCreateOrm ($cronjob = Cronjob::create (array (
          'title' => '備份 User Log',
          'rule' => '每日上午 03點30分 開始',
          'content' => '',
          'start_at' => microtime (true),
          'end_at' => 0,
          'status' => Cronjob::STATUS_1
        )));
    }) && $cronjob)) return $cronjob ? $this->_save_cronjob ($cronjob, '初始化錯誤！') : '';

    $logs = UserLog::find ('all', array ('select' => 'id, backup, json', 'limit' => 1000, 'conditions' => array ('json = ?', '')));

    if (!UserLog::transaction (function () use ($logs) {
      $tmp = array_filter (array_map (function ($log) {
        if (!write_file ($t = FCPATH . 'temp' . DIRECTORY_SEPARATOR . 'backup_user_log_' . $log->id . '.json', $log->backup)) return false;
        return $log->json->put ($t) && !($log->backup = '') && $log->save ();
      }, $logs));
      return count ($tmp) == count ($logs);
    })) return $this->_save_cronjob ($cronjob, '寫入檔案或上傳有失敗！');

    $cronjob->status = Cronjob::STATUS_2;
    return $this->_save_cronjob ($cronjob, '處理了 ' . count ($logs) . ' 筆');
  }

//   public function x ($id = 0) {
//     // 35 88 98
//     $is = InvoiceImage::find ('all', array ('order' => 'id ASC', 'conditions' => array ('id > 0')));
    
//     foreach ($is as $i) {
//       echo $i->id;
//       $o = IncomeItemImage::find ('one', array ('conditions' => array ('income_item_id = ?', $i->invoice_id)));
// // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
// $x1 = str_replace ('https://cdn.zeusdesign.com.tw/', '', $i->name->url ());
// $x2 = str_replace ('https://cdn.zeusdesign.com.tw/', '', $i->name->url ('800w'));

// $x1 = str_replace ('invoice_images', 'income_item_images', $x1);
// $x2 = str_replace ('invoice_images', 'income_item_images', $x2);


//       download_web_file ($i->name->url (), $w1 = FCPATH . 'temp/' . ((string) $i->name));
//       download_web_file ($i->name->url ('800w'), $w2 = FCPATH . 'temp/800w_' . ((string) $i->name));

//       S3::putFile ($w1, 'cdn.zeusdesign.com.tw', $x1);
//       S3::putFile ($w2, 'cdn.zeusdesign.com.tw', $x2);
//       $o->name = (string) $i->name;
//       echo " - ok\n";
//     }
//   }
  // public function o ($id = 0) {
  //   // echo $id ? Mail::find_by_id ($id)->content : Mail::last ()->content;
  // }
  // public function x () {
  //   array_filter(json_decode (read_file (FCPATH . 'temp/users.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = User::create (array_intersect_key ($t, User::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/user_sets.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = UserSet::create (array_intersect_key ($t, UserSet::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/user_logs.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = UserLog::create (array_intersect_key ($t, UserLog::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/ckeditor_images.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = CkeditorImage::create (array_intersect_key ($t, CkeditorImage::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/article_tags.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = ArticleTag::create (array_intersect_key ($t, ArticleTag::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/articles.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Article::create (array_intersect_key ($t, Article::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/article_sources.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = ArticleSource::create (array_intersect_key ($t, ArticleSource::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/article_tag_mappings.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = ArticleTagMapping::create (array_intersect_key ($t, ArticleTagMapping::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/banners.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Banner::create (array_intersect_key ($t, Banner::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/promos.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Promo::create (array_intersect_key ($t, Promo::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/work_tags.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = WorkTag::create (array_intersect_key ($t, WorkTag::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/works.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Work::create (array_intersect_key ($t, Work::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/work_tag_mappings.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = WorkTagMapping::create (array_intersect_key ($t, WorkTagMapping::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/work_images.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = WorkImage::create (array_intersect_key ($t, WorkImage::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
    
  //   array_filter(json_decode (read_file (FCPATH . 'temp/work_items.json'), true), function ($t) {
        
  //       if (in_array ($t['type'], array ('Client'))) $t['type'] = WorkItem::TYPE_1;
  //       else if (in_array ($t['type'], array ('Details'))) $t['type'] = WorkItem::TYPE_2;
  //       else if (in_array ($t['type'], array ('Technology'))) $t['type'] = WorkItem::TYPE_3;
  //       else if (in_array ($t['type'], array ('Links', 'APP Link - android'))) $t['type'] = WorkItem::TYPE_4;
  //       else if (in_array ($t['type'], array ('Demo Links'))) $t['type'] = WorkItem::TYPE_5;
  //       else $t['type'] = WorkItem::TYPE_6;

  //     if (!verifyCreateOrm ($obj = WorkItem::create (array_intersect_key ($t, WorkItem::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });


  //   array_filter(json_decode (read_file (FCPATH . 'temp/companies.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Company::create (array_intersect_key ($t, Company::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/company_pms.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = CompanyPm::create (array_intersect_key ($t, CompanyPm::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/company_pm_items.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = CompanyPmItem::create (array_intersect_key ($t, CompanyPmItem::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });


  //   array_filter(json_decode (read_file (FCPATH . 'temp/income_items.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = IncomeItem::create (array_intersect_key ($t, IncomeItem::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/income_item_images.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = IncomeItemImage::create (array_intersect_key ($t, IncomeItemImage::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/income_item_details.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = IncomeItemDetail::create (array_intersect_key ($t, IncomeItemDetail::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });

  //   array_filter(json_decode (read_file (FCPATH . 'temp/schedule_tags.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = ScheduleTag::create (array_intersect_key ($t, ScheduleTag::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/schedules.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Schedule::create (array_intersect_key ($t, Schedule::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/schedule_items.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = ScheduleItem::create (array_intersect_key ($t, ScheduleItem::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/ftps.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Ftp::create (array_intersect_key ($t, Ftp::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });

  //   array_filter(json_decode (read_file (FCPATH . 'temp/tasks.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Task::create (array_intersect_key ($t, Task::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/task_user_mappings.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = TaskUserMapping::create (array_intersect_key ($t, TaskUserMapping::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/task_attachments.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = TaskAttachment::create (array_intersect_key ($t, TaskAttachment::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/task_commits.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = TaskCommit::create (array_intersect_key ($t, TaskCommit::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });

  //   array_filter(json_decode (read_file (FCPATH . 'temp/notices.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Notice::create (array_intersect_key ($t, Notice::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });

  //   array_filter(json_decode (read_file (FCPATH . 'temp/mails.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Mail::create (array_intersect_key ($t, Mail::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/outcomes.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Outcome::create (array_intersect_key ($t, Outcome::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/deploys.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Deploy::create (array_intersect_key ($t, Deploy::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  //   array_filter(json_decode (read_file (FCPATH . 'temp/incomes.json'), true), function ($t) {
  //     if (!verifyCreateOrm ($obj = Income::create (array_intersect_key ($t, Income::table ()->columns)))) return false;
  //     if (isset ($t['updated_at'])) $obj->updated_at = $t['updated_at'];
  //     if (isset ($t['created_at'])) $obj->created_at = $t['created_at'];
  //     return $obj->save ();
  //   });
  // }

  // public function ptt () {
  //   if (!$tags = PttTag::find ('all', array ('select' => 'id, uri', 'conditions' => array ('uri != ?', ''))))
  //     return ;

  //   $this->load->library ('PttGeter');

  //   foreach ($tags as $tag) {
  //     $uri = $tag->uri;

  //     for ($i = 0; $i < 100; $i++) { 

  //       $gets = PttGeter::getListAndPrevNextUri ($uri, $tag->id);
  //       if (!$gets['list']) break;
  //       $uri = $gets['prev'];

  //       foreach ($gets['list'] as $article) {
  //         if (!$obj = Ptt::find ('one', array ('select' => 'id, pid, cnt, updated_at', 'conditions' => array ('pid = ?', $article['pid'])))) {
  //           Ptt::create ($article);
  //         } else if ($obj->cnt != $article['cnt']) {
  //           $obj->cnt = $article['cnt'];
  //           $obj->save ();
  //         }
  //       }
        
  //       echo "$i \n";
  //     }
  //   }
  // }
  // public function token () {
  // }
}