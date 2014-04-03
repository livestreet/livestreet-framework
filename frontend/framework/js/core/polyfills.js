/**
 * Bind
 */
Function.prototype.bind = function(context) {
	var fn = this;

	if (jQuery.type(fn) != 'function') {
		throw new TypeError('Function.prototype.bind: call on non-function');
	};

	if (jQuery.type(context) == 'null') {
		throw new TypeError('Function.prototype.bind: cant be bound to null');
	};

	return function() {
		return fn.apply(context, arguments);
	};
};