'use strict';

var $ = window.$ || window.jQuery,
    MinimalClass = require('./BaseClass');

module.exports = MinimalClass.extend({
	pre : function(opt)
	{
		this.opt = {
			debug : false,
			regionHandlerSize : 25,
			invertRegion : true,
			resizable : true,

			size : { width : 738, height : 400 },
			targetSize : { width : 160, height : 90 },
			mode : 'draw',
			aspect : true,

			regionBorderColor : '#FFFFFF',
			recieverBackground : '#000000',
			recieverOpacity : 0.6
		};
	},
	create : function()
	{
		this.cropping = false;
		this.resizingRegion = false;
		this.invertRegionImage = false;

		this.opt.imageSource = this.element.data('src');
		this.opt.targetSize.width = parseInt(this.element.data('target-width'));
		this.opt.targetSize.height = parseInt(this.element.data('target-height'));
		this.opt.aspect = parseFloat(this.element.data('target-aspect')||0);
		this.opt.min_width = this.opt.targetSize.width || 100;
		this.opt.min_height = this.opt.targetSize.height || 100;

		if(this.opt.aspect && this.opt.targetSize.width && this.opt.targetSize.height){
			this.opt.aspect = this.opt.targetSize.width / this.opt.targetSize.height;
		}

		this.log('imageSource: ', this.opt.imageSource);
		this.image = {
			url : this.opt.imageSource,
			img : $('<img></img>').css({ position : 'absolute', top : 0, left : 0, opacity : 0 }).appendTo(this.element),
			width : 0,
			height : 0,
			loaded : false
		};

		this.reciever = $('<div></div>').css({ position : 'absolute', top : 0, left : 0, width : '100%', height : '100%' }).appendTo(this.element);

		// REGION
		switch(this.opt.mode)
		{
			case 'draw':
				this.prepareRegion();
				break;
			case 'scroll':
				break;
		}

		// OK, LET'S GO!
		this.loadImage();
	},
	prepareRegion : function()
	{
		var self = this;

		this.region = $('<div></div>').css({ position : 'absolute', top : 0, left : 0, overflow : 'hidden', display : 'none' }).appendTo(this.element);
		this.region.bind('mouseover.cropper mouseout.cropper',function(e){ self.mouseOverRegion(e); })

		if(this.opt.invertRegion)
		{
			this.invertRegionImage = $('<img></img>').css({ position : 'absolute', top : 0, left : 0 }).appendTo(this.region);
			this.reciever.css({ backgroundColor : this.opt.recieverBackground, opacity : this.opt.recieverOpacity });
		}else{
			this.regionBackground = $('<div></div>').css({ position : 'absolute', top : 0, left : 0, width : '100%', height : '100%', backgroundColor : '#fff', opacity : 0.5 }).appendTo(this.region);
		}

		this.region_m = $('<div></div>').css({ position : 'absolute', top : 0, left : 0, width : '100%', height : '100%' }).appendTo(this.region);
		this.region_m.bind('mousedown.cropper', function(e){ self.cropperEvent(e); });

		if(this.opt.resizable)
		{
			this.region_t = $('<div></div>').css({ position : 'absolute', top : 0, left : 0, width : '100%', height : this.opt.regionHandlerSize, borderColor : this.opt.regionBorderColor, borderStyle : 'solid', borderWidth : '2px 0 0 0', opacity : 0 }).appendTo(this.region);
			this.region_l = $('<div></div>').css({ position : 'absolute', top : 0, left : 0, width : this.opt.regionHandlerSize, height : '100%', borderColor : this.opt.regionBorderColor, borderStyle : 'solid', borderWidth : '0 0 0 2px', opacity : 0 }).appendTo(this.region);
			this.region_b = $('<div></div>').css({ position : 'absolute', bottom : 0, left : 0, width : '100%', height : this.opt.regionHandlerSize, borderColor : this.opt.regionBorderColor, borderStyle : 'solid', borderWidth : '0 0 2px 0', opacity : 0 }).appendTo(this.region);
			this.region_r = $('<div></div>').css({ position : 'absolute', top : 0, right : 0, width : this.opt.regionHandlerSize, height : '100%', borderColor : this.opt.regionBorderColor, borderStyle : 'solid', borderWidth : '0 2px 0 0', opacity : 0 }).appendTo(this.region);
			
			this.region_tl = $('<div></div>').css({ position : 'absolute', top : 0, left : 0, width : this.opt.regionHandlerSize, height : this.opt.regionHandlerSize, borderColor : this.opt.regionBorderColor, borderStyle : 'solid', borderWidth : '2px 0 0 2px', opacity : 0 }).appendTo(this.region);
			this.region_tr = $('<div></div>').css({ position : 'absolute', top : 0, right : 0, width : this.opt.regionHandlerSize, height : this.opt.regionHandlerSize, borderColor : this.opt.regionBorderColor, borderStyle : 'solid', borderWidth : '2px 2px 0 0', opacity : 0 }).appendTo(this.region);
			this.region_bl = $('<div></div>').css({ position : 'absolute', bottom : 0, left : 0, width : this.opt.regionHandlerSize, height : this.opt.regionHandlerSize, borderColor : this.opt.regionBorderColor, borderStyle : 'solid', borderWidth : '0 0 2px 2px', opacity : 0 }).appendTo(this.region);
			this.region_br = $('<div></div>').css({ position : 'absolute', bottom : 0, right : 0, width : this.opt.regionHandlerSize, height : this.opt.regionHandlerSize, borderColor : this.opt.regionBorderColor, borderStyle : 'solid', borderWidth : '0 2px 2px 0', opacity : 0 }).appendTo(this.region);

			this.region_t.bind('mousedown.cropper mouseenter.cropper mouseleave.cropper', { x : 0, y : -1, elm : this.region_t, cursor : 'n' },function(e){ self.regionHandlerEvent(e); });
			this.region_l.bind('mousedown.cropper mouseenter.cropper mouseleave.cropper', { x : -1, y : 0, elm : this.region_l, cursor : 'w' },function(e){ self.regionHandlerEvent(e); });
			this.region_b.bind('mousedown.cropper mouseenter.cropper mouseleave.cropper', { x : 0, y : 1, elm : this.region_b, cursor : 's' },function(e){ self.regionHandlerEvent(e); });
			this.region_r.bind('mousedown.cropper mouseenter.cropper mouseleave.cropper', { x : 1, y : 0, elm : this.region_r, cursor : 'e' },function(e){ self.regionHandlerEvent(e); });
			
			this.region_tl.bind('mousedown.cropper mouseenter.cropper mouseleave.cropper', { x : -1, y : -1, elm : this.region_tl, cursor : 'nw' },function(e){ self.regionHandlerEvent(e); });
			this.region_tr.bind('mousedown.cropper mouseenter.cropper mouseleave.cropper', { x : 1, y : -1, elm : this.region_tr, cursor : 'ne' },function(e){ self.regionHandlerEvent(e); });
			this.region_bl.bind('mousedown.cropper mouseenter.cropper mouseleave.cropper', { x : -1, y : 1, elm : this.region_bl, cursor : 'sw' },function(e){ self.regionHandlerEvent(e); });
			this.region_br.bind('mousedown.cropper mouseenter.cropper mouseleave.cropper', { x : 1, y : 1, elm : this.region_br, cursor : 'se' },function(e){ self.regionHandlerEvent(e); });
		}
	},
	loadImage : function()
	{
		var self = this;
		this.image.img.bind('load error',function(e){ self.imageLoaded(e,this); }).attr('src',this.image.url);
	},
	imageLoaded : function(e,img)
	{
		switch(e.type)
		{
			case 'error':
				this.log('Error loading image');
				alert('Не удалось загрузить исходное изображение, перезагрузите картинку в бОльшем размере в панели управления.');
				break;
			case 'load':
				this.image.width = img.width;
				this.image.height = img.height;
				this.image.aspect = img.width / img.height;
				this.image.loaded = true;

				this.log('Image Loaded: ',img.width,'x',img.height);

				if(this.installImage())
					this.setupCropperEvents();

				break;
		}
	},
	installImage : function()
	{
		this.log('Installing image');


		var ow = this.image.width;
		var oh = this.image.height;
		var width = ow;
		var height = oh;
		var resize = 1;
		var targetWidth, targetHeight;
		
		if(!this.opt.targetSize.width && !this.opt.targetSize.height){
			this.opt.targetSize.width = Math.round(this.image.width / 2);
			this.opt.targetSize.height = this.opt.aspect ? Math.round(this.opt.targetSize.width / this.opt.aspect) : Math.round(this.image.height / 2);
		}else if(!this.opt.targetSize.width){
			this.opt.targetSize.width = this.opt.aspect ? Math.round(this.opt.targetSize.hegiht * this.opt.aspect) : Math.round(this.image.width / 2);
		}else if(!this.opt.targetSize.height){
			this.opt.targetSize.height = this.opt.aspect ? Math.round(this.opt.targetSize.width / this.opt.aspect) : Math.round(this.image.height / 2);
		}

		if(ow < this.opt.targetSize.width)
		{
			this.opt.targetSize.width = targetWidth = ow;
			if( this.opt.aspect ){
				targetHeight = Math.round(ow / this.opt.aspect);
				if( targetHeight > oh ){
					targetHeight = oh;
					this.opt.targetSize.width = targetWidth = Math.round(targetHeight * this.opt.aspect);
				}
				this.opt.targetSize.height = targetHeight;
			}
		}

		if(oh < this.opt.targetSize.height)
		{
			this.opt.targetSize.height = oh;
			if( this.opt.aspect ){
				targetWidth = Math.round(oh * this.opt.aspect);
				if( targetWidth > ow ){
					targetWidth = ow;
					this.opt.targetSize.height = targetHeight = Math.round(targetWidth / this.opt.aspect);
				}
				this.opt.targetSize.width = targetWidth;
			}
		}

		if(width > this.opt.size.width)
		{
			width = this.opt.size.width;
			height = Math.round(oh * (resize = (this.opt.size.width / ow)));
		}

		if(height > this.opt.size.height)
		{
			height = this.opt.size.height;
			width = Math.round(ow * (resize = (this.opt.size.height / oh)));
		}

		this.opt.targetSize.width = Math.round( this.opt.targetSize.width * resize );
		this.opt.targetSize.height = Math.round( this.opt.targetSize.height * resize );

		this.opt.min_width  = Math.round( this.opt.min_width * resize );
		this.opt.min_height  = Math.round( this.opt.min_height * resize );

		switch(this.opt.mode)
		{
			case 'draw':
				this.opt.size.width = width;
				this.opt.size.height = height;

				if(this.invertRegionImage)
				{
					this.invertRegionImage.attr('src', this.opt.imageSource).css({ width : width, height : height });
				}

				this.regionData = this.recalcRegionData();
				break;
			case 'scroll':
				this.reciever.css({ cursor : 'move' });
				break;
		}

		this.element.css({
			position : 'relative',
			width : this.opt.size.width,
			height : this.opt.size.height,
			border : '1px #cecece solid',
			overflow : 'hidden'
		});

		this.imageData = {
			_width : ow,
			_height : oh,
			width : width,
			height : height,
			resize : resize,
			x : 0,
			y : 0,
			cx : 0,
			cy : 0,
			minx : -(width - this.opt.size.width),
			miny : -(height - this.opt.size.height),
			maxx : 0,
			maxy : 0
		};

		this.image.img.css({
			top : this.imageData.posx,
			left : this.imageData.posy,
			width : this.imageData.width,
			height : this.imageData.height
		});

		this.opt._targetSize = this.opt.targetSize;

		return true;
	},
	setupCropperEvents : function()
	{
		var self = this;
		this.reciever.bind('mousedown.cropper', function(e){ self.cropperEvent(e); });
		this.image.img.animate({ opacity : 1 });
	},
	preserveAspect : function(o,aspect)
	{
		var nx = o.nx, ny = o.ny, overhead = 0;

		if(Math.abs(o.dx) > Math.abs(o.dy)){
			var aspectH = Math.round(o.nw / aspect);
			overhead = o.nh - aspectH;
			if(o.yside < 0){ ny += overhead; }
			
			if(ny < 0 || ny + aspectH > this.opt.size.height) return false;

			o.ny = ny;
			o.nh = aspectH;
		}else{
			var aspectW = Math.round(o.nh * aspect);
			overhead = o.nw - aspectW;
			if(o.xside < 0){ nx += overhead; }

			if(nx < 0 || nx + aspectW > this.opt.size.width) return false;

			o.nx = nx;
			o.nw = aspectW;
		}

		return o;
	},
	mouseOverRegion : function(e)
	{
		if(this.resizingRegion) return false;

		switch(e.type)
		{
			case 'mouseenter':
			case 'mouseover':
				$(document.body).css({ cursor : 'move' });
				break;
			case 'mouseleave':
			case 'mouseout':
				$(document.body).css({ cursor : 'default' });
				break;
		}
	},
	regionHandlerEvent : function(e)
	{
		this.cancelEvent(e);

		switch(e.type)
		{
			case 'mousedown':
				if(this.resizingRegion) return false;
				this.resizingRegion = true;

				this.mouseData = {
					cx : e.pageX,
					cy : e.pageY,
					dx : 0,
					dy : 0,
					regionData : this.regionData
				};

				var self = this;
				$(document).bind('mousemove.cropper mouseup.cropper',e.data,function(e){ self.regionHandlerEvent(e); });
				break;
			case 'mousemove':
				var xside = e.data.x;
				var yside = e.data.y;

				var o = {
					xside : xside,
					yside : yside,
					dx : xside ? e.pageX - this.mouseData.cx : 0,
					dy : yside ? e.pageY - this.mouseData.cy : 0,
					nx : this.regionData.x,
					ny : this.regionData.y,
					nw : this.regionData.width,
					nh : this.regionData.height
				};

				this.mouseData.cx = e.pageX;
				this.mouseData.cy = e.pageY;

				if(o.xside < 0)
				{
					o.nx += o.dx;
					if(o.nx < this.regionData.minx) // -x overhead compensation
					{
						o.dx += this.regionData.minx - o.nx;
						o.nx = this.regionData.minx;
					}
				}

				if(o.yside < 0)
				{
					o.ny += o.dy;
					if(o.ny < this.regionData.miny) // -y overhead compensation
					{
						o.dy += this.regionData.miny - o.ny;
						o.ny = this.regionData.miny;
					}
				}

				if(xside) o.nw += o.xside * o.dx;
				if(yside) o.nh += o.yside * o.dy;

				if(o.nw < this.regionData.minw)
				{
					if(o.xside < 0){
						o.nx -= (this.regionData.minw - o.nw); // +x overhead compansation
					}else{
						o.nx = this.regionData.x;
					}
					o.nw = this.regionData.minw;
				}
				if(o.nh < this.regionData.minh)
				{
					if(o.yside < 0){
						o.ny -= (this.regionData.minh - o.nh); // +y overhead compansation
					}else{
						o.ny = this.regionData.y;
					}
					o.nh = this.regionData.minh;
				}

				if(o.nw > this.opt.size.width - o.nx)
				{
					o.nw = this.regionData.maxw;
					o.nx = this.regionData.x;
				}

				if(o.nh > this.opt.size.height - o.ny)
				{
					o.nh = this.regionData.maxh;
					o.ny = this.regionData.y;
				}

				if( this.opt.aspect )
					o = this.preserveAspect(o,this.opt.aspect);

				if(o)
					this.regionData = this.recalcRegionData({ x : o.nx, y : o.ny, width : o.nw, height : o.nh });

				break;
			case 'mouseup':
				$(document).unbind('mousemove.cropper mouseup.cropper');
				this.resizingRegion = false;
			case 'mouseleave':
				if(this.resizingRegion) return;
				if(e.type == 'mouseleave' || !this.mouseOverElement(e,e.data.elm))
				{
					e.data.elm.stop().animate({ opacity : 0 }, 100, 'swing');
					$(document.body).css({ cursor : this.mouseOverElement(e,this.region) ? 'move' : 'default' });
				}
				break;
			case 'mouseenter':
				if(this.resizingRegion) return;
				e.data.elm.stop().animate({ opacity : 1 }, 100, 'swing');
				$(document.body).css({ cursor : e.data.cursor + '-resize' });
				break;
		}
	},
    mouseOverElement: function(e,elm)
    {
      var offset = elm.offset();
      var width = elm.outerWidth(true);
      var height = elm.outerHeight(true);
      return !( e.pageY < offset.top || e.pageY > offset.top + height || e.pageX < offset.left || e.pageX > offset.left + width )
    },
    ajaxFailFunction : function(xhr,textStatus,errorThrown)
    {
        alert( xhr.responseText );
    },
    ajaxCheckStatus : function(data,xhr,context,failCallBack,doneCallBack)
    {
        if(data.status > 0)
        {
            alert(data.msg);

            if(typeof failCallBack == 'function') failCallBack(data,xhr,context);
            return false;
        }

        if(typeof doneCallBack == 'function') doneCallBack(data,xhr,context);
        return true;
    },
	cropperEvent : function(e)
	{
		this.cancelEvent(e);

		switch(e.type)
		{
			case 'mousedown':
				this.mouseData = {
					sx : e.pageX,
					sy : e.pageY,
					cx : e.pageX,
					cy : e.pageY,
					dx : 0,
					dy : 0
				};

				var self = this;
				$(document).bind('mousemove.cropper mouseup.cropper',function(e){ self.cropperEvent(e); });
				break;
			case 'mousemove':
				this.mouseData.cx = e.pageX;
				this.mouseData.cy = e.pageY;
				this.mouseData.dx = this.mouseData.cx - this.mouseData.sx;
				this.mouseData.dy = this.mouseData.cy - this.mouseData.sy;

				// console.log('-- MOUSE DATA --');
				// console.log(this.mouseData.sx,this.mouseData.sy);
				// console.log(this.mouseData.cx,this.mouseData.cy);
				// console.log(this.mouseData.dx,this.mouseData.dy);

				switch(this.opt.mode)
				{
					case 'draw':
						var nx = this.regionData.x + this.mouseData.dx;
						var ny = this.regionData.y + this.mouseData.dy;

						if(nx < this.regionData.minx) nx = this.regionData.minx;
						else if(nx > this.regionData.maxx) nx = this.regionData.maxx;

						if(ny < this.regionData.miny) ny = this.regionData.miny;
						else if(ny > this.regionData.maxy) ny = this.regionData.maxy;

						this.regionData.cx = nx;
						this.regionData.cy = ny;

						this.region.css({ top : this.regionData.cy, left : this.regionData.cx });

						if(this.invertRegionImage)
							this.invertRegionImage.css({ top : -ny, left : -nx });
						break;
					case 'scroll':
						var nx = this.imageData.x + this.mouseData.dx;
						var ny = this.imageData.y + this.mouseData.dy;

						if(nx < this.imageData.minx){
							nx = this.imageData.minx;
						}
						else if(nx > this.imageData.maxx){
							nx = this.imageData.maxx;
						}

						if(ny < this.imageData.miny){
							ny = this.imageData.miny;
						}
						else if(ny > this.imageData.maxy){
							ny = this.imageData.maxy;
						}

						this.imageData.cx = nx;
						this.imageData.cy = ny;

						this.image.img.css({ top : this.imageData.cy, left : this.imageData.cx });
						break;
				}

				break;
			case 'mouseup':
				switch(this.opt.mode)
				{
					case 'draw':
						this.regionData.x = this.regionData.cx;
						this.regionData.y = this.regionData.cy;	

						this.traceRegionData(this.regionData);
						break;
					case 'scroll':
						this.imageData.x = this.imageData.cx;
						this.imageData.y = this.imageData.cy;
						break;
				}
				$(document).unbind('mousemove.cropper mouseup.cropper');
				break;
		}
	},
	recalcRegionData : function(data)
	{
		data = data || {};

		var regionData = {
			x : (typeof data.x != 'undefined') ? data.x : Math.round((this.opt.size.width - this.opt.targetSize.width)/2),
			y : (typeof data.y != 'undefined') ? data.y : Math.round((this.opt.size.height - this.opt.targetSize.height)/2),
			width : (typeof data.width != 'undefined') ? data.width : this.opt.targetSize.width,
			height : (typeof data.height != 'undefined') ? data.height : this.opt.targetSize.height,
			minx : 0,
			miny : 0,
			minw : this.opt.min_width,
			minh : this.opt.min_height
		};

		regionData.maxx = (this.opt.size.width - regionData.width); 
		regionData.maxy = (this.opt.size.height - regionData.height);
		regionData.maxw = (this.opt.size.width - regionData.x);
		regionData.maxh = (this.opt.size.height - regionData.y);

		if(this.invertRegionImage)
			this.invertRegionImage.css({ top : -regionData.y, left : -regionData.x });

		this.region.css({ top : regionData.y, left : regionData.x, width : regionData.width, height : regionData.height, display : 'block' });
		this.traceRegionData(regionData);

		return regionData;
	},
	collectCropData : function()
	{
		var x = Math.floor( this.regionData.x / this.imageData.resize );
		var y = Math.floor( this.regionData.y / this.imageData.resize );
		var width = Math.ceil( this.regionData.width / this.imageData.resize );
		var height = Math.ceil( this.regionData.height / this.imageData.resize );

		if( width < this.opt._targetSize.width ) width = this.opt._targetSize.width;
		if( height < this.opt._targetSize.height ) height = this.opt._targetSize.height;

		if( x + width > this.imageData._width ){ x -= this.imageData._width - width; }
		if( y + height > this.imageData._height ){ y -= this.imageData._height - height; }

		if(x < 0 || y < 0)
		{
			alert('Error: Out of range.');
		}

		var data = { x : x, y : y, width : width, height : height, _token: this._token() };

		switch(this.opt.mode)
		{
			case 'draw':
				break;
			case 'scroll':
				data.x *= -1;
				data.y *= -1;
				break;
		}

		if(window.console) console.log( 'XY[' + x + 'x' + y + "], WH[" + width + 'x' + height + ']' );

		return data;
	},
	cropIt : function(cb)
	{
		if(this.cropping) return;
		this.cropping = true;

		var data = {
			url : this.opt.url,
			type : 'POST',
			dataType : 'json',
			context : this,
			data : this.collectCropData()
		};

		$.ajax(data)
		.done(function(data,textStatus,xhr){

			if(this.ajaxCheckStatus(data,xhr,this))
			{
				if( data.redirect ){
					window.location = data.result.redirect;
				}else if( data.file ){
					console.log(data.file);
				}
			}

		})
		.fail(this.ajaxFailFunction)
		.always(function(){
			this.cropping = false;
			( typeof cb === 'function' ) && cb();
		});
	},
	traceRegionData : function(regionData)
	{
		if(!this.opt.debug) return;
		this.log( 'regionData :: x : ', regionData.x, '\n y : ', regionData.y, '\n w : ' + regionData.width + '\n h : ' + regionData.height);
	},
	log : function()
	{
		if(!this.opt.debug) return;

		var count = arguments.length;
		var args = [];
		for(var i=0;i<count;i++) args.push( arguments[i] );

		console.log(args.join(' '));
	}
});

// new cropper({
// 	_element : $('#cropper-{__id}'),
// 	data : {__data},
// 	url : '{__cropperURL}',
// 	imageSource : "{__imageURL}",
// 	size : { width : {__cropperWidth}, height : {__cropperHeight} },
// 	targetSize : { width : {__width}, height : {__height} },
// 	regionBorderColor : '#0f0',
// 	aspect : {__aspect}{_#if:$mode},
// 	mode : '{__mode}'{/if}
// });