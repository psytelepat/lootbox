'use strict';

var lib = require('../lib/index'),
    BaseClass = require('../lib/BaseClass'),
    Slider = require('../../../../../../../../../../resources/assets/js/lib/slider/Slider.js'),
    ttUploadGallery = require('../lib/ttUploadGallery');

module.exports = BaseClass.extend({
  __className : 'ContentBlockForm',
  __put : true,
  pre : function(opt)
  {
    this.id       = false;
    this.element  = false;
    this.delegate = false;
    this.state    = 0;
    this.mo       = false;
    this.xhr      = false;
    this.url      = false;
  },
  create : function()
  {
    var self = this;

    this.onMouseBlock = function(e){ self.mouseBlock(e); };

    this.busy = false;
    if(!this.state) this.state = parseInt(this.element.getAttribute('state')) || 0;

    this.process();
  },
  process : function()
  {
    var self = this;
    this.grp = this.element.getAttribute('grp');
    this.url = this.delegate.opt.url + '/' + this.grp;
    this.mode = parseInt(this.element.getAttribute('mode'));

    this.updateEvents();
  },
  processFileGallery : function()
  {
    var self = this,
        elm = this.element.querySelector('.form-gallery');
    this.fileGallery = elm ? elm : false;
    if(this.fileGallery)
    {
      this.checkAllFiles = this.fileGallery.querySelector('span.checkAllFiles');
      this.deleteCheckedFiles = this.fileGallery.querySelector('span.deleteCheckedFiles');

      if(this.checkAllFiles)
        lib.bindEvent(this.checkAllFiles,'click',function(){
          var hasChecked = false, boxes = lib.querySelectorAll('input.cb-del',self.fileGallery);
          boxes.forEach(function(elm,i){if(hasChecked){return;}else if(elm.checked){hasChecked=true;}});
          if(hasChecked){boxes.forEach(function(elm,i){ elm.checked = false; });}else{ boxes.each(function(){ elm.checked = true; });}
        });

      if(this.deleteCheckedFiles)
        lib.bindEvent(this.deleteCheckedFiles,'click',function(e){
          var self = this,
              vals = [],
              boxes = self.fileGallery.querySelectorAll('.cb-del');
              boxes.forEach(function(elm,i){ if(elm.checked){ vals.push(elm.value); } });

          if(vals.length){
            lib.ajaxPost(self.url, { ids : vals }, function(response){
              var data = JSON.parse(response);
              if(data.success){
                self.reloadFileGallery(); 
              }else{
                alert(data.error);
              }
            });
          }else{
            alert('Не выбрано ни одного файла для удаления');
          }
        });
    }

    var fileUpload = this.element.querySelector('.ttUploadGallery');
    if(fileUpload)
    {
      new ttUploadGallery({ _element : fileUpload });
    }
  },
  // reloadFileGallery : function()
  // {
  //   var self = this;
  //   this.ajaxGet(this.delegate.opt.url + '/images',this.collectData({ cmd : 'images' }),function(response){
  //     var data = JSON.parse(response);
  //     if(data.success) {
  //       var fileUpload = self.element.querySelector('.fileUpload');
  //       if(fileUpload)
  //       {
  //         fileUpload.innerHTML = data.html;
  //         self.processFileGallery();
  //       }
  //     }else{
  //       alert(data.error);
  //     }
  //   });
  // },
  updateEvents : function()
  {
    var self = this;
    lib.unbindEvent(this.element,'click touchend mouseenter mouseleave mousemove', this.onMouseBlock);

    switch(this.state)
    {
      case 0:
        if(this.delegate.opt.tools)
        {
          var tool = lib.create('tool',this.element,'DIV',false,{ 'whiteSpace' : 'nowrap' }),
              // refresh = lib.create('fa fa-sync-alt',tool,'DIV',{ title : 'обновить' },{ display : 'inline-block' }),
              pencil = lib.create('fa fa-pencil-alt', tool, 'DIV', { title : 'редактировать' },{ display : 'inline-block' }),
              arrows = lib.create('fa fa-arrows-alt', tool, 'DIV', { title : 'переместить' },{ display : 'inline-block' }),
              times = lib.create('fa fa-times', tool, 'DIV', { title : 'удалить' },{ display : 'inline-block' });
          // lib.bindEvent(refresh,'click',function(e){ self.view(); });
          lib.bindEvent(arrows,'click',function(e){ e.preventDefault();e.stopPropagation();self.delegate.__startRepos(self); });
          lib.bindEvent(pencil,'click',function(e){ self.edit(); });
          lib.bindEvent(times,'click',function(e){ if(confirm('Удалить?')) self.drop(); else return; });
        }

        lib.bindEvent(this.element,'click touchend mouseenter mouseleave mousemove', this.onMouseBlock);

        lib.querySelectorAll('.js-slider',this.element).forEach(function(elm,i){ new Slider({ element : elm }) });
        break;
      case 1:
        this.processFileGallery();

        this.saveButton = this.element.querySelector('.saveBlock');
        if(this.saveButton) lib.bindEvent(this.saveButton,'click',function(e){ self.save(); });

        var contentField = this.element.querySelector('.cnt-fld');
        if(contentField){ window.$R(contentField,{
          formatting: ['h2','h3'],
          buttons: ['html', 'format', 'bold', 'italic', 'lists', 'link'],
          linkNewTab: true,
          linkTarget: '_blank'
        }); }

        var contentField1 = this.element.querySelector('.cnt-fld1');
        if(contentField1){ window.$R(contentField1,{
          buttons: ['html', 'bold', 'italic', 'lists', ],
          linkNewTab: true,
          linkTarget: '_blank'
        }); }

        var contentField2 = this.element.querySelector('.cnt-fld2');
        if(contentField2){ window.$R(contentField2,{
          buttons: ['html', 'bold', 'italic', 'lists', ],
          linkNewTab: true,
          linkTarget: '_blank'
        }); }
        break;
    }
  },
  collectData : function(data)
  {
    if(typeof data == 'undefined') data = {};
    data.trg = this.delegate.opt.trg;
    data.usr = this.delegate.opt.usr;
    data.grp = this.grp;
    data._token = this._token();

    return data;
  },
  view : function()
  {
    if(this.busy) return;
    this.busy = true;

    this.delegate.unsetEditingBlock(this);

    var self = this,
        data = this.collectData();

    lib.ajaxGet(this.url, data, function(response){
      var data = JSON.parse(response);
      if(data.success)
      {
        var element = lib.htmlToElement(data.html);
        self.element.innerHTML = element.innerHTML;

        self.state = 0;
        self.element.setAttribute('state',self.state);

        self.process();
      }

      self.busy = false;
    });
  },
  edit : function()
  {
    if(this.busy) return;
    this.busy = true;

    this.delegate.setEditingBlock(this);

    var self = this,
        data = this.collectData({ cmd : 'edit' });
    
    lib.ajaxGet(this.url + '/edit', data, function(response){
      var data = JSON.parse(response);
      if(data.success) {
        var element = lib.htmlToElement(data.html);
        self.element.innerHTML = element.innerHTML;

        self.state = 1;
        self.element.setAttribute('state',self.state);

        self.updateEvents();
      }else{
        alert(data.error);
      }

      self.busy = false;
    });
  },
  save : function()
  {
    if(this.busy) return;
    this.busy = true;

    var self = this,
        data = this.collectData(),
        style = this.element.querySelector('select.style-fld'),
        align = this.element.querySelector('select.align-fld'),
        title = this.element.querySelector('input.title-fld'),
        description = this.element.querySelector('input.description-fld'),
        content = this.element.querySelector('textarea.cnt-fld'),
        content1 = this.element.querySelector('textarea.cnt-fld1'),
        content2 = this.element.querySelector('textarea.cnt-fld2'),
        code = this.element.querySelector('textarea.code-fld');

    if(style) data.style = style.options[style.selectedIndex].value;
    if(align) data.align = align.options[align.selectedIndex].value;
    if(content) data.content = content.value;
    if(content1) data.content1 = content1.value;
    if(content2) data.content2 = content2.value;
    if(code) data.code = code.value;
    if(title) data.title = title.value;
    if(description) data.description = description.value;

    lib.ajaxPost(this.url + '/edit', data, function(response){
      var data = JSON.parse(response);
      self.busy = false;
      if(data.success){
        self.view();
      }else{
        alert(data.error);
      }
    });
  },
  drop : function()
  {
    if(this.busy) return;
    this.busy = true;

    // TODO
    var self = this,
        data = this.collectData();

    lib.ajaxPost(this.url + '/delete', data, function(response){
      var data = JSON.parse(response);
      self.busy = false;
      if(data.success){
        self.remove();
      }else{
        alert(data.error)
      }
    });
  },
  remove : function()
  {
    this.element.parentNode.removeChild(this.element);
  },
  mouseBlock : function(e)
  {
    if(!this.delegate.reposing) return;

    var rect = this.element.getBoundingClientRect(),
        scrollTop = lib.scrollTop();

    switch(e.type)
    {
      case 'click':
      case 'touchend':
        if(this == this.delegate.reposingBlock)
        {
          this.delegate.__stopRepos();
          return;
        }

        // TODO
        
        var after = Math.round(rect.top + scrollTop + rect.height / 2) < e.pageY;
        this.delegate.__reposBlockTo(this,after);

        break;
      case 'mousemove':
        if(!this.mo) break;
      case 'mouseenter':
        this.mo = true;

        if(this.delegate.reposingBlock == this) return;
        var after = Math.round(rect.top + lib.scrollTop + rect.height / 2) < e.pageY;
        this.delegate.__setReposMarkerTo(this,after);
        break;
      case 'mouseleave':
        this.mo = false;
        if(this.delegate.reposingBlock == this) return;
        this.delegate.__unsetReposMarker();
        break;
    }
  }
});