(function($){
	$(document).ready(function () {
		// Dropdown menu
		if ($('.parent').children('ul').length > 0) {
			$('.parent').addClass('dropdown');
			$('.parent > a').addClass('dropdown-toggle');
			$('.parent > a').attr('data-toggle', 'dropdown');
			$('.parent > a').append('<b class="caret"></b>');
			$('.parent > ul').addClass('dropdown-menu');
		}
var offset = 100;
// grab an element
var myElement = document.querySelector("header");
// construct an instance of Headroom, passing the element
var headroom  = new Headroom(myElement, {
	"offset": offset
 });
// initialise
headroom.init();
		// Fix hide dropdown
		$('.dropdown-menu input, .dropdown-menu label').click(function(e) {
			e.stopPropagation();
		});
		// Tooltip
		$('.tooltip').tooltip({
			html: true
		});
		// To top
		var duration = 500;
		$(window).scroll(function() {
			if ($(this).scrollTop() > offset) {
				//$('.back-to-top').fadeIn(duration);
				$('.back-to-top').addClass('slidein');
			} else {
				//$('.back-to-top').fadeOut(duration);
				$('.back-to-top').removeClass('slidein');
			}
		});
		$('.back-to-top').click(function(event) {
			event.preventDefault();
			var position = 0;
			// Link inside tabs, return to active tab
			if ($(this).attr('href') != '#') {
				var node = $('#tabs li.active');
				if (node.length) {
					position = node.offset().top - $('#navigation').outerHeight(true);
				}
				$(this).attr('href', '#');
				$(this).find('span').removeClass('glyphicon-link').addClass('glyphicon-chevron-up');
			}
			$('html, body').animate({scrollTop: position}, duration);
			return false;
		});

		// Fix mootools hide
		var bootstrapLoaded = (typeof $().carousel == 'function');
		var mootoolsLoaded = (typeof MooTools != 'undefined');
		if (bootstrapLoaded && mootoolsLoaded) {
			Element.implement({
				hide: function () {
					return this;
				},
				show: function (v) {
					return this;
				},
				slide: function (v) {
					return this;
				}
			});
		}

		fakewaffle.responsiveTabs();
	});
})(jQuery);