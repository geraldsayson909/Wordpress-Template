// start_of DEFAULT
$(document).ready(function(){

	// Global Variables

		var toggle_primary_button = $('.nav_toggle_button'),
				toggle_primary_icon = $('.nav_toggle_button i'),
				toggle_secondary_button = $('.page_nav li span'),
				primary_menu = $('.page_nav'),
				secondary_menu = $('.page_nav ul ul'),
				webHeight = $(document).height(),
				window_width = $(window).width();

	// Company name and phone number on content area
	$("main * :not('h1')").not('.woocommerce *').each(function() {
		var regex1 = /(?![^<]+>)((\+\d{1,2}[\s.-])?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{6})/g;
		var regex2 = /(?![^<]+>)((\+\d{1,2}[\s.-])?\(?\d{3}\)?[\s.-]?\d{4}[\s.-]?\d{4})/g;
		var regex = /(?![^<]+>)((\+\d{1,2}[\s.-])?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4})/g;
				$(this).html(
						$(this).html()
						.replace(/The Woosah Box/g, "<mark class='comp'>$&</mark>")
						.replace(regex1, "<mark class='main_phone'>$&</mark>").replace(regex2, "<mark class='main_phone'>$&</mark>").replace(regex, "<mark class='main_phone'>$&</mark>"));
		});

		$("main a[href]").each(function() {
		   var newHref = $(this).attr('href').replace("<mark class='comp'>", "").replace("</mark>", "");
			 $(this).attr('href', newHref);
		});


		// Forms on content area
		var form = $('main').find('#myframe');
			if(form.length > 0) {
			document.getElementById('myframe').onload = function(){
			  calcHeight();
			};
		}

	// Add class to tab having drop down
	$( ".page_nav li:has(ul)").find('span i').addClass("fa-caret-down");


	//Multi-line Tab
	toggle_secondary_button.click(function(){
		$(this).parent('li').siblings('li').children('ul').slideUp(400, function() {
			$(this).removeAttr('style');
		});

		$(this).parent('li').siblings('li').find('.fa').removeClass("fa-caret-up").addClass("fa-caret-down");
		$(this).parent('li').children('ul').slideToggle();
		$(this).children().toggleClass("fa-caret-up").toggleClass("fa-caret-down");
	});

	// Basic functionality for nav_toggle

	var hamburger = $(".hamburger");
    // hamburger.each(function(){
        // $(this).click(function(){
         // $(this).toggleClass("is-active");
        // });
      // });

	hamburger.click(function(){
		primary_menu.addClass('toggle_right_style');
		$('.toggle_right_nav').addClass('toggle_right_cont');
		$(".nav_toggle_button").toggleClass('active');
		$(".hamburger").toggleClass("is-active");
		$('body').addClass('active');
	});


		$('.toggle_nav_close, .menu_slide_right .hamburger').click(function(){
		primary_menu.removeClass('toggle_right_style');
		secondary_menu.removeAttr('style');
		toggle_secondary_button.children().removeClass("fa-caret-up").addClass("fa-caret-down");
		$('.toggle_right_nav').removeClass('toggle_right_cont');
		$(".nav_toggle_button").removeClass('active');
		$(".hamburger").removeClass("is-active");
		$('body').removeClass('active');
	});

  // end_of default

// start_of CHANGES
	// Swap Elements

	function swap_this(){
    if(window_width <= 800){
			$('.main_logo').insertAfter('#nav_area .logo_wrap');
			$('#nav_area').insertBefore('.header1');

			$('.head_btns').insertBefore('.head_info');
			$('.copyright').insertAfter('.footer_nav');
		} else if (window_width <= 1010){
			$('.main_logo').insertBefore('.head_info');
			$('#nav_area').insertAfter('.header1');

			$('.head_btns').insertBefore('.head_info');
			$('.main_logo2').insertBefore('.head_info2');
			$('.head_btns2').insertBefore('.head_info2');
			$('.footer_nav').appendTo('.footer_btm_main');
			$('.copyright').insertAfter('.footer_nav');
		} else {
			$('.main_logo').insertBefore('.head_info');
			$('#nav_area').insertAfter('.header1');

			$('.head_btns').insertBefore('.head_info');
			$('.main_logo2').insertBefore('.head_info2');
			$('.head_btns2').insertBefore('.head_info2');
			$('.footer_nav').insertAfter('.contact_info ul');
			$('.copyright').insertAfter('.footer_logo');
		}
	}

	swap_this();


  $(window).scroll(function(){
    var windowScroll2 = $(this).scrollTop();

    // PARALLAX
    // $('.btm1_bg').css('top', -370 + (windowScroll2 * .23) + "px");
    // END OF PARALLAX


    // STICKY
    // var parent_pos = $("#parent").position();
    //
    //  if ($(this).scrollTop() >= parent_pos.top) {
    //    $('.parent_bg').addClass('sticky');
    //  } else {
    //    $('.parent_bg').removeClass('sticky');
    //  }
    // .sticky {position: fixed; top: 0; left: 50%; transform: translateX(-50%); z-index: -1; width: 1920px;}
    // END OF STICKY

    if($(this).scrollTop() <= 70) {
    	$('.header_holder2').addClass('d_none');
    } else {
    	$('.header_holder2').removeClass('d_none');
    }

  });


  $('.rslides').responsiveSlides();
  $('.box_skitter_large').skitter({
		theme: 'square',
		numbers_align: 'center',
		progressbar: false,
		navigation: false,
		numbers: false,
		dots:true,
		preview: false,
		interval: 6000
	});

  // FAQ
	// 	$('.faq h6').click(function(){
	// 	$(this).next().slideToggle()
 	// .siblings('.faq div').slideUp();
 	// //toggle sign
	// 	$(this).toggleClass('sign')
	// 	.siblings('.faq h6').removeClass();
	// 	});

// end_of CHANGES

// start_of DEFAULT
	// Reset all configs when width > 800
	$(window).resize(function(){
		window_width = $(this).width();

		swap_this();

		if(window_width > 800) {
			$(".nav_toggle_button").removeClass('active');
			$(".hamburger").removeClass("is-active");
			primary_menu.removeClass('toggle_right_style');
			$('.toggle_right_nav').removeClass('toggle_right_cont');
			$('body').removeClass('active');
		}
		else{
			secondary_menu.removeAttr('style');
			toggle_secondary_button.children().removeClass("fa-caret-up").addClass("fa-caret-down");
		}

	});


	$('.back_top').click(function () { // back to top
		$("html, body").animate({
			scrollTop: 0
		}, 900);
		return false;
	});

	$(window).scroll(function(){  // fade in fade out button
	var windowScroll = $(this).scrollTop();

		if (windowScroll > (webHeight * 0.5) && window_width <= 600 ) {
			$(".back_top").fadeIn();
		} else{
			$(".back_top").fadeOut()
		};

		//-----parallax code----

		// $('.slider').css('margin-top', windowScroll * .45);

		// For (AddThis) Plugins
		if($('body #at-share-dock').hasClass('at-share-dock')) {
			$('.back_top').addClass('withAddThis_plugins');
			$('.footer_btm').addClass('withAddThis_ftr_btm');
		} else {
			$('.back_top').removeClass('withAddThis_plugins');
			$('.footer_btm').removeClass('withAddThis_ftr_btm');
		}
		// End (AddThis) Plugins


		//---scroll fixed code---

		//if (windowScroll > 45 && window_width >= 1011){
		//	$('.header_holder').addClass('fixedholder');
		//} else {
		//	$('.header_holder').removeClass('fixedholder');
		//}



	//----Parallax Fixed---

	// if ($('#bottom2').length >= 1) {
	// 		var fixbtm = $('#bottom2').offset().top;
	// 		if (fixbtm <= windowScroll){
	// 				$("#bottom2").css({
	// 						'background-attachment' : 'fixed'
	// 				});
	// 		} else {
	// 						$("#bottom2").css({
	// 								'background-attachment' : 'unset',
	// 								'background-position' : 'center top'
	// 						});
	// 				}
	// 		}



	});






	// -----FOR ONE COLOR-----

	//function thetext(elem, LePos, LeColor) {
    //var splt = $(elem).text().split('');

    //$.each(LePos, function(k, v) {
     //     splt[v] = '<span style="color: '+LeColor[k]+' !important;">'+splt[v]+'</span>';
   // });

    //$(elem).html(splt.join(''));
	//}

	//thetext('h2', [1, 5], ['red', 'green']);


	// ------FOR FIRST WORD COLOR-----

	//$("#firstWord").html(function(){
	//var text= $(this).text().trim().split(" ");
	//var first = text.shift();
	//return (text.length > 0 ? "<q>"+ first + "</q> " : first) + text.join(" ");
	//});


	// ------FOR LAST WORD COLOR--------

	//$("#lastWord").html(function(){
	//var text= $(this).text().trim().split(" ");
	//var last = text.pop();
	//return text.join(" ") + (text.length > 0 ? " <q>" + last + "</q>" : last);
	//});


	// ------Carousel--------

	// $(".owl-carousel").owlCarousel({
	// 	items: 3,
	// 	nav: true,
	// 	dots: false,
	// 	loop: true,
	// 	margin:0,
	// 	responsive : {
	// 	291 : {
	// 		items:1
	// 	},
	// 	751 : {
	// 		items:2
	// 	},
	// 	1011 : {
	// 		items:3
	// 	}
	// }
	// });



	//---------------------- Testimonial CODE --------------------------------------
	// COMMENTS STYLE PLUGIN //

	//$('.commentlist li:last-child').css('background','none');
	//$('.commentlist li ul li').css('background','none');
	//$('.commentlist li ul li:last-child').css('border-bottom','none');

	//----------------------Testimonial END OF CODE -------------------------------


	//--- POP UP JS ----

	//$('.close').click (function(){
	//$('.pop_cont').hide();
	//});



});
// end_of DEFAULT
