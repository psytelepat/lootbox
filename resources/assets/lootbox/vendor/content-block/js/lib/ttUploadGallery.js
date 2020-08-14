'use strict';

var lib = require('./index'),
    BaseClass      = require('./BaseClass'),
    ttDragDrop     = require('./ttDragDrop'),
    ttImageEditor  = require('./ttImageEditor'),
    ttBaseUploader = require('./ttBaseUploader');

module.exports = BaseClass.extend({
  __className : 'ttUploadGallery',
  __put : true,
  pre : function(opt) {

    this.gallery = false;
    this.uploader = false;
    this.dragdrop = false;
    this.select_all = false;
    this.delete_selected = false;
    this.deleteImagesURL = false;

  },
  create : function() {
    var self = this,
        bu = this.element.querySelector('.ttBaseUploader');

    this.gallery = this.element.querySelector('.image-file-upload-gallery');
    this.uploader = bu ? new ttBaseUploader({ _element : bu, complete : function(uploader,json){ if(json.success) self.updateWithResponse(json); } }) : false;

    if(this.gallery && this.uploader)
    {
      this.redrawWithHTML();
      return (this._inited = true);
    }

    return false;
  },
  updateWithResponse : function(json) {
    this.redrawWithHTML( json.html, json );
  },
  redrawWithHTML : function(html, json) {

    var self = this;

    if(typeof html != 'undefined')
      this.gallery.innerHTML = html;

    if(this.dragdrop) this.dragdrop = null;
    if(this.select_all) this.select_all = null;
    if(this.delete_selected) this.delete_selected = null;
    
    this.uploader.element.style.display = json.canUpload ? 'block' : 'none';  

    var dd = this.gallery.querySelector('.ttDragDrop'),
        select_all = this.gallery.querySelector('.select-all'),
        delete_selected = this.gallery.querySelector('.delete-selected');
    
    this.dragdrop = dd ? new ttDragDrop({ _element : dd }) : false;

    lib.querySelectorAll('.ttImageEditor',this.element).forEach(function(elm,i){ new ttImageEditor({ _element : elm }) });

    if(select_all) {
      lib.bindEvent(select_all, 'click', function(e){ self.selectAll(e); });
      this.select_all = select_all;
    }

    if(delete_selected) {
      lib.bindEvent(delete_selected, 'click', function(e){ self.deleteSelected(e); });
      this.deleteImagesURL = delete_selected.getAttribute('url');
      this.delete_selected = delete_selected;
    }

  },
  selectAll : function() {
    var checkboxes = lib.querySelectorAll('.drop-id',this.gallery),
        checked = false;

    for(var i=0;i<checkboxes.length;i++) {
      if(checkboxes[i].checked) {
        checked = true;
        break;
      }
    }

    checkboxes.forEach(function(elm,i){
      elm.checked = !checked;
    });
  },
  deleteSelected : function() {
    if(!this.deleteImagesURL) return;

    var self = this,
        checkboxes = lib.querySelectorAll('.drop-id',this.gallery),
        ids = [];
    checkboxes.forEach(function(elm,i){
      if(elm.checked) {
        ids.push( elm.value );
      }
    });

    if(ids.length) {
      var data = { delete : ids.join(','), _token: this._token() };
      lib.ajaxPost(this.deleteImagesURL,data,function(response){ self.onImagesDeleted(response); });
    }
  },
  onImagesDeleted : function(response) {
    var data = JSON.parse(response);
    if( data.success ) {
      this.updateWithResponse( data );
    }else{
      this.log(data);
    }
  }
});