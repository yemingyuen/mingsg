
;(function( $, window, document, undefined ) {

	var MINITWEETS_CONFIG = {
		usernameUrlBase: 'https://twitter.com/', 
		hashtagUrlBase: 'https://twitter.com/#!/search?q=%23', 
		replyUrlBase: 'https://twitter.com/intent/tweet?in_reply_to=', 
		retweetUrlBase: 'https://twitter.com/intent/retweet?tweet_id=', 
		favoriteUrlBase: 'https://twitter.com/intent/favorite?tweet_id='
	}

	, MINITWEETS_HELPERS = {

		htmlEscape: function( text ) {
			var HTML_ENTITIES = {
				'&': '&amp;',
				'>': '&gt;',
				'<': '&lt;',
				'"': '&quot;',
				"'": '&#39;'
			};
			return ( text + '' ).replace( /[&"'><]/g, function( character ) {
				return HTML_ENTITIES[ character ];
			});
		}, 

		toRelativeTime: function( timestamp ) {
			var delta = parseInt( ( new Date() - timestamp ) / 1000 );

			if( delta < 1 ) {
				return 'just now';
			} else if( delta < 60 ) {
				return delta + ' seconds ago';
			} else if( delta < 120 ) {
				return 'about a minute ago';
			} else if( delta < 2700 ) {
				return 'about ' + parseInt( delta / 60 ) + ' minutes ago';
			} else if( delta < 7200 ) {
				return 'about an hour ago';
			} else if( delta < 86400 ) {
				return 'about ' + parseInt( delta / 3600 ) + ' hours ago';
			} else if( delta < 172800 ) {
				return 'about a day ago';
			} else {
				return 'about ' + parseInt( delta / 86400 ) + ' days ago';
			}
		}, 

		linkEntities: function( text, entities ) {

			var urlEntities = ( entities.urls || [] ).concat( entities.media || [] );

			// Use the official twitter-text plugin if available
			if( typeof twttr !== 'undefined' && twttr.txt && $.isFunction( twttr.txt.autoLink ) ) {
				return twttr.txt.autoLink( text, {
					urlEntities: urlEntities
				});
			}

			// See http://daringfireball.net/2010/07/improved_regex_for_matching_urls
			var url_regexp = /\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?«»“”‘’]))/gi;
			var htmlEscape = this.htmlEscape;

			return text

				// Urls
				.replace( url_regexp, function( match ) {
					var url = /^[a-z]+:/i.test( match ) ? match : "http://" + match;
					var text = match;

					for( var i = 0; i < urlEntities.length; ++i ) {
						var entity = urlEntities[i];
						if( entity.url == url && entity.expanded_url ) {
							url = entity.expanded_url;
							text = entity.display_url;
							break;
						}
					}

					return "<a href=\"" + htmlEscape( url ) + "\">" + htmlEscape( text ) + "</a>";
				})

				// Mentions
				.replace( /(^|[\W])@(\w+)/gi, '$1@<a href="' + MINITWEETS_CONFIG.usernameUrlBase + '$2">$2</a>' )

				// Hashtags
				.replace( /(?:^| )[\#]+([\w\u00c0-\u00d6\u00d8-\u00f6\u00f8-\u00ff\u0600-\u06ff]+)/gi, ' <a href="' + MINITWEETS_CONFIG.hashtagUrlBase + '$1">#$1</a>' );
		}, 

		template: (function() {

			// By default, Underscore uses ERB-style template delimiters, change the
			// following template settings to use alternative delimiters.
			var settings = {
				evaluate: /<%([\s\S]+?)%>/g,
				interpolate: /<%=([\s\S]+?)%>/g,
				escape: /<%-([\s\S]+?)%>/g
			};

			// When customizing `templateSettings`, if you don't want to define an
			// interpolation, evaluation or escaping regex, we need one that is
			// guaranteed not to match.
			var noMatch = /.^/;

			// Certain characters need to be escaped so that they can be put into a
			// string literal.a
			var escapes = {
				'\\': '\\',
				"'": "'",
				'r': '\r',
				'n': '\n',
				't': '\t',
				'u2028': '\u2028',
				'u2029': '\u2029'
			};

			for (var p in escapes) {
				escapes[escapes[p]] = p;
			}

			var escaper = /\\|'|\r|\n|\t|\u2028|\u2029/g;
			var unescaper = /\\(\\|'|r|n|t|u2028|u2029)/g;

			return function (text, data, objectName) {
				settings.variable = objectName;

				// Compile the template source, taking care to escape characters that
				// cannot be included in a string literal and then unescape them in code
				// blocks.
				var source = "__p+='" + text
					.replace(escaper, function (match) {
						return '\\' + escapes[match];
					})
					.replace(settings.escape || noMatch, function (match, code) {
						return "'+\n_.escape(" + unescape(code) + ")+\n'";
					})
					.replace(settings.interpolate || noMatch, function (match, code) {
						return "'+\n(" + unescape(code) + ")+\n'";
					})
					.replace(settings.evaluate || noMatch, function (match, code) {
						return "';\n" + unescape(code) + "\n;__p+='";
					}) + "';\n";

				// If a variable is not specified, place data values in local scope.
				if (!settings.variable) {
					source = 'with(obj||{}){\n' + source + '}\n';
				}

				source = "var __p='';var print=function(){__p+=Array.prototype.join.call(arguments, '')};\n" + source + "return __p;\n";

				var render = new Function(settings.variable || 'obj', source);

				if (data) {
					return render(data);
				}

				var template = function (data) {
					return render.call(this, data);
				};

				// Provide the compiled function source as a convenience for build time
				// precompilation.
				template.source = 'function(' + (settings.variable || 'obj') + '){\n' + source + '}';

				return template;
			};
		})()
	}

	, MINITWEETS_DEFAULT_TEMPLATE = function(b){with(b||{})b=""+('<div class="twitter-header"><div class="twitter-avatar"><a href="'+user_url+'"><img src="'+avatar_normal+'" alt="'+user_screen_name+'" title="'+user_screen_name+'"></a></div><div class="twitter-info"><a href="'+user_url+'" class="twitter-name">'+user_name+'</a><a href="'+user_url+'" class="twitter-user">@'+user_screen_name+'</a></div></div><div class="twitter-text">'+text+'</div><a class="twitter-time" href="'+tweet_url+'">'+relative_time+'</a><div class="twitter-intents"><ul><li><a href="'+reply_url+'" title="Reply">Reply</a></li><li><a href="'+retweet_url+'" title="Retweet">Retweet</a></li><li><a href="'+favorite_url+'" title="Favorite">Favorite</a></li></ul></div>');return b}

	, MiniTweets = function( element, options ) {
		this.element = $( element );
		this.init( options || {} );
	};

	MiniTweets.prototype = {

		defaults: {
			username: null, 
			count: 1, 
			api: {
				host: 'api.twitter.com', 
				url: '/1.1/statuses/user_timeline'
			}, 
			entryPath: '/twitter/', 
			userParams: '', 
			template: null, 
			includeRaw: false, 
			loadingText: 'Loading Tweets...', 
			beforeAppend: $.noop, // function()
			afterAppend: $.noop // function()
		}, 

		init: function( options ) {
			this.options = $.extend( true, {}, this.defaults, options );
			this._makeRequest();
		}, 

		_makeRequest: function() {
			var t = this, 
				request = {
					type: 'post', 
					dataType: 'json', 
					url: this.options.entryPath, 
					data: $.extend(
						true, {}, 
						this._getRequestData(), 
						this.options.userParams
					), 
					beforeSend: function() {
						t.element.html( '<p class="minitweets-loading">' + t.options.loadingText + '</p>' );
					}
				};

			$.ajax( request )
				.done( $.proxy( this._handleResponse, this ) )
				.fail( function( xhr, status, error ) {
					t._appendErrors( error );
				});
		}, 

		_getRequestData: function() {
			return {
				request: {
					host: this.options.api.host, 
					url: this.options.api.url, 
					parameters: {
						screen_name: this.options.username, 
						count: this.options.count, 
						include_rts: true, 
						include_entities: true
					}
				}
			};
		}, 

		_handleResponse: function( json ) {

			if( json.hasOwnProperty( 'message' ) && json.message ) {
				console.log( json.message );
			}

			if( json.hasOwnProperty( 'response' ) && json.response ) {

				if( $.isArray( json.response ) ) {

					var tweets = this._extractTweets( json.response );

					if( $.isFunction( this.options.beforeAppend ) ) {
						this.options.beforeAppend.call( this.element, tweets );
					}

					this._appendTweets( tweets );

					if( $.isFunction( this.options.afterAppend ) ) {
						this.options.afterAppend.call( this.element, tweets );
					}

				} else if( json.response.hasOwnProperty( 'errors' ) ) {
					this._appendErrors( json.response.errors );
				}
			}

		}, 

		_extractTweets: function( tweets ) {

			var t = this, 
				avatarRegex = /_normal(\.[A-Za-z]+)$/;

			return $.map( tweets || [], function( tweet ) {

				if( ! $.isPlainObject( tweet ) || ! tweet.hasOwnProperty( 'id_str' ) ) {
					return;
				}
			
				var avatar = tweet.user.profile_image_url_https || tweet.user.profile_image_url, 
					user_url = MINITWEETS_CONFIG.usernameUrlBase + tweet.user.screen_name, 
					timestamp = Date.parse( tweet.created_at );

				var result = {
					id: tweet.id_str, 

					user_name: tweet.user.name, 

					user_screen_name: tweet.user.screen_name, 

					user_url: user_url, 

					avatar_normal: avatar, 

					avatar_bigger: avatar.replace( avatarRegex , '_bigger$1' ), 

					avatar_mini: avatar.replace( avatarRegex, '_mini$1' ), 

					avatar: avatar.replace( avatarRegex, '$1' ), 

					text: MINITWEETS_HELPERS.linkEntities( tweet.text, tweet.entities ), 

					timestamp: timestamp, 

					relative_time: MINITWEETS_HELPERS.toRelativeTime( timestamp ), 

					tweet_url: user_url + '/statuses/' + tweet.id_str, 

					reply_url: MINITWEETS_CONFIG.replyUrlBase + tweet.id_str, 

					retweet_url: MINITWEETS_CONFIG.retweetUrlBase + tweet.id_str, 

					favorite_url: MINITWEETS_CONFIG.favoriteUrlBase + tweet.id_str
				};

				if( t.options.includeRaw ) {
					result.raw = tweet;
				}

				return result;

			}).slice( 0, this.options.count );

		}, 

		_appendTweets: function( tweets ) {

			var tmpl = this.options.template ? MINITWEETS_HELPERS.template( this.options.template ) : MINITWEETS_DEFAULT_TEMPLATE;
			var result = $.map( tweets, function( tweet ) {
				try {
					return tmpl( tweet );
				} catch( err ) {
					console.log( '[MiniTweets]: ' + err.message );
				};
			});

			this.element.html( result );
		}, 

		_appendErrors: function( errors ) {
			this.element.html( '<p class="minitweets-error">An error has occured while fetching your tweets. Check your JavaScript console to see the error messages.</p>' );
			console.log( errors );
		}
	};

	$.fn.miniTweets = function( options ) {

		if( 'string' == typeof options ) {

			var i, len, instance, returnValue;
			var args = Array.prototype.slice.call( arguments, 1 );

			for( i = 0, len = this.length; i < len; i++ ) {

				instance = $.data( this[i], 'minitweets' );
				if( instance instanceof MiniTweets ) {
					if( '_' !== options[0] && $.isFunction( instance[options ] ) ) {
						returnValue = instance[options].apply( instance, args );
						if( 'undefined' !== typeof returnValue ) {
							return returnValue;
						}
					} else {
						console.error( 'The method MiniTweets.' + options + ' doesn\'t exists.' );
					}
				} else {
					if( 'destroy' != options ) {
						console.error( 'Calling MiniTweets.' + options + ' before initialization.' );
					}
				}
			}
		} else {
			this.each(function() {

				var instance = $.data( this, 'minitweets' );
				if( instance instanceof MiniTweets ) {
					return;
				}
				$.data( this, 'minitweets', new MiniTweets( this, options ) );
			});
		}

		return this;
	}

})( jQuery, window, document );
