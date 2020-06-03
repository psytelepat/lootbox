'use strict';

var lib = require('../lib/index'),
    BaseClass = require('../lib/BaseClass'),
    contentBlockView = require('./ContentBlockView');

module.exports = BaseClass.extend({
  __className : 'ContentBlockViewer',
  __put : true,
  pre : function(opt)
  {
    this.item = [];
  },
  create : function()
  {
    var self = this,
        elm  = lib.querySelectorAll('.cntBlk', this.element);
    
    if(elm)
    {
      for(var i=0,count=elm.length;i<count;i++)
      {
        var obj = elm[i],
            mode = parseInt( obj.getAttribute('mode') ),
            item = new contentBlockView({ _element : obj, _delegate : this, _mode : mode });
        this.item.push(item);
      }
    }

    lib.bindEvent(window,'resize',function(e){ self.resize(); });
    this.resize();
  },
  resize : function()
  {
    var rect = this.element.getBoundingClientRect();
    for(var i=0,count=this.item.length;i<count;i++)
      this.item[i].resize(rect.width,window.innerHeight);
  },
  remove : function()
  {
    for(var i=this.item.length-1;i>=0;i--)
      this.item[i].remove();
    // this.element.parentNode.removeChild( this.element );
  }
});