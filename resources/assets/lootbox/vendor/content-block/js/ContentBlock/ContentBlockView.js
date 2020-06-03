'use strict';

var lib = require('../lib/index'),
    BaseClass = require('../lib/BaseClass');

module.exports = BaseClass.extend({
  __className : 'ContentBlockView',
  __put : true,
  pre : function(opt)
  {
    this.mode = 0;
  },
  create : function()
  {
    var self = this, elm;

    this.width = 0;
    this.height = 0;

    this.process();
  },
  process : function()
  {
    switch(this.mode) {
      case 2:
        this.img = this.element.querySelector('.img');
        if(this.img)
        {
          this.defW = parseInt( this.img.getAttribute('data-width') || 0 );
          this.defH = parseInt( this.img.getAttribute('data-height') || 0 );
        }
        break;
      case 4:
        var self = this, elm;

        this.width = 0;
        this.height = 0;

        this.iframe = this.element.querySelector('iframe.videoPlayer');
        if(this.iframe)
        {
          this.defW = parseInt( this.iframe.getAttribute('data-width') || 0 );
          this.defH = parseInt( this.iframe.getAttribute('data-height') || 0 );
        }
        break;
    }
  },
  resize : function(ww,wh)
  {
    switch(this.mode)
    {
      case 2:
        if(this.defW && this.defH)
        {
          this.width = ww;
          this.height = Math.round(ww / this.defW * this.defH);
          lib.css(this.img,{ width : this.width, height : this.height });
        }
        break;
      case 4:
        if(this.defW && this.defH)
        {
          this.width = ww;
          console.log(this.defW + ' x ' + this.defH);
          this.height = Math.round(ww / this.defW * this.defH);
          lib.css(this.iframe,{ width : this.width, height : this.height });
          lib.css(this.element,{ width : this.width, height : this.height });
        }
        break;
    }
  },
  remove : function()
  {

  }
});