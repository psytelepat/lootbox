var $ = require('jquery'),
	App = require('../../../../../../../resources/assets/js/lib/App'),
	Sortable = require('sortablejs');

window.jQuery = window.$ = $;

new App({ desktop: true });

var lib = require('../vendor/content-block/js/lib/index.js');
require('../vendor/redactor/redactor.js');
require('../vendor/content-block/js/content-block.js');

$R('.js-redactor',{
	formatting: ['h2','h3'],
	buttons: ['html', 'format', 'bold', 'italic', 'lists', 'link'],
	linkNewTab: true,
	linkTarget: '_blank'
});

$('.table.sortable tbody').each(function(i,elm){
	var order = $(elm).find('tr'),
		url = $('.rows-number-selection form').attr('action') + '/repos/',
		sortable = new Sortable.default(elm,{
			handle: '.js-repos-handle',
			onUpdate: function(e){
				$.ajax({
					url: url + $(order[e.oldIndex]).data('id'),
					data : {
						to: $(order[e.newIndex]).data('id'),
					},
					type: 'GET',
					dataType: 'json',
					context: this,
					beforeSend: function(){
						sortable.option("disabled", true);
					}
				})
				.done(function(resp){
					if( resp.error ){
						console.log(resp.message);
						return;
					}
				})
				.always(function(){
					sortable.option("disabled", false);
				})
			},
			onEnd: function(e){
				order = $(elm).find('tr');
			}
		});
});

var ttUploadGallery = require('../vendor/content-block/js/lib/ttUploadGallery.js');

$(document).ready(function(){

	if( typeof $.fn.tagsinput === 'function' )
	{
		$('.tagsinput').each(function(i,elm){
			var obj = $(elm),
				typeahead_source = obj.data('typeahead-source')
				data = {
					tagClass: 'label label-primary',
					confirmKeys: [13, 188]
				};

			if( typeahead_source && typeahead_source.length ){
				typeahead_source = typeahead_source.split(',');
				data.typeaheadjs = {
					name: 'tags',
					source: function(q, cb) {
						var matches = [], substringRegex;
						substrRegex = new RegExp(q, 'i');
						$.each(typeahead_source, function(i,str) {if(substrRegex.test(str)){matches.push(str);}});
						cb(matches);
					}
				};
				data.freeInput = true;
			}

			obj.tagsinput(data);
		})

		$('.bootstrap-tagsinput input').on('keypress', function(e){
			if (e.keyCode == 13){
				e.keyCode = 188;
				e.preventDefault();
			};
		});
	}

	if( typeof $.fn.iCheck === 'function' )
	{
	    $('.i-checks').iCheck({
	        checkboxClass: 'icheckbox_square-green',
	        radioClass: 'iradio_square-green',
	    });
	}

	$('.ttUploadGallery').each(function(i,elm){
		new ttUploadGallery({ _element: elm });
	});

	$('form[data-form-mode="create"]').each(function(i,elm){
		var title = $(elm).find('input[name=title]'),
			slug = $(elm).find('input[name=slug]');

		if( title.length && slug.length ){
			title.on('keyup change', function(){
				slug.val( lib.translit( title.val() ).toLowerCase() );
			});
		}
	});

	var datepickers = $('.js-datepicker .input-group.date');
	if( datepickers.length ){
		datepickers.datepicker({
		    todayBtn: "linked",
		    keyboardNavigation: false,
		    forceParse: false,
		    calendarWeeks: true,
		    autoclose: true,
		    format: 'dd.mm.yyyy',
		});
	}

	var clockpickers = $('.js-clockpicker');
	if( clockpickers.length ){ clockpickers.clockpicker(); }

	(function(){
		var translations = $('.js-translations');
		if( !translations.length ) return;

		var ajax_url = translations.data('url'),
			ajax_typo_url = translations.data('typo-url'),
			file = translations.data('file'),
			folder = translations.data('folder'),
			translation_busy = false,
			translation_inputs = [],
			save_all_btn = $('<button>Сохранить всё (<span class="js-changes-counter">0</span>)</button>').addClass('btn btn-primary btn-sm').css({
				position: 'fixed',
				bottom: 20,
				left: 20,
				zIndex: 100,
			})
			.attr('disabled',1)
			.appendTo( document.body )
			.click(save_all_changed);

		translations.find('input').each(function(i,elm){
			var obj = $(elm),
				save_btn = $('button.js-' + elm.name + '-save'),
				typo_btn = $('button.js-' + elm.name + '-typo'),
				item = {
					obj: obj,
					name: elm.name,
					save_btn: save_btn.attr('disabled',1),
					typo_btn: typo_btn,
					oldval: obj.val(),
					changed: false
				};

			obj.bind('keyup change blur',function(){
				var changed = ( item.obj.val() != item.oldval );
				if( item.changed != changed ){
					item.changed = changed;
					changed ? item.save_btn.removeAttr('disabled') : item.save_btn.attr('disabled',1);
					update_changes_counter();
				}
			});

			obj.bind('keyup keydown', function(e){
				if( e.keyCode == 13 ){
					e.preventDefault();
					e.stopPropagation();
					if( e.type == 'keyup' ){
						translation_update([item]);
					}
				}
			});

			translation_inputs.push(item);

			save_btn.click(function(e){
				e.preventDefault();
				e.stopPropagation();

				if( item.changed ){
					translation_update([item]);
				}
			});

			typo_btn.click(function(e){
				e.preventDefault();
				e.stopPropagation();
				translation_typo(item);
			});
		});

		function update_changes_counter(){
			var counter = 0;
			translation_inputs.forEach(function(item){ if( item.changed ){ counter++; } });
			$('.js-changes-counter').html(counter);
			counter ? save_all_btn.removeAttr('disabled') : save_all_btn.attr('disabled',1);
			return counter;
		}

		function save_all_changed(cb)
		{
			var items_to_update = [];
			translation_inputs.forEach(function(item){ if( item.changed ){ items_to_update.push( item ); } });
			translation_update(items_to_update);
		}

		function translation_typo(item,cb){
			if( translation_busy ) return;
			translation_busy = true;

			$.ajax({
				url: ajax_typo_url,
				type: 'POST',
				dataType: 'json',
				data: {
					text: item.obj.val(),
					_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
				}
			}).done(function(resp){
				if( resp.error ){
					console.log(resp.message);
					return;
				}
				item.obj.val( resp.result ).trigger('change');
			}).always(function(){
				translation_busy = false;
			});
		}

		function translation_update(items,cb) {
			if( translation_busy ) return;
			translation_busy = true;

			var data = {};
			data._token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

			items.forEach(function(item){ data[item.name] = item.obj.val(); });
			
			$.ajax({
				url: ajax_url,
				data: data,
				type: 'POST',
				dataType: 'json'
			}).done(function(resp){
				if( resp.error ){
					alert(resp.message);
					return;
				}

				items.forEach(function(item){
					item.oldval = item.obj.val();
					item.changed = false;
					item.save_btn.attr('disabled',1);
				});

				update_changes_counter();

				(typeof cb === 'function') && cb(items);
			}).always(function(){
				translation_busy = false;
			});
		}

	})();
});
