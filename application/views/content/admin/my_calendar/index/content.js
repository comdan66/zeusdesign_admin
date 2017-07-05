/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */
/*! sprintf-js v1.0.3 | Copyright (c) 2007-present, Alexandru Marasteanu <hello@alexei.ro> | BSD-3-Clause */
!function(e){"use strict";function t(){var e=arguments[0],r=t.cache;return r[e]&&r.hasOwnProperty(e)||(r[e]=t.parse(e)),t.format.call(null,r[e],arguments)}function r(e){return"number"==typeof e?"number":"string"==typeof e?"string":Object.prototype.toString.call(e).slice(8,-1).toLowerCase()}function n(e,t){return t>=0&&7>=t&&i[e]?i[e][t]:Array(t+1).join(e)}var s={not_string:/[^s]/,not_bool:/[^t]/,not_type:/[^T]/,not_primitive:/[^v]/,number:/[diefg]/,numeric_arg:/bcdiefguxX/,json:/[j]/,not_json:/[^j]/,text:/^[^\x25]+/,modulo:/^\x25{2}/,placeholder:/^\x25(?:([1-9]\d*)\$|\(([^\)]+)\))?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-gijostTuvxX])/,key:/^([a-z_][a-z_\d]*)/i,key_access:/^\.([a-z_][a-z_\d]*)/i,index_access:/^\[(\d+)\]/,sign:/^[\+\-]/};t.format=function(e,a){var i,o,l,c,p,f,u,g=1,_=e.length,d="",b=[],h=!0,x="";for(o=0;_>o;o++)if(d=r(e[o]),"string"===d)b[b.length]=e[o];else if("array"===d){if(c=e[o],c[2])for(i=a[g],l=0;l<c[2].length;l++){if(!i.hasOwnProperty(c[2][l]))throw new Error(t('[sprintf] property "%s" does not exist',c[2][l]));i=i[c[2][l]]}else i=c[1]?a[c[1]]:a[g++];if(s.not_type.test(c[8])&&s.not_primitive.test(c[8])&&"function"==r(i)&&(i=i()),s.numeric_arg.test(c[8])&&"number"!=r(i)&&isNaN(i))throw new TypeError(t("[sprintf] expecting number but found %s",r(i)));switch(s.number.test(c[8])&&(h=i>=0),c[8]){case"b":i=parseInt(i,10).toString(2);break;case"c":i=String.fromCharCode(parseInt(i,10));break;case"d":case"i":i=parseInt(i,10);break;case"j":i=JSON.stringify(i,null,c[6]?parseInt(c[6]):0);break;case"e":i=c[7]?parseFloat(i).toExponential(c[7]):parseFloat(i).toExponential();break;case"f":i=c[7]?parseFloat(i).toFixed(c[7]):parseFloat(i);break;case"g":i=c[7]?parseFloat(i).toPrecision(c[7]):parseFloat(i);break;case"o":i=i.toString(8);break;case"s":i=String(i),i=c[7]?i.substring(0,c[7]):i;break;case"t":i=String(!!i),i=c[7]?i.substring(0,c[7]):i;break;case"T":i=r(i),i=c[7]?i.substring(0,c[7]):i;break;case"u":i=parseInt(i,10)>>>0;break;case"v":i=i.valueOf(),i=c[7]?i.substring(0,c[7]):i;break;case"x":i=parseInt(i,10).toString(16);break;case"X":i=parseInt(i,10).toString(16).toUpperCase()}s.json.test(c[8])?b[b.length]=i:(!s.number.test(c[8])||h&&!c[3]?x="":(x=h?"+":"-",i=i.toString().replace(s.sign,"")),f=c[4]?"0"===c[4]?"0":c[4].charAt(1):" ",u=c[6]-(x+i).length,p=c[6]&&u>0?n(f,u):"",b[b.length]=c[5]?x+i+p:"0"===f?x+p+i:p+x+i)}return b.join("")},t.cache={},t.parse=function(e){for(var t=e,r=[],n=[],a=0;t;){if(null!==(r=s.text.exec(t)))n[n.length]=r[0];else if(null!==(r=s.modulo.exec(t)))n[n.length]="%";else{if(null===(r=s.placeholder.exec(t)))throw new SyntaxError("[sprintf] unexpected placeholder");if(r[2]){a|=1;var i=[],o=r[2],l=[];if(null===(l=s.key.exec(o)))throw new SyntaxError("[sprintf] failed to parse named argument key");for(i[i.length]=l[1];""!==(o=o.substring(l[0].length));)if(null!==(l=s.key_access.exec(o)))i[i.length]=l[1];else{if(null===(l=s.index_access.exec(o)))throw new SyntaxError("[sprintf] failed to parse named argument key");i[i.length]=l[1]}r[2]=i}else a|=2;if(3===a)throw new Error("[sprintf] mixing positional and named placeholders is not (yet) supported");n[n.length]=r}t=t.substring(r[0].length)}return n};var a=function(e,r,n){return n=(r||[]).slice(0),n.splice(0,0,e),t.apply(null,n)},i={0:["","0","00","000","0000","00000","000000","0000000"]," ":[""," ","  ","   ","    ","     ","      ","       "],_:["","_","__","___","____","_____","______","_______"]};"undefined"!=typeof exports?(exports.sprintf=t,exports.vsprintf=a):(e.sprintf=t,e.vsprintf=a,"function"==typeof define&&define.amd&&define(function(){return{sprintf:t,vsprintf:a}}))}("undefined"==typeof window?this:window);

