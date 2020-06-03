'use strict';

var lib = require('../lib/index'),
    BaseClass = require('../lib/BaseClass'),
    contentBlockForm = require('./ContentBlockForm');

module.exports = BaseClass.extend({
  __className : 'ContentBlockController',
  __put : true,
  pre : function(opt)
  {
    this.config = null;
    this.opt = {
      trg : 0,
      usr : 0,
      tools : true
    };
  },
  create : function()
  {
    if(!this.element)
      this.element = document.body;

    var self = this;

    this.opt.url = this.element.getAttribute('url');
    this.opt.trg = parseInt(this.element.getAttribute('trg'));
    this.opt.usr = parseInt(this.element.getAttribute('usr'));
    this.opt.url += '/' + this.opt.trg + '/' + this.opt.usr;

    this.config = window['content_block_config_'+this.opt.trg+'_'+this.opt.usr];

    this.editingBlock = false;

    this.block = [];
    this.collector();

    this.addButtons = lib.create('addButtons', this.element);

    for(var block_mode_id in this.config.block_modes){
      var block_mode = this.config.block_modes[block_mode_id];
      (function(id,name,icon){
        var btn = lib.create('btn btn-primary btn-xs addBlock',self.addButtons,'div',{ title : name });
        lib.create('fa '+icon,btn,'i');
        lib.create(false,btn,'span',false,false,'&nbsp;'+name);
        lib.bindEvent(btn,'click', function(e){ self.addBlock(id); });
      })(block_mode_id,block_mode.name,block_mode.icon);
    }

    // this.addPhoto = lib.create('btn btn-primary btn-xs addBlock',this.addButtons,'div',{ title : "добавить фото" });
    // lib.create('fa fa-camera',this.addPhoto,'i');
    // lib.create(false,this.addPhoto,'span',false,false,'&nbsp;фото');
    // lib.bindEvent(this.addPhoto,'click', function(e){ self.addBlock('photo'); });

    // this.addGallery = lib.create('btn btn-primary btn-xs addBlock',this.addButtons,'div',{ title : "добавить галерею" });
    // lib.create('fa fa-th',this.addGallery,'i');
    // lib.create(false,this.addGallery,'span',false,false,'&nbsp;галерея');
    // lib.bindEvent(this.addGallery,'click', function(e){ self.addBlock('gallery'); });

    // this.addVideo = lib.create('btn btn-primary btn-xs addBlock',this.addButtons,'div',{ title : "добавить видео" });
    // lib.create('fa fa-camera',this.addVideo,'i');
    // lib.create(false,this.addVideo,'span',false,false,'&nbsp;видео');
    // lib.bindEvent(this.addVideo,'click', function(e){ self.addBlock('video'); });

    // this.addQuote = lib.create('btn btn-primary btn-xs addBlock',this.addButtons,'div',{ title : "добавить цитату" });
    // lib.create('fa fa-align-left',this.addQuote,'i');
    // lib.create(false,this.addQuote,'span',false,false,'&nbsp;цитата');
    // lib.bindEvent(this.addQuote,'click', function(e){ self.addBlock('quote'); });

    // this.addSubscription = lib.create('btn btn-primary btn-xs addBlock',this.addButtons,'div',{ title : "добавить форму подписки" });
    // lib.create('fa fa-rss-square',this.addSubscription,'i');
    // lib.create(false,this.addSubscription,'span',false,false,'&nbsp;форма подписки');
    // lib.bindEvent(this.addSubscription,'click', function(e){ self.addBlock('subscription'); });

    // this.addDoubleColumn = lib.create('btn btn-primary btn-xs addBlock',this.addButtons,'div',{ title : "добавить блок &laquo;до и после&raquo;" });
    // lib.create('fa fa-columns',this.addDoubleColumn,'i');
    // lib.create(false,this.addDoubleColumn,'span',false,false,'&nbsp;до и после');
    // lib.bindEvent(this.addDoubleColumn,'click', function(e){ self.addBlock('double-column'); });

    this.reposing = false;
    this.reposingBlock = false;
    this.reposingBlockID = false;
    this.reposMarker = false;

    if(!this.reposMarker)
    {
      this.reposMarker = lib.create('reposMarker',this.element,'div',false,{ opacity : 0, zIndex : 10000 });
    }
  },
  setEditingBlock : function(block)
  {
    if(this.editingBlock == block) return;
    if(this.editingBlock) this.editingBlock.view();
    this.editingBlock = block;
  },
  unsetEditingBlock : function(block)
  {
    if(this.editingBlock == block) this.editingBlock = false;
  },
  collector : function()
  {
    var elm = lib.querySelectorAll('.cntBlk',this.element);
    for(var i=0,count=elm.length;i<count;i++) this.processElement(elm[i]);
  },
  processElement : function(elm)
  {
    var mode = elm.getAttribute('mode'), id = this.block.length;
    this.block.push( new contentBlockForm({ _id : id, _delegate : this, element : elm, mode : mode }) );
  },
  addBlock : function(mode)
  {
    var self = this;
    if(!mode) mode = parseInt(prompt('Режим:'));

    if(mode)
    {
      lib.ajaxGet( this.opt.url + '/create/' + mode, { trg: this.opt.trg, usr: this.opt.usr, mode: mode }, function(response){
        var data = JSON.parse(response);
        // console.log(data);
        if(data.success){

          var elm = lib.htmlToElement( data.html );

          if(self.addButtons) self.element.insertBefore(elm, self.addButtons);
          else self.element.appendChild(elm);

          self.processElement(elm);

        }else{
          self.log(resp.error);
        }
      });
    }
  },
  __startRepos : function(block)
  {
    this.reposing = true;
    this.reposingBlock = block;
    this.reposingBlockID = block.id;

    for(var i=0;i<this.block.length;i++)
    {
      if(this.block[i].id == block.id)
        continue;

      lib.css(this.block[i].element,{ opacity : 0.50 });
    }
  },
  __setReposMarkerTo : function(block,after)
  {
    var rect = this.element.getBoundingClientRect(),
        blockRect = block.element.getBoundingClientRect(),
        scrollTop = lib.scrollTop();
    var markerTop = (blockRect.top - rect.top) + (after?blockRect.height:0);
    lib.css(this.reposMarker,{ opacity : 1, top : markerTop });
  },
  __swapBlocks : function(block1,block2)
  {
    this.block[block1.id] = block2;
    this.block[block2.id] = block1;

    var tmp = block2.id;
    block2.id = block1.id;
    block1.id = tmp;
  },
  __reposBlockTo : function(block,after)
  {
    if((after&&((block.id+1)==this.reposingBlockID))||(!after&&((block.id-1)==this.reposingBlockID)))
    {
      this.__stopRepos();
      return false;
    }

    if(after) lib.insertAfter( this.reposingBlock.element, block.element );
    else this.element.insertBefore( this.reposingBlock.element, block.element );

    this.__swapBlocks(this.reposingBlock,block);
    block.updateEvents();

    var self = this;
    lib.ajaxPost(this.opt.url + '/' + this.reposingBlock.grp + '/repos/' + block.grp, { trg: this.opt.trg, usr: this.opt.usr, grp: this.reposingBlock.grp, from : this.reposingBlock.grp, to : block.grp, _token: this._token() }, function(response){
      var data = JSON.parse(response);
      self.reposing = self.reposingBlock = self.reposingBlockID = false;
      if(data.success){
        // 
      }else{
        alert(data.error);
      }
    });

    this.__stopRepos();
  },
  __unsetReposMarker : function()
  {
    lib.css(this.reposMarker,{ opacity : 0 });
  },
  __stopRepos : function()
  {
    lib.css(this.reposMarker,{ opacity : 0 });
    for(var i=0;i<this.block.length;i++)
      lib.css(this.block[i].element,{ opacity : 1 });
  }
});