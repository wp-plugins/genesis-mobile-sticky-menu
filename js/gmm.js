jQuery(document).ready(function($) {
	function genesis_mobile_menu() {
		var width = $(window).width() - 32;
		$( '.genesis-mobile-menu' ).css( 'marginLeft', '-' + width + 'px' );
		$( '.genesis-mobile-menu' ).css( 'width', width + 'px' );
		$( 'html' ).css( 'backgroundPosition', (width - 9) + 'px 0' );
		if( $(window).width() < genesisMobileMenuBP.breakpoint && $('.nav-primary').is(":visible") || $(window).width() < genesisMobileMenuBP.breakpoint && $('.nav-secondary').is(":visible") ) {
			$( 'div.site-container' ).prepend( '<a class="toggle-menu"></a>' );
			$( 'div.site-container' ).after( '<div class="genesis-mobile-menu"></div>' );
			$('.nav-primary').clone().removeClass('nav-primary').addClass('nav-primary-mobile').appendTo(".genesis-mobile-menu");
			$('.nav-secondary').clone().removeClass('nav-secondary').addClass('nav-secondary-mobile').appendTo(".genesis-mobile-menu");
			$('.nav-primary-mobile ul').removeClass();
			$('.nav-secondary-mobile ul').removeClass();
			$('.nav-primary').hide();
			$('.nav-secondary').hide();
		}
		else if( $(window).width() > genesisMobileMenuBP.breakpoint && $('.nav-primary').is(":hidden") || $(window).width() > genesisMobileMenuBP.breakpoint && $('.nav-secondary').is(":hidden") ) {
			$('.toggle-menu').remove();
			$('.genesis-mobile-menu').remove();
			$('.nav-primary').show();
			$('.nav-secondary').show();
			$('html').css('paddingLeft','0px').css('marginRight','0px');
		}
		$('.toggle-menu').toggle(
		function () {
			$('html').addClass('genesis-mobile-menu-on').animate({ paddingLeft: width + 'px', marginRight: '-' + width + 'px' }, { duration: 200 });
			$('.genesis-mobile-menu').animate({ marginLeft: '0px' }, { duration: 200, specialEasing: {}, complete: function() {} });
		},
		function () {
			$('html').animate({ paddingLeft: '0px', marginRight: '0px' }, { duration: 200, complete: function() { $('html').removeClass('genesis-mobile-menu-on'); } });
			$('.genesis-mobile-menu').animate({ marginLeft: '-' + width + 'px' }, { duration: 200 });
		});
	}
	genesis_mobile_menu(genesisMobileMenuBP.breakpoint);
	$(window).resize(function() { genesis_mobile_menu(genesisMobileMenuBP.breakpoint); });
	var width = $(window).width() - 32;
	$( '.genesis-mobile-menu' ).css( 'marginLeft', '-' + width + 'px' );
	$( '.genesis-mobile-menu' ).css( 'width', width + 'px' );
	$( 'html' ).css( 'backgroundPosition', (width - 9) + 'px 0' );
});