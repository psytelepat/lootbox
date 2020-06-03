'use strict';
var lib = require('./index'),
    BaseClass = require('./BaseClass'),
    Cropper = require('./Cropper'),
    $ = window.$ || window.jQuery;

module.exports = BaseClass.extend({
  __className : 'ttImageEditor',
  __put : true,
  pre : function(opt)
  {
    this.id = false;
    this.url = false;

    this.editing = false;
    this.editing_mode = false;
    this.busy = false;
  },
  create : function()
  {
    var self = this,
        elm;
    
    this.url = this.element.getAttribute('url');
    this.wnd = lib.create('modal fade bd-example-modal-lg', document.body);
    this.wndCnt = lib.create('modal-dialog modal-lg', this.wnd);

    elm = lib.querySelectorAll('.js-image-edit',this.element);
    elm.forEach(function(elm,i){
      lib.bindEvent(elm,'dblclick',function(e){
        self.edit(elm);
      });
      lib.bindEvent(elm,'click',function(e){
        if(e.shiftKey) self.edit(elm);
        if(e.ctrlKey) self.crop(elm);
      });
    });

    $(this.wnd).on('hide.bs.modal', function(){
      self.close();
    });
  },
  edit : function(elm)
  {
    if(this.editing || this.busy) return;
    this.busy = true;

    var self = this;
    this.editing = parseInt( elm.getAttribute('data-id') || 0 );
    this.editing_mode = 'edit';

    lib.ajaxGet(this.url + '/' + this.editing_mode + '/' + this.editing,{ fileID : this.editing },function(response){
      var data = JSON.parse(response);
      if(data.success){
        self.showFormWith(data);
      }else{
        alert(data.error);
        self.editing = false;
      }
      self.busy = false;
    });
  },
  crop: function(elm){
    if(this.editing || this.busy) return;
    this.busy = true;

    var self = this;
    this.editing = parseInt( elm.getAttribute('data-id') || 0 );
    this.editing_mode = 'crop';

    lib.ajaxGet(this.url + '/' + this.editing_mode + '/' + this.editing,{ fileID : this.editing },function(response){
      var data = JSON.parse(response);
      if(data.success){
        self.showFormWith(data);
      }else{
        alert(data.error);
        self.editing = false;
      }
      self.busy = false;
    });
  },
  close : function(e)
  {
    this.form = false;
    this.editing = false;
    this.editing_mode = false;
    this.wndCnt.innerHTML = '';
  },
  showFormWith : function(data)
  {
    var self = this;

    this.wndCnt.innerHTML = data.html;
    
    this.submit = this.wndCnt.querySelector('.js-submit');
    if(this.submit){ lib.bindEvent(this.submit,'click',function(e){ return self.onSubmit(e); }); }

    switch( this.editing_mode ){
      case 'edit':
        this.form = this.wndCnt.querySelector('.js-form');
        if(this.form) { lib.bindEvent(this.form,'submit',function(e){ return self.onSubmit(e); }); }
        break;
      case 'crop':
        var cropper = this.wndCnt.querySelector('.js-cropper');
        this.cropper = cropper ? new Cropper({ _element: $(cropper), url: this.url + '/crop/' + this.editing }) : null;
        break;
    }

    $(this.wnd).modal('show');
  },
  onSubmit : function(e)
  {
    if(this.busy) return;
    this.busy = true;

    var self = this;

    switch(this.editing_mode){
      case 'edit':
        var alt = this.wndCnt.querySelector('.js-alt'),
            href = this.wndCnt.querySelector('.js-href'),
            title = this.wndCnt.querySelector('.js-title'),
            description = this.wndCnt.querySelector('.js-description');

        lib.ajaxPost(this.url + '/edit/' + this.editing,{ fileID : this.editing, alt : alt.value, href: href.value, title : title.value, description: description.value, _token: this._token() },function(response){
          var data = JSON.parse(response);
          if(data.success){
            $(self.wnd).modal('hide');
          }else{
            alert(data.error);
          }
          self.busy = false;
        });
        break;
      case 'crop':
        var onCrop = function(){
          self.busy = false;
          $(self.wnd).modal('hide');
        }

        this.cropper ? this.cropper.cropIt(onCrop) : onCrop;
        break;
    }
  }
});