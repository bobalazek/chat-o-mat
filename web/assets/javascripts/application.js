var Application = function () {
	var initialized = false;

	return {
		initialize: function()
		{
			// Some stuff here
			jQuery(document).ready( function() {
				Application.tooltipsInitialize();
				Application.timeAgoInitialize();
				Application.chatInitialize();

                jQuery('#preloader').fadeOut(); // Hide preloader, when everything is ready...

                initialized = true;
                console.log('Application Initialized');
            });
		},
		tooltipsInitialize: function() {
			jQuery('[data-toggle="tooltip"]').tooltip();
		},
		timeAgoInitialize: function() {
			function updateTime() {
				var now = moment();

				jQuery('time.time-ago').each( function() {
					var time = moment(jQuery(this).attr('datetime'));

					jQuery(this).text(time.from(now));
				});
			}

			updateTime();

			setInterval(updateTime, 10000);
		},
		chatInitialize: function() {
			var currentDate = new Date();
			var currentDateMinus12Hours = currentDate.setHours(
				currentDate.getHours() - 12
			);

			// To-Do: Check for before and after variable
			//   (on global scope, from the template)
			var before = moment(currentDateMinus12Hours).unix();
			var after = moment().unix();

			initializeTriggers();
			setHeights();
            autosize(jQuery('textarea'));

			// Nanoscroller
            jQuery('#chat-sidebar, #chat-messages-wrapper').nanoScroller();

	        jQuery(window).resize( function() {
	            setHeights();
	        });

			jQuery(window).load( function() {
				setHeights();
				jQuery('#chat-messages-wrapper').nanoScroller({ scroll: 'bottom' });
			});

            // Pjax
            jQuery(document).pjax(
                '#chat-sidebar .list-group a, .channel-link',
                '#chat-content',
                {
                    fragment: '#chat-content',
                }
            );

            jQuery(document).on(
				'submit',
				'#chat-form-wrapper form',
				function(event) {
	                jQuery.pjax.submit(
	                    event,
	                    '#chat-content',
	                    {
	                        fragment: '#chat-content',
	                    }
	                );

					jQuery('#chat-messages-wrapper').nanoScroller({ scroll: 'bottom' });
            	}
			);

            jQuery(document).on('pjax:complete', function(event) {
				initializeTriggers();
                setHeights();
                autosize(jQuery('textarea'));
                jQuery('#chat-sidebar, #chat-messages-wrapper').nanoScroller();
				jQuery('#chat-messages-wrapper').nanoScroller({ scroll: 'bottom' });

				Application.tooltipsInitialize();
				Application.timeAgoInitialize();

            });

			// Intervals & timeouts
	        setInterval( function() {
	        	setHeights();
	        }, 500);

			setInterval( function() {
				// TO-DO: Must be fixed first. The reload forces the input / texarea
				//   to lose focus, which is very annoying.
				/* jQuery.pjax.reload('#chat-sidebar-users', {
					fragment: '#chat-sidebar-users',
				});*/
			}, 5000);

			setTimeout( function() {
				jQuery('#flash-messages .alert').fadeOut();
			}, 2000);

	        function setHeights() {
	            var height = jQuery(window).outerHeight() - jQuery('#header').outerHeight() - jQuery('#footer').outerHeight();
				var chatMessagesWrapperHeight = height - jQuery('#chat-form-wrapper').outerHeight() - jQuery('#footer').outerHeight();

	            jQuery('#chat-sidebar').css('height', height);
	            jQuery('#chat-messages-wrapper').css('height', chatMessagesWrapperHeight);
	        }

			var beforeFetchedLast = moment().unix();

			function fetchNewMessages(type, time) {
				if(type == 'after') {
					jQuery.get('?after=' + time, function(response) {
						var html = jQuery(response);
						var messages = html.find('.chat-message');
						var existingMessages = jQuery(document).find('.chat-message');
						var existingMessagesIds = [];

						if(messages.length) {
							if(existingMessages.length) {
								for(var i = 0; existingMessages.length > i; i++) {
									var existingMessage = existingMessages[i];

									existingMessagesIds.push(
										parseInt(
											existingMessage.id.replace('chat-message-', '')
										)
									)
								}
							}

							jQuery('#chat-messages-wrapper').find('.alert').fadeOut();

							var addedNewMessage = false;

							for(var i = 0; messages.length > i; i++) {
								var message = messages[i];
								var messageId = parseInt(
									message.id.replace('chat-message-', '')
								);

								if(jQuery.inArray(messageId, existingMessagesIds) == -1) {
									jQuery('#chat-messages').append(message);

									addedNewMessage = true;
								}
							}

							if(addedNewMessage) {
								jQuery('#chat-messages-wrapper').nanoScroller({ scroll: 'bottom' });

								Application.tooltipsInitialize();
								Application.timeAgoInitialize();
							}
						}

						after = moment().unix();
					});
				} else if(type == 'before') {
					jQuery('#chat-messages').prepend(
						'<div id="chat-messages-loading" class="alert alert-info text-center">' +
							'Loading...' +
						'</div>'
					);

					jQuery.get('?before=' + time, function(response) {
						jQuery('#chat-messages-loading').slideUp();

						var html = jQuery(response);
						var messages = html.find('.chat-message');

						if(messages.length) {
							var newMessagesHeight = 0;

							jQuery('#chat-messages').prepend('<hr />');

							for(var i = messages.length; i--; ) {
								var message = jQuery(messages[i]);

								message.css('opacity', 0.1);

								jQuery('#chat-messages').prepend(message);
								newMessagesHeight += jQuery(message).outerHeight(true);
							}

							var secondFirstMessage = jQuery('#chat-messages .chat-message.first').eq(1);

							jQuery('#chat-messages-wrapper').nanoScroller({
								scrollTo: secondFirstMessage,
							});

							jQuery('#chat-messages-no-messages-alert').slideUp();

							setTimeout( function() {
								jQuery('#chat-messages .chat-message').animate({
									'opacity': 1
								});
							}, 2000)

							Application.tooltipsInitialize();
							Application.timeAgoInitialize();
						} else {
							jQuery('#chat-messages').prepend(
								'<div id="chat-messages-loading" class="alert alert-info text-center">' +
									'No more posts...' +
								'</div>'
							);

							jQuery('#chat-messages-load-more-button').slideUp();

							setTimeout( function() {
								jQuery('#chat-messages-loading').slideUp();
							}, 2000);
						}

						var currentDate = new Date(before*1000);
						var currentDateMinus12Hours = currentDate.setHours(
							currentDate.getHours() - 12
						);

						before = moment(currentDateMinus12Hours).unix();

						beforeFetchedLast = moment().unix();
					});
				}

				jQuery('#chat-messages table').addClass('table');
			}

			setInterval(function() {
				fetchNewMessages('after', after);
			}, 2000);

			function initializeTriggers() {
				var currentDate = new Date();
				var currentDateMinus12Hours = currentDate.setHours(
					currentDate.getHours() - 12
				);

				// To-Do: Check for before and after variable
				//   (on global scope, from the template)
				before = moment(currentDateMinus12Hours).unix();

				jQuery('#chat-messages-wrapper').on('scrolltop', function() {
					fetchNewMessages('before', before);
				});

				jQuery('#chat-messages-load-more-button').on('click', function() {
					fetchNewMessages('before', before);
				});

				jQuery('#chat-sidebar .list-group a').on('click', function() {
					jQuery('#chat-sidebar .list-group a').removeClass('active');
					jQuery(this).addClass('active');
				});

				jQuery('.channel-link').on('click', function() {
					var channel = jQuery(this).attr('data-channel');
					jQuery('#chat-sidebar .list-group a').removeClass('active');

					jQuery('#chat-sidebar .list-group a[data-channel="'+channel+'"]').addClass('active');
				});

				jQuery('#chat-messages table').addClass('table');

				// Autocomplete
				jQuery('#chat-form-wrapper textarea').textcomplete([
					{
						match: /\B:([\-+\w]*)$/,
				        search: function (term, callback) {
				            callback(jQuery.map(emojiKeys, function (emoji) {
				                return emoji.indexOf(term) === 0 ? emoji : null;
				            }));
				        },
						replace: function (emoji) {
							return emoji + ' ';
						},
						template: function (value) {
							var imageUrl = emoji[value].image.url;
				            return '<span class="emoji-icon"' +
								'style="background-image: url(\'' +
								imageUrl + '\');" /></span> :' +
								value +
								': '
							;
				        },
				        replace: function (value) {
				            return ':' + value + ': ';
				        },
				        index: 1,
					},
					{
					    match: /\B@(\w*)$/,
					    search: function (term, callback) {
					        callback(jQuery.map(usersKeys, function (mention) {
					            return mention.indexOf(term) === 0 ? mention : null;
					        }));
					    },
						template: function (value) {
							var imageUrl = users[value].image.url;
				            return '<span class="emoji-icon"' +
								'style="background-image: url(\'' +
								imageUrl + '\');" /></span> ' +
								value
							;
				        },
					    index: 1,
					    replace: function (mention) {
					        return '@' + mention + ' ';
					    }
					},
					{

					    match: /\B#(\w*)$/,
					    search: function (term, callback) {
					        callback(jQuery.map(channelsKeys, function (channel) {
					            return channel.indexOf(term) === 0 ? channel : null;
					        }));
					    },
						template: function (value) {
				            return '#' + value;
				        },
					    index: 1,
					    replace: function (channel) {
					        return '#' + channel + ' ';
					    }
					},
				], {
					className: 'textcomplete-dropdown',
					maxCount: 20,
					placement: 'top',
				});
			}
		},
	}
}();
