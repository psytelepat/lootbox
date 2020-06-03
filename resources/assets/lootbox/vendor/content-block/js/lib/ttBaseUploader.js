'use strict';

var lib = require('./index'),
    BaseClass = require('./BaseClass');

module.exports = BaseClass.extend({
    __className : 'ttBaseUploader',
    __put : true,
    pre : function(opt)
    {
        this.opt.mode = null;
        this.opt.multiple = true;
        this.opt.url = null;
        this.opt.start = null;
        this.opt.progress = null;
        this.opt.complete = null;
        this.opt.error = null;
        this.opt.switcher = false;
        this.opt._token = false;
    },
    create : function()
    {
        if(this.opt.mode == 'none'){return;}

        var self = this;

        this.fileUploadField = this.element.querySelector('.file-upload-field');
        if(!this.fileUploadField){ alert('BaseUploader error: no .file-upload-field');return; }

        this.input = this.fileUploadField.querySelector('input[type=file]');
        if(!this.input){ alert('BaseUploader error: unable to find input');return; }
        
        this.form = this.input.form;

        this.opt._token = this._token();
        
        this.opt.url = this.opt.url || this.element.getAttribute('url') || this.form.getAttribute('action');
        if(!this.opt.url){ alert('BaseUploader error: unable to find uploadPath');return; }

        this.caption = this.element.querySelector('.caption');
        this.progressorBar = lib.create('bar',this.fileUploadField);
        this.progressorPrc = lib.create('prc',this.fileUploadField);

        var mode = 'iframe',
            supportAjaxUpload = this.supportAjaxUploadWithProgress(),
            supportFormData = this.supportFormData();

        if(!this.opt.mode)
        {
            if(supportFormData){ mode = 'formData'; }
            else if(supportAjaxUpload){ mode = 'ajax'; }
            else{ mode = 'iframe'; }

            this.opt.mode = mode;
        }

        this.normalMode = this.opt.mode;

        switch(this.opt.mode)
        {
            case 'iframe':
                lib.bindEvent(this.input,'change',function(e){ self.uploadIFrame(e); });
                break;
            case 'ajax':
                lib.bindEvent(this.input,'change',function(e){ self.fileSelect(e); });
                break;
            case 'formData':
                lib.bindEvent(this.input,'change',function(e){ return self.uploadForm(e); });
                break;
            case 'shutdown':
            default:
                this.element.addClass('shutdown');
                return;
                break;
        }

        if(this.opt.multiple){
            this.input.setAttribute('multiple',true);
        }else{
            this.input.setAttribute('multiple',false);
        }

        return ( this._inited = true );
    },
    supportAjaxUploadWithProgress : function()
    {
        return this.supportFileAPI() && this.supportAjaxUploadProgressEvents();
    },
    supportFileAPI : function(){
        var fi = document.createElement('INPUT');
        fi.type='file';
        return 'files' in fi;
    },
    supportAjaxUploadProgressEvents : function()
    {
        var xhr = new XMLHttpRequest();
        return !! (xhr && ('upload' in xhr) && ('onprogress' in xhr.upload));
    },
    supportFormData : function() {
        return !!window.FormData;
    },
    supportFlash : function() {
        return (typeof swfobject != 'undefined' && swfobject.getFlashPlayerVersion().major >= 9);
    },

    initProgress : function(prc) {
        this.progressorBar.style.width = '0%';
        this.progressorBar.style.display = 'none';
        this.progressorPrc.style.display = 'none';
        this.progressorPrc.style.innerHTML = '0%';
        if(this.progressor){ this.progressor.style.display = 'none'; }
        this.caption.style.opacity = 1;
    },
    initForm : function() {
        this.pendingFiles = [];
        this.currentPendingFile = 0;
        if(this.iframeDIV){ this.iframeDIV.remove(); }
        this.iframeDIV = this.iframe = this.iframeID = null;
        this.initProgress();
    },
    viewLoading : function() {
        this.caption.style.opacity = 0;
        this.progressorBar.style.display = 'block';
        this.progressorPrc.style.display = 'block';
    },

// iframe upload
    uploadIFrame : function()
    {
        var self = this;
        this.iframeID = 'f' + Math.floor(Math.random() * 99999);

        this.iframeDIV = lib.create(false,document.body);

        this.iframe = lib.create(false,false,this.iframeDIV);
        this.iframe.id = this.iframeID;
        this.iframe.name = this.iframeID;
        this.iframe.style.display = 'none';

        this.form.setAttribute('target', this.iframeID);
        lib.bindEvent(this.iframe,'load',function(e){ self.iframeLoaded(e); });
        
        this.form.submit();

        this.viewLoading();
        this.form.removeAttribute('target');
    },
    iframeLoaded : function(e)
    {
        var d, i = document.getElementById(this.iframeID);
        if(i.contentDocument){ d = i.contentDocument; }
        else if(i.contentWindow){ d = i.contentWindow.document; }
        else{ d = window.frames[this.iframeID].document; }
        this.uploadComplete( d.body.innerHTML );
    },

// ajax upload
    fileSelect : function(e)
    {
        var files = e.target.files || e.dataTransfer.files;
        this.pendingFiles = files;
        this.currentPendingFile = 0;
        this.viewLoading();
        this.uploadNext();
    },
    uploadNext : function(xhr)
    {
        if(typeof this.pendingFiles.length != 'undefined')
        {
            var count = this.pendingFiles.length;
            if(this.currentPendingFile < count){
                this.uploadFile(this.pendingFiles[this.currentPendingFile]);
                this.currentPendingFile++;
            }else{
                this.uploadComplete(xhr.responseText);
            }
        }       
    },
    uploadFile : function(file)
    {
        var self = this,
            xhr = new XMLHttpRequest(),
            url = this.opt.url + '/upload/ajax';

        xhr.upload.addEventListener("progress",function(e) {
            var prc=parseInt(e.loaded/e.total*100);
            self.updateProgress(prc);
        },false);

        xhr.onreadystatechange = function(e) {
            if(xhr.readyState == 4)
            {
                if(xhr.status == 200){ self.uploadNext(xhr); }
                else{ self.uploadError(xhr.responseText); }
            }
        };

        //xhr.timeout = 4000;
        //xhr.ontimeout = function () { alert("Timed out!!!"); }

        xhr.open("POST", url, true);
        xhr.setRequestHeader("x-filename", encodeURIComponent(file.name));
        xhr.setRequestHeader("x-filetype", file.type);
        xhr.setRequestHeader("x-filesize", file.size);
        xhr.setRequestHeader("x-requested-with", "XMLHttpRequest");
        xhr.setRequestHeader("x-csrf-token", this.opt._token);
        xhr.send(file);
    },

// formData upload
    uploadForm : function(e)
    {
        var self = this,
            formData = new FormData(),
            xhr = new XMLHttpRequest(),
            url = this.opt.url + '/upload/formData';

        for(var i=0;i<this.input.files.length;i++){
            formData.append(this.input.name, this.input.files[i]);
        }

        formData.append('_token', this._token());

        xhr.open("POST", url, true);
        xhr.setRequestHeader("x-requested-with", "XMLHttpRequest");
        xhr.setRequestHeader("x-csrf-token", this.opt._token);

        xhr.upload.addEventListener("progress",function(e){
            var prc = parseInt(e.loaded/e.total*100);
            self.updateProgress(prc);
        },false);

        xhr.onreadystatechange = function(e)
        {
            if(xhr.readyState == 4)
            {
                if(xhr.status == 200){self.uploadComplete(xhr.responseText);}
                else{self.uploadError(xhr.responseText);}
            }
        }

        //xhr.timeout = 4000;
        //xhr.ontimeout = function () { alert("Timed out!!!"); }

        xhr.open("POST", url, true);
        xhr.send(formData);

        this.viewLoading();
        return false;
    },
    updateProgress : function(prc)
    {
        if(this.pendingFiles && this.pendingFiles.length > 1)
        {
            var partPrc = 100 / this.pendingFiles.length;
            prc = (partPrc * prc / 100);
            if(this.currentPendingFile > 0){prc += (this.currentPendingFile-1) * partPrc;}
        }

        this.progressorBar.style.width = prc + '%';
        this.progressorPrc.innerHTML = Math.round(prc) + '%';
    },
    uploadComplete : function(response)
    {
        if(window.console) console.log(response);

        this.initForm();
        var json = JSON.parse(response);

        if(typeof this.opt.complete == 'function') {
            this.opt.complete(this,json);
        }
    },
    uploadError : function(responseText)
    {
        this.initForm();
        if(typeof this.opt.error == 'function'){
            this.opt.error(this,responseText);
        }
    }
});