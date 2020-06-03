'use strict';
var lib = require('./lib/index'),
    ContentBlockController = require('./ContentBlock/ContentBlockController');
lib.querySelectorAll('.js-content-block-controller').forEach(function(elm,i){ new ContentBlockController({ _element : elm }); });