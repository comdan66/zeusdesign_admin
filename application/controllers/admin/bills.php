<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Bills extends Admin_controller {
  private $uri_1 = null;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('bills')))
      return redirect_message (array ('admin'), array (
            '_flash_danger' => '您的權限不足，或者頁面不存在。'
          ));

    $this->uri_1 = 'admin/bills';

    $this->add_param ('now_url', base_url ($this->uri_1));
  }
  private function _search_columns () {
    return array ( 
        array ('key' => 'month',   'title' => '月份',    'sql' => 'MONTH(date_at) = ?', 'select' => array (array ('value' => '2', 'text' => '01~02月'), array ('value' => '4', 'text' => '03~04月'), array ('value' => '6', 'text' => '05~06月'), array ('value' => '8', 'text' => '07~08月'), array ('value' => '10', 'text' => '09~10月'), array ('value' => '12', 'text' => '11~12月'))), 
        array ('key' => 'year',    'title' => '年份',    'sql' => 'YEAR(date_at) = ?', 'select' => array_reverse (array (array ('value' => '2014', 'text' => '2014 年'), array ('value' => '2015', 'text' => '2015 年'), array ('value' => '2016', 'text' => '2016 年'), array ('value' => '2017', 'text' => '2017 年')))), 
      );
  }
  public function index ($offset = 0) {
    $columns = $this->_search_columns ();
    
    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $year = date ('Y');
    $month = date ('m');

    $m = $y = 0;
    foreach ($columns as $column) {
      if ($column['key'] == 'month' && $column['value'])
        $m = $column['value'];
      if ($column['key'] == 'year' && $column['value'])
        $y = $column['value'];
    }

    $objs = array ();
    for ($i = $year; $i >= 2014 ; $i--) { 
      if (!(!$y || $y == $i)) continue;

      $objs[$i] = array ();
      for ($j = $i == $year ? ($month % 2 ? $month + 1 : $month) : 12; $j >= 1; $j -= 2) {
        if (!(!$m || $m == $j)) continue;

        $in_condition = array_values ($conditions);
        $ou_condition = array_values ($conditions);

        OaModel::addConditions ($in_condition, 'is_finished = ? AND date_at BETWEEN ? AND ?', Billin::IS_FINISHED, $i . '-' . sprintf ('%02d', $j - 1) . '-' . '01', date ('Y-m-d', strtotime ($i . '-' . sprintf ('%02d', $j) . '-' . '01' . ' +1 month -1 day')));
        OaModel::addConditions ($ou_condition, 'is_finished = ? AND date_at BETWEEN ? AND ?', Billou::IS_FINISHED, $i . '-' . sprintf ('%02d', $j - 1) . '-' . '01', date ('Y-m-d', strtotime ($i . '-' . sprintf ('%02d', $j) . '-' . '01' . ' +1 month -1 day')));

        $range = sprintf ('%02d', $j - 1) . '~' . sprintf ('%02d', $j) . '月';
        $ins = Billin::find ('all', array ('select' => 'money, zeus_money', 'order' => 'date_at DESC', 'conditions' => $in_condition));
        $ous = Billou::find ('all', array ('select' => 'money', 'order' => 'date_at DESC', 'conditions' => $ou_condition));
        $in_monry = array_sum (array_map (function ($o) { return $o->money; }, $ins));
        $in_z_monry = array_sum (array_map (function ($o) { return $o->zeus_money; }, $ins));
        $ou_monry = array_sum (array_map (function ($o) { return $o->money; }, $ous));

        array_push ($objs[$i], array (
            'range' => $range,
            'in_monry' => $in_monry,
            'in_z_monry' => $in_z_monry,
            'ou_monry' => $ou_monry
          ));
      }
    }

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns
      ));
  }
}
