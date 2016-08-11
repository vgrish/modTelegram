(function (window, document, $, modTelegramConfig) {

	var modTelegram = modTelegram || {};


	modTelegram.selector = {};


	modTelegram.setup = function () {

		modTelegram.timeout = 60000;

		modTelegram.prefix = modTelegramConfig.prefix || 'modtelegram-';

		modTelegram.classActive = 'modtelegram-active';
		modTelegram.classHidden = 'modtelegram-hidden';

		modTelegram.selector.helperForm = '.modtelegram-helper-form';
		modTelegram.selector.helperSubmit = '[type="submit"], [type="button"]';

		modTelegram.selector.helperWrapper = '.modtelegram-helper-wrapper';
		modTelegram.selector.helperButton = '.modtelegram-helper-button';
		modTelegram.selector.helperChat = '.modtelegram-helper-chat';
		modTelegram.selector.helperClose = '.modtelegram-helper-close';
		modTelegram.selector.helperChatWelcome = '.modtelegram-helper-chat-welcome';
		modTelegram.selector.helperChatInitialize = '.modtelegram-helper-chat-initialize';
		modTelegram.selector.helperChatBody = '.modtelegram-helper-chat-body';
		modTelegram.selector.helperChatInput = '.modtelegram-helper-chat-input';
		modTelegram.selector.helperChatMessage = '.modtelegram-helper-chat-input [name="message"]';

		modTelegram.helper.timestamp = 0;

		modTelegram.helper.config = {
			type: 'popup',
			template: 'base',
			position: 'rb',
			attach: false,
		};

		modTelegram.$doc = $(document);

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
					'<form class="modtelegram-helper-form">',
					'<input type="hidden" name="name" value="">',
					'<button type="submit" value="chat/initialize">start chat?</button>',
					'</form>',
					'</div>',

					'<div class="modtelegram-helper-chat-body {type} {template} {position}">',
					'</div>',

					'<div class="modtelegram-helper-chat-input modtelegram-hidden">',
					'<form class="modtelegram-helper-form">',
					'<textarea name="message" placeholder="enter message..."></textarea>',
					'<button type="submit" value="chat/sendmessage" style="display:none;">send</button>',
					'</form>',
					'</div>',

					'<div class="modtelegram-helper-chat-footer {type} {template} {position}">',
					'modtelegram',
					'</div>',
					'</div>'
				],
			},

			message: {
				text: [
					'<div class="modtelegram-helper-message modtelegram-from-{from}" id="modtelegram-message-{id}">',

					'<div class="modtelegram-helper-message-header">',
					'<span class="modtelegram-helper-message-data">{data}</span>',
					'</div>',

					'<div class="modtelegram-helper-message-body">',
					'<span class="modtelegram-helper-message-text">{message}</span>',
					'</div>',

					'<div class="modtelegram-helper-message-footer">',
					'<span class="modtelegram-helper-message-user"><small>{user_username}</small></span>',
					'</div>',

					'</div>'
				]
			},

			get: function (type, name, data) {
				name = name || modTelegram.helper.config.template;

				if (this[type] && this[type][name]) {

					template = this[type][name].join('');

					data = $.extend(true, data || {}, {
						'type': modTelegram.prefix + modTelegram.helper.config.type,
						'template': modTelegram.prefix + modTelegram.helper.config.template,
						'position': modTelegram.prefix + modTelegram.helper.config.position
					});

					for (var key in data) {
						template = template.replace(new RegExp('{' + key + '}', "g"), data[key]);
					}

					return template;
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

		action: function (form, button) {
			var action = $(button).prop('value');

			var formData = $(form).serializeArray();
			formData = modTelegram.tools.getDataFromSerializedArray(formData);

			$.ajax({
				type: 'POST',
				url: modTelegramConfig.actionUrl,
				dataType: 'json',
				data: $.extend({}, formData, {
					action: action,
					propkey: modTelegramConfig.propkey,
					ctx: modTelegramConfig.ctx
				}),
				async: true,
				timeout: modTelegram.timeout,
				beforeSend: function () {
					$(form).find(modTelegram.selector.helperSubmit).attr('disabled', 'disabled');
					return true;
				},
				success: function (r) {
					if (r.success) {
						$(form).find(modTelegram.selector.helperSubmit).removeAttr('disabled');

						switch (action) {
							case 'chat/initialize':
								modTelegram.tools.hide(modTelegram.selector.helperChatWelcome);
								modTelegram.tools.show(modTelegram.selector.helperChatBody);
								modTelegram.tools.show(modTelegram.selector.helperChatInput);

								modTelegram.helper.listener();
								break;
							case 'chat/sendmessage':
								modTelegram.tools.clear(modTelegram.selector.helperChatMessage);
								break;
							default:
								break;
						}

					}
					else {

						modTelegram.Message.error(r.message);
						if (!modTelegram.tools.empty(r.data)) {
							modTelegram.Message.error(r.data.error.join('<br>'));
						}
					}
				}
			}).done(function (answer) {

			}).fail(function (jqXHR, textStatus, errorThrown) {

			});

		},

		listener: function () {
			if (!!window.EventSource) {

				var listener = $.SSE(
					modTelegramConfig.actionUrl +
					'?action=chat/getmessage',
					{
						onOpen: function (e) {
							modTelegram.tools.log('Open connect');
						},
						onEnd: function (e) {
							modTelegram.tools.log('End connect');
						},
						onError: function (e) {
							modTelegram.tools.error('Could not connect');
						},
						onMessage: function (e) {
							var data = JSON.parse(e.data);
							modTelegram.helper.handleMessage(data, this);
							modTelegram.helper.handleHeader(data, this);
						},
						options: {
							forceAjax: false
						},
						headers: {
							data: JSON.stringify({
								propkey: modTelegramConfig.propkey,
								ctx: modTelegramConfig.ctx,
								timestamp: modTelegram.helper.timestamp,
							})
						},
						events: {}
					}, this);

				listener.start();
			} else {
				modTelegram.tools.error('Browser not EventSource');
			}
		},

		getTimestamp: function () {
			return modTelegram.helper.timestamp;
		},

		setTimestamp: function (timestamp) {
			return modTelegram.helper.timestamp = timestamp;
		},

		handleMessage: function (data) {
			if (modTelegram.tools.empty(data.messages)) {
				return;
			}

			var wrapper = $(modTelegram.selector.helperChatBody);
			if (!wrapper) {
				modTelegram.tools.error('wrapper is not defined');
				return false;
			}

			data.messages.filter(function (row) {
				if (row.timestamp > modTelegram.helper.timestamp) {
					row = $.extend(true, row || {}, data.user || {}, data.manager || {});
					wrapper.append(modTelegram.helper.template.get('message', row.type, row));
					modTelegram.helper.setTimestamp(row.timestamp);
				}
			});
		},


		handleHeader: function (data, listener) {
			if (modTelegram.tools.empty(listener.headers)) {
				return;
			}
			var $data = JSON.parse(listener.headers.data || '{}');
			$data.timestamp = modTelegram.helper.getTimestamp();

			listener.headers.data = JSON.stringify($data);
		}

	};


	modTelegram.initialize = function () {
		modTelegram.setup();

		if (!jQuery.SSE) {
			document.writeln('<script src="' + modTelegramConfig.assetsUrl + 'vendor/sse/jquery.sse.min.js"><\/script>');
		}

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

		modTelegram.$doc.on('submit', modTelegram.selector.helperForm, function (e) {
			modTelegram.helper.action(this, $(this).find(modTelegram.selector.helperSubmit)[0]);
			e.preventDefault();
			return false;
		});

		modTelegram.$doc.on('keydown', modTelegram.selector.helperForm, function (e) {
			if ((e.ctrlKey || e.metaKey) && (e.keyCode == 13 || e.keyCode == 10)) {
				modTelegram.helper.action(this, $(this).find(modTelegram.selector.helperSubmit)[0]);
				e.preventDefault();
				return false;
			}
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

		clear: function (selector) {
			var $this = $(selector);
			if (!$this) {
				return;
			}
			$this.val('');
		},

		empty: function (value) {
			return (typeof(value) == 'undefined' || value == 0 || value === null || value === false || (typeof(value) == 'string' && value.replace(/\s+/g, '') == '') || (typeof(value) == 'object' && value.length == 0));
		},

		getDataFromSerializedArray: function (arr) {
			var data = {};
			$.each(arr, function () {
				data[this.name] = this.value;

			});

			return data;
		}

	};


	modTelegram.initialize();
	window.modTelegram = modTelegram;


})(window, document, jQuery, modTelegramConfig);
