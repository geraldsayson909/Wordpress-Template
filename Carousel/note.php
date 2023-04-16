CAROUSEL =======================================================================

head

<link rel="stylesheet" href="<?php bloginfo('template_url');?>/css/owl.carousel.css">
<link rel="stylesheet" href="<?php bloginfo('template_url');?>/css/owl.theme.default.css">

<script src="<?php bloginfo('template_url');?>/js/owl.carousel.js"></script>


$(".owl-carousel").owlCarousel({
	items: 3,
	nav: true,
	dots: false,
	loop: true,
	margin:0,
	responsive : {
	291 : {
		items:1
	},
	751 : {
		items:2
	},
	1011 : {
		items:3
	}
}
});


ibutang sa parent:
owl-carousel


.owl-nav {font-size: 0; top: 50%; transform:translate(0,-50%); z-index: 50; display: block; position:absolute; width: 100%;}
.owl-prev{background: url(images/icon_prev.png) no-repeat center top; display:inline-block; width: 35px; height: 43px;position: absolute; top: 0; left: 15px;}
.owl-prev:hover {opacity:0.7;}
.owl-next {background: url(images/icon_next.png) no-repeat center top; display:inline-block; width: 35px; height: 43px; position: absolute; top: 0; right: 15px;}
.owl-next:hover {opacity:0.7;}
