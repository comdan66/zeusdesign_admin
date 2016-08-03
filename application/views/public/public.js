/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

Array.prototype.column = function (k) {
  return this.map (function (t) { return k ? eval ("t." + k) : t; });
};
Array.prototype.diff = function (a, k) {
  return this.filter (function (i) { return a.column (k).indexOf (eval ("i." + k)) < 0; });
};
Array.prototype.max = function (k) {
  return Math.max.apply (null, this.column (k));
};
Array.prototype.min = function (k) {
  return Math.min.apply (null, this.column (k));
};

function getStorage (key) { return ((typeof (Storage) !== 'undefined') && (value = localStorage.getItem (key)) && (value = JSON.parse (value))) ? value : undefined; }
function setStorage (key, data) { if (typeof (Storage) !== 'undefined') { localStorage.setItem (key, JSON.stringify (data)); return true; } return false; }

window.ajaxError = function (result) {
  console.error (result.responseText);
};

$(function () {
  $('._i').imgLiquid ({ verticalAlign:'center' });
  $('._it').imgLiquid ({ verticalAlign:'top' });
  window.vars = {};
  window.funs = {};

  window.funs.storage = {};
  window.funs.storage.minMenu = {
    storageKey: 'zeus.menu.min',
    isMin: function (val) {
      if (typeof val !== 'undefined') setStorage (this.storageKey, val);
      var tmp = getStorage (this.storageKey);
      return tmp ? tmp : false;
    },
  };

  window.vars.$body = $('body');
});