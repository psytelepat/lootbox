'use strict';
var MinimalClass = require('./MinimalClass');

var __class = {
    instances : {},
    put : function(className,instance)
    {
        if(typeof this.instances[className] == 'undefined') this.instances[className] = ['null'];
        var id = this.instances[className].length;
        this.instances[className].push(instance);
        if( typeof instance.element != 'undefined' )
            if( typeof instance.element.attr == 'function')
                instance.element.attr(className,id);
            else
                instance.element.setAttribute(className,id);
        return id;
    },
    get : function(className,element)
    {
        if( typeof this.instances[className] != 'undefined' )
        {
            var id = ( typeof element.attr == 'function' ) ? parseInt(element.attr(className)) : parseInt(element.getAttribute(className));
            return ( typeof this.instances[className][id] != 'undefined' ) ? this.instances[className][id] : null;
        }
        return false;
    },
    rm : function(instance)
    {
        var id = instance._id || false;
        var className = instance.__className;
        if( id && className && ( typeof this.instances[className][id] != 'undefined' ) )
        {
            instance.element.removeAttr(className);
            this.instances[className][id] = null;
            return true;
        }

        return false;
    }
};

module.exports = MinimalClass.extend({
    __className : 'BaseClass',
    put : function(className)
    {
        if(!this.element || this.element.getAttribute(this.__className)) return false;
        this._id = __class.put(this.__className,this);
        return this._id;
    },
    rm : function()
    {
        return __class.rm(this);
    },
    cancelEvent : function(e)
    {
        var evt = e ? e : window.event;
        if(evt.stopPropagation){evt.stopPropagation();}
        if(evt.cancelBubble!=null){evt.cancelBubble=true;}
        if(typeof evt.preventDefault != 'undefined') evt.preventDefault(e);
    },
    _token : function() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }
});