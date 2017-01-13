<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Prices extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;

  public function __construct () {
    parent::__construct ();

    if (!User::current ()->in_roles (array ('price')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/prices';
    $this->icon = 'icon-abacus';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Price::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  public function export () {
    if (!(($ids = OAInput::get ('ids')) && ($ids = array_filter (explode (',', $ids)))))
      return redirect_message (array ($this->uri_1, 'abacus'), array ('_flash_danger' => '匯出失敗'));

    $prices = array_map (function ($id) { return Price::find ('one', array ('include' => array ('type'), 'conditions' => array ('id = ?', $id))); }, $ids);

    $types = array ();
    foreach ($prices as $price)
      if (!isset ($types[$price->price_type_id])) $types[$price->price_type_id] = array ('name' => $price->type->name, 'prices' => array ($price));
      else array_push ($types[$price->price_type_id]['prices'], $price);

    $types = array_values ($types);

    $this->load->library ('OAExcel');
    $excel = new OAExcel ();
    
    $infos = array (
      array ('title' => '序號', 'exp' => '$obj->id',        'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT),
      array ('title' => '項目', 'exp' => '$obj->name',      'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT),
      array ('title' => '規格', 'exp' => '$obj->desc',      'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT),
      array ('title' => '數量', 'exp' => '1',               'format' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER),
      array ('title' => '單價', 'exp' => '$obj->money',     'format' => PHPExcel_Style_NumberFormat::FORMAT_MONEY),
      array ('title' => '總計', 'exp' => '1 * $obj->money', 'format' => PHPExcel_Style_NumberFormat::FORMAT_MONEY),
      array ('title' => '備註', 'exp' => '$obj->memo',      'format' => PHPExcel_Style_NumberFormat::FORMAT_TEXT));
    $col = count ($infos);
    $row = 1;

// ============================
    $excel->getActiveSheet ()->getColumnDimension ('A')->setWidth (14);
    $excel->getActiveSheet ()->getColumnDimension ('B')->setWidth (14);
    $excel->getActiveSheet ()->getColumnDimension ('C')->setWidth (16);
    $excel->getActiveSheet ()->getColumnDimension ('D')->setWidth (14);
    $excel->getActiveSheet ()->getColumnDimension ('E')->setWidth (14);
    $excel->getActiveSheet ()->getColumnDimension ('F')->setWidth (16);
    $excel->getActiveSheet ()->getColumnDimension ('G')->setWidth (16);
// ============================

    $excel->getActiveSheet ()->getRowDimension ($row)->setRowHeight (35);
    $excel->getActiveSheet ()->mergeCells ('A' . $row . ':' . chr (65 + $col - 1) . '1');
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->applyFromArray (array ('font' => array ('size'  => 15, 'bold'  => true, 'color' => array ('rgb' => '2ba97f'))));
    $excel->getActiveSheet ()->SetCellValue ('A' . $row, "宙思設計");
    $row += 1;

// ============================
    $excel->getActiveSheet ()->getRowDimension ($row)->setRowHeight (25);
    $excel->getActiveSheet ()->mergeCells ('A' . $row . ':' . chr (65 + $col - 1) . '2');
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->applyFromArray (array ('font' => array ('size'  => 10, 'bold'  => false, 'color' => array ('rgb' => '676767'), )));
    $excel->getActiveSheet ()->SetCellValue ('A' . $row, "報價單");
    $row += 1;

// ============================
    $excel->getActiveSheet ()->mergeCells ('A' . $row . ':' . chr (65 + $col - 1) . $row);
    $row += 1;

// ============================
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('A' . $row, "專案編號：");

    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('B' . $row, '');
    $excel->getActiveSheet ()->mergeCells ('B' . $row . ':C' . $row);

    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('D' . $row, "報價日期：");

    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('E' . $row, date ('Y.m.d'));
    $excel->getActiveSheet ()->mergeCells ('E' . $row . ':' . chr (65 + $col - 1) . $row);

    $row += 1;
// ============================
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('A' . $row, "公司名稱：");

    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('B' . $row, '');
    $excel->getActiveSheet ()->mergeCells ('B' . $row . ':C' . $row);

    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('D' . $row, "專案聯絡人：");

    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('E' . $row, '');
    $excel->getActiveSheet ()->mergeCells ('E' . $row . ':' . chr (65 + $col - 1) . $row);

    $row += 1;
// ============================
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('A' . $row, "聯絡電話：");

    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('B' . $row, '');
    $excel->getActiveSheet ()->mergeCells ('B' . $row . ':C' . $row);

    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('D' . $row, "電子信箱：");

    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('E' . $row, '');
    $excel->getActiveSheet ()->mergeCells ('E' . $row . ':' . chr (65 + $col - 1) . $row);

    $row += 1;
// ============================
    $excel->getActiveSheet ()->mergeCells ('A' . $row . ':' . chr (65 + $col - 1) . $row);
    $row += 1;
// ============================
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('A' . $row, "報價人員：");

    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('B' . $row, User::current ()->name);
    $excel->getActiveSheet ()->mergeCells ('B' . $row . ':C' . $row);

    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $excel->getActiveSheet ()->getStyle ('D' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('D' . $row, "聯絡電話：");

    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('E' . $row, '');
    $excel->getActiveSheet ()->mergeCells ('E' . $row . ':' . chr (65 + $col - 1) . $row);

    $row += 1;
// ============================
    $excel->getActiveSheet ()->mergeCells ('A' . $row . ':' . chr (65 + $col - 1) . $row);
    $row += 1;
    $excel->getActiveSheet ()->mergeCells ('A' . $row . ':' . chr (65 + $col - 1) . $row);
    $row += 1;

// ============================

    foreach ($types as $type) {
      $excel->getActiveSheet ()->mergeCells ('A' . $row . ':' . chr (65 + $col - 1) . $row);
      $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
      $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setWrapText (true);
      $excel->getActiveSheet ()->getRowDimension ($row)->setRowHeight (28);
      $excel->getActiveSheet ()->getStyle ('A' . $row)->applyFromArray (array (
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => 'fff2cc')
        ),
        'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN,
              'color' => array('rgb' => 'AAAAAA')
          )),
        'font' => array (
            'size'  => 13,
            'bold'  => true,
            'color' => array ('rgb' => '272822'),
        )));
      $excel->getActiveSheet ()->SetCellValue ('A' . $row, $type['name']);

      $row += 1;
// --------------------------

      $excel->getActiveSheet ()->getStyle ('A' . $row . ':' . chr (65 + $col - 1) . $row)->applyFromArray (array (
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array('rgb' => 'fff8e4')),
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN,
                  'color' => array('rgb' => 'AAAAAA')
              ))));
      $excel->getActiveSheet ()->getRowDimension ($row)->setRowHeight (25);
      foreach ($infos as $i => $val) {
        $excel->getActiveSheet ()->getStyle (chr (65 + $i) . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $excel->getActiveSheet ()->getStyle (chr (65 + $i) . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getActiveSheet ()->getStyle (chr (65 + $i) . $row)->getAlignment ()->setWrapText (true);
        $excel->getActiveSheet ()->SetCellValue (chr (65 + $i) . $row, $val['title']);
      }

      $row += 1;
// --------------------------
      $rows = array ();
      foreach ($type['prices'] as $j => $obj) {
        $row += $j;
        array_push ($rows, $row);

        foreach ($infos as $i => $info) {
          eval ('$val = ' . $info['exp'] . ';');
          $excel->getActiveSheet ()->getStyle (chr (65 + $i) . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
          $excel->getActiveSheet ()->getStyle (chr (65 + $i) . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet ()->getStyle (chr (65 + $i) . $row)->getNumberFormat ()->setFormatCode ($info['format']);
          if ($i == 5) {
            $excel->getActiveSheet ()->SetCellValue (chr (65 + $i) . $row, '=PRODUCT(D' . $row . ', E' . $row . ')');
          } else {
            $excel->getActiveSheet ()->SetCellValue (chr (65 + $i) . $row, $val);
          }
        }
      }
      $row += 1;
// --------------------------
// 
      $excel->getActiveSheet ()->getStyle ('A' . $row . ':' . chr (65 + $col - 1) . $row)->applyFromArray (array (
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'e6e6e6')),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => 'AAAAAA')
            ))));

      $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $excel->getActiveSheet ()->getStyle ('E' . $row)->applyFromArray (array ('font' => array ('color' => array ('rgb' => 'be5150'))));
      $excel->getActiveSheet ()->getStyle ('E' . $row)->getAlignment ()->setWrapText (true);
      $excel->getActiveSheet ()->SetCellValue ('E' . $row, '小計');
      
      $excel->getActiveSheet ()->getStyle ('F' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $excel->getActiveSheet ()->getStyle ('F' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $excel->getActiveSheet ()->getStyle ('F' . $row)->getNumberFormat ()->setFormatCode (PHPExcel_Style_NumberFormat::FORMAT_MONEY);
      $excel->getActiveSheet ()->SetCellValue ('F' . $row, '=SUM(' . implode (', ', array_map (function ($row) { return 'F' . $row; }, $rows)) . ')');
      $row += 1;

      $excel->getActiveSheet ()->mergeCells ('A' . $row . ':' . chr (65 + $col - 1) . $row);
      $row += 1;
// --------------------------
    }


    $excel->getActiveSheet ()->mergeCells ('A' . $row . ':' . chr (65 + $col - 1) . $row);
    $row += 1;
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->applyFromArray (array ('font' => array ('bold'  => true, 'color' => array ('rgb' => '6f6f6f'))));
    $excel->getActiveSheet ()->SetCellValue ('A' . $row, '備註');

    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->applyFromArray (array ('font' => array ('bold'  => false, 'color' => array ('rgb' => '6f6f6f'))));
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->SetCellValue ('B' . $row, '本報價單有效期為報價日起 30 天。');
    $excel->getActiveSheet ()->mergeCells ('B' . $row . ':' . chr (65 + $col - 1) . $row);
    $row += 1;
      
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->getStyle ('B' . $row)->applyFromArray (array ('font' => array ('bold'  => false, 'color' => array ('rgb' => '6f6f6f'))));
    $excel->getActiveSheet ()->mergeCells ('B' . $row . ':' . chr (65 + $col - 1) . $row);
    $excel->getActiveSheet ()->SetCellValue ('B' . $row, '本報價單金額皆未稅，稅金一律外加註明。');
    $row += 1;

    $excel->getActiveSheet ()->mergeCells ('A' . $row . ':' . chr (65 + $col - 1) . $row);
    $row += 1;
    
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $excel->getActiveSheet ()->getStyle ('A' . $row)->applyFromArray (array ('font' => array ('size'  => 10)));
    $excel->getActiveSheet ()->getStyle ('A' . $row)->getAlignment ()->setWrapText (true);
    $excel->getActiveSheet ()->mergeCells ('A' . $row . ':' . chr (65 + $col - 1) . $row);
    $excel->getActiveSheet ()->SetCellValue ('A' . $row, 'ZEUS Design CO., Ltd.');
    

    $excel->getActiveSheet ()->getStyle ('A1:' . chr (65 + $col - 1) . $row)->applyFromArray (array (
      'font' => array (
        'name'  => '微軟正黑體'),
      'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN,
              'color' => array('rgb' => 'AAAAAA')
          ))));

    $excel->getActiveSheet ()->setTitle ('宙思報價單');
    $excel->getActiveSheet ();

    $filename = '宙思報價單_' . date ('Ymd') . '.xlsx';
    header ('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf8');
    header ('Content-Disposition: attachment; filename=' . $filename);

    $objWriter = new PHPExcel_Writer_Excel2007 ($excel);
    $objWriter->save ("php://output");

  }
  public function abacus ($offset = 0) {
    $limit = 25;
    $types = array_map (function ($type) {
      return array (
          'id' => $type->id,
          'name' => $type->name,
          'prices' => array_map (function ($price) {
              return array (
                  'id' => $price->id,
                  'name' => $price->name,
                  'money' => $price->money,
                  'desc' => $price->desc,
                );
            }, $type->prices)
        );
    }, PriceType::find ('all', array ('include' => 'prices')));

    return $this->add_param ('now_url', base_url ($this->uri_1 . '/abacus'))
                ->load_view (array (
        'types' => $types,
      ));
  }
}
