(function (window, document, $, modTelegramConfig) {

	var modTelegram = modTelegram || {};


	modTelegram.selector = {};


	modTelegram.setup = function () {

		modTelegram.timeout = 60000;

		modTelegram.prefix = modTelegramConfig.prefix || 'modtelegram-';

		modTelegram.classActive = 'modtelegram-active';
		modTelegram.classHidden = 'modtelegram-hidden';

		modTelegram.selector.helperWrapper = '.modtelegram-helper-wrapper';
		modTelegram.selector.helperButton = '.modtelegram-helper-button';
		modTelegram.selector.helperChat = '.modtelegram-helper-chat';
		modTelegram.selector.helperClose = '.modtelegram-helper-close';
		modTelegram.selector.helperChatWelcome = '.modtelegram-helper-chat-welcome';
		modTelegram.selector.helperChatInitialize = '.modtelegram-helper-chat-initialize';
		modTelegram.selector.helperChatBody = '.modtelegram-helper-chat-body';

		modTelegram.helper.config = {
			type: 'popup',
			template: 'base',
			position: 'rb',
			attach: false,
		};

		modTelegram.$doc = $(document);

	};


	modTelegram.chat = {
		initialize: function () {
			modTelegram.tools.log('initialize chatq 11');

			$.ajax({
				type: 'POST',
				url: modTelegramConfig.actionUrl,
				dataType: 'json',
				data: {
					action: 'chat/initialize',
					propkey: modTelegramConfig.propkey,
					ctx: modTelegramConfig.ctx
				},
				async: true,
				timeout: modTelegram.timeout,
				beforeSend: function () {
				},
				success: function (r) {
					if (r.success && r.data) {

						console.log(r.data);

						modTelegram.Message.info(r.message);

					} else if (!r.success) {
						modTelegram.Message.error(r.message);
					}
				}
			});

		},

	};


	modTelegram.helper = {
		template: {
			wrapper: {
				base: [
					'<div class="modtelegram-helper-wrapper modtelegram-active {type} {template} {position}">',
					'</div>'
				],
			},
			button: {
				base: [
					'<div class="modtelegram-helper-button modtelegram-active {type} {template} {position}">',
					'helper',
					'</div>'
				],
			},
			chat: {
				base: [
					'<div class="modtelegram-helper-chat modtelegram-hidden {type} {template} {position}">',
					'<div class="modtelegram-helper-chat-header {type} {template} {position}">',
					'helper',
					'<div class="modtelegram-helper-close {type} {template} {position}">x</div>',
					'</div>',

					'<div class="modtelegram-helper-chat-welcome {type} {template} {position}">',
					'<p>Welcome!</p>',
					'<button class="modtelegram-helper-chat-initialize">start chat?</button>',
					'</div>',

					'<div class="modtelegram-helper-chat-body {type} {template} {position}">',
					'</div>',
					'<div class="modtelegram-helper-chat-footer {type} {template} {position}">',
					'modtelegram',
					'</div>',
					'</div>'
				],
			},
			get: function (type) {
				if (this[type] && this[type][modTelegram.helper.config.template]) {
					return this[type][modTelegram.helper.config.template]
						.join('')
						.replace(new RegExp("{type}", "g"), modTelegram.prefix + modTelegram.helper.config.type)
						.replace(new RegExp("{template}", "g"), modTelegram.prefix + modTelegram.helper.config.template)
						.replace(new RegExp("{position}", "g"), modTelegram.prefix + modTelegram.helper.config.position)
				}

				return '';
			}
		},

		create: function () {
			this.config = $.extend(true, {}, this.config, modTelegramConfig.helper || {});

			if (this.config.type == 'embed' && !this.config.wrapper) {
				modTelegram.tools.error('wrapper is not defined');
				this.config.type = 'popup';
			}

			var wrapper;
			if (this.config.type == 'embed') {
				wrapper = $(this.config.wrapper);
			}
			else {
				wrapper = $(document.body);
			}

			if (!wrapper) {
				modTelegram.tools.error('wrapper is not defined');
				return false;
			}

			wrapper
				.append(modTelegram.helper.template.get('button'))
				.append(modTelegram.helper.template.get('chat'));

			console.log('-----');
			console.log(this.config);

		},

	};


	modTelegram.initialize = function () {
		modTelegram.setup();

		modTelegram.helper.create();

		modTelegram.$doc.on('click touchend', modTelegram.selector.helperButton, function (e) {
			modTelegram.tools.hide(modTelegram.selector.helperButton);
			modTelegram.tools.show(modTelegram.selector.helperChat);
			e.preventDefault();
			return false;
		});

		modTelegram.$doc.on('click touchend', modTelegram.selector.helperClose, function (e) {
			modTelegram.tools.hide(modTelegram.selector.helperChat);
			modTelegram.tools.show(modTelegram.selector.helperButton);
			e.preventDefault();
			return false;
		});

		modTelegram.$doc.on('click touchend', modTelegram.selector.helperChatInitialize, function (e) {
			modTelegram.tools.hide(modTelegram.selector.helperChatWelcome);
			modTelegram.tools.show(modTelegram.selector.helperChatBody);
			modTelegram.chat.initialize();
			e.preventDefault();
			return false;
		});

	};


	$(document).ready(function ($) {

	});


	modTelegram.Message = {
		initialize: function () {

		},
		success: function (message) {
			if (!modTelegram.tools.empty(message)) {
				alert(message);
			}
		},
		error: function (message) {
			if (!modTelegram.tools.empty(message)) {
				alert(message);
			}
		},
		info: function (message) {
			if (!modTelegram.tools.empty(message)) {
				alert(message);
			}
		}
	};
	
	
	modTelegram.tools = {
		log: function (msg) {
			console.log('modTelegram > ' + msg);
		},
		error: function (msg) {
			console.error('modTelegram > ' + msg);
		},

		hide: function (selector) {
			var $this = $(selector);
			if (!$this) {
				return;
			}
			$this.removeClass(modTelegram.classActive).addClass(modTelegram.classHidden);
		},

		show: function (selector) {
			var $this = $(selector);
			if (!$this) {
				return;
			}
			$this.removeClass(modTelegram.classHidden).addClass(modTelegram.classActive);
		},

		empty: function (value) {
			return (typeof(value) == 'undefined' || value == 0 || value === null || value === false || (typeof(value) == 'string' && value.replace(/\s+/g, '') == '') || (typeof(value) == 'object' && value.length == 0));
		},

		
	};


	modTelegram.initialize();
	window.modTelegram = modTelegram;


})(window, document, jQuery, modTelegramConfig);
