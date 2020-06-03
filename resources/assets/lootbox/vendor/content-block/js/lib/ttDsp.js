'use strict';

var lib = require('../lib/index'),
    BaseClass      = require('../lib/BaseClass');

module.exports = BaseClass.extend({
  __className : 'ttDsp',
  __put : true,
  pre : function(opt)
  {
    this.url = false;
    this.busy = false;
    this.dsp = 0;
  },
  create : function()
  {
    var self = this,
        dsp = parseInt(this.element.getAttribute('dsp')) ? 1 : 0;
    
    this.url = this.element.getAttribute('url');
    this.setDsp(dsp);

    lib.bindEvent(this.element,'click',function(){ self.toggleDsp(); });
  },
  toggleDsp : function()
  {
    if( this.busy || !this.url ) return;
    this.busy = true;

    var self = this,
        dsp = !this.dsp ? 1 : 0;

    lib.ajaxPost(this.url,{ dsp : dsp, _token: this._token() },function(response){
      var data = JSON.parse(response);
      if(data.success){
        self.setDsp(data.dsp);
      }else{
        alert(data.error);
      }
      self.busy = false;
    });
  },
  setDsp : function(dsp)
  {
    lib.removeClass(this.element,'fa-eye');
    lib.removeClass(this.element,'fa-eye-slash');
    lib.addClass(this.element, 'fa-eye' + ( dsp ? '' : '-slash' ) );
    this.dsp = dsp;
  }
});