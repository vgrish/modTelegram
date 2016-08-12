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

		modTelegram.dataBleep = "data:audio/mp3;base64,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///////////////////9AJptuSSySXNupuQBAzGDNYi80v1z4dkrcEAyXzDAQ5WMXP2AiorfR3uFlyZtVW4ywVWFaxNioVM17QFBHw2QVip2W0iPfUHZZBKG540kjXn6AnaVl/qI345X/1f7epj/UccZ7WIvlyCsO/Xdzqdf+ve18zb0r9ptTfvlL0vua/Gs5hnYKVbmluZvX0r+ahcw84Aezcp0s5xVl6glpJuONyuOSifAHQ6pWUwhNjibY0FleqVQwkVFR0DLtISVImSFU0KSJNksKcshMliaeKuRNXktjYNQ9yQakA2EMFR4NUFrWrKFnWBa9qYaK0VYqio0rB0mypRxJrNNrRRy01tYrRTiqFQdIrDSKwsMSa1k//uQwPGAGpmjOaw9DbJsqqU1h7G1mrRVrTWtrDSKoc7OsNKwzNNFNNNRVrTSqLBzrDSsmwVRBKX/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////tklJKU1S2AoNQaBmHohDyQCuv5E8tOio0HypwuojYnBNVJdg0KQsMh4aTSTSTSTODR0ojYaZld/KIkhIEBiQIWYTQuCzSj4WicaUz1NGnAYEKAzD4uHZni0UjTiy2dnOLKLLKPi8392vKk4sootnZ2LKOLMtBc0/+VJxpRZZRZlw7FllXGzTsztNGnFlFFlXFw7M8XlSU9T///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////uQwP+AHoGTK6elDapfs9kwxJm1//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//uQwP+AMYABLgAAACAAACXAAAAEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA";

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
					'<span class="modtelegram-helper-message-user">{user_username}</span>',
					'<span class="modtelegram-helper-message-data">{data}</span>',
					'</div>',

					'<div class="modtelegram-helper-message-body">',
					'<span class="modtelegram-helper-message-text">{message}</span>',
					'</div>',

					'<div class="modtelegram-helper-message-footer">',
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

								modTelegram.helper.listener.init();
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

		listener: {
			source: null,

			init: function () {
				if (!window.EventSource) {
					modTelegram.tools.error('Browser not EventSource');
					return;
				}

				if (!this.source) {
					this.source = new EventSource(modTelegramConfig.actionUrl +
							'?action=chat/getmessage'+
							'&propkey='+ modTelegramConfig.propkey +
							'&ctx='+ modTelegramConfig.ctx +
							''
					);
				}

				this.source.onerror = function(e) {
					if (this.readyState == EventSource.CONNECTING) {
						modTelegram.tools.log('reconect');
					}
				};

				this.source.onmessage = function(e) {
					var data = JSON.parse(e.data);
					modTelegram.helper.handleMessage(data, this);
				};

			},
		},


		getTimestamp: function () {
			return modTelegram.helper.timestamp;
		},

		setTimestamp: function (timestamp) {
			return modTelegram.helper.timestamp = timestamp;
		},

		scrollMessage: function () {
			var wrapper = $(modTelegram.selector.helperChatBody);
			var h = wrapper[0].scrollHeight;
			wrapper.scrollTop(h);
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

					switch (row.from) {
						case 'user':
							row = $.extend(true, row || {}, data.user || {});
							break;
						case 'manager':
							row = $.extend(true, row || {}, data.manager || {});
							break;
					}

					wrapper.append(modTelegram.helper.template.get('message', row.type, row));

					modTelegram.helper.scrollMessage();
					modTelegram.helper.setTimestamp(row.timestamp);
					modTelegram.tools.bleep();
				}
			});
		},

	};


	modTelegram.initialize = function () {
		modTelegram.setup();
		modTelegram.helper.create();

		modTelegram.$doc.on('click touchend', modTelegram.selector.helperButton, function (e) {
			modTelegram.tools.hide(modTelegram.selector.helperButton);
			modTelegram.tools.show(modTelegram.selector.helperChat);
			modTelegram.helper.scrollMessage();
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
			if (
				(e.ctrlKey || e.metaKey) && (e.keyCode == 13 || e.keyCode == 10)
				||
				(e.keyCode == 13 || e.keyCode == 10)
			) {
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
		},

		bleep: function () {
			if (!modTelegram.bleep) {
				modTelegram.bleep = new Audio(modTelegram.dataBleep);
			}
			modTelegram.bleep.play();
		}

	};


	modTelegram.initialize();
	window.modTelegram = modTelegram;


})(window, document, jQuery, modTelegramConfig);
