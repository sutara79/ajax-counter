/**
 * @file jQuery Plugin: jquery.ajax-counter
 * @version 1.0.0
 * @author Yuusaku Miyazaki <toumin.m7@gmail.com>
 * @license MIT License
 *
 * @original-author Shun Kawahara https://github.com/shun91/ajax-counter
 */
(function ($) {

/**
 * @desc プラグインをjQueryのプロトタイプに追加する
 * @global
 * @memberof jQuery
 * @param {Object} [option] オプションを格納した連想配列
 * @param {string} [option.total='.count-total'] - 総計を挿入するHTML要素のセレクタ
 * @param {string} [option.today='.count-today'] - 今日の訪問者数を挿入するHTML要素のセレクタ
 * @param {string} [option.yesterday='.count-yesterday'] - 昨日の訪問者数を挿入するHTML要素のセレクタ
 */
$.fn.ajaxCounter = function (option) {
  return this.each(function () {
    new AjaxCounter(this, option);
  });
};

/**
 * @global
 * @constructor
 * @classdesc 要素ごとに適用される処理を集めたクラス
 * @param {Object} elem - プラグインを適用するHTML要素
 * @param {Object} option - オプションを格納した連想配列
 */
function AjaxCounter (elem, option) {
  this.elem = elem;
  this.option = option;

  this.setOption();
  this.getCount();
}

$.extend(AjaxCounter.prototype, /** @lends AjaxCounter.prototype */ {
  /**
   * @private
   * @desc オプションの初期化
   */
  setOption: function() {
    this.option =  $.extend({
      total: ".count-total",
      today: ".count-today",
      yesterday: ".count-yesterday"
    }, this.option);
  },

  /**
   * @private
   * @desc カウントする
   */
  getCount: function () {
    var self = this;
    $.ajax({
      url: 'jquery.ajax-counter.php',
      cache: false,
      dataType: 'json',
      success: function(res) {
        $(self.elem).filter(self.option.total).text(res.total);
        $(self.elem).filter(self.option.today).text(res.today);
        $(self.elem).filter(self.option.yesterday).text(res.yesterday);
      }
    });
  }
}); // end of "$.extend"

})( /** namespace */ jQuery);