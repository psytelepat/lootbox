'use strict';
var __slice = Array.prototype.slice;

module.exports.querySelectorAll = function(val, el) {
  return el 
    ? __slice.call(el.querySelectorAll(val))
    : __slice.call(document.querySelectorAll(val));
};

module.exports.log = function(a)
{
  if(typeof window.console.log != 'function')
    window.console.log(a);
}

module.exports.bindEvent = function(elm, evt, callback) {
  evt = evt.split(' ');
  evt.map(function(evt){
    if(elm.addEventListener){
      elm.addEventListener(evt, callback, false);
    }else{
        elm.attachEvent("on"+evt, callback);
    }    
  });
};

module.exports.unbindEvent = function(elm, evt, callback) {
  evt = evt.split(' ');
  evt.map(function(evt){
    if(elm.removeEventListener){
      elm.removeEventListener(evt, callback, false);
    }else{
        elm.detachEvent("on"+evt, callback);
    }
  });
};

module.exports.create = function(className,appendTo,tagName,attr,style, html) {

  if(typeof className == 'undefined') className = false;
  if(typeof appendTo == 'undefined') appendTo = false;
  if(typeof tagName == 'undefined') tagName = 'DIV';
  if(typeof attr == 'undefined') attr = false;
  if(typeof style == 'undefined') style = false;
  if(typeof html == 'undefined') html = false;

  var elm = document.createElement(tagName);
  if(className) elm.className = className;
  if(appendTo) appendTo.appendChild(elm);
  if(attr)
    for(var key in attr)
      elm.setAttribute(key,attr[key]);
  if(style)
    for(var key in style)
      elm.style[key] = style[key];
  if(html)
    elm.innerHTML = html;
  return elm;
};

module.exports.htmlToElement = function(s) {
  var div = document.createElement('div');
  div.innerHTML = s;
  return div.childNodes[0];
}

module.exports.scrollTop = function() {
  var doc = document.documentElement,
      scrollTop = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
  return scrollTop;
};

module.exports.scrollTo = function(element, duration, easing, callback, returnDiff) {
  if(typeof duration == 'undefined') duration = 750;
  if(typeof easing == 'undefined') easing = 'ease';

  var self = this,
      doc = document.documentElement,
      scrollTop = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0),
      diff = element.getBoundingClientRect().top - ((window.innerWidth < 768) ? 50 : 0 );

  if(returnDiff) return diff;

  if(diff) {
    this.animate(duration, easing, function(percent) {
      window.scrollTo(0, scrollTop + (diff * percent));
      if(percent >= 1) self.cb(callback);
    });
    return duration;
  }
  return 0;
};

module.exports.insertAfter = function(elem, refElem) {
  var parent = refElem.parentNode;
  var next = refElem.nextSibling;
  if (next) {
    return parent.insertBefore(elem, next);
  } else {
    return parent.appendChild(elem);
  }
}

module.exports.evalHTML = function(string) {
  var cnt = document.createElement('DIV');
  cnt.innerHTML = string;
  return cnt.firstChild;
}

module.exports.transitionEndEventName  = function() {
  var i,
    undefined,
    el = document.createElement('div'),
    eventNames = {
      'transition':'transitionend',
      'OTransition':'otransitionend',
      'MozTransition':'transitionend',
      'WebkitTransition':'webkitTransitionEnd',
      'msTransition' : 'MSTransitionEnd'
    };

  for (i in eventNames) {
    if (eventNames.hasOwnProperty(i) && el.style[i] !== undefined) {
      return eventNames[i];
    }
  }
};

module.exports.animationEndEventName  = function() {
  var i,
    undefined,
    el = document.createElement('div'),
    eventNames = {
      'animation':'animationend',
      'OAnimation':'oAnimationEnd',
      'WebkitAnimation':'webkitAnimationEnd',
      'MozAnimation':'mozAnimationRnd',
      'msAnimation':'MSAnimationEnd'
    };

  for (i in eventNames) {
    if (eventNames.hasOwnProperty(i) && el.style[i] !== undefined) {
      return eventNames[i];
    }
  }
};

module.exports.css = function(el, css) {
  var k, v;
  for(k in css) {
    v = css[k];

    switch(k) {
      case 'top':
      case 'left':
      case 'right':
      case 'bottom':
      case 'width':
      case 'height':
        v += 'px';
        break;
    }

    el.style[k] = v;
  }
};

