/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

$(function () {
  var $select = $('select[name="customer_company_id"]');
  var $telephone = $('#telephone');
  function telephone () { $telephone.text ($select.find ('option:selected').data ('telephone')); }
  $select.change (telephone);
  telephone ();
});