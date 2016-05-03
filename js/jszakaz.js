	//коэффициент работы в зависимости от срока заказа (надо делать так, чтобы всего этого видно не было...  тянуть значения, например, из БД)
	//var a = new Array(1, 1, 1, 1, 1, 1);
	$(function() {
		$( "#slider-range-max" ).slider({
			range: "max",
			min: 0,
			max: 5,
			value: 1,
			temp: 'месяц',
			slide: function( event, ui ) {
				//work = a[ui.value - 1]
				//ui.value = 1;
				if (ui.value == 0) {work = 0; temp = 'неделя'}
				else if (ui.value == 1) {work = 1; temp = 'месяц'}
				else if (ui.value == 2) {work = 2; temp = '3 месяца'}
				else if (ui.value == 3) {work = 3; temp = 'Полгода'}
				else if (ui.value == 4) {work = 4; temp = 'Год'}
				else if (ui.value == 5) {work = 5; temp = 'Всё время'}
				//else if (ui.value == 6) {ui.value = 6; temp = '---'}
				else  {work = 1; temp = 'месяц'};
				$( "#duration" ).val( temp );
				calc();
			}
		});
		$( "#duration" ).val( $( "#slider-range-max" ).slider( "value" ) );
	});
 
	function calc(par){
	template = document.cl_form.template.value;
	duration = document.cl_form.duration.value;
	
	if ((Number(template) == 0) || (work == 0))
		period = 0;
	else
		period = work;
	document.cl_form.period.value=period;
	document.getElementById("period").innerHTML=period;
	return false; 
	}

	$(document).ready(function(){
									   
		//When you click on a link with class of poplight and the href starts with a # 
		$('a.poplight[href^=#]').click(function() {
			var popID = $(this).attr('rel'); //Get Popup Name
			var popURL = $(this).attr('href'); //Get Popup href to define size
					
			//Pull Query & Variables from href URL
			var query= popURL.split('?');
			var dim= query[1].split('&');
			var popWidth = dim[0].split('=')[1]; //Gets the first query string value
	 
			//Fade in the Popup and add close button
			$('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<a href="#" class="close"><img src="images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');
			
			//Define margin for center alignment (vertical + horizontal) - we add 80 to the height/width to accomodate for the padding + border width defined in the css
			var popMargTop = ($('#' + popID).height() + 80) / 2;
			var popMargLeft = ($('#' + popID).width() + 80) / 2;
			
			//Apply Margin to Popup
			$('#' + popID).css({ 
				'margin-top' : -popMargTop,
				'margin-left' : -popMargLeft
			});
			
			//Fade in Background
			$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
			$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer 
			
			return false;
		});
		
		
		//Close Popups and Fade Layer
		$('a.close, #fade').live('click', function() { //When clicking on the close or fade layer...
			$('#fade , .popup_block').fadeOut(function() {
				$('#fade, a.close').remove();  
		}); //fade them both out
			
			return false;
		});
	 
		
	});
