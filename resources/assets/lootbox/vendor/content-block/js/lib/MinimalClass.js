'use strict';
var Class = require('class.extend');

module.exports = Class.extend({
    __className : 'MinimalClass',
    __put : false,
    init : function(opt)
    {
        this._id = 0;
        this._clid = false;
        this._inited = false;
        this.delegate = false;
        this.element = false;
        this.opt = {};

        this.pre(opt);
        if(this._pre) this._pre(opt);
        
        this.setOptions(opt);

        if(this.__put)
        {
            if(!this.put()) return false;
            this._clid = this.__className + this._id;
        }

        this.create();
    },
    create : function(){ this._inited = true; },
    pre : function(opt){},
    setOption : function(key,val)
    {
        if( key == 'element' ){
            this.element = val;
        }else if( key.substr(0,1) == '_' ){
            key = key.substr(1);
            this[key] = val;
        }

        this.opt[key] = val;
    },
    setOptions : function(opt)
    {
        if(typeof opt == 'undefined') return;
        for(var key in opt)
        {
            this.setOption(key,opt[key]);
        }
    },
    find : function(obj,needle)
    {
        var elm = obj.find(needle);
        return elm.length ? elm : false;
    },
    child : function(obj,needle)
    {
        var elm = obj.children(needle);
        return elm.length ? elm : false;
    },
    dumpObj : function(obj)
    {
        if(window.console)
        {
            for(var k in obj)
                console.log( k + ' = ' + obj[k] );
        }
    },
    log : function(str){
        if(window.console)
            window.console.log(str);
    }
});