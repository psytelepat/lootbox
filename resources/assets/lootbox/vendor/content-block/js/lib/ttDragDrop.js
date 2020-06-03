'use strict';

var lib = require('./index'),
    BaseClass = require('./BaseClass');

window.clickBlocked = false;
window.clickBlocker = function(state){
  if(typeof state == 'undefined'){
    return window.clickBlocked;
  }else{
    window.clickBlocked = state;
  }
}

module.exports = BaseClass.extend({
  __className : 'ttDragDrop',
  __put : true,
  pre : function(opt) {
    this.element = false;
    this.ghost = false;

    this.power = false;

    this.item = [];
    this.itemInfo = [];
    this.movingItem = false;
    this.movingItemData = {};

    this.animationSpeed = 150;
  },
  create : function() {

    var self = this;

    this.opt.id = 1;
    this.opt.find = this.opt.find ? this.opt.find : '.item';
    this.opt.dataAttr = this.opt.dataAttr ? this.opt.dataAttr : 'data-id';
    this.opt.noAutoPower = this.opt.noAutoPower ? this.opt.noAutoPower : false;
    if(this.opt.noAutoPower)
      return false;

    if(!this.element){
      return;
    }

    this.opt.url = this.opt.url ? this.opt.url : this.element.getAttribute('url');
    this.opt.defaultPosition = this.opt.defaultPosition || this.element.getAttribute('defaultPosition') || 'absolute';
    lib.removeClass(this.element,'x');

    this.item = lib.querySelectorAll(this.opt.find,this.element);
    if(this.item.length < 1) return;
    this.gatherGridData();

    for(var i=0;i<this.item.length;i++)
    {
      this.item[i].setAttribute('dragDrop',this.opt.id);
      this.bindMouseDown(this.item[i],this.item[i]);
    }

    this.element.setAttribute('dragDrop',this.opt.id);

    this.updateDraggingFunc = function(e){ self.updateDragging(e); };
    this.finishDraggingFunc = function(e){ self.finishDragging(e); };

    this.power = true;
    return ( this._inited = true );
  },
  kill : function() {
    this.power = false;
    this.finishDragging();
  },
  bindMouseDown : function(item,itemToMove){
    var self = this,
        dd_control = item.querySelector('.ttdd');
    
    lib.unbindEvent(item,'mousedown');
    if(dd_control){item = dd_control;}

    lib.bindEvent(item,'mousedown',function(e){
      if(self.movingObject){return;}
      if(itemToMove.getAttribute('dragDrop') != self.opt.id){
        return;
      }
      e.preventDefault();
      self.initMovingObject(itemToMove,e);
    });
  },
  gatherGridData : function(){
    if(!this.opt.cellWidth){
      this.opt.cellWidth = parseInt(this.element.getAttribute('cellWidth'));
      if(!this.opt.cellWidth) this.opt.cellWidth = this.item[0].getBoundingClientRect().width;
    }
    if(!this.opt.cellHeight){
      this.opt.cellHeight = parseInt(this.element.getAttribute('cellHeight'));
      if(!this.opt.cellHeight) this.opt.cellHeight = this.item[0].getBoundingClientRect().height;
    }
    if(!this.opt.padWidth){
      this.opt.padWidth = parseInt(this.element.getAttribute('padWidth'));
      if(!this.opt.padWidth) this.opt.padWidth=10;
    }
    if(!this.opt.padHeight){
      this.opt.padHeight = parseInt(this.element.getAttribute('padHeight'));
      if(!this.opt.padHeight) this.opt.padHeight=10;
    }
    if(!this.opt.cellInLine){
      this.opt.cellInLine = parseInt(this.element.getAttribute('cellInLine'));
      if(!this.opt.cellInLine) console.log('Error: unable to calculate <cellInLine>');
    }
  },
  gatherItemInfo : function(){
    var itemInfo = [],
        containerPos = this.element.getBoundingClientRect();
    
    // collect items information
    for(var i=0;i<this.item.length;i++){
      var pos = this.item[i].getBoundingClientRect();
      itemInfo.push({
        pos : 0,
        position : this.item[i].style.position,
        float : this.item[i].style.float,
        obj : this.item[i],
        width : this.item[i].getBoundingClientRect().width,
        height : this.item[i].getBoundingClientRect().height,
        outerWidth : this.item[i].getBoundingClientRect().width,
        outerHeight : this.item[i].getBoundingClientRect().height,
        top : pos.top - containerPos.top,
        left : pos.left - containerPos.left
      });
    }

    // sort items by position
    for(var i=0;i<itemInfo.length;i++){
      for(var j=0;j<itemInfo.length;j++){
        if(((itemInfo[i].left < itemInfo[j].left) && (itemInfo[i].top <= itemInfo[j].top)) || (itemInfo[i].top < itemInfo[j].top)){
          var tmp = itemInfo[j];
          itemInfo[j] = itemInfo[i];
          itemInfo[i] = tmp;
        }
      }
    }

    return(itemInfo);
  },
  initMovingObject : function(obj,e){
    if(this.movingItem || !this.power){return;}

    if(typeof obj == 'string'){obj = document.getElementById(obj);}

    if(!obj){return;}
    window.clickBlocker(true);

    this.movingItem = obj;

    lib.css(this.element,{
      width : this.element.getBoundingClientRect().width,
      height : this.element.getBoundingClientRect().height
    });

    this.gatherGridData();
    var itemInfo = this.gatherItemInfo();

    for(var i=0;i<itemInfo.length;i++){
      itemInfo[i].obj.setAttribute('pos',i);
      lib.css(itemInfo[i].obj,{
        position : "absolute",
        float : "none",
        top : itemInfo[i].top,
        left : itemInfo[i].left,
        width : itemInfo[i].width,
        height : itemInfo[i].height
      });
    }

    var doc = document.documentElement,
        scrollTop = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0),
        cntOffset = this.element.getBoundingClientRect();

    this.itemInfo = itemInfo;
    this.movingItemData = {
      obj          : this.movingItem,
      mov_ndx      : parseInt(this.movingItem.getAttribute('pos')),
      cntTop       : scrollTop + cntOffset.top,
      cntLeft      : cntOffset.left, 
      px           : e.pageX, // starting pageX
      py           : e.pageY, // starting pageY
      sx           : parseInt(this.movingItem.style.left), // start x position
      sy           : parseInt(this.movingItem.style.top), // start y position
      sw           : this.movingItem.getBoundingClientRect().width, // start width
      sh           : this.movingItem.getBoundingClientRect().height, // start height
      cx           : 0, // current x position
      cy           : 0, // current y position
      cpx          : e.pageX, // current pageX
      cpy          : e.pageY, // current pageY
      xch          : 0,
      ych          : 0,
      dummyMouseUp : 0,
      collision    : false
    };

    lib.css(this.movingItem,{ opacity : 0.8 });

    this.movingItemData.ghost = this.createGhost(this.movingItemData.sw,this.movingItemData.sh + 50);
    lib.insertAfter(this.movingItemData.ghost,this.movingItem);

    var self = this;
    lib.bindEvent(window, 'mousemove', this.updateDraggingFunc);
    lib.bindEvent(window, 'mouseup', this.finishDraggingFunc);

    switch(this.opt.moveMode){
      case 'predictor':
        this.movingItemData.dummyMouseUp = 1;
        break;
      default:
        break;
    }

    lib.css(this.movingItem,{
      width : this.movingItemData.sw,
      height : this.movingItemData.sh,
      zIndex : 1000
    });

    document.onselectstart = function(){return(false);}
    return(true);
  },
  updateDragging : function(e) {

    if(!this.movingItem){
      lib.unbindEvent(window,'mousemove',this.updateDraggingFunc);
      lib.unbindEvent(window,'mouseup',this.finishDraggingFunc);
      return;
    }

    var pos = {
      x : this.movingItemData.sx + (e.pageX - this.movingItemData.px),
      y : this.movingItemData.sy + (e.pageY - this.movingItemData.py)
    };

    this.movingItemData.xch += Math.abs(e.pageX - this.movingItemData.px);
    this.movingItemData.ych += Math.abs(e.pageY - this.movingItemData.py);

    switch(this.opt.moveMode){
      case 'predictor':
        break;
      default:
        lib.css(this.movingItem,{ left : pos.x, top : pos.y });
        break;
    }

    this.movingItem.cx = pos.x;
    this.movingItem.cy = pos.y;

    this.movingItemData.cpx = e.pageX;
    this.movingItemData.cpy = e.pageY;

    this.collisionCheck(e);
  },
  collisionCheck : function(e){

    var collision;

    for(var i=0;i<this.itemInfo.length;i++){
      if(this.itemInfo[i].obj != this.movingItem){
        if(collision = this.testForCollision(this.itemInfo[i])){
          break;
        }
      }
    }

    var sameCollision = collision && (collision.obj == this.movingItemData.collision.obj);

    if(this.movingItemData.collision && !sameCollision){
      this.unsetColliding(this.movingItemData.collision);
      this.movingItemData.collision = false;
    }

    if(collision){
      this.movingItemData.collision = collision;
      this.setColliding(this.movingItemData.collision,sameCollision);
    }

    return(collision);
  },
  testForCollision : function(itemInfo){
    var doc = document.documentElement,
        scrollTop = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);

    var top  = this.movingItemData.cntTop + itemInfo.top;
    var left = this.movingItemData.cntLeft + itemInfo.left;
    var ih = itemInfo.outerHeight;
    var iw = itemInfo.outerWidth;

    if(this.movingItemData.cpy > top && this.movingItemData.cpy < top + ih + this.opt.padHeight){
      if(this.movingItemData.cpx > left && this.movingItemData.cpx < left + iw + this.opt.padWidth){
        
        var collision = {
          obj : itemInfo.obj,
          mov_ndx : this.movingItemData.mov_ndx,
          col_ndx : parseInt(itemInfo.obj.getAttribute('pos')),
          collideTop : (this.movingItemData.cpy - top < ih/2),
          collideLeft : (this.movingItemData.cpx - left < iw/2)
        };

        if(this.opt.moveMode == 'predictor'){
          if( (collision.collideTop && (collision.col_ndx == collision.mov_ndx+1)) || (!collision.collideTop && (collision.col_ndx == collision.mov_ndx-1)) ){
            return(false);
          }
        }

        return( this.calcRealCollisionIndex(collision) );
      }
    }
    return(false);
  },
  calcRealCollisionIndex : function(collision){
    if(this.opt.moveMode == 'predictor'){
      if((collision.mov_ndx < collision.col_ndx) && collision.collideTop){
        if(collision.col_ndx - 1 > 0){
          collision.col_ndx--;
          collision.collideTop = false;
          collision.obj = this.itemInfo[collision.col_ndx].obj;
        }
      }
      if((collision.mov_ndx > collision.col_ndx) && !collision.collideTop){
        if(collision.col_ndx + 1 < this.itemInfo.length){
          collision.col_ndx++;
          collision.collideTop = true;
          collision.obj = this.itemInfo[collision.col_ndx].obj;
        }
      }
    }
    return(collision);
  },
  setColliding : function(collision,sameCollision){
    switch(this.opt.moveMode){
      case 'predictor':
        
        if(!this.movingItemData.predictor){
          this.movingItemData.predictor = document.createElement('DIV');
          lib.css(this.movingItemData.predictor,{
            position : "absolute",
            top : 0,
            left : 0,
            height : 1,
            width : 1,
            display : "none",
            backgroundColor : this.opt.predictorColor ? this.opt.predictorColor : "#FF0000",
            zIndex : 5000
          });
          this.element.appendChild(this.movingItemData.predictor);
        }

        var predictorTop = parseInt(collision.obj.style.top) + ( collision.collideTop ? -5 : collision.obj.getBoundingClientRect().height + 5 );
        var predictorLeft = parseInt(collision.obj.style.left);

        lib.css(this.movingItemData.predictor,{
          top : predictorTop,
          left : predictorLeft,
          width : collision.obj.getBoundingClientRect().width,
          height : 1,
          display : "block"
        });

        break;
      default:
        if(!sameCollision) this.moveItemsByCollision();
        break;
    }
  },
  moveItemsByCollision : function(){
    switch(this.opt.defaultPosition){
      case 'relative':
        this.moveItemsByCollision_Relative();
        break;
      default:
        this.moveItemsByCollision_Default();
        break;
    }
  },
  moveItemsByCollision_Relative : function(){
    var top = 0;

    var mov_ndx = this.movingItemData.mov_ndx;
    var col_ndx = this.movingItemData.collision.col_ndx;

    if(mov_ndx > col_ndx){
      top = this.itemInfo[col_ndx].top + this.movingItem.getBoundingClientRect().height;
      for(var i=col_ndx;i<mov_ndx;i++){
        lib.css(this.itemInfo[i].obj,{
          top : top,
          left : this.itemInfo[i+1].left
        });
        top += this.itemInfo[i].obj.getBoundingClientRect().height;
      }
    }else{
      top = this.itemInfo[mov_ndx].top;
      for(var i=(mov_ndx+1);i<=col_ndx;i++){
        lib.css(this.itemInfo[i].obj,{
          top : top,
          left : this.itemInfo[i-1].left
        });
        top += this.itemInfo[i].obj.getBoundingClientRect().height;
      }
    }
  },
  moveItemsByCollision_Default : function(){

    var mov_ndx = this.movingItemData.mov_ndx;
    var col_ndx = this.movingItemData.collision.col_ndx;

    if(mov_ndx > col_ndx){
      for(var i=col_ndx;i<mov_ndx;i++){
        lib.css(this.itemInfo[i].obj,{
          top : this.itemInfo[i+1].top,
          left : this.itemInfo[i+1].left
        });
      }
    }else{
      for(var i=(mov_ndx+1);i<=col_ndx;i++){
        lib.css(this.itemInfo[i].obj,{
          top : this.itemInfo[i-1].top,
          left : this.itemInfo[i-1].left
        });
      }
    }
  },
  unsetColliding : function(collision){
    switch(this.opt.moveMode){
      case 'predictor':
        if(this.movingItemData.predictor){
          lib.css(this.movingItemData.predictor,{
            display : "none"
          });
        }
        break;
      default:
        this.returnItemsFromCollision();
        break;
    }
  },
  returnItemsFromCollision : function(){
    for(var i=0;i<this.itemInfo.length;i++)
      if(this.itemInfo[i].obj != this.movingItem)
        lib.css(this.itemInfo[i].obj,{
          top : this.itemInfo[i].top,
          left : this.itemInfo[i].left
        });
  },
  createGhost : function(w,h,ghostClassName){
    if(ghostClassName === null){ghostClassName='ghost';}

    if(w && h){
      var ghost = document.createElement('DIV');
      ghost.className = ghostClassName;
      ghost.style.width = w + 'px';
      ghost.style.height = h + 'px';

      return ghost;
    }
    return false;
  },
  finishDragging : function(e){
    if(this.movingItem){

      switch(this.opt.moveMode){
        case 'predictor':
          if(this.movingItemData.collision){this.moveItemsByCollision();}
          else if(this.movingItemData.dummyMouseUp > 0){this.movingItemData.dummyMouseUp--;return;}
          this.unsetColliding();
          break;
        default:
          break;
      }

      document.onselectstart = null;
      lib.unbindEvent(window,'mousemove',this.updateDraggingFunc);
      lib.unbindEvent(window,'mouseup',this.finishDraggingFunc);

      lib.css(this.movingItem,{ opacity : 1 });

      if(this.movingItemData.ghost) {
        this.movingItemData.ghost.parentNode.removeChild(this.movingItemData.ghost);
      }

      if(this.movingItemData.collision){
        if(typeof this.opt.reposFunc == 'function'){
          this.opt.reposFunc(this);
        }else{
          this.reposFunc(this);
        }
      }else{
        var self = this;
        this.returnItemsFromCollision();

        if(this.movingItemData.xch < 5 && this.movingItemData.ych < 5){
          if(typeof this.opt.click == 'function'){
            this.opt.click(this.movingItem);
          }
        }

        lib.css(this.movingItem,{
          position : 'absolute',
          left : this.movingItemData.sx,
          top : this.movingItemData.sy,
          zIndex : 100
        });

        this.finishRepositioning();
      }
    }
  },
  finishRepositioning : function(){

    if(this.opt.defaultPosition == 'relative'){
      var itemInfo = this.gatherItemInfo();
      this.element.innerHTML = '';
      for(var i=0;i<itemInfo.length;i++){
        itemInfo[i].obj.style.position = 'relative';
        itemInfo[i].obj.style.top = 'auto';
        itemInfo[i].obj.style.left = 'auto';
        itemInfo[i].obj.style.height = 'auto';
        this.element.appendChild(itemInfo[i].obj);
        this.bindMouseDown(itemInfo[i].obj,itemInfo[i].obj);
      }
      
      this.element.style.height = 'auto';

      var len = itemInfo.length;
      if(len > 1){
        for(var i=0;i<len;i++){
          lib.removeClass(itemInfo[i].obj,'first');
          lib.removeClass(itemInfo[i].obj,'middle');
          lib.removeClass(itemInfo[i].obj,'last');

          if( i == 0 ){ lib.addClass(itemInfo[i].obj,'first'); }
          else if( i == len-1 ){ lib.addClass(itemInfo[i].obj,'last'); }
          else{ lib.addClass(itemInfo[i].obj,'middle'); }
        }
      }
    }

    if(window.clickBlocker)
      window.clickBlocker(false);

    this.movingItemData = null;
    this.movingItem = null;
    this.itemInfo = null;

    this.element.style.opacity = 1;
  },
  reposFunc : function(self){

    var collision = this.movingItemData.collision,
        from = self.movingItem.getAttribute(self.opt.dataAttr),
        to = collision.obj.getAttribute(self.opt.dataAttr),
        mov_ndx = this.movingItemData.collision.mov_ndx,
        col_ndx = this.movingItemData.collision.col_ndx,
        tmpLeft = self.itemInfo[col_ndx].left,
        tmpTop = self.itemInfo[col_ndx].top;

    self.itemInfo[col_ndx].left = self.itemInfo[mov_ndx].left;
    self.itemInfo[col_ndx].top = self.itemInfo[mov_ndx].top;

    if( ( mov_ndx < col_ndx ) && ( this.opt.defaultPosition == 'relative' ) ) {
      tmpTop += self.itemInfo[col_ndx].obj.getBoundingClientRect().height - self.itemInfo[mov_ndx].obj.getBoundingClientRect().height;
    }

    self.itemInfo[mov_ndx].left = tmpLeft;
    self.itemInfo[mov_ndx].top = tmpTop;

    self.movingItem.style.position = 'absolute';

    lib.css(self.movingItem,{
      left : tmpLeft,
      top : tmpTop,
      zIndex : 100
    });

    if(self.opt.url){

      // self.element.opacity = .8;

      var url = self.opt.url + '/repos/' + from + '/' + to,
          repos_data = { from : from, to : to, _token: this._token() };

      lib.ajaxPost(url, repos_data, function(response){

        if(window.console)
          window.console.log(response);

        var res = JSON.parse(response);
        if(res.success) {

          if(typeof self.opt.reposCompleteFunc == 'function'){
            self.opt.reposCompleteFunc(res);
          }else{
            self.reposCompleteFunc(res);
          }
          self.finishRepositioning();
        }else{
          self.finishRepositioning();
        }

      });
    }
  },
  reposCompleteFunc : function(self, data, msg, xhr) {
    //alert('OKAY');
  }
});