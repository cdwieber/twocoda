(function( $, document ) {
	var jck_sfr = {

		/**
		 * Set up cache with common elements and vars.
		 */
		cache: function() {
			jck_sfr.vars = {};

			jck_sfr.vars.vote_button_selector = '[data-jck-sfr-vote]';
			jck_sfr.vars.button_status_classes = {
				'voting': 'jck-sfr-vote-button--voting',
				'voted': 'jck-sfr-vote-button--voted'
			};
			jck_sfr.vars.toggle_button_selector = '[data-jck-sfr-toggle]';
			jck_sfr.vars.toggle_user_type_selector = '[data-jck-sfr-toggle-submission-user-type]';

			jck_sfr.els = {};

			jck_sfr.els.container = $( '.jck-sfr-container' );
			jck_sfr.els.filters = $( '.jck-sfr-filters' );
			jck_sfr.els.submission_form = {
				'form': $( '.jck-sfr-form--submission' ),
				'title': $( '.jck-sfr-form--submission .jck-sfr-form__title' ),
				'reveal': $( '.jck-sfr-form--submission .jck-sfr-form__reveal' ),
				'loader': $( '.jck-sfr-search-field__icon--loader' ),
				'clear': $( '.jck-sfr-search-field__icon--clear' ),
				'choices': {
					'container': $( '.jck-sfr-form--submission .jck-sfr-form__choices' ),
					'count': $( '.jck-sfr-form--submission .jck-sfr-form__choices-count' ),
					'vote': $( '.jck-sfr-form--submission .jck-sfr-form__choices-vote' ),
					'or': $( '.jck-sfr-form--submission .jck-sfr-form__choices-or' ),
					'post': $( '.jck-sfr-form--submission .jck-sfr-form__choices-post' )
				}
			};
			jck_sfr.els.loop = {
				'container': $( '.jck-sfr-content' ),
			};
		},

		/**
		 * Run on doc ready.
		 */
		on_ready: function() {
			jck_sfr.cache();
			jck_sfr.setup_vote_buttons();
			jck_sfr.setup_toggle_buttons();
			jck_sfr.setup_toggle_user_type();
			jck_sfr.setup_submission_form();
		},

		/**
		 * Setup vote buttons.
		 */
		setup_vote_buttons: function() {
			$( document ).on( 'click', jck_sfr.vars.vote_button_selector, function() {
				var $button = $( this );

				if ( jck_sfr.is_voting( $button ) ) {
					return;
				}

				if ( jck_sfr.has_user_voted( $button ) ) {
					jck_sfr.update_vote_count( $button, 'remove' );
					return;
				}

				jck_sfr.update_vote_count( $button, 'add' );
			} );
		},

		/**
		 * Update vote count.
		 *
		 * @key $button
		 * @key type add|remove
		 */
		update_vote_count: function( $button, type ) {
			var $button_text = $button.text(),
				$button_class = $button.attr( 'class' );

			jck_sfr.add_button_status_class( $button, 'voting' );
			$button.text( jck_sfr_vars.il8n.voting + '...' );

			var post_id = jck_sfr.get_post_id( $button ),
				$badge = $button.closest( '.jck-sfr-vote-badge' ),
				$votes_counter = $badge.find( '.jck-sfr-vote-badge__count strong' ),
				$votes_text = $badge.find( '.jck-sfr-vote-badge__count span' ),
				data = {
					'action': 'jck_sfr_update_vote_count',
					'post_id': post_id,
					'type': type,
					'nonce': jck_sfr_vars.nonce
				};

			$.post( jck_sfr_vars.ajax_url, data, function( response ) {
				if ( ! response.success ) {
					jck_sfr.display_notice( response.message, 'error' );
					$button.attr( 'class', $button_class ).text( $button_text );
					return;
				}

				if ( type === 'add' ) {
					$button.text( jck_sfr_vars.il8n.voted );
					jck_sfr.add_button_status_class( $button, 'voted' );
					$votes_counter.text( response.votes );
					$votes_text.text( response.votes_wording );
					return;
				}

				jck_sfr.remove_button_status_classes( $button );
				$button.text( jck_sfr_vars.il8n.vote );
				$votes_counter.text( response.votes );
				$votes_text.text( response.votes_wording );
			} );
		},

		/**
		 * Display notice.
		 *
		 * @key message
		 * @key type
		 */
		display_notice: function( message, type ) {
			alert( message );
		},

		/**
		 * Get post ID from button.
		 *
		 * @key $button
		 * @return {Number}
		 */
		get_post_id: function( $button ) {
			return parseInt( $button.data( 'jck-sfr-vote' ) );
		},

		/**
		 * Has user voted?
		 *
		 * @key $button
		 */
		has_user_voted: function( $button ) {
			return $button.hasClass( jck_sfr.vars.button_status_classes.voted );
		},

		/**
		 * Is voting in progress?
		 *
		 * @key $button
		 */
		is_voting: function( $button ) {
			return $button.hasClass( jck_sfr.vars.button_status_classes.voting );
		},

		/**
		 * Add button status classes.
		 *
		 * @key $button
		 * @key type
		 */
		add_button_status_class: function( $button, type ) {
			jck_sfr.remove_button_status_classes( $button );

			$button.addClass( jck_sfr.vars.button_status_classes[ type ] );
		},

		/**
		 * Remove button status classes.
		 *
		 * @key $button
		 */
		remove_button_status_classes: function( $button ) {
			$.each( jck_sfr.vars.button_status_classes, function( index, status_class ) {
				$button.removeClass( status_class );
			} );
		},

		/**
		 * Setup toggle buttons.
		 */
		setup_toggle_buttons: function() {
			$( document ).on( 'click', jck_sfr.vars.toggle_button_selector, function() {
				var $button = $( this ),
					$toggle = $( '.jck-sfr-js-toggle-' + $button.data( 'jck-sfr-toggle' ) );

				$toggle.toggle();
			} );
		},

		/**
		 * Setup toggle user type.
		 */
		setup_toggle_user_type: function() {
			$( document ).on( 'click', jck_sfr.vars.toggle_user_type_selector, function() {
				var $button = $( this ),
					type = $button.data( 'jck-sfr-toggle-submission-user-type' );

				$( '[name="jck-sfr-login-user-type"]' ).val( type );
			} );
		},

		/**
		 * Setup submission form.
		 */
		setup_submission_form: function() {
			if ( jck_sfr.els.submission_form.form.length <= 0 ) {
				return;
			}

			jck_sfr.els.submission_form.title.keypress( function( e ) {
				if ( e.which === 13 ) {
					e.preventDefault();
				}
			} );

			var timeout_id = null;

			jck_sfr.els.submission_form.title.keyup( function( e ) {
				clearTimeout( timeout_id );

				timeout_id = setTimeout( function() {
					jck_sfr.search_feature_requests( e.target.value );
				}, 500 );
			} );

			jck_sfr.els.submission_form.choices.post.on( 'click', function() {
				jck_sfr.reveal_submission_form();
				return false;
			} );

			$( '.jck-sfr-js-clear-search-field' ).on( 'click', function() {
				jck_sfr.els.submission_form.title.val('').keyup();
				$( this ).hide();
			} );
		},

		/**
		 * Search feature requests based on string.
		 *
		 * @key search
		 */
		search_feature_requests: function( search ) {
			jck_sfr.update_query_args( 'search', search );
			jck_sfr.toggle_loader( 'show' );

			var data = {
				'action': 'jck_sfr_search_feature_requests',
				'search': search,
				'nonce': jck_sfr_vars.nonce
			};

			$.extend( data, jck_sfr.get_url_parameters( window.location.href ) );

			$.post( jck_sfr_vars.ajax_url, data, function( response ) {
				if ( ! response.success && ! response.html ) {
					jck_sfr.display_notice( response.message, 'error' );
					jck_sfr.toggle_loader( 'hide' );
					return;
				}

				if ( ! response.success ) {
					jck_sfr.reveal_submission_form();
				} else {
					jck_sfr.hide_submission_form();
				}

				jck_sfr.els.loop.container.html( response.html );
				jck_sfr.replace_pagination( response.pagination );
				jck_sfr.toggle_filters( response );
				jck_sfr.toggle_choices( response );
				jck_sfr.toggle_loader( 'hide' );
			} );
		},

		/**
		 * Update a URL query arg.
		 *
		 * @param key
		 * @param value
		 */
		update_query_args: function( key, value ) {
			key = encodeURIComponent( key );

			var url = window.location.href,
				params = jck_sfr.get_url_parameters( url );

			if ( value.length > 0 ) {
				params[ key ] = value;
			} else {
				delete params[ key ];
			}

			url = url.split( '?' )[ 0 ];

			if ( $.param( params ).length > 0 ) {
				url += "?" + $.param( params );
			}

			window.history.pushState( { key: key, value: value }, document.title, url );
		},

		/**
		 * Get URL parameters.
		 *
		 * @param url
		 *
		 * @return object
		 */
		get_url_parameters: function( url ) {
			var result = {},
				searchIndex = url.indexOf( "?" );

			if ( searchIndex === - 1 ) {
				return result;
			}

			var sPageURL = url.substring( searchIndex + 1 ),
				sURLVariables = sPageURL.split( '&' );

			for ( var i = 0; i < sURLVariables.length; i ++ ) {
				var sParameterName = sURLVariables[ i ].split( '=' );
				result[ sParameterName[ 0 ] ] = sParameterName[ 1 ];
			}

			return result;
		},

		/**
		 * Replace pagination.
		 *
		 * @param pagination
		 */
		replace_pagination: function( pagination ) {
			if ( $( '.jck-sfr-pagination' ).length <= 0 ) {
				return;
			}

			$( '.jck-sfr-pagination' ).replaceWith( pagination );
		},

		/**
		 * Toggle filters.
		 *
		 * @param search
		 */
		toggle_filters: function( response ) {
			jck_sfr.els.filters.hide();

			if ( response.search.length <= 0 ) {
				jck_sfr.els.filters.show();
			}
		},

		/**
		 * Toggle choices.
		 *
		 * @param response
		 */
		toggle_choices: function( response ) {
			jck_sfr.els.submission_form.choices.vote.show();
			jck_sfr.els.submission_form.choices.or.show();
			jck_sfr.els.submission_form.choices.post.show();
			jck_sfr.els.submission_form.choices.count.text( response.count );
			jck_sfr.els.submission_form.choices.container.show();

			if ( response.count <= 0 || response.search.length <= 0 ) {
				jck_sfr.els.submission_form.choices.container.hide();
			}
		},

		/**
		 * Reveal submission form.
		 */
		reveal_submission_form: function() {
			jck_sfr.els.submission_form.choices.or.hide();
			jck_sfr.els.submission_form.choices.post.hide();
			jck_sfr.els.submission_form.reveal.show();
			jck_sfr.focus_submission_title();
		},

		/**
		 * Hide submission form.
		 */
		hide_submission_form: function() {
			jck_sfr.els.submission_form.reveal.hide();
		},

		/**
		 * Focus in submission title field.
		 */
		focus_submission_title: function() {
			var value = jck_sfr.els.submission_form.title.val();
			jck_sfr.els.submission_form.title.focus();
			jck_sfr.els.submission_form.title.val( '' ).val( value );
		},

		/**
		 * Toggle loader.
		 *
		 * @param visiblity
		 */
		toggle_loader: function( visiblity ) {
			if ( typeof visiblity === 'undefined' ) {
				return;
			}

			if ( visiblity === 'show' ) {
				jck_sfr.els.submission_form.clear.hide();
				jck_sfr.els.submission_form.loader.show();
			} else {
				jck_sfr.els.submission_form.loader.hide();

				if ( jck_sfr.els.submission_form.title.val().length > 0 ) {
					jck_sfr.els.submission_form.clear.show();
				}
			}
		}
	};

	$( document ).ready( jck_sfr.on_ready );
}( jQuery, document ));