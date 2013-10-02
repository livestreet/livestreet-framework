/**
 * Flash загрузчик
 *
 * @module swfupload
 * 
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

var ls = ls || {};

ls.swfupload = (function ($) {

	this.swfu = null;
	this.swfOptions = {};

	this.initOptions = function() {

		this.swfOptions = {
			// Backend Settings
			upload_url: aRouter['photoset']+"upload",
			post_params: {'SSID':SESSION_ID, 'security_ls_key': LIVESTREET_SECURITY_KEY},

			// File Upload Settings
			file_types : "*.jpg;*.jpe;*.jpeg;*.png;*.gif;*.JPG;*.JPE;*.JPEG;*.PNG;*.GIF",
			file_types_description : "Images",
			file_upload_limit : "0",

			// Event Handler Settings
			file_queue_error_handler : this.handlerFileQueueError,
			file_dialog_complete_handler : this.handlerFileDialogComplete,
			upload_progress_handler : this.handlerUploadProgress,
			upload_error_handler : this.handlerUploadError,
			upload_success_handler : this.handlerUploadSuccess,
			upload_complete_handler : this.handlerUploadComplete,

			// Button Settings
			button_placeholder_id : "start-upload",
			button_width: 122,
			button_height: 30,
			button_text : '<span class="button">'+ls.lang.get('topic_photoset_upload_choose')+'</span>',
			button_text_style : '.button { color: #1F8AB7; font-size: 14px; }',
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_text_left_padding: 6,
			button_text_top_padding: 3,
			button_cursor: SWFUpload.CURSOR.HAND,

			// Flash Settings
			flash_url : PATH_FRAMEWORK_FRONTEND+'/js/vendor/swfupload/swfupload.swf',

			custom_settings : {
			},

			// Debug Settings
			debug: false
		};

		ls.hook.run('ls_swfupload_init_options_after',arguments,this.swfOptions);

	};

	this.loadSwf = function() {
		var f = {};

		f.onSwfobject = function(){
			if(window.swfobject && swfobject.swfupload){
				f.onSwfobjectSwfupload();
			}else{
				ls.debug('window.swfobject && swfobject.swfupload is undefined, load swfobject/plugin/swfupload.js');
				$.getScript(
					PATH_FRAMEWORK_FRONTEND+'/js/vendor/swfobject/plugin/swfupload.js',
					f.onSwfobjectSwfupload
				);
			}
		}.bind(this);

		f.onSwfobjectSwfupload = function(){
			if(window.SWFUpload){
				f.onSwfupload();
			}else{
				ls.debug('window.SWFUpload is undefined, load swfupload/swfupload.js');
				$.getScript(
					PATH_FRAMEWORK_FRONTEND+'/js/vendor/swfupload/swfupload.js',
					f.onSwfupload
				);
			}
		}.bind(this);

		f.onSwfupload = function(){
			this.initOptions();
			$(this).trigger('load');
		}.bind(this);


		(function(){
			if(window.swfobject){
				f.onSwfobject();
			}else{
				ls.debug('window.swfobject is undefined, load swfobject/swfobject.js');
				$.getScript(
					PATH_FRAMEWORK_FRONTEND+'/js/vendor/swfobject/swfobject.js',
					f.onSwfobject
				);
			}
		}.bind(this))();
	};


	this.init = function(opt) {
		if (opt) {
			$.extend(true,this.swfOptions,opt);
		}
		this.swfu = new SWFUpload(this.swfOptions);
		return this.swfu;
	};

	this.handlerFileQueueError = function(file, errorCode, message) {
		$(this).trigger('eFileQueueError',[file, errorCode, message]);
	};

	this.handlerFileDialogComplete = function(numFilesSelected, numFilesQueued) {
		$(this).trigger('eFileDialogComplete',[numFilesSelected, numFilesQueued]);
		if (numFilesQueued>0) {
			this.startUpload();
		}
	};

	this.handlerUploadProgress = function(file, bytesLoaded) {
		var percent = Math.ceil((bytesLoaded / file.size) * 100);
		$(this).trigger('eUploadProgress',[file, bytesLoaded, percent]);
	};

	this.handlerUploadError = function(file, errorCode, message) {
		$(this).trigger('eUploadError',[file, errorCode, message]);
	};

	this.handlerUploadSuccess = function(file, serverData) {
		$(this).trigger('eUploadSuccess',[file, serverData]);
	};

	this.handlerUploadComplete = function(file) {
		var next = this.getStats().files_queued;
		if (next > 0) {
			this.startUpload();
		}
		$(this).trigger('eUploadComplete',[file, next]);
	};

	return this;
}).call(ls.swfupload || {},jQuery);