!function(){function t(t){return I[t-S][0]}function n(t){for(var n=I[t-S],r=n[0],e=n[3].toString(2).split(""),a=0;a<16-e.length;a++)e.unshift(0);for(var o=r?13:12,d=0,u=[],a=0;a<o;a++)0==e[a]?(d+=29,u.push(29)):(d+=30,u.push(30));return{yearDays:d,monthDays:u}}function r(t,r){for(var e=n(t),a=r>0?r:e.yearDays-Math.abs(r),o=e.monthDays,d=0,u=0,i=0;i<o.length;i++)if((d+=o[i])>a){u=i,d-=o[i];break}return[t,u,a-d+1]}function e(t,n,e){var o=I[t-S],d=a(t,o[1]-1,o[2],t,n,e);return 0==d?[t,0,1]:r(d>0?t:t-1,d)}function a(t,n,r,e,a,o){var d=new Date(t,n,r).getTime();return(new Date(e,a,o).getTime()-d)/864e5}function o(t,r,e){for(var a=n(t).monthDays,o=0,d=0;d<a.length&&d<r;d++)o+=a[d];return o+e-1}function d(t,n){return new Date(31556925974.7*(t-1890)+6e4*U[n]+Date.UTC(1890,0,5,16,2,31)).getUTCDate()}function u(t){for(var n={},r=0,e=0;e<24;e++){var a=d(t,e);e%2==0&&r++,n[F(r-1,a)]=Y.solarTerm[e]}return n}function i(t){var n=t-1890+25;return Y.zodiac[n%12]}function h(t){return Y.heavenlyStems[t%10]+Y.earthlyBranches[t%12]}function f(t,n){return n=n||0,h(t-1890+25+n)}function l(t,n,r){return r=r||0,h(12*(t-1890)+n+12+r)}function s(t,n,r){return h(Date.UTC(t,n,r)/864e5+29219+18)}function c(t,n){return[31,m(t)?29:28,31,30,31,30,31,31,30,31,30,31][n]}function m(t){return t%4==0&&t%100!=0||t%400==0}function D(t,n,r,e){var a=arguments.length,o=new Date;return t=a?parseInt(t,10):o.getFullYear(),n=a?parseInt(n-1,10):o.getMonth(),r=a?parseInt(r,10)||o.getDate():o.getDate(),t<(e||S+1)||t>b?{error:100,msg:T[100]}:{year:t,month:n,day:r}}function y(t,n,r){var e=D(t,n,r);if(e.error)return e;var a=e.year,d=o(a,e.month,e.day),u=I[a-S],i=u[1],h=u[2],f=new Date(a,i-1,h).getTime()+864e5*d;return f=new Date(f),{year:f.getFullYear(),month:f.getMonth()+1,day:f.getDate()}}function g(r,a,o){var h=D(r,a,o,S);if(h.error)return h;var c=h.year,m=h.month,y=h.day;L.setCurrent(c);var g=L.get("term2")?L.get("term2"):L.set("term2",d(c,2)),v=L.get("termList")?L.get("termList"):L.set("termList",u(c)),p=d(c,2*m),C=m>1||1==m&&y>=g?c+1:c,w=y>=p?m+1:m,T=e(c,m,y),M=t(T[0]),b="";b=M>0&&M==T[1]?"閏"+Y.monthCn[T[1]-1]+"月":M>0&&T[1]>M?Y.monthCn[T[1]-1]+"月":Y.monthCn[T[1]]+"月";var I=z[F(m,y)]?z[F(m,y)]:[],U=!1,Z=n(T[0]).monthDays;return T[1]==Z.length-1&&T[2]==Z[Z.length-1]?U=G.d0100:M>0&&T[1]>M&&(U=G[F(T[1]-1,T[2])]?G[F(T[1]-1,T[2])]:[]),{zodiac:i(C),GanZhiYear:f(C),GanZhiMonth:l(c,w),GanZhiDay:s(c,m,y),worktime:"清明"==v[F(m,y)]||!!I.length&&I[1]||!!U.length&&U[1],term:v[F(m,y)],lunarYear:T[0],lunarMonth:T[1]+1,lunarDay:T[2],lunarMonthName:b,lunarDayName:Y.dateCn[T[2]-1],lunarLeapMonth:M,solarFestival:I.length?I[0]:[],lunarFestival:U.length?U[0]:[]}}function v(t,n,r){var e=D(t,n);if(e.error)return e;for(var a=p(e.year,e.month+1,r),o=0;o<a.monthData.length;o++){var d=a.monthData[o],u=g(d.year,d.month,d.day);C(a.monthData[o],u)}return a}function p(t,n,r){var e=D(t,n);if(e.error)return e;var a,o,d,u=e.year,i=e.month,h={firstDay:new Date(u,i,1).getDay(),monthDays:c(u,i),monthData:[]};if(h.monthData=w(u,i+1,h.monthDays,1),r){if(h.firstDay>0){var f=i-1<0?u-1:u,l=i-1<0?11:i-1;a=c(f,l),o=w(f,l+1,h.firstDay,a-h.firstDay+1),h.monthData=o.concat(h.monthData)}if(42-h.monthData.length!=0){var s=i+1>11?u+1:u,m=i+1>11?0:i+1,y=42-h.monthData.length;d=w(s,m+1,y,1),h.monthData=h.monthData.concat(d)}}return h}var C=function(t,n){if(t&&n&&"object"==typeof n)for(var r in n)t[r]=n[r];return t},w=function(t,n,r,e){var a=[];if(e=e||0,r<1)return a;for(var o=e,d=0;d<r;d++)a.push({year:t,month:n,day:o}),o++;return a},T={100:"年份超出了可達的範圍，僅支持1891年至2100年",101:"參數輸入錯誤，請查閱文檔"},M=null,L={current:"",setCurrent:function(t){this.current!=t&&(this.current=t,this.clear())},set:function(t,n){return M||(M={}),M[t]=n,M[t]},get:function(t){return M||(M={}),M[t]},clear:function(){M=null}},F=function(t,n){return t+=1,t=t<10?"0"+t:t,n=n<10?"0"+n:n,"d"+t+n},S=1890,b=2100,Y={solarTerm:["小寒","大寒","立春","雨水","驚蟄","春分","清明","穀雨","立夏","小滿","芒種","夏至","小暑","大暑","立秋","處暑","白露","秋分","寒露","霜降","立冬","小雪","大雪","冬至"],heavenlyStems:["甲","乙","丙","丁","戊","己","庚","辛","壬","癸"],earthlyBranches:["子","丑","寅","卯","辰","巳","午","未","申","酉","戌","亥"],zodiac:["鼠","牛","虎","兔","龍","蛇","馬","羊","猴","雞","狗","豬"],monthCn:["正","二","三","四","五","六","七","八","九","十","十一","十二"],dateCn:["初一","初二","初三","初四","初五","初六","初七","初八","初九","初十","十一","十二","十三","十四","十五","十六","十七","十八","十九","二十","廿一","廿二","廿三","廿四","廿五","廿六","廿七","廿八","廿九","三十","卅一"]},z={d0101:[["元旦","中華民國開國紀念日"],!0],d0111:[["司法節"],!1],d0115:[["藥師節"],!1],d0123:[["自由日"],!1],d0204:[["農民節"],!1],d0214:[["情人節"],!1],d0215:[["戲劇節"],!1],d0219:[["新生活運動紀念日"],!1],d0228:[["和平紀念日"],!0],d0301:[["兵役節"],!1],d0305:[["童子軍節"],!1],d0308:[["婦女節"],!1],d0312:[["植樹節","國父逝世紀念日"],!1],d0317:[["國醫節"],!1],d0320:[["郵政節"],!1],d0321:[["氣象節"],!1],d0325:[["美術節"],!1],d0326:[["廣播節"],!1],d0329:[["青年節","革命先烈紀念日"],!1],d0330:[["出版節"],!1],d0401:[["愚人節","主計節"],!1],d0404:[["婦幼節"],!1],d0405:[["音樂節"],!1],d0407:[["衛生節"],!1],d0422:[["世界地球日"],!1],d0501:[["勞動節"],!0],d0504:[["文藝節"],!1],d0505:[["舞蹈節"],!1],d0510:[["珠算節"],!1],d0512:[["護士節"],!1],d0603:[["禁煙節"],!1],d0606:[["工程師節","水利節"],!1],d0609:[["鐵路節"],!1],d0615:[["警察節"],!1],d0630:[["會計師節"],!1],d0701:[["漁民節","公路節","稅務節"],!1],d0711:[["航海節"],!1],d0712:[["聾啞節"],!1],d0808:[["父親節"],!1],d0814:[["空軍節"],!1],d0827:[["鄭成功誕辰"],!1],d0901:[["記者節"],!1],d0903:[["軍人節","抗戰紀念"],!1],d0909:[["體育節","律師節"],!1],d0913:[["法律日"],!1],d0928:[["教師節","孔子誕辰"],!1],d1006:[["老人節"],!1],d1010:[["國慶紀念日"],!0],d1021:[["華僑節"],!1],d1025:[["台灣光復節"],!1],d1031:[["萬聖節","蔣公誕辰紀念日","榮民節"],!1],d1101:[["商人節"],!1],d1111:[["工業節","地政節"],!1],d1117:[["自來水節"],!1],d1112:[["國父誕辰紀念日","醫師節","中華文化復興節"],!1],d1121:[["防空節"],!1],d1205:[["海員節","盲人節"],!1],d1210:[["人權節"],!1],d1212:[["憲兵節"],!1],d1225:[["行憲紀念日","民族復興節","聖誕節"],!1],d1227:[["建築師節"],!1],d1228:[["電信節"],!1],d1231:[["受信節"],!1]},G={d0101:[["春節"],!0],d0102:[["回娘家"],!0],d0103:[["祭祖"],!0],d0104:[["迎神"],!1],d0105:[["開市"],!1],d0109:[["天公生"],!1],d0115:[["元宵節","觀光節"],!1],d0202:[["頭牙","土地公生"],!1],d0323:[["媽祖生"],!1],d0408:[["浴佛節"],!1],d0505:[["端午節","詩人節"],!0],d0701:[["開鬼門"],!1],d0707:[["七夕情人節"],!1],d0715:[["中元節"],!1],d0800:[["關鬼門"],!1],d0815:[["中秋節"],!0],d0909:[["重陽節"],!1],d1208:[["臘八節"],!1],d1216:[["尾牙"],!1],d1224:[["送神"],!1],d0100:[["除夕"],!0]},I=[[2,1,21,22184],[0,2,9,21936],[6,1,30,9656],[0,2,17,9584],[0,2,6,21168],[5,1,26,43344],[0,2,13,59728],[0,2,2,27296],[3,1,22,44368],[0,2,10,43856],[8,1,30,19304],[0,2,19,19168],[0,2,8,42352],[5,1,29,21096],[0,2,16,53856],[0,2,4,55632],[4,1,25,27304],[0,2,13,22176],[0,2,2,39632],[2,1,22,19176],[0,2,10,19168],[6,1,30,42200],[0,2,18,42192],[0,2,6,53840],[5,1,26,54568],[0,2,14,46400],[0,2,3,54944],[2,1,23,38608],[0,2,11,38320],[7,2,1,18872],[0,2,20,18800],[0,2,8,42160],[5,1,28,45656],[0,2,16,27216],[0,2,5,27968],[4,1,24,44456],[0,2,13,11104],[0,2,2,38256],[2,1,23,18808],[0,2,10,18800],[6,1,30,25776],[0,2,17,54432],[0,2,6,59984],[5,1,26,27976],[0,2,14,23248],[0,2,4,11104],[3,1,24,37744],[0,2,11,37600],[7,1,31,51560],[0,2,19,51536],[0,2,8,54432],[6,1,27,55888],[0,2,15,46416],[0,2,5,22176],[4,1,25,43736],[0,2,13,9680],[0,2,2,37584],[2,1,22,51544],[0,2,10,43344],[7,1,29,46248],[0,2,17,27808],[0,2,6,46416],[5,1,27,21928],[0,2,14,19872],[0,2,3,42416],[3,1,24,21176],[0,2,12,21168],[8,1,31,43344],[0,2,18,59728],[0,2,8,27296],[6,1,28,44368],[0,2,15,43856],[0,2,5,19296],[4,1,25,42352],[0,2,13,42352],[0,2,2,21088],[3,1,21,59696],[0,2,9,55632],[7,1,30,23208],[0,2,17,22176],[0,2,6,38608],[5,1,27,19176],[0,2,15,19152],[0,2,3,42192],[4,1,23,53864],[0,2,11,53840],[8,1,31,54568],[0,2,18,46400],[0,2,7,46752],[6,1,28,38608],[0,2,16,38320],[0,2,5,18864],[4,1,25,42168],[0,2,13,42160],[10,2,2,45656],[0,2,20,27216],[0,2,9,27968],[6,1,29,44448],[0,2,17,43872],[0,2,6,38256],[5,1,27,18808],[0,2,15,18800],[0,2,4,25776],[3,1,23,27216],[0,2,10,59984],[8,1,31,27432],[0,2,19,23232],[0,2,7,43872],[5,1,28,37736],[0,2,16,37600],[0,2,5,51552],[4,1,24,54440],[0,2,12,54432],[0,2,1,55888],[2,1,22,23208],[0,2,9,22176],[7,1,29,43736],[0,2,18,9680],[0,2,7,37584],[5,1,26,51544],[0,2,14,43344],[0,2,3,46240],[4,1,23,46416],[0,2,10,44368],[9,1,31,21928],[0,2,19,19360],[0,2,8,42416],[6,1,28,21176],[0,2,16,21168],[0,2,5,43312],[4,1,25,29864],[0,2,12,27296],[0,2,1,44368],[2,1,22,19880],[0,2,10,19296],[6,1,29,42352],[0,2,17,42208],[0,2,6,53856],[5,1,26,59696],[0,2,13,54576],[0,2,3,23200],[3,1,23,27472],[0,2,11,38608],[11,1,31,19176],[0,2,19,19152],[0,2,8,42192],[6,1,28,53848],[0,2,15,53840],[0,2,4,54560],[5,1,24,55968],[0,2,12,46496],[0,2,1,22224],[2,1,22,19160],[0,2,10,18864],[7,1,30,42168],[0,2,17,42160],[0,2,6,43600],[5,1,26,46376],[0,2,14,27936],[0,2,2,44448],[3,1,23,21936],[0,2,11,37744],[8,2,1,18808],[0,2,19,18800],[0,2,8,25776],[6,1,28,27216],[0,2,15,59984],[0,2,4,27424],[4,1,24,43872],[0,2,12,43744],[0,2,2,37600],[3,1,21,51568],[0,2,9,51552],[7,1,29,54440],[0,2,17,54432],[0,2,5,55888],[5,1,26,23208],[0,2,14,22176],[0,2,3,42704],[4,1,23,21224],[0,2,11,21200],[8,1,31,43352],[0,2,19,43344],[0,2,7,46240],[6,1,27,46416],[0,2,15,44368],[0,2,5,21920],[4,1,24,42448],[0,2,12,42416],[0,2,2,21168],[3,1,22,43320],[0,2,9,26928],[7,1,29,29336],[0,2,17,27296],[0,2,6,44368],[5,1,26,19880],[0,2,14,19296],[0,2,3,42352],[4,1,24,21104],[0,2,10,53856],[8,1,30,59696],[0,2,18,54560],[0,2,7,55968],[6,1,27,27472],[0,2,15,22224],[0,2,5,19168],[4,1,25,42216],[0,2,12,42192],[0,2,1,53584],[2,1,21,55592],[0,2,9,54560]],U=[0,21208,42467,63836,85337,107014,128867,150921,173149,195551,218072,240693,263343,285989,308563,331033,353350,375494,397447,419210,440795,462224,483532,504758],Z={solarToLunar:g,lunarToSolar:y,calendar:v,solarCalendar:p,getSolarMonthDays:c,setSolarFestival:function(t){C(z,t)},setLunarFestival:function(t){C(G,t)}};"function"==typeof define?define(function(){return Z}):"object"==typeof exports?module.exports=Z:window.LunarCalendar=Z}();

$(function () {
  var _panel = {
    y: 0,
    m: 0,
    d: 0,
    api: {
      create: '',
      update: '',
      day: '',
      daysort: '',
      month: '',
      content: '',
      status: '',
      delete: '',
    },
    $ckb: $('#fix_pnl_ckb'),
    $panel: $('#fix-panel'),
    $label: $('#fix-panel + label'),
    $left: $('<a />'),
    $right: $('<a />'),
    $title: $('<span />'),
    $header: $('<header />'),
    $list: $('<div />').addClass ('list'),
    $add: $('<div />').addClass ('add'),
    $content: $('<div />').addClass ('content'),
    $edit: $('<div />').addClass ('edit'),
    $task: $('<div />').addClass ('task'),
    $datas: $('#datas'),
    $body: $('body'),
    $calendar: $('.calendar'),

    init: function () {
      this.$label.unbind ('click').click (function () { this.close (); }.bind (this));

      this.$header.append (this.$left)
                  .append (this.$title)
                  .append (this.$right);

      this.api.create = this.$datas.data ('api_create');
      this.api.update = this.$datas.data ('api_update');
      this.api.day = this.$datas.data ('api_day');
      this.api.daysort = this.$datas.data ('api_daysort');
      this.api.month = this.$datas.data ('api_month');
      this.api.content = this.$datas.data ('api_content');
      this.api.status = this.$datas.data ('api_status');
      this.api.delete = this.$datas.data ('api_delete');
      this.api.task = this.$datas.data ('api_task');

      return this;
    },
    clean: function () {
      this.$panel.empty ();
      return this;
    },
    close: function () {
      this.closeTask ().closeEdit ().closeContent ().closeAdd ().clean ();
      this.$ckb.prop ('checked', false);
      this.$body.css('overflow', 'visible');
      return this;
    },
    show: function (y, m, d) {
      this.y = y;
      this.m = m;
      this.d = d;

      this.clean ();
      this.$panel.append (this.$header);
      this.$panel.append (this.$list);
      this.$panel.append (this.$add);
      this.$panel.append (this.$content);
      this.$panel.append (this.$edit);
      this.$panel.append (this.$task);

      this.showList ();
      this.$ckb.prop ('checked', true);
      this.$body.css('overflow', 'hidden');
    },
    ajaxMonth: function  ($obj, y, m, d) {
      m = isNaN (y) ? y.m : parseInt (m, 10);
      y = isNaN (y) ? y.y : parseInt (y, 10);

      $obj = $obj == null ? this.$calendar.find ('.months') : $obj;

      $.ajax ({
        url: this.api.month,
        data: {
          date: y + '-' + m + (typeof d !== 'undefined' ? '-' + d : ''),
        },
        async: true, cache: false, dataType: 'json', type: 'GET'
      })
      .done (function (r) {
        if (typeof d === 'undefined')
          $obj.find ('.days > div > div').remove ();
        else
          $obj.find ('.days > div[data-y="' + y + '"][data-m="' + m + '"][data-d="' + d + '"] > div').remove ();

        r.map (function (s) {
          $obj.find ('.days > div[data-y="' + s.y + '"][data-m="' + s.m + '"][data-d="' + s.d + '"]').append (s.c.map (function (t) {
            return $('<div />').data ('id', t.id).addClass (t.finish ? 'finish' : null).addClass ('type' + t.type).append (t.type == 2 ? $('<i />').addClass ('icon-shield') : $('<img />').attr ('src', t.img)).append ($('<span />').addClass (_color.has (_color.hex2rgb (t.color)) ? 'dark' : 'light').css ({'background-color': _color.rgbToHex (_color.hex2rgb (t.color))}).text (t.text));
          }));
        });
      })
      .fail (window.fns.ajaxFail)
      .complete (function () {});
    },
    closeTask: function () {
      this.$task.attr ('class', 'task');
      this.$right.show ();
      this.showList (true);
      return this;
    },
    closeContent: function () {
      this.$content.attr ('class', 'content');
      this.$right.show ();
      this.showList (true);
      return this;
    },
    showDelete: function (id) {
      window.fns.showLoading ('刪除中，請稍候..');

      $.ajax ({
        url: sprintf (this.api.delete, id),
        data: { _method: 'delete' },
        async: true, cache: false, dataType: 'json', type: 'get'
      })
      .done (function (r) {
        this.closeContent ();
        this.ajaxMonth (null, this.y, this.m, this.d);
      }.bind (this))
      .fail (window.fns.ajaxFail)
      .complete (function () { window.fns.closeLoading (); }.bind (this));
    },
    renderContent: function (r) {
      if (r.type == 1) this.$right.show ();
      if (r.finish) this.$content.addClass ('finish');

      this.$content.append (
        $('<span />').text ('標題')).append (
        $('<div />').addClass ('row').text (r.title));
      
      if (r.items.length)
        this.$content.append (
          $('<span />').text ('待辦事項')).append (
          $('<div />').addClass ('row').addClass ('mtckbs').append (r.items.map (function (t) {
            var that = this;

            return $('<div />').append (
              r.type == 1 ? $('<label />').data ('id', t.id).addClass ('checkbox').append (
                $('<input />').attr ('type', 'checkbox').prop ('checked', t.status).click (function () {
                  var $that = $(this).parent ();
                  if ($that.hasClass ('loading')) return;
                  $that.addClass ('loading');

                  var id = $that.data ('id');
                  var status = $(this).prop ('checked') ? 1 : 0;
                  
                  $.ajax ({
                    url: sprintf (that.api.status, id),
                    data: {status: status},
                    async: true, cache: false, dataType: 'json', type: 'POST'
                  })
                  .done (function (result) {
                    $that.removeClass ('loading');
                    $(this).prop ('checked', status.result);
                    
                    if (result.finish) that.$content.addClass ('finish');
                    else that.$content.removeClass ('finish');

                    that.ajaxMonth (null, that.y, that.m, that.d);

                  }.bind ($(this)))
                  .fail (function (result) {
                    $that.removeClass ('loading');
                    $(this).prop ('checked', !status);

                    window.fns.ajaxFail (result);
                  }.bind ($(this)));
                  
                })).append (
                $('<span />')) : $('<i/>').addClass (t.status ? 'icon-check-square' : 'icon-check_box_outline_blank')).append (
              $('<span />').text (t.content));
          }.bind (this))));
      
      if (r.tag != null)
        this.$content.append (
          $('<span />').text ('行程分類')).append (
          $('<div />').addClass ('row').addClass ('tag').append (
            $('<i />').css ({'background-color': _color.rgbToHex (_color.hex2rgb (r.tag.color))})).append (
            $('<span />').text (r.tag.name)));

      if (r.users.length)
        this.$content.append (
          $('<span />').text ('分享對象')).append (
          $('<div />').addClass ('row').addClass ('users').append (r.users.map (function (t) {
            return $('<div />').append (
              $('<img />').attr ('src', t.avatar)).append (
              $('<span />').text (t.name));
          })));

      if (r.memo.length)
        this.$content.append (
          $('<span />').text ('備註')).append (
          $('<div />').addClass ('row').text (r.memo));
      
      this.$content.append (r.type == 1 ? 
        $('<a />').text ('刪除行程').click (function () {
          if (!confirm ('確定刪除？')) return false;
          this.showDelete (r.id);
        }.bind (this)) : null);
    },
    renderTask: function (r) {
        this.$right.attr ('class', r.edit === false ? 'icon-eye' : 'icon-pencil2').unbind ('click').click (function () {
          window.open (r.edit === false ? r.href : r.edit, '_blank');
        }.bind (this)).show ();

      if (r.finish) this.$task.addClass ('finish');

      this.$task.append (
        $('<span />').text ('任務標題')).append (
        $('<div />').addClass ('row').text (r.title));
      
      $cursor = $('<span />').data ('val', r.levelv).css ({'width': r.levelu + '%'});

      this.$task.append (
        $('<span />').text ('優先權')).append (
        $('<div />').addClass ('row').append (
          $('<div />').addClass ('colors').append (r.levelc.map (function (t) {
            return $('<span />').css ({'background-color': t, 'width': r.levelu + '%'});
          })).append ($cursor)).append ($('<span />').text (r.level)));

      this.$task.append (
        $('<span />').text ('任務內容')).append (
        $('<div />').addClass ('row').append ($('<article />').html (r.content.trim ())));

      if (r.users.length)
        this.$task.append (
          $('<span />').text ('參與人員')).append (
          $('<div />').addClass ('row').addClass ('users').append (r.users.map (function (t) {
            return $('<div />').append (
              $('<img />').attr ('src', t.avatar)).append (
              $('<span />').text (t.name));
          })));

      if (r.commits.length)
        this.$task.append (
          $('<span />').text ('留言、紀錄')).append (
          $('<div />').addClass ('row').addClass ('commits').append (r.commits.map (function (t) {
            return $('<div />').append (
              $('<img />').attr ('src', t.avatar)).append (
              $('<span />').text (t.action).attr ('data-content', t.content));
          })));

      this.$task.append ( $('<a />').text ('檢視細節').click (function () { window.open (r.href); }.bind (this)));

      setTimeout (function () { $cursor.css ({left: $cursor.data ('val')}); }, 500);
      
    },
    showTask: function (id) {
      this.$task.empty ().attr ('class', 'task').addClass ('loading');
      this.$task.addClass ('s');
      this.$left.attr ('class', 'icon-keyboard_arrow_left').unbind ('click').click (function () { this.closeTask (); }.bind (this));
      this.$right.hide ();
      this.$title.text ('任務內容').attr ('data-sub', '');

      $.ajax ({
        url: sprintf (this.api.task, id),
        data: { },
        async: true, cache: false, dataType: 'json', type: 'get'
      })
      .done (this.renderTask.bind (this))
      .fail (window.fns.ajaxFail)
      .complete (function () {
        this.$content.removeClass ('loading')
      }.bind (this));
    },
    showContent: function (id) {
      this.$content.empty ().attr ('class', 'content').addClass ('loading');
      this.$content.addClass ('s');
      
      this.$left.attr ('class', 'icon-keyboard_arrow_left').unbind ('click').click (function () { this.closeContent (); }.bind (this));
      this.$right.hide ().attr ('class', 'icon-pencil2').unbind ('click').click (function () { this.showEdit (id); }.bind (this));
      this.$title.text ('行程內容').attr ('data-sub', '');
      
      $.ajax ({
        url: sprintf (this.api.content, id),
        data: { },
        async: true, cache: false, dataType: 'json', type: 'get'
      })
      .done (this.renderContent.bind (this))
      .fail (window.fns.ajaxFail)
      .complete (function () {
        this.$content.removeClass ('loading')
      }.bind (this));
    },
    closeAdd: function () {
      this.$add.attr ('class', 'add');
      this.showList (true);
      return this;
    },
    renderForm: function (r) {
      r = typeof r === 'undefined' ? false : r;

      var renderMtckbs = function (t) {
        t = typeof t === 'undefined' ? false : t;

        return $('<div />').attr ('name', 'items').append (
                  $('<label />').addClass ('checkbox').append (
                    $('<input />').attr ('type', 'checkbox').prop ('checked', t && t.status)).append (
                    $('<span />'))).append (
                  $('<input />').attr ('type', 'text').attr ('placeholder', '請輸入待辦事項..').attr ('title', '輸入標題!').val (t ? t.content : '')).append (
                  $('<a />').click (function () {
                    $(this).parent ().remove ();
                  }));
      };

      var $r0 = $('<div />').addClass ('note');

      var $r1 = $('<div />').addClass ('row').append (
                  $('<b />').addClass ('need').text ('標題')).append (
                  $('<input />').attr ('type', 'text').attr ('name', 'title').attr ('placeholder', '請輸入行程標題..').attr ('maxlength', '200').attr ('pattern', '.{1,200}').prop ('required', true).attr ('title', '輸入行程標題!').val (r ? r.title : ''));

      var $r2 = $('<div />').addClass ('row').append (
                  $('<b />').text ('待辦事項')).append (
                  $('<div />').addClass ('mtckbs').append (r ? r.items.map (renderMtckbs) : renderMtckbs ()).append (
                    $('<span />').append (
                      $('<a />').click (function () {
                        var $t = $('<input />').attr ('type', 'text').attr ('placeholder', '請輸入待辦事項..').attr ('title', '輸入標題!');
                        $('<div />').attr ('name', 'items').append (
                          $('<label />').addClass ('checkbox').append (
                            $('<input />').attr ('type', 'checkbox')).append (
                            $('<span />'))).append (
                          $t).append (
                          $('<a />').click (function () {
                            $(this).parent ().remove ();
                          })).insertBefore ($(this).parent ());
                        setTimeout (function () { $t.focus (); }, 100);
                      }))));
      
      var $r3 = this.$datas.data ('tags').length ? $('<div />').addClass ('row').append (
                  $('<b />').text ('行程分類')).append (
                  $('<div />').addClass ('rdos').append (this.$datas.data ('tags').map (function (t) {
                    return $('<label />').addClass ('radio').css ({'border-color': _color.rgbToHex (_color.hex2rgb (t.color))}).append (
                      $('<input />').attr ('type', 'radio').attr ('name', 'tag').val (t.id).prop ('checked', r && r.tag != null && r.tag.id == t.id)).append (
                      $('<span />')).append (t.name);
                  }))) : null;

      var $r4 = $('<div />').addClass ('row').append (
                  $('<b />').text ('分享對象').attr ('data-title', '他們只能看，不能修改。')).append (
                  $('<div />').addClass ('ckbs').append (this.$datas.data ('users').map (function (t) {
                    return $('<label />').addClass ('checkbox').append (
                      $('<input />').attr ('type', 'checkbox').attr ('name', 'users').val (t.id).prop ('checked', r && $.inArray (t.id, r.users.map (function (u) { return u.id; })) !== -1)).append (
                      $('<span />')).append (t.name);
                  })));

      var $r5 = $('<div />').addClass ('row').append (
                  $('<b />').text ('備註')).append (
                  $('<textarea />').addClass ('pure').attr ('name', 'memo').attr ('placeholder', '請輸入備註..').attr ('title', '輸入行程備註!').val (r ? r.memo : ''));

      return $('<form />').append ($r0)
                          .append ($r1)
                          .append ($r2)
                          .append ($r3)
                          .append ($r4)
                          .append ($r5);
    },
    formDate: function ($obj) {
      var title = $obj.find ('input[name="title"]').val ().trim ();
      if (!title.length && $obj.find ('.note').text ('未填寫標題！')) return false;

      var items = $obj.find ('.mtckbs div[name="items"]').map (function () {
        return {
          status: $(this).find ('input[type="checkbox"]').prop ('checked') ? 2 : 1,
          content: $(this).find ('input[type="text"]').val ().trim ()
        }
      }).filter (function (i, t) { return t.content.length; }).toArray ();

      var tag = $obj.find ('.rdos input[type="radio"][name="tag"]').filter (function () { return $(this).prop ('checked') && $(this).val ().length; }).map (function () { return $(this).val (); }).toArray ();
      tag = tag.length ? tag[0] : 0;
      
      var users = $obj.find ('.ckbs input[type="checkbox"][name="users"]').filter (function () { return $(this).prop ('checked') && $(this).val ().length; }).map (function () { return $(this).val (); }).toArray ();

      var memo = $obj.find ('textarea[name="memo"]').val ().trim ();
      return {
        title: title,
        schedule_tag_id: tag,
        items: items,
        user_ids: users,
        memo: memo,
      };
    },
    editSubmit: function (id) {
      window.fns.showLoading ('更新中，請稍候..');
      var data = this.formDate (this.$edit);
      if (data === false) return window.fns.closeLoading ();;

      data.date = this.y + '-' + this.m + '-' + this.d;
      data._method = 'put';

      $.ajax ({
        url: sprintf (this.api.update, id),
        data: data,
        async: true, cache: false, dataType: 'json', type: 'POST'
      })
      .done (function (r) {
        this.closeEdit (id);
        this.ajaxMonth (null, this.y, this.m, this.d);
      }.bind (this))
      .fail (window.fns.ajaxFail)
      .complete (function () { window.fns.closeLoading (); });
    },
    addSubmit: function () {
      window.fns.showLoading ('新增中，請稍候..');
      var data = this.formDate (this.$add);
      if (data === false) return window.fns.closeLoading ();;

      data.date = this.y + '-' + this.m + '-' + this.d;

      $.ajax ({
        url: this.api.create,
        data: data,
        async: true, cache: false, dataType: 'json', type: 'POST'
      })
      .done (function (r) {
        this.closeAdd ();
        this.ajaxMonth (null, this.y, this.m, this.d);
      }.bind (this))
      .fail (window.fns.ajaxFail)
      .complete (function () { window.fns.closeLoading (); });
    },
    closeEdit: function (id) {
      this.$edit.attr ('class', 'edit');
      typeof id !== 'undefined' && this.showContent (id);
      return this;
    },
    showEdit: function (id) {
      this.$edit.empty ().addClass ('loading');
      this.$edit.addClass ('s');

      $.ajax ({
        url: sprintf (this.api.content, id),
        data: { },
        async: true, cache: false, dataType: 'json', type: 'get'
      })
      .done (function (r) {
        if (r.type != 1) this.closeEdit (id);
        this.$edit.append (this.renderForm (r));
      }.bind (this))
      .fail (window.fns.ajaxFail)
      .complete (function () {
        this.$edit.removeClass ('loading')
      }.bind (this));


      this.$left.attr ('class', 'icon-keyboard_arrow_left').unbind ('click').click (function () { this.closeEdit (id); }.bind (this));
      this.$right.attr ('class', 'icon-checkmark').unbind ('click').click (function () { this.editSubmit (id); }.bind (this));
      this.$title.text ('修改行程');
    },
    showAdd: function () {
      this.$add.empty ().append (this.renderForm ());
      
      this.$add.addClass ('s');
      this.$left.attr ('class', 'icon-keyboard_arrow_left').unbind ('click').click (function () { this.closeAdd (); }.bind (this));
      this.$right.attr ('class', 'icon-checkmark').unbind ('click').click (function () { this.addSubmit (); }.bind (this));
      this.$title.text ('新增行程');
    },
    renderList: function (obj) {
      return $('<a />').attr ('data-id', obj.id).addClass (obj.finish ? 'finish' : null).addClass (typeof obj.img !== 'undefined' && obj.img.length ? 'img' : null).append (
               typeof obj.img !== 'undefined' && obj.img.length ? $('<img />').attr ('src', obj.img) : (typeof obj.icon !== 'undefined' ? $('<i />').addClass (obj.icon) : null)).append (
               $('<span />').text (obj.note)).append (
               $('<div />').text (obj.title).attr ('data-sub', obj.sub)).click (function (event) {
                 if (obj.type == 2) this.showTask (obj.id);
                 else this.showContent (obj.id);
               }.bind (this));
    },
    updateDaySort: function (s, notReload) {
      $.ajax ({
        url: this.api.daysort,
        data: {
          data: s
        },
        async: true, cache: false, dataType: 'json', type: 'post'
      })
      .done (function (r) { if (notReload) this.ajaxMonth (null, this.y, this.m, this.d) }.bind (this))
      .fail (window.fns.ajaxFail)
      .complete (function () {});
    },
    showList: function (back) {
      this.$title.text ('行程列表').attr ('data-sub', this.y + '.' + this.m + '.' + this.d);
      this.$left.attr ('class', 'icon-cross').unbind ('click').click (function () { this.close (); }.bind (this));
      this.$right.attr ('class', 'icon-r').unbind ('click').click (function () { this.showAdd (); }.bind (this));

      this.$list.empty ().addClass ('loading');

      $.ajax ({
        url: this.api.day,
        data: {
          date: this.y + '-' + this.m + '-' + this.d,
        },
        async: true, cache: false, dataType: 'json', type: 'get'
      })
      .done (function (r) {
        this.$list.append (r.map (function (t) {
          return $('<span />').text (t.text).add ($('<div />').addClass (t.sort ? 'sort' : null).append (t.objs.map (this.renderList.bind (this))))
        }.bind (this)));
      }.bind (this))
      .fail (window.fns.ajaxFail)
      .complete (function () {
        this.$list.removeClass ('loading');
      }.bind (this));

      this.$list.sortable ({
        items: 'div.sort > a',
        handle: '>img',
        cursor: 'move',
        placeholder: 'placeholder',
        update: function () {
          this.updateDaySort ($('div.sort > a').map (function (i) {
            return {id: $(this).data ('id'), sort: i + 1};
          }).toArray (), true);
        }.bind (this)
      });

      if (back === true) return true;
      this.$list.addClass ('s');
    }
  }
  _panel.init ();
  


  var _color = {
    brightness: function (c) {
      return (c.r * 0.299 + c.g * 0.587 + c.b * 0.114) / 255 * 100;
    },
    has: function (c, d, h) {
      d = typeof d == 'undefined' ? {r:0, g:0, b:0} : d;
      h = typeof h == 'undefined' ? {r:255, g:255, b:255} : h;

      var a = this.brightness (c);
      var b = this.brightness (d);
      var c = this.brightness (h);
      return Math.abs (a - c) > Math.abs (a - b);
    },
    text: function (c, d, h) {
      d = typeof d == 'undefined' ? {r:0, g:0, b:0} : d;
      h = typeof h == 'undefined' ? {r:255, g:255, b:255} : h;
      return this.has (c, d, h) ? h : d;
    },
    hex2rgb: function (str) {
      var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec (str);
      return result ? {
          r: parseInt (result[1], 16),
          g: parseInt (result[2], 16),
          b: parseInt (result[3], 16)
      } : {r: 255, g: 255, b: 255};
    },
    rgbToHex: function (c) {
      return '#' + ((1 << 24) + (c.r << 16) + (c.g << 8) + c.b).toString (16).slice (1);
    },
    textColor: function (str) {
      return this.rgbToHex (this.text (this.hex2rgb (str)));
    }
  }

  var _weeks = ['日', '一', '二', '三', '四', '五', '六'];

  var _gan = ['甲','乙','丙','丁','戊','己','庚','辛','壬','癸'];
  var _zhi = ['子','丑','寅','卯','辰','巳','午','未','申','酉','戌','亥'];
  var _animals = ['鼠','牛','虎','兔','龍','蛇','馬','羊','猴','雞','狗','豬'];
  
  function lunarYear (y) { return y - 1911; }
  function animals (y) { return _animals[(y - 4) % 12]; }
  function ganZhi (n) { n = n - 1900 + 36; return _gan[n % 10] + _zhi[n % 12]; }

  function monthDayCount (y, m) {
    m = parseInt (m, 10);
    y = parseInt (y, 10);
    
    return (--m == 1) ? ((y % 4) === 0) && ((y % 100) !== 0) || ((y % 400) === 0) ? 29 : 28 : [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][m];
  }
  function prevMonth (y, m) {
    m = isNaN (y) ? y.m : parseInt (m, 10);
    y = isNaN (y) ? y.y : parseInt (y, 10);

    return { y: m == 1 ? y - 1 : y, m: m == 1 ? 12 : (m - 1), c: monthDayCount (y, m == 1 ? 12 : (m - 1)) };
  }
  function nextMonth (y, m) {
    m = isNaN (y) ? y.m : parseInt (m, 10);
    y = isNaN (y) ? y.y : parseInt (y, 10);
    return { y: m == 12 ? y + 1 : y, m: m == 12 ? 1 : (m + 1) };
  }
  function createMonth (y, m) {
    m = isNaN (y) ? y.m : parseInt (m, 10);
    y = isNaN (y) ? y.y : parseInt (y, 10);

    var firstDayWeek = new Date(y, m - 1, 1).getDay ();
    var monthCount = monthDayCount (y, m);
    var weekCount = parseInt ((firstDayWeek + monthCount) / 7, 10) + (((firstDayWeek + monthCount) % 7) ? 1 : 0);
    var prev = prevMonth (y, m);
    var next = nextMonth (y, m);
    var time = new Date ();

    return Array.apply (null, Array (weekCount)).map (function (_, i) {
      return Array.apply (null, Array (7)).map (function (_, j) {
        var d = i * 7 + j - firstDayWeek + 1;

        var r = {
          y: d > 0 ? d > monthCount ? next.y : y : prev.y,
          m: d > 0 ? d > monthCount ? next.m : m : prev.m,
          d: (d > 0 ? d > monthCount ? -monthCount : 0 : prev.c) + d,
        };
        
        var lun = window.LunarCalendar.solarToLunar (r.y, r.m, r.d);
        
        
        return {
          y: r.y,
          m: r.m,
          d: r.d,
          t: r.y == time.getFullYear () && r.m == time.getMonth () + 1 && r.d == time.getDate (),
          l: lun.lunarDay == 1 ? lun.lunarMonthName : lun.lunarDayName,
          nl: lun.lunarDay == 1,
          h: lun.worktime,
          f: (lun.term ? [lun.term] : []).concat (lun.solarFestival.concat (lun.lunarFestival))
        };
      });
    });
  }

  function initMonth (y, m) {
    m = isNaN (y) ? y.m : parseInt (m, 10);
    y = isNaN (y) ? y.y : parseInt (y, 10);

    var $month = $('<div />').addClass ('month').append (
      $('<div />').addClass ('weeks').append (
        _weeks.map (function (i) {
          return $('<div />').text (i);
        }))).append (
      createMonth (y, m).map (function (i) {
        return $('<div />').addClass ('days').append (i.map (function (j) {
          return $('<div />').addClass (!(j.y == y && j.m == m) ? 'not-this-month' : null)
                             .addClass (j.t ? 'today' : null)
                             .addClass (j.nl ? 'new-lunar' : null)
                             .addClass (j.h ? 'holiday' : null)
                             .attr ('data-y', j.y)
                             .attr ('data-m', j.m)
                             .attr ('data-d', j.d)
                             .attr ('data-l', j.l)
                             .append (
                  j.f.length ? $('<span />').text (j.f.join (', ')) : null).click (function () {
                    _panel.show (j.y, j.m, j.d);
                  });
        }));
      }));

    return $month;
  }

  function resetMonth ($obj, y, m) {
    m = isNaN (y) ? y.m : parseInt (m, 10);
    y = isNaN (y) ? y.y : parseInt (y, 10);

    var prev = prevMonth (y, m),
        next = nextMonth (y, m);
    
    return $obj.empty ()
               .append (initMonth (prev))
               .append (initMonth (y, m))
               .append (initMonth (next));
  }

  window.LunarCalendar.setSolarFestival ({'d0907': [['泰瑞生日'], false]});
  window.LunarCalendar.setLunarFestival ({'d0812': [['泰瑞生日'], false]});

  $('.calendar').each (function () {
    var $that = $(this),
        time = new Date (),
        url = $that.data ('url');
        
    var $now = $that.find ('.now')
                    .attr ('data-y', time.getFullYear ())
                    .attr ('data-m', time.getMonth () + 1)
                    .attr ('data-l', lunarYear (time.getFullYear ()))
                    .attr ('data-gz', ganZhi (time.getFullYear ()))
                    .attr ('data-a', animals (time.getFullYear ()));


    var $months = resetMonth ($that.find ('.months'), $now.data ('y'), $now.data ('m'));
    _panel.ajaxMonth ($months, $now.data ('y'), $now.data ('m'));

    $that.find ('.arr a')
         .click (function () {
            var o = {};

            if ($(this).index () === 0) {
              var time = new Date ();
              o = {y: time.getFullYear (), m: time.getMonth () + 1};
              resetMonth ($months, o.y, o.m);
              _panel.ajaxMonth ($months, o);

            } else if ($(this).index () == 1) {
              o = prevMonth ($now.attr ('data-y'), $now.attr ('data-m'));

              $months.prepend (initMonth (prevMonth (o)));
              _panel.ajaxMonth ($months, o);
              $months.find ('.month').last ().remove ();
            } else if ($(this).index () == 2) {
              o = nextMonth ($now.attr ('data-y'), $now.attr ('data-m'));
              $months.append (initMonth (nextMonth (o)));
              _panel.ajaxMonth ($months, o);
              $months.find ('.month').first ().remove ();
            }

            $now.attr ('data-y', o.y)
                .attr ('data-m', o.m)
                .attr ('data-l', lunarYear (o.y))
                .attr ('data-gz', ganZhi (o.y))
                .attr ('data-a', animals (o.y));
         });

    if (_panel.$datas.data ('date').length) {
      var u = _panel.$datas.data ('date').split ('-');
      if (u.length == 3)
        setTimeout (function () {
          $that.find ('.days > div[data-y="' + parseInt (u[0], 10) + '"][data-m="' + parseInt (u[1], 10) + '"][data-d="' + parseInt (u[2], 10) + '"]').click ();
            if (_panel.$datas.data ('id'))
              setTimeout (function () {
                _panel.$list.find ('>div a[data-id="' + _panel.$datas.data ('id') + '"]').click ();
              }, 300);
        }, 500);
    }
  });

  $('.calendar .days > div').sortable ({
    items: 'div.type1',
    connectWith: '.days > div',
    update: function (e, ui) {
      var y = $(this).data ('y');
      var m = $(this).data ('m');
      var d = $(this).data ('d');

      _panel.updateDaySort ($(this).find ('div.type1').map (function (i) {
          return {id: $(this).data ('id'), sort: i + 1, date: y + '-' + m + '-' + d};
        }).toArray (), false);
    }
  });

});