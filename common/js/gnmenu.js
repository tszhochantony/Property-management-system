/**
 * gnmenu.js v1.0.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2013, Codrops
 * http://www.codrops.com
 */
;( function( window ) {


	'use strict';

	// http://stackoverflow.com/a/11381730/989439
	function mobilecheck() {
		var check = false;
		(function(a){if(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
		return check;
	}

	function gnMenu( el, options ) {
		this.el = el;
		this._init();
		this._scroll();
	}

	gnMenu.prototype = {

		_init : function() {
			this.trigger = this.el.querySelector( 'a.gn-icon-menu' );
			this.trigger1 = this.el.querySelector( 'a.gn-icon-menu1' );
			this.trigger2 = this.el.querySelector( 'a.gn-icon-menu2' );
      this.trigger3 = this.el.querySelector( 'a.gn-icon-menu3' );
      this.trigger4 = this.el.querySelector( 'a.gn-icon-menu4' );
      this.trigger5 = this.el.querySelector( 'a.gn-icon-menu5' );

			this.menu = this.el.querySelector( 'nav.gn-menu-wrapper' );
			this.menu1 = this.el.querySelector( 'nav.gn-menu-wrapper1' );
			this.menu2 = this.el.querySelector( 'nav.gn-menu-wrapper2' );
      this.menu3 = this.el.querySelector( 'nav.gn-menu-wrapper3' );
      this.menu4 = this.el.querySelector( 'nav.gn-menu-wrapper4' );
      this.menu5 = this.el.querySelector( 'nav.gn-menu-wrapper5' );

			this.isMenuOpen = false;
			this.scrollOpen = false;

			this.eventtype = mobilecheck() ? 'touchstart' : 'click';
			this._initEvents();

			var self = this;
			this.bodyClickFn = function() {
				self._closeMenu();
				this.removeEventListener( self.eventtype, self.bodyClickFn );
			};
		},
		_initEvents : function() {
			var self = this;

/* 指住就會出少少
			if( !mobilecheck() ) {
				this.trigger.addEventListener( 'mouseover', function(ev) { self._openIconMenu(); } );
				this.trigger.addEventListener( 'mouseout', function(ev) { self._closeIconMenu(); } );

				this.menu.addEventListener( 'mouseover', function(ev) {
					self._openMenu();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				} );
			}
			*/
			this.trigger.addEventListener( this.eventtype, function( ev ) {
				ev.stopPropagation();
				ev.preventDefault();
				if( self.isMenuOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
				//self._scroll();
				if( self.scrollOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
			} );
			this.menu.addEventListener( this.eventtype, function(ev) { ev.stopPropagation(); } );
			//第二個menu
			this.trigger1.addEventListener( this.eventtype, function( ev ) {
				ev.stopPropagation();
				ev.preventDefault();
				if( self.isMenuOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu1();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
				//self._scroll();
				if( self.scrollOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu1();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
			} );
			this.menu1.addEventListener( this.eventtype, function(ev) { ev.stopPropagation(); } );
			//第三個menu
			this.trigger2.addEventListener( this.eventtype, function( ev ) {
				ev.stopPropagation();
				ev.preventDefault();
				if( self.isMenuOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu2();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
				//self._scroll();
				if( self.scrollOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu2();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
			} );
			this.menu2.addEventListener( this.eventtype, function(ev) { ev.stopPropagation(); } );
      //第四個menu
			this.trigger3.addEventListener( this.eventtype, function( ev ) {
				ev.stopPropagation();
				ev.preventDefault();
				if( self.isMenuOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu3();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
				//self._scroll();
				if( self.scrollOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu3();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
			} );
			this.menu3.addEventListener( this.eventtype, function(ev) { ev.stopPropagation(); } );
      //第五個menu
			this.trigger4.addEventListener( this.eventtype, function( ev ) {
				ev.stopPropagation();
				ev.preventDefault();
				if( self.isMenuOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu4();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
				//self._scroll();
				if( self.scrollOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu4();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
			} );
			this.menu4.addEventListener( this.eventtype, function(ev) { ev.stopPropagation(); } );
      //第六個menu
			this.trigger5.addEventListener( this.eventtype, function( ev ) {
				ev.stopPropagation();
				ev.preventDefault();
				if( self.isMenuOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu5();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
				//self._scroll();
				if( self.scrollOpen ) {
					self._closeMenu();
					document.removeEventListener( self.eventtype, self.bodyClickFn );
				}
				else {
					self._openMenu5();
					document.addEventListener( self.eventtype, self.bodyClickFn );
				}
			} );
			this.menu5.addEventListener( this.eventtype, function(ev) { ev.stopPropagation(); } );
		},

		_openIconMenu : function() {
			classie.add( this.menu, 'gn-open-part' );
		},
		_openIconMenu1 : function() {
			classie.add( this.menu1, 'gn-open-part' );
		},
		_openIconMenu2 : function() {
			classie.add( this.menu2, 'gn-open-part' );
		},
    _openIconMenu3 : function() {
			classie.add( this.menu3, 'gn-open-part' );
		},
    _openIconMenu4 : function() {
			classie.add( this.menu4, 'gn-open-part' );
		},
    _openIconMenu5 : function() {
			classie.add( this.menu5, 'gn-open-part' );
		},
		_closeIconMenu : function() {
			classie.remove( this.menu, 'gn-open-part' );
		},
		_closeIconMenu1 : function() {
			classie.remove( this.menu1, 'gn-open-part' );
		},
		_closeIconMenu2 : function() {
			classie.remove( this.menu2, 'gn-open-part' );
		},
    _closeIconMenu3 : function() {
			classie.remove( this.menu3, 'gn-open-part' );
		},
    _closeIconMenu4 : function() {
			classie.remove( this.menu4, 'gn-open-part' );
		},
    _closeIconMenu5 : function() {
			classie.remove( this.menu5, 'gn-open-part' );
		},
		_openMenu : function() {
			if( this.isMenuOpen ) return;
			classie.add( this.trigger, 'gn-selected' );
			this.isMenuOpen = true;
			classie.add( this.menu, 'gn-open-all' );
			this._closeIconMenu();
		},
		_openMenu1 : function() {
			if( this.isMenuOpen ) return;
			classie.add( this.trigger1, 'gn-selected' );
			this.isMenuOpen = true;
			classie.add( this.menu1, 'gn-open-all' );
			this._closeIconMenu1();
		},
		_openMenu2 : function() {
			if( this.isMenuOpen ) return;
			classie.add( this.trigger2, 'gn-selected' );
			this.isMenuOpen = true;
			classie.add( this.menu2, 'gn-open-all' );
			this._closeIconMenu2();
		},
    _openMenu3 : function() {
			if( this.isMenuOpen ) return;
			classie.add( this.trigger3, 'gn-selected' );
			this.isMenuOpen = true;
			classie.add( this.menu3, 'gn-open-all' );
			this._closeIconMenu3();
		},
    _openMenu4 : function() {
			if( this.isMenuOpen ) return;
			classie.add( this.trigger4, 'gn-selected' );
			this.isMenuOpen = true;
			classie.add( this.menu4, 'gn-open-all' );
			this._closeIconMenu4();
		},
    _openMenu5 : function() {
			if( this.isMenuOpen ) return;
			classie.add( this.trigger5, 'gn-selected' );
			this.isMenuOpen = true;
			classie.add( this.menu5, 'gn-open-all' );
			this._closeIconMenu5();
		},
		_closeMenu : function() {
			if( !this.isMenuOpen ) return;
			classie.remove( this.trigger, 'gn-selected' );
			classie.remove( this.trigger1, 'gn-selected' );
			classie.remove( this.trigger2, 'gn-selected' );
      classie.remove( this.trigger3, 'gn-selected' );
      classie.remove( this.trigger4, 'gn-selected' );
      classie.remove( this.trigger5, 'gn-selected' );
			this.isMenuOpen = false;
			classie.remove( this.menu, 'gn-open-all' );
			classie.remove( this.menu1, 'gn-open-all' );
			classie.remove( this.menu2, 'gn-open-all' );
      classie.remove( this.menu3, 'gn-open-all' );
      classie.remove( this.menu4, 'gn-open-all' );
      classie.remove( this.menu5, 'gn-open-all' );
			this._closeIconMenu();
			this._closeIconMenu1();
      this._closeIconMenu2();
      this._closeIconMenu3();
      this._closeIconMenu4();
      this._closeIconMenu5();
		},
		_scroll : function() {
		    var prevScrollpos = window.pageYOffset;
            window.onscroll = function() {
                var currentScrollPos = window.pageYOffset;
                if (prevScrollpos >= currentScrollPos) {
                    document.getElementById("gn-menu").style.top = "0";
				} else {
                    document.getElementById("gn-menu").style.top = "-60px";
					  $("a.gn-icon-menu").removeClass("gn-selected");
				    $("nav.gn-menu-wrapper").removeClass("gn-open-all");
				    $("a.gn-icon-menu1").removeClass("gn-selected");
				    $("nav.gn-menu-wrapper1").removeClass("gn-open-all");
				    $("a.gn-icon-menu2").removeClass("gn-selected");
				    $("nav.gn-menu-wrapper2").removeClass("gn-open-all");
            $("a.gn-icon-menu3").removeClass("gn-selected");
				    $("nav.gn-menu-wrapper3").removeClass("gn-open-all");
            $("a.gn-icon-menu4").removeClass("gn-selected");
				    $("nav.gn-menu-wrapper4").removeClass("gn-open-all");
            $("a.gn-icon-menu5").removeClass("gn-selected");
				    $("nav.gn-menu-wrapper5").removeClass("gn-open-all");
                 }
            prevScrollpos = currentScrollPos;
            }
			this.scrollOpen = false;
		}
	}

	// add to global namespace
	window.gnMenu = gnMenu;

} )( window );
