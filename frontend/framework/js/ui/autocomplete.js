/**
 * Автокомплитер
 * Обертка для jQuery UI виджета autocomplete
 *
 * @module autocomplete
 * 
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

var ls = ls || {};

ls.autocomplete = (function ($) {
	/**
	 * Добавляет автокомплитер к полю ввода
	 */
	this.add = function(obj, sPath, multiple) {
		if (multiple) {
			obj.bind("keydown", function(event) {
				if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				source: function(request, response) {
					ls.ajax.load(sPath, { value: ls.autocomplete.extractLast(request.term) }, function(data){
						response(data.aItems);
					});
				},
				search: function() {
					var term = ls.autocomplete.extractLast(this.value);
					if (term.length < 2) {
						return false;
					}
				},
				focus: function() {
					return false;
				},
				select: function(event, ui) {
					var terms = ls.autocomplete.split(this.value);
					terms.pop();
					terms.push(ui.item.value);
					terms.push("");
					this.value = terms.join(", ");
					return false;
				}
			});
		} else {
			obj.autocomplete({
				source: function(request, response) {
					var params = {};
					params.value = ls.autocomplete.extractLast(request.term);
					
+          			ls.ajax.load(sPath, params, function(data){
						response(data.aItems);
					});
				}
			});
		}
	};

	this.split = function(val) {
		return val.split( /,\s*/ );
	};

	this.extractLast = function(term) {
		return ls.autocomplete.split(term).pop();
	};

	return this;
}).call(ls.autocomplete || {},jQuery);