//https://raw.github.com/oleersoy/jquery-ui/selectmenu/ui/jquery.ui.input.js
/*
 * jQuery UI Button @VERSION
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Input
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.widget.js
 *
 * TODO
 * - Create a ui-input css class and replace ui-button with it.  The method _setStyle performs overrides of the button css
 * - Unit test and visual test implementation of the disabled parameter.
 * - Does the ":ui-input" selector magically work or does it need to be custom defined? 
 * - The options include some icon properties...add support for a left or right aligned "Field purpose indication" icon (For search, etc.)
 */
(function( $, undefined ) {

//STATIC VARIABLES
var	LEFT_RIGHT_PADDING_VALUE = 10,	
	baseClasses = "ui-button ui-widget ui-state-default ui-corner-all",
	stateClasses = "ui-state-hover ui-state-active ",
	hoverClass = "ui-state-hover",
	focusClass = "ui-state-focus",
	activeClass= "ui-state-active",

	formResetHandler = function() {
		var inputs = $( this ).find( ":ui-input" );
		setTimeout(function() {
			inputs.input( "refresh" );
		}, 1 );
	};


$.widget( "ui.input", {
	options: {
		disabled: null,
		icon: {
			enabled: null,
			disabled: null
		}
	},
	_create: function() {
		this.element.closest( "form" )
			.unbind( "reset.input" )
			.bind( "reset.input", formResetHandler );

		if ( typeof this.options.disabled !== "boolean" ) {
			this.options.disabled = this.element.propAttr( "disabled" );
		}

		var self = this,
			options = this.options;

		if ( this.element.is( ":disabled" ) ) {
			options.disabled = true;
		}

		this.element
			.addClass( baseClasses )
			.attr( "role", "input" )
			.bind( "mouseenter.input", function() {
				if ( options.disabled ) {
					return;
				}
				$( this ).addClass( hoverClass );
			})
			.bind( "mouseleave.input", function() {
				if ( options.disabled ) {
					return;
				}
				$( this ).removeClass( hoverClass );
			})
			.bind( "click.input", function( event ) {
				if ( options.disabled ) {
					event.preventDefault();
					event.stopImmediatePropagation();
				}
			})
			.bind( "focus.input", function() {
				// no need to check disabled, focus won't be triggered anyway
				self.element.addClass( focusClass );
			})
			.bind( "blur.input", function() {
				self.element.removeClass( focusClass );
			})
			.bind( "mousedown.input", function() {
				if ( options.disabled ) {
					return false;
				}
				$( this ).addClass( activeClass );
			})
			.bind( "mouseup.input", function() {
				if ( options.disabled ) {
					return false;
				}
				$( this ).removeClass( activeClass );
			});



		// TODO: pull out $.Widget's handling for the disabled option into
		// $.Widget.prototype._setOptionDisabled so it's easy to proxy and can
		// be overridden by individual plugins
		this._setOption( "disabled", options.disabled );
		this._resetInput();
		this._setStyle();
	},



	widget: function() {
		return this.element;
	},

	destroy: function() {
		this.element
			.removeClass( baseClasses + " " + stateClasses)
			.removeAttr( "role" );

		$.Widget.prototype.destroy.call( this );
	},

	_setOption: function( key, value ) {
		$.Widget.prototype._setOption.apply( this, arguments );
		if ( key === "disabled" ) {
			if ( value ) {
				this.element.propAttr( "disabled", true );
			} else {
				this.element.propAttr( "disabled", false );
			}
			return;
		}
		this._resetInput();
	},

	refresh: function() {
		var isDisabled = this.element.is( ":disabled" );
		if ( isDisabled !== this.options.disabled ) {
			this._setOption( "disabled", isDisabled );
		}
	},

	_resetInput: function() {
		this.element.val("");
	},

	_setStyle: function() {
		var width = parseInt(this.element.css('width'), 10) - 2*LEFT_RIGHT_PADDING_VALUE;
		var css = 
		{
			"padding-top": "6px",
			"padding-bottom": "6px",
			"padding-left": LEFT_RIGHT_PADDING_VALUE,
			"padding-right": LEFT_RIGHT_PADDING_VALUE,
			'width': width,
			"text-align": "left"	
		}
		this.element.css(css);
	}
});

}( jQuery ) );
