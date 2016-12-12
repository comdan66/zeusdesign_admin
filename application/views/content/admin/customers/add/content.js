/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

$(function () {
  var $select = $('select[name="customer_company_id"]');
  var $telephone = $('#telephone');
  function telephone () { $telephone.text ($select.find ('option:selected').data ('telephone')); }
  $select.change (telephone);
  telephone ();
});