module.exports.hasClass = function(el, className) {
  return el.className.indexOf(className) === -1 ? false : true;
};

module.exports.addClass = function(el, className) {
  if (!this.hasClass(el, className)) el.className += ' ' + className;
};

module.exports.removeClass = function(el, className, accurate) {
  if(typeof accurate == 'undefined') accurate = true;

  if(!this.hasClass(el, className)) return;
  if(!accurate){
    el.className = el.className.replace(className, '');  
  }else{
    var newClassNames = [];
    var classNames = el.className.split(' ');
    classNames.forEach(function(item,i){
      if( item.length && item != className )
        newClassNames.push( item );
    });
    el.className = newClassNames.join(' ');
  }
};

module.exports.addClasses = function(el, classNames) {
  var self = this;
  classNames.forEach(function(className,i){ self.addClass(el, className); });
};

module.exports.removeClasses = function(el, classNames) {
  var self = this;
  classNames.forEach(function(className,i){ self.removeClass(el, className); });
};

module.exports.ajaxGet = function(url, data, callback) {
  var req = new XMLHttpRequest(), params = data;
  if(typeof data == 'object') {
    params = [];
    for(var k in data) params.push(k+'='+encodeURIComponent(data[k]));
    params = params.join('&');
    url += '?' + params;
  }
  req.open('GET', url, true);
  req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  req.setRequestHeader('Accept', 'application/JSON');
  req.onreadystatechange = function(){
    if (req.readyState !== 4) return void 0;
    callback(req.responseText);
  };
  req.send();
};

module.exports.ajaxPost = function(url, data, callback) {
  var req = new XMLHttpRequest(), params = data;
  if(typeof data == 'object') {
    params = [];
    for(var k in data) params.push(k+'='+encodeURIComponent(data[k]));
    params = params.join('&');
  }
  req.open('POST', url, true);
  req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  req.setRequestHeader('Accept', 'application/JSON');
  // if(window.csrfToken) req.setRequestHeader('X-CSRF-Token', window.csrfToken);
  req.onreadystatechange = function(){
    if (req.readyState !== 4) return void 0;
    callback(req.responseText);
  };
  req.send(params);
};

module.exports.cb = function(cb,data) {
  if( typeof cb != 'function' ) return false;
  if( typeof data == 'undefined' ) data = false;
  return cb( data );
};

module.exports.xlink = function( xlink, appendTo ) {
  var svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
  svg.innerHTML = '<use xlink:href="#' + xlink + '"></use>';
  if(appendTo) appendTo.appendChild(svg);
  return svg;
}

module.exports.translit = function(txt,removeSpecialChars,removeTags) {
  if(typeof removeSpecialChars == 'undefined') removeSpecialChars = true;
  if(typeof removeTags == 'undefined') removeTags = true;

  var reg,
      translitRules = {
          src : [' ','А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ъ','Ы','Э','Ю','Я','а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ъ','ы','э','ю','я','\'','"','`','«','»','‘','’','“','”',':','/'],
          trg : ['-','a','b','v','g','d','e','yo','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','kh','c','ch','sh','sh','','','i','e','u','ya','a','b','v','g','d','e','yo','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','kh','c','ch','sh','sh','y','','i','e','u','ya','','','','','','','','','','',''],
          remove : ['`','~','!','@','#','$','%','^','&','*','(',')','[',']',':',';','\'','"','.',',','|','/','\\','?','<','>','{','}','=','+']
      };

  txt = txt.replace(/\?/g,"");
  txt = txt.replace(/\!/g,"");
  for(var i=0;i<translitRules.src.length;i++)
  {
    reg = new RegExp(translitRules.src[i],"g");
    txt = txt.replace(reg,translitRules.trg[i]);
  }

  if(removeTags)
  {
    reg = new RegExp("<[^>]*>","g");
    txt = txt.replace(reg,'');
  }

  if(removeSpecialChars)
  {
    for(var i=0;i<translitRules.remove.length;i++)
    {
      reg = new RegExp("\\"+translitRules.remove[i],"g");
      txt = txt.replace(reg,'');
    }
  }

  return txt;
}