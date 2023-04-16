function initAll() {



	window.log=function(){log.history=log.history||[];log.history.push(arguments);if(this.console){arguments.callee=arguments.callee.caller;var a=[].slice.call(arguments);(typeof console.log==="object"?log.apply.call(console.log,console,a):console.log.apply(console,a))}};

	(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,timeStamp,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();){b[a]=b[a]||c}})((function(){try

	{console.log();return window.console;}catch(err){return window.console={};}})());



	(function($)

	{

	    $.fn.removeStyle = function(style)

	    {

	        var search = new RegExp(style + '[^;]+;?', 'g');



	        return this.each(function()

	        {

	            $(this).attr('style', function(i, style)

	            {

	                return style.replace(search, '');

	            });

	        });

	    };

	}(jQuery));



	//pagination Global variables

	var currpage = 1;

	var limit = 50;

	var sorter = 'date_sent';

	var order = 'DESC';



	$(document).ready(function(){



		$("#resources a").each(function(){

			$(this).parent().append("<span>"+$(this).attr("href")+"</span>");

		$(this).attr("target","_blank");

		});



		// Global Variables

		var toggle_primary_button    = $('.nav-toggle-button'),

			toggle_primary_icon    	 = $('.nav-toggle-button i'),

			toggle_secondary_button  = $('.page-nav li span'),

			toggle_secondary_icon    = $('.page-nav li span i'),

			primary_menu        	 = $('.page-nav'),

			secondary_menu   		 = $('.page-nav ul ul'),

			window_width			 = $(window).width();







		//Multi-line Tab

		toggle_secondary_button.each(function(){

			$(this).click(function(){

				$(this).parent("li").children("ul").toggle();

				$(this).children().toggleClass("fa-caret-up").toggleClass("fa-caret-down");;

			});

		});





		// Basic functionality for nav-toggle-button

		$(toggle_primary_button).click(function(){

			primary_menu.toggle();

			toggle_primary_icon.toggleClass("fa-times").toggleClass("fa-navicon");

		});



		// Add class to tab having drop down

		$( "nav #nav li:has(ul)").find('span i').addClass("fa-caret-down");



		// Reset all configs when width > 760

		$(window).resize(function(){



			if(window_width > 760 && primary_menu.is(':visible') || primary_menu.is(':hidden') || secondary_menu.is(':visible') || secondary_menu.is(':hidden')) {

				primary_menu.removeAttr('style');

				toggle_primary_icon.removeClass("fa-times").addClass("fa-navicon");



				secondary_menu.removeAttr('style');

				toggle_secondary_icon.removeClass("fa-caret-up").addClass("fa-caret-down");

			}

		});

	});



    /*$(document).ready(function(){

		// all plugin here

		$('body').supersleight({shim: 'images/transparent.gif'});

	});*/

	var trashexempt = 0;



	/*$(document).on('mouseover', '#trigger-menu', function() {

		$('.drop-menu').show();

	});



	$(document).on('mouseleave','.drop-menu',function(){



		$(this).hide();

	});*/



	// if($(window).width() <=700){

	// 	$('#main .email-table table tbody tr td.subject').removeStyle('width');

	// 	$('#main .email-table table tbody tr td.subject').css('width', '');

	// }



	$(document).ready(function(){

		var pageParam = getUrlParameter();



   		if(pathName() == 'index.php' || pathName() == ''){

   			$.ajax({

   				url: 'controller/check_user.php',

   				success:function(data){

   					data = JSON.parse(data);
						data = data.message;
   					if(data == 'success'){

   						window.location.href = 'login-page.php';

   					}

   				}

   			});

   		}



   		if(pathName() == 'mobile-panel.php'){

   			$('#main .email-table').css('width', '100%');

   			$('#main .form_pane').css('width', '90.5%');

   			$('#banner').css('background', 'none');

   			$('#main .form_pane').hide();

   		}



   		if(pathName() == 'view-form.php'){

   			$('#main .form_pane').css({'width': 'auto', 'height': 'auto', 'padding': '30px'});

			$('#back-panel').show();

			$('.loader').html('<img src="images/loadinfo.gif" alt="Wait">');



			if(pageParam == ''){

				$('#wrapper').html('<div class="container" id="error"><div class="error-content"><h1><img src="images/error.png" />You do not have sufficient permission to access this page.</h1></div><button type="button" onclick="goBack()" >« Go Back</button></div>');

			}else{



				var dataID = pageParam.substring(pageParam.lastIndexOf("@") + 1, pageParam.lastIndexOf("&"));

				$.ajax({

					url:'controller/display-subject.php',

					type: 'POST',

					data: {id:dataID},

					success: function(data){

						data = JSON.parse(data);
						data = data.message;
						$('hr.border-line').show();

						$('.show-subject').html(data);

					}

				});

				$.ajax({

				url: 'controller/display_attachment.php',

				type: 'POST',

				data: {id:dataID},

				success:function(data){

					data = JSON.parse(data);
					data = data.message;
					$('.form_pane').find('.show-attachment').html(data);

				}

				});

				$.ajax({

					url:'controller/display_form_content.php',

					type: 'POST',

					data: {id:dataID},

					success: function(data){

						data = JSON.parse(data);
						data = data.message;
						$('.form_pane').find('.showtable').html(data);



						//remove style on form

						$(".form_pane .showtable *").removeAttr("style");

						$(".form_pane .showtable p").remove();

						$(".form_pane .showtable table").removeAttr("width");

						$(".form_pane .showtable table").css('width', '60%');



					}

				});



	   			setTimeout(function(){ window.location.href = 'panel.php' }, 18000000);

			}



   		}



   		if(pathName() == 'view-form-mobile.php'){

   			$('#main .form_pane').show();

   			$('#back-panel').show();

   			$('#main .form_pane').css({'width': '90.5%', 'height': 'auto', 'padding': '30px'});

			$('.loader').html('<img src="images/loadinfo.gif" alt="Wait">');



			if(pageParam == ''){

				$('#mobile-wrapper').html('<div class="container" id="error"><div class="error-content"><h1><img src="images/error.png" />You do not have sufficient permission to access this page.</h1></div><button type="button" onclick="goBack()" >« Go Back</button></div>');

			}else{



				var dataID = pageParam.substring(pageParam.lastIndexOf("@") + 1, pageParam.lastIndexOf("&"));



				$.ajax({

					url:'controller/display-subject.php',

					type: 'POST',

					data: {id:dataID},

					success: function(data){

						data = JSON.parse(data);
						data = data.message;
						$('hr.border-line').show();

						$('.show-subject').html(data);

					}

				});



				$.ajax({

				url: 'controller/display_attachment.php',

				type: 'POST',

				data: {id:dataID},

				success:function(data){

					data = JSON.parse(data);
					data = data.message;
					$('.form_pane').find('.show-attachment').html(data);

				}

				});



				$.ajax({

	   				url: 'controller/display_form_content.php',

					type: 'POST',

					data: {id:dataID},

	   				success:function(data){

							data = JSON.parse(data);
							data = data.message;
						$('.form_pane').find('.showtable').html(data);



						//remove style on form

						$(".form_pane .showtable *").removeAttr("style");

						$(".form_pane .showtable p").remove();

						$(".form_pane .showtable table").removeAttr("width");

						$(".form_pane .showtable table").css('width', '100%');



						// if($(window).width() >= 500) {

						// 	$('.form_table').removeStyle('width');

						// 	$('.form_table').css('width','600px');

						// }

	   				}

	   			});



	   			setTimeout(function(){ window.location.href = 'panel.php' }, 18000000);

			}

   		}

	});



	$(document).ready(function(){

		$.ajax({

   				url:'controller/check_user.php',

   				success:function(data){

						data = JSON.parse(data);
						data = data.message;
   					if(data == 'success'){

   						$('.create_user').remove();

   					}



   				}

   			});

	});



	$(document).ready(function() {

	    $("#mytable")

	    .tablesorter({widthFixed: true, widgets: ['zebra']})

	    .tablesorterPager({container: $(".tableinfo")});

	});



	$(document).ready(function(){

		$.ajax({

			url:'controller/count-items.php',

			method: 'POST',

			data:{emailtype:1},

			success:function(data){

				data = JSON.parse(data);
				data = data.message;
				$('.tableinfo').html(data);

			}

		});

	});



	var width = $(window).width();



	$( window ).resize(function() {




	});



	$(document).click(function(event){

		if( !$(event.target).closest('.drop-settings').length ){

			if( $('.dropmenu').is(':visible') ){

				$('.dropmenu').hide();



			}

		}



	});



	$(document).on('click','.drop-settings',function(){

		$('#myDropdown').removeAttr('style');

		$('#myDropdown').toggleClass('dropmenu');

		clearform();

		$('#current_password').css('background','#fff');

		$('#confirm_password').css('background','#fff');

		$('.result-message').empty();

	});



	$(document).on('click','#back-panel',function(){

		if(screen.width <= 800){

			window.location.href="mobile-panel.php";

		}else{

			window.location.href="panel.php";

		}

	});







	$(document).ready(function(){

		$.ajax({

			url:'controller/display_table.php',

			success:function(data){

				data = JSON.parse(data);
				data = data.message;
				$('.email-table').html(data);

				// $('#sortreceived').find('a').append('<img src="images/sort-desc.png" alt="img-asc"/>');

			}

		});

	});



	$(document).ready(function(){

		$.ajax({

			url:'controller/count-inbox.php',

			success:function(data){

				data = JSON.parse(data);
				data = data.message;
				$('#inbox').html(data);

			}

		});

	});



	$(document).on('click','#trigger-menu',function(e){

		$('.drop-menu').show();

	});



	$(document).on('click','#createUserbtn',function(e){

		e.preventDefault();

		var username = $('#username').val();

		var password = $('#password').val();

		if(username != '' && password != ''){

			$.ajax({

				url:'controller/create_user.php',

				method:'POST',

				data:{username:username,password:password},

				success:function(data){

					data = JSON.parse(data);
					data = data.message;
					if(data == 'success'){

						$(document).find('.msg').html('<p class="success">User created. Click Login to proceed.</p>');

						$('#createUserform')[0].reset();

					}else if (data == 'failed'){

						$(document).find('.msg').html('<p class="warning">User creation failed.</p>');

					}else{

						$(document).find('.msg').html('<p class="warning">'+data+'</p>');

					}

				}

			});

		}else{

			$(document).find('.msg').html('<p class="warning">Please enter a valid Information.</p>');

		}

	});



	$(document).on('dblclick','#mytable tr',function(e){

		e.stopPropagation();

		//alert('Double clicked!');

		$('#modal_form_content').fadeIn('slow');

		var dataId = $(this).find('input:text').val();

		$.ajax({

			url:'controller/display_attachment.php',

			type: 'POST',

			data: {id:dataId},

			success: function(data){

				data = JSON.parse(data);
				data = data.message;

				$('.attachment-modal').html(data);

			}

		});

		$.ajax({

			url:'controller/display_form_content.php',

			type: 'POST',

			data: {id:dataId},

			success: function(data){

				data = JSON.parse(data);
				data = data.message;
				console.log(data);
				$('#modal_form_content').find('.form_content').html(data);

				$('.form_table').css('background', '#fff');

				if($(window).width() >= 600) {

					$('.form_table').removeStyle('width');

					$('.form_table').css('width','600px');

				}

			}

		});

	});



	$(document).on('click','.close-modal',function(e){

		e.preventDefault();

		$('#modal_form_content').hide();

	});



	$(document).mouseup(function (e)

	{

	    var container = $("#modal_form_content");



	    if ( (!container.is(e.target) // if the target of the click isn't the container...

				&& container.has(e.target).length === 0) ) // ... nor a descendant of the container

	    {

	        container.hide();

	    }

	});



	$(document).mouseup(function (e)

	{

	    var container = $("#password_modal");



	    if ( (!container.is(e.target) // if the target of the click isn't the container...

				&& container.has(e.target).length === 0) ) // ... nor a descendant of the container

	    {

	        container.hide();

	    }

	});



	$(document).mouseup(function (e)

	{

	    var container = $(".drop-menu");



	    if ( (!container.is(e.target) // if the target of the click isn't the container...

				&& container.has(e.target).length === 0) ) // ... nor a descendant of the container

	    {

	        container.hide();

	    }

	});







	$(document).on('click','#mytable tr',function(e){

		var dataId = $(this).find('input:text').val();

		var pagePathName= window.location.pathname;

		$('#mytable tr').removeClass('current');

		$(this).addClass('current');

		$('.form_pane').find('.showtable').empty();

		$(this).find('td label a img').attr('src', 'images/circle-inactive.png');





   		if(pathName() == 'mobile-panel.php'){

			$('#main .email-table').css('display', 'none');

			$('#main .back-btn').css('display', 'block');

			$('#main .form_pane').css('display', 'block');

			$('.tableinfo').css('display', 'none');

		}



		if(e.target.type == 'checkbox'){

			e.stopPropagation();

		}

		else{

			if(trashexempt == 1){

				$(this).removeClass('new');

				$(this).addClass('read');

				$.ajax({

					url:'controller/display-subject.php',

					type: 'POST',

					data: {id:dataId},

					success: function(data){

						data = JSON.parse(data);
						data = data.message;

						$('hr.border-line').show();

						$('.show-subject').html(data);



					}

				});

				$.ajax({

					url:'controller/display_attachment.php',

					type: 'POST',

					data: {id:dataId},

					success: function(data){

						data = JSON.parse(data);
						data = data.message;
						$('.show-attachment').html(data);

					}

				});

				$.ajax({

					url:'controller/change_status.php',

					type: 'POST',

					data: {id:dataId},

					success: function(data){



					}

				});

				$.ajax({

					url:'controller/display_form_content.php',

					type: 'POST',

					data: {id:dataId},

					success: function(data){

						data = JSON.parse(data);
						data = data.message;
						
						$('.form_pane').find('.showtable').html(data);

						$(".form_pane .showtable *").removeAttr("style");

						$(".form_pane .showtable p").remove();

						$(".form_pane .showtable table").removeAttr("width");

						$(".form_pane .showtable table").css('width', '100%');

					}

				});



			}else{

				$(this).removeClass('new');

				$(this).addClass('read');



				$.ajax({

					url:'controller/display-subject.php',

					type: 'POST',

					data: {id:dataId},

					success: function(data){

						data = JSON.parse(data);
						data = data.message;
						$('hr.border-line').show();

						$('.show-subject').html(data);

					}

				});

				$.ajax({

					url:'controller/display_attachment.php',

					type: 'POST',

					data: {id:dataId},

					success: function(data){
						data = JSON.parse(data);
						data = data.message;
						$('.show-attachment').html(data);

					}

				});

				$.ajax({

					url:'controller/change_status.php',

					type: 'POST',

					data: {id:dataId},

					success: function(data){

						$.ajax({

							url:'controller/count-inbox.php',

							success:function(data){

								data = JSON.parse(data);
								data = data.message;
								$('#inbox').html(data);

							}

						});

					}

				});



				$.ajax({

					url:'controller/display_form_content.php',

					type: 'POST',

					data: {id:dataId},

					success: function(data){

						data = JSON.parse(data);
						data = data.message;
						$('.form_pane').find('.showtable').html(data);



						//remove style on form

						$(".form_pane .showtable *").removeAttr("style");

						$(".form_pane .showtable p").remove();

						$(".form_pane .showtable table").removeAttr("width");

						$(".form_pane .showtable table").css('width', '100%');

					}

				});

			}

		}

	});







	var checklist = [];

	$(document).on('change','.checkbox',function(){

		cboxid = $(this).val();

		if( $(this).is(':checked') ){

			$(this).parents('tr').addClass('checked');

			checklist.push(cboxid);

		}

		else{

			$(this).parents('tr').removeClass('checked');

			$(this).parents('tr').removeClass('current');

			var index = checklist.indexOf(cboxid);

			if( index > -1){

				checklist.splice(index,1);

			}

		}

	});





	function countCheckList(checklist){

		if(checklist.length > 0){

			$.each(checklist,function(index,value){

			});

		}

		else{

		}

	}



	$(document).on('click','#print',function(){

		if(checklist.length > 0){

			countCheckList(checklist);

			$.ajax({

				url:'controller/print-form.php',

				method:'POST',

				data:{checklist:checklist},

				success:function(data){

					data = JSON.parse(data);
					data = data.message;
					var a = window.open();

					a.document.write(data);

					a.print();

					a.close();

					$('#checkall').prop('checked',false);

					$('tr input[type=checkbox]').prop('checked',false);

					$('#mytable tr').removeClass('checked');

					$('#mytable tr').removeClass('current');

					checklist = [];

					countCheckList(checklist);

				}

			});

		}

		else{

		}

	});



	$(document).on('click', '#deletemarked',function(){

		if(checklist.length > 0){

			var choice = confirm('Confirm Deletion?');

			countCheckList(checklist);

			var email_type = 2;

			currpage = 1;

			limit = 50;



			if(choice == true){

				if(trashexempt == 1){

					$.ajax({

						url:'controller/delete_emails.php',

						method:'POST',

						data:{checklist:checklist},

						success:function(data){

							$('input:checked').each(function(){

								$(this).closest('tr').fadeOut();

							});

							$('#checkall').prop('checked',false);

							$.ajax({

								url:'controller/display_trash.php',

								success:function(data){

									$('.email-table').html(data);

									// $('.form_pane').find('.showtable').remove();

									$('.form_pane').find('.showtable').empty();

								}

							});

							$.ajax({

								url:'controller/count-items.php',

								method:'POST',

								data:{emailtype:2},

								success:function(data){

									$('.tableinfo').html(data);

								}

							});

						}

					});

				}

				else{

					$.ajax({

						url:'controller/change_email_stat.php',

						method:'POST',

						data:{checklist:checklist, email_type:email_type},

						success:function(data){

							// alert(data);

							$('input:checked').each(function(){

								$(this).closest('tr').fadeOut();

							});

							$('#checkall').prop('checked',false);

							$.ajax({

								url:'controller/count-inbox.php',

								success:function(data){

									$('#inbox').find('span').replaceWith(data);

									$.ajax({

										url:'controller/count-items.php',

										method:'POST',

										data:{emailtype:1},

										success:function(data){

											$('.tableinfo').html(data);

										}

									});

									$.ajax({

										url:'controller/display_table.php',

										success:function(data){

											data = JSON.parse(data);
											data = data.message;
											$('.email-table').html(data);

											$('.form_pane').find('.showtable').empty();

										}

									});

								}

							});

							//window.location.href = 'panel.php';

						}

					});



				}



			}

			else{


			}

		}

		else{

		}

	});



	$(document).on('click', '#markednew',function(){

		if(checklist.length > 0){

			countCheckList(checklist);

			var status = 1;

			$.ajax({

				url:'controller/change_email_stat.php',

				method:'POST',

				data:{checklist:checklist, status:status},

				success:function(data){

					// alert(data);

					$('input:checked').each(function(){

						$(this).closest('tr').removeClass('read');

					});

					$('input:checked').each(function(){

						$(this).closest('tr').addClass('new');

					});

					$.ajax({

						url:'controller/count-inbox.php',

						success:function(data){

							data = JSON.parse(data);
							data = data.message;
							$('#inbox').html(data);

						}

					});

					$('#checkall').prop('checked',false);

					$('tr input[type=checkbox]').prop('checked',false);

					checklist = [];

					countCheckList(checklist);

					$('#mytable tr').removeClass('checked');

					$('.drop-menu').hide();

				}

			});

		}

		else{

		}



	});



	$(document).on('click', '#markedread',function(e){

		if(checklist.length > 0){

			countCheckList(checklist);

			var status = 2;

				$.ajax({

					url:'controller/change_email_stat.php',

					method:'POST',

					data:{checklist:checklist, status:status},

					success:function(data){

						// alert(data);

						$('input:checked').each(function(){

							$(this).closest('tr').removeClass('new');

						});

						$('input:checked').each(function(){

							$(this).closest('tr').addClass('read');

						});

						$.ajax({

							url:'controller/count-inbox.php',

							success:function(data){

								data = JSON.parse(data);
								data = data.message;
								$('#inbox').html(data);

							}

						});

						$('#checkall').prop('checked',false);

						$('tr input[type=checkbox]').prop('checked',false);

						checklist = [];

						countCheckList(checklist);

						$('#mytable tr').removeClass('checked');

						$('.drop-menu').hide();

					}

				});

		}

		else{

		}



	});



	$(document).on('click','#trash',function(){

		currpage = 1;

		limit = 50;



		$('#trash-list').hide();

		$('#inbox-list').css("display", "inline-block");

		$('#inbox').removeClass('current');

		$(this).addClass('current');

		$('table').remove();

		$('.email-table').show();

		$('.back-btn').hide();

		// $('.attachment-wrapper').remove();

		// $('.subject-wrapper').remove();

		// $('hr.border-line').hide();

		$('#sortfrom').find('a').find('img').remove();

		$('#sortsubject').find('a').find('img').remove();

		$('#sortreceived').find('a').find('img').remove();

		$('#sortreceived').find('a').append('<img src="images/sort-desc.png" alt="img-asc"/>');

		$('#checkall').prop('checked',false);

		$('tr input[type=checkbox]').prop('checked',false);

		checklist = [];





		trashexempt = 1;

		$('#checkall').prop('checked',false);

		$.ajax({

			url:'controller/display_trash.php',

			success:function(data){

				$('.email-table').html(data);

				$('.form_pane').find('.showtable').html('<p id="no-email">To view an email, click on it.</p>');



			}

		});

		$.ajax({

			url:'controller/count-items.php',

			method:'POST',

			data:{emailtype:2},

			success:function(data){

				data = JSON.parse(data);
				data = data.message;
				$('.tableinfo').html(data);

			}

		});

		$.ajax({

			url:'controller/count-inbox.php',

			success:function(data){

				data = JSON.parse(data);
				data = data.message;
				$('#inbox').html(data);

			}

		});

	});



	$(document).on('click','#inbox',function(){

		currpage = 1;

		limit = 50;



		$('#inbox-list').hide();

		$('#trash-list').show();

		$('#sortfrom').find('a').find('img').remove();

		$('#sortsubject').find('a').find('img').remove();

		$('#sortreceived').find('a').find('img').remove();

		$('.email-table').show();

		$('.back-btn').hide();

		$('.form_pane').hide();

		// $('.attachment-wrapper').remove();

		// $('.subject-wrapper').remove();

		// $('hr.border-line').hide();

		$('table').remove();

		$(this).addClass('current');

		$('#trash').removeClass('current');

		$('#sortreceived').find('a').append('<img src="images/sort-desc.png" alt="img-asc"/>');

		$('tr input[type=checkbox]').prop('checked',false);

		checklist = [];

		countCheckList(checklist);



		trashexempt = 0;

		$('#checkall').prop('checked',false);

		$.ajax({

			url:'controller/display_table.php',

			success:function(data){

				data = JSON.parse(data);
				data = data.message;
				$('.email-table').html(data);

				$('.form_pane').find('.showtable').html('<p id="no-email">To view an email, click on it.</p>');

			}

		});

		$.ajax({

			url:'controller/count-items.php',

			method:'POST',

			data:{emailtype:1},

			success:function(data){

				data = JSON.parse(data);
				data = data.message;
				$('.tableinfo').html(data);

			}

		});

		$.ajax({

			url:'controller/count-inbox.php',

			success:function(data){

				data = JSON.parse(data);
				data = data.message;
				$('#inbox').html(data);

			}

		});

	});



	$(document).on('click','#loginbtn',function(e){

		e.preventDefault();

		var user = $('#username').val();

		var pass = $('#password').val();

		if(user != '' && pass != ''){

			$.ajax({

				url: 'controller/login.php',

				method: 'POST',

				data: {user:user, pass:pass},

				success:function(data){

					data = JSON.parse(data);
					data = data.message;
					if(data == 'success'){

						$('.msg').remove();

						$(document).find('.se-pre-con').html('<img src="images/loader2.gif" alt="Wait"/>');



						if(screen.width <= 800){

							setTimeout("window.location.href='mobile-panel.php'",3000);

						}else{

							setTimeout("window.location.href='panel.php'",3000);

						}



					}

					else{

						$(document).find('.msg').html('<p class="warning">Invalid Information. Please try again.</p>');

					}

				}

			});

		}

		else{

			$(document).find('.msg').html('<p class="warning">Invalid Information. Please try again.</p>');

		}



	});



	$(document).on('click','#sortsubject',function(e){

		e.preventDefault();

		var clicks = $(this).data('clicks');

		sorter = 'form_subject';

		var cond;

		$('#checkall').prop('checked',false);

		$('tr input[type=checkbox]').prop('checked',false);

		checklist = [];

		countCheckList(checklist);

		if(trashexempt == 1){

			 cond = 2;

		}

		else{

			cond = 1;

		}

		if(clicks){

			order = 'ASC';

			$('#sortfrom').find('a').find('img').remove();

			$('#sortsubject').find('a').find('img').remove();

			$('#sortreceived').find('a').find('img').remove();

			$('#sortsubject').find('a').append('<img src="images/sort-asc.png" alt="img-asc"/>');

		}else{

			order = 'DESC';

			$('#sortfrom').find('a').find('img').remove();

			$('#sortsubject').find('a').find('img').remove();

			$('#sortreceived').find('a').find('img').remove();

			$('#sortsubject').find('a').append('<img src="images/sort-desc.png" alt="img-desc"/>');

		}

		$.ajax({

				url:'controller/sort-table.php',

				method: 'POST',

				data: {sort:sorter,order:order,cond:cond},

				success:function(data){

					$('.email-table').html(data);

				}

			});

		$(this).data("clicks", !clicks);

	});



	/*$(document).ready(function(){

		$('#sortfrom').find('a').append('<img src="images/sort-asc.png" alt="img-asc"/>');

	});*/



	$(document).on('click','#sortfrom',function(e){

		e.preventDefault();

		var clicks = $(this).data('clicks');

		sorter = 'form_from';

		var cond;

		$('#checkall').prop('checked',false);

		$('tr input[type=checkbox]').prop('checked',false);

		checklist = [];

		countCheckList(checklist);

		if(trashexempt == 1){

			 cond = 2;

		}

		else{

			cond = 1;

		}

		if(clicks){

			order = 'ASC';

			$('#sortfrom').find('a').find('img').remove();

			$('#sortsubject').find('a').find('img').remove();

			$('#sortreceived').find('a').find('img').remove();

			$('#sortfrom').find('a').append('<img src="images/sort-desc.png" alt="img-asc"/>');

		}else{

			order = 'DESC';

			$('#sortfrom').find('a').find('img').remove();

			$('#sortsubject').find('a').find('img').remove();

			$('#sortreceived').find('a').find('img').remove();

			$('#sortfrom').find('a').append('<img src="images/sort-asc.png" alt="img-asc"/>')

		}

		$.ajax({

				url:'controller/sort-table.php',

				method: 'POST',

				data: {sort:sorter,order:order,cond:cond},

				success:function(data){

					$('.email-table').html(data);


				}

			});

		$(this).data("clicks", !clicks);

	});



	$(document).on('click','#sortreceived',function(e){

		e.preventDefault();

		var clicks = $(this).data('clicks');

		sorter = 'date_sent';

		var cond;

		$('#checkall').prop('checked',false);

		$('tr input[type=checkbox]').prop('checked',false);

		checklist = [];

		countCheckList(checklist);

		if(trashexempt == 1){

			 cond = 2;

		}

		else{

			cond = 1;

		}

		if(clicks){

			order = 'ASC';

			$('#sortfrom').find('a').find('img').remove();

			$('#sortsubject').find('a').find('img').remove();

			$('#sortreceived').find('a').find('img').remove();

			$('#sortreceived').find('a').append('<img src="images/sort-desc.png" alt="img-asc"/>');



		}else{

			order = 'DESC';

			$('#sortfrom').find('a').find('img').remove();

			$('#sortsubject').find('a').find('img').remove();

			$('#sortreceived').find('a').find('img').remove();

			$('#sortreceived').find('a').append('<img src="images/sort-asc.png" alt="img-asc"/>');

		}

		$.ajax({

			url:'controller/sort-table.php',

			method: 'POST',

			data: {sort:sorter,order:order,cond:cond},

			success:function(data){

				$('.email-table').html(data);

			}

		});

		$(this).data("clicks", !clicks);

	});



	$(document).on('submit','#searchform',function(e){

		e.preventDefault();

		$('.attachment-wrapper').remove();

		$('.form_table').remove();

	});



	//Search email on keyup

	$(document).ready(function(){

		$('#searchinput').keyup(function(){

			var val = $(this).val().toLowerCase();



			var cond;

			if(val != ''){

				if(trashexempt == 1){

					cond = 2;

				}else{

					cond = 1;

				}

				$.ajax({

					url:'controller/search-email.php',

					method:'POST',

					data:{searchval:val,cond:cond},

					success:function(data){

						$('.email-table').html(data);

					}

				});

			}

			else{

				if(trashexempt == 1){

					$.ajax({

						url:'controller/display_trash.php',

						success:function(data){

							$('.email-table').html(data);

							// $('.form_pane').find('.showtable').remove();

							// $('.form_pane').find('.showtable').empty();

							$('.form_pane').html('<p>To view an email, click on it.</p>');

						}

					});

				}else{

					$.ajax({

						url:'controller/display_table.php',

						success:function(data){

							data = JSON.parse(data);
							data = data.message;
							$('.email-table').html(data);

							$('.form_pane').html('<p>To view an email, click on it.</p>');

						}

					});

				}

			}

		});

	});



	//Search email on click

	$(document).on('click','#search',function(e){

		var val = $('#searchinput').val().toLowerCase();



		var cond;

		if(val != ''){

			if(trashexempt == 1){

				cond = 2;

			}else{

				cond = 1;

			}

			$.ajax({

				url:'controller/search-email.php',

				method:'POST',

				data:{searchval:val,cond:cond},

				success:function(data){

					$('.email-table').html(data);

				}

			});

		}

		else{

			if(trashexempt == 1){

				$.ajax({

					url:'controller/display_trash.php',

					success:function(data){

						$('.email-table').html(data);

						// $('.form_pane').find('.showtable').remove();

						// $('.form_pane').find('.showtable').empty();

						$('.form_pane').find('.showtable').html('<p>To view an email, click on it.</p>');

						$('.form_pane').find('.show-subject').empty();

						$('.border-line').css('display', 'none');

					}

				});

			}else{

				$.ajax({

					url:'controller/display_table.php',

					success:function(data){

						data = JSON.parse(data);
						data = data.message;
						$('.email-table').html(data);

						$('.form_pane').find('.showtable').html('<p>To view an email, click on it.</p>');

							$('.form_pane').find('.show-subject').empty();

							$('.border-line').css('display', 'none');

					}

				});

			}

		}

	});



	$(document).on('click','.password-wrapper',function(e){

		$('#password_modal').fadeIn('slow');

	});



	$(document).on('click','#password_modal_save',function(){

		var curpass, newpass,confpass;

		curpass = $('#current_password').val();

		newpass = $('#new_password').val();

		confpass = $('#confirm_password').val();

		if(curpass == '' || newpass == '' || confpass == ''){

			$('.result-message').html('<p style="color:red">Some fields are empty.</p>');

		}else{

			$.ajax({

				url:'controller/change_password.php',

				method:'POST',

				data:{pass:newpass},

				success:function(data){

					data = JSON.parse(data);
					data = data.message;

					if(data == 'success'){

						$('.result-message').html('<p style="color:green">Password changed.</p>');

						clearform();

						$('#current_password').css('background','#fff');

						$('#confirm_password').css('background','#fff');

					}

					else{

						$('.result-message').html('<p style="color:green">Invalid Password.</p>');

					}

				}

			});

		}

	});



	$(document).on('keyup','#current_password',function(){

		curpass = $('#current_password').val();

		$.ajax({

			url:'controller/check_password.php',

			method:'POST',

			data:{pass:curpass},

			success:function(data){

				data = JSON.parse(data);
				data = data.message;
				if(data == 'success'){

					$('#current_password').css('background','#89de89');

				}else{

					$('#current_password').css('background','#fba6a6');

				}

			}

		});

	});



	$(document).on('keyup','#confirm_password',function(){

			var newpass = $('#new_password').val();

			var confpass = $('#confirm_password').val();

			if(newpass == confpass){

				$('#confirm_password').css('background','#89de89');

			}

			else{

				$('#confirm_password').css('background','#fba6a6');

			}

	});



	$(document).on('click','.cancel',function(){

		$('#password_modal').fadeOut('slow');

		$('#modal_form_content').fadeOut('slow');

	});



	//Clear password fields

	function clearform()

	{

    	document.getElementById("current_password").value=""; //don't forget to set the textbox ID

    	document.getElementById("new_password").value=""; //don't forget to set the textbox ID

    	document.getElementById("confirm_password").value=""; //don't forget to set the textbox ID

	}



	$(document).on('click','#forgotpass',function(e){

		e.preventDefault();

		var username = $('#forgotform').find('#username').val();

		if(username == ''){

			$('.msg').html('<p style="color:red">Username field is empty.</p>');

		}else{

			$.ajax({

				url:'controller/reset_password.php',

				method:'POST',

				data:{user:username},

				success:function(data){

					if(data == 'failed'){

						$('.msg').html('<p style="color:#F60900">User does not exist.</p>');

					}else{

					$('.msg').html('<p style="color:#F60900">Temporary password: '+data+'</p><p style="color:#050505">You can change your password once you login.</p>');

					}

				}

			});

		}



	});





	//own table pagination

	$(document).ready(function(){

		$(document).on('click','#nextpage',function(e){

			e.preventDefault();

			var eType = 0;


			currpage += limit;


			if(trashexempt == 1){

				eType = 2;

			}else{

				eType = 1;

			}

			$.ajax({

				url:'controller/paginate.php',

				method:'POST',

				data:{order:order,sorter:sorter,email_type: eType,start:currpage,limit:limit},

				success:function(data){

					$('.email-table').html(data);

				}

			});

			$(document).ready(function(){

				$.ajax({

					url:'controller/update-items.php',

					method: 'POST',

					data:{emailtype:eType,start:currpage},

					success:function(data){

						$('.tableinfo').html(data);

					}

				});

			});



		});

		$(document).on('click','#lastpage',function(e){

			e.preventDefault();

			var eType = 0;

			var totalemail = 0;

			if(trashexempt == 1){

				eType = 2;

			}else{

				eType = 1;

			}



			function callTotalEmail(data){

				var r = 0;

				r = totalemail / 50;

				currpage = (Math.floor(r)*50)+1;

				if(currpage > totalemail){

					r -=1;

					currpage = (Math.floor(r)*50)+1;

				}

			}



			$.ajax({

				url:'controller/count-emails.php',

				method:'POST',

				data:{emailtype:eType},

				success:function(data){

					totalemail = data;

					callTotalEmail(totalemail);

					$.ajax({

						url:'controller/paginate.php',

						method:'POST',

						data:{order:order,sorter:sorter,email_type: eType,start:currpage,limit:limit},

						success:function(data){

							$('.email-table').html(data);

						}

					});

					$.ajax({

						url:'controller/update-items.php',

						method: 'POST',

						data:{emailtype:eType,start:currpage},

						success:function(data){

							$('.tableinfo').html(data);

						}

					});

				}

			});

		});



		$(document).on('click','#previouspage',function(e){

			e.preventDefault();

			var eType = 0;

			currpage -= limit;



			if(trashexempt == 1){

				eType = 2;

			}else{

				eType = 1;

			}

			$.ajax({

				url:'controller/paginate.php',

				method:'POST',

				data:{order:order,sorter:sorter,email_type: eType,start:currpage,limit:limit},

				success:function(data){

					$('.email-table').html(data);

				}

			});

			$.ajax({

				url:'controller/update-items.php',

				method: 'POST',

				data:{emailtype:eType,start:currpage},

				success:function(data){

					$('.tableinfo').html(data);

				}

			});

		});

		$(document).on('click','#firstpage',function(e){

			e.preventDefault();

			var eType = 0;

			currpage =1;



			if(trashexempt == 1){

				eType = 2;

			}else{

				eType = 1;

			}

			$.ajax({

				url:'controller/paginate.php',

				method:'POST',

				data:{order:order,sorter:sorter,email_type: eType,start:currpage,limit:limit},

				success:function(data){

					$('.email-table').html(data);

				}

			});

			$.ajax({

				url:'controller/update-items.php',

				method: 'POST',

				data:{emailtype:eType,start:currpage},

				success:function(data){

					$('.tableinfo').html(data);

				}

			});

		});

	});



	$(document).on('change','#checkall',function(){

		if( $(this).is(':checked') ){

			$('#mytable tr').addClass('checked');

			$('tr input[type=checkbox]').prop('checked',true);

			$('td input[type=checkbox]:checked').each(function(index){

				tobeDel = $(this).val();

				checklist.push(tobeDel);

			});

			countCheckList(checklist);

		} else {

			$('tr input[type=checkbox]').prop('checked',false);

			$('#mytable tr').removeClass('checked');

			$('#mytable tr').removeClass('current');

			checklist = [];

			countCheckList(checklist);

		}

	});



	$(document).on('click','#printform',function(e){

		e.stopPropagation();

		var id=$(this).data('id');



		$.ajax({

			url:'controller/print-single-form.php',

			method:'POST',

			data:{id:id},

			success:function(data){
				
				var w = 900;

				var h = 1000;

				var left = Number((screen.width/2)-(w/2));

				var tops = Number((screen.height/2)-(h/2));

				var a = window.open('', '', 'width=900,height=900,scrollbars=yes');

				a.document.write(data);

				a.print();

				a.close();

			}

		});



	});



	$(document).on('click','#delete-email',function(e){

		e.stopPropagation();

		var id=$(this).data('id');

		var choice = confirm('Confirm Deletion?');



		if(choice == true){

			if(trashexempt == 1){

				$.ajax({

					url:'controller/delete-trash.php',

					method:'POST',

					data:{id:id},

					success:function(data){

						$.ajax({

							url:'controller/display_trash.php',

							success:function(data){

								$('.email-table').html(data);

								$('.form_pane').find('.showtable').empty();

							}

						});

					}

				});



			}else{

				$.ajax({

					url:'controller/delete-single-email.php',

					method:'POST',

					data:{id:id},

					success:function(data){

						$.ajax({

							url:'controller/display_table.php',

							success:function(data){

								data = JSON.parse(data);
								data = data.message;
								$('.email-table').html(data);

								$('.form_pane').find('.showtable').empty();

							}

						});



					}

				});

			}



		}

		else{

		}



	});



	$(document).on('click','#convertform',function(e){

		e.stopPropagation();

		var id=$(this).data('id');

		var choice = confirm('Convert to PDF file?');



		if(choice == true){

			$.ajax({

				url:'controller/convert-form.php',

				method:'POST',

				data:{id:id},

				success:function(data){

					var w = 900;

	    				var h = 500;

			        	var left = Number((screen.width/2)-(w/2));

			        	var tops = Number((screen.height/2)-(h/2));

					window.open(data, '', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+tops+', left='+left);

				}

			});

		}else{

		}



	});



	$(document).on('click', '#unread_msg',function(e){

		e.stopPropagation();

		var status = 1;

		var id = $(this).data('id');



		$.ajax({

			url:'controller/change_email_stat.php',

			method:'POST',

			data:{id:id, status:status},

			success:function(data){

			}

		});



		$.ajax({

			url:'controller/count-inbox.php',

			success:function(data){

				$('#inbox-list span').html(data);

			}

		});



		$(this).parent('td').parent('tr').removeClass('read');

		$(this).parent('td').parent('tr').addClass('new');

		$(this).find('a img').attr('src', 'images/circle-active.png');

		$(this).attr('id', 'read_msg');

		$(this).attr('title', 'Mark as Read');



	});



	$(document).on('click', '#read_msg',function(e){

		e.stopPropagation();

		var status = 2;

		var id = $(this).data('id');



		$.ajax({

			url:'controller/change_email_stat.php',

			method:'POST',

			data:{id:id, status:status},

			success:function(data){

			}

		});



		$.ajax({

			url:'controller/count-inbox.php',

			success:function(data){

				$('#inbox-list span').html(data);

			}

		});



		$(this).parent('td').parent('tr').removeClass('new');

		$(this).parent('td').parent('tr').addClass('read');

		$(this).find('a img').attr('src', 'images/circle-inactive.png');

		$(this).attr('id', 'unread_msg');

		$(this).attr('title', 'Mark as New');





	});



	$(document).on('click','#back',function(){

		$('#main .email-table').css('display', 'block');

		$('#main .form_pane').css('display', 'none');

		$('#main .back-btn').css('display', 'none');

	});



	$(document).on('click','#go-panel',function(){

		window.location = window.location.href = 'login-page.php';;

	});



}



function pathName(){

	var path = window.location.pathname;

	return path.substring(path.lastIndexOf("/") + 1);

}



function getUrlParameter(){

	var param = window.location.search;



	if(param.length != 0){

		return param.split('=')[1];

	}else{

		return '';

	}

}



function goBack() {

    window.history.back();

}

