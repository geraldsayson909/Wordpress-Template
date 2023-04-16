$(document).ready(function(){
	function calcHeight()
	{
		var the_height=parent.document.getElementById('myframe').contentWindow.document.body.scrollHeight;
		parent.document.getElementById('myframe').height=the_height +200;
	}

	$('.section').hide();
	$('#first select').change(function() {
		if($(this).val()!== ""){ 
			$('#second').fadeIn();
		} else {
			$('#second, .section').fadeOut();
			$("#ninth input, #tenth input").val('');
			$('#second select, #third select, #fourth select, #fifth select').val("");
			$(".field .paid_for:checked, .field .medicaid:checked, .field .Type_of_care:checked").removeAttr('checked');
		}
		calcHeight();
	});
	$('#second select').change(function() {
		if($(this).val()!== ""){ 
			$('#third').fadeIn();
		} else {
			$('#third, #fourth').fadeOut();
			$('#fifth').fadeOut();
			$('#sixth').fadeOut();
			$("#seventh").fadeOut();
			$("#eigth").fadeOut();
			$("#ninth").fadeOut();
			$("#tenth").fadeOut();
			$("#ninth input, #tenth input").val('');
			
			$('#third select, #fourth select, #fifth select').val("");
			$(".field .paid_for:checked, .field .medicaid:checked, .field .Type_of_care:checked").removeAttr('checked');
		}
		calcHeight();
	});
	$('#third select').change(function() {
		if($(this).val()!== ""){ 
			$('#fourth').fadeIn();
		} else {
			$('#fourth, #fifth').fadeOut();
			$('#sixth').fadeOut();
			$("#seventh").fadeOut();
			$("#eigth").fadeOut();
			$("#ninth").fadeOut();
			$("#tenth").fadeOut();
			$("#ninth input, #tenth input").val('');
			
			$('#fourth select, #fifth select').val("");
			$(".field .paid_for:checked, .field .medicaid:checked, .field .Type_of_care_needed:checked").removeAttr('checked');
		}
		calcHeight();
	});
	$('#fourth select').change(function() {
		if($(this).val()!== ""){ 
			$('#fifth').fadeIn();
		} else {
			$('#fifth, #sixth').fadeOut();
			$("#seventh").fadeOut();
			$("#eigth").fadeOut();
			$("#ninth").fadeOut();
			$("#tenth").fadeOut();
			$("#ninth input, #tenth input").val('');
			
			$('#fifth select').val("");
			$(".field .paid_for:checked, .field .medicaid:checked, .field .Type_of_care_needed:checked").removeAttr('checked');
		}
		calcHeight();
	});
	$('#fifth select').change(function() {
		if($(this).val()!== ""){ 
			$('#sixth').fadeIn();
		} else {
			$('#sixth, #seventh').fadeOut();
			$("#eigth").fadeOut();
			$("#ninth").fadeOut();
			$("#tenth").fadeOut();
			$("#ninth input, #tenth input").val('');
			
			$(".field .paid_for:checked, .field .medicaid:checked, .field .Type_of_care_needed:checked").removeAttr('checked');
		}
		calcHeight();
	});
	$(".field .Type_of_care_needed").click(function(){
		var check = $(".field .Type_of_care_needed:checked").length;
		if(check) {
			$("#seventh").fadeIn();
		} else {
			$("#seventh").fadeOut();
			$("#eigth").fadeOut();
			$("#ninth").fadeOut();
			$("#tenth").fadeOut();
			$("#ninth input, #tenth input").val('');
			
			$(".field .paid_for:checked, .field .medicaid:checked").removeAttr('checked');
		}
		calcHeight();
	});
	$(".field .paid_for, .field .medicaid").click(function(){
		var check = $(".field .paid_for:checked").length;
		var check2 = $(".field .medicaid:checked").length;
		if(check && !check2) {
			$("#ninth").fadeIn();
			$("#eigth").fadeOut();
		} else if (check && check2) {
			$("#eigth").fadeIn();
			$("#ninth").fadeIn();
		} else if (!check && check2) {
			$("#eigth").fadeIn();
			$("#ninth").fadeIn();
		} else if (!check && !check){
			$("#eigth").fadeOut();
			$("#ninth").fadeOut();
			$("#tenth").fadeOut();
			$("#ninth input, #tenth input").val('');
		}
		calcHeight();
	});
	
	$("#zip_code").keyup(function(){
		if ($(this).val().length == 0){
			$("#tenth").fadeOut();
			
			$("#tenth input").val('');
			
		} else {
			$("#tenth").fadeIn();
		}
		calcHeight();
	});
	
	$("#submit").submit(function(){
		self.parent.$('html, body').animate(
			{ scrollTop: self.parent.$('#foot').offset().top },
			500
		);
	});
	
});