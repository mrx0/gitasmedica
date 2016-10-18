
	function Ajax_add_client(session_id) {
		// убираем класс ошибок с инпутов
		$('input').each(function(){
			$(this).removeClass('error_input');
		});
		// прячем текст ошибок
		$('.error').hide();
		 
		$.ajax({
			// метод отправки 
			type: "POST",
			// путь до скрипта-обработчика
			url: "ajax_test.php",
			// какие данные будут переданы
			data: {
				fname:document.getElementById("f").value,
				iname:document.getElementById("i").value,
				oname:document.getElementById("o").value,
				
				sel_date:document.getElementById("sel_date").value,
				sel_month:document.getElementById("sel_month").value,
				sel_year:document.getElementById("sel_year").value,
				
				sex:sex_value,
			},
			// тип передачи данных
			dataType: "json",
			// действие, при ответе с сервера
			success: function(data){
				// в случае, когда пришло success. Отработало без ошибок
				if(data.result == 'success'){   
					//alert('форма корректно заполнена');
					ajax({
						url:"add_client_f.php",
						statbox:"errrror",
						method:"POST",
						data:
						{
							f: document.getElementById("f").value,
							i: document.getElementById("i").value,
							o: document.getElementById("o").value,
									
							comment: document.getElementById("comment").value,
								
							card: document.getElementById("card").value,
								
							therapist: document.getElementById("search_client2").value,
							therapist2: document.getElementById("search_client4").value,
							
							sel_date: document.getElementById("sel_date").value,
							sel_month: document.getElementById("sel_month").value,
							sel_year: document.getElementById("sel_year").value,
							
							telephone:document.getElementById("telephone").value,
							
							passport:document.getElementById("passport").value,
							passportvidandata:document.getElementById("passportvidandata").value,
							passportvidankem:document.getElementById("passportvidankem").value,
							
							alienpassportser:document.getElementById("alienpassportser").value,
							alienpassportnom:document.getElementById("alienpassportnom").value,							
							
							address:document.getElementById("address").value,
							polis:document.getElementById("polis").value,
							
							sex:sex_value,
								
							session_id: session_id,
						},
						success:function(data){document.getElementById("errrror").innerHTML=data;}
					})
				// в случае ошибок в форме
				}else{
					// перебираем массив с ошибками
					for(var errorField in data.text_error){
						// выводим текст ошибок 
						$('#'+errorField+'_error').html(data.text_error[errorField]);
						// показываем текст ошибок
						$('#'+errorField+'_error').show();
						// обводим инпуты красным цветом
					   // $('#'+errorField).addClass('error_input');                      
					}
					document.getElementById("errror").innerHTML='<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>'
				}
			}
		});		
	};

	function Ajax_edit_client(session_id) {
		// убираем класс ошибок с инпутов
		$('input').each(function(){
			$(this).removeClass('error_input');
		});
		// прячем текст ошибок
		$('.error').hide();
		 
		$.ajax({
			// метод отправки 
			type: "POST",
			// путь до скрипта-обработчика
			url: "ajax_test.php",
			// какие данные будут переданы
			data: {
				
				sel_date:document.getElementById("sel_date").value,
				sel_month:document.getElementById("sel_month").value,
				sel_year:document.getElementById("sel_year").value,
				
				sex:sex_value,
			},
			// тип передачи данных
			dataType: "json",
			// действие, при ответе с сервера
			success: function(data){
				// в случае, когда пришло success. Отработало без ошибок
				if(data.result == 'success'){   
					//alert('форма корректно заполнена');
					ajax({
						url:"client_edit_f.php",
						statbox:"errrror",
						method:"POST",
						data:
						{
							id:document.getElementById("id").value,
							comment:document.getElementById("comment").value,
							
							card:document.getElementById("card").value,
							
							therapist:document.getElementById("search_client2").value,
							therapist2:document.getElementById("search_client4").value,
							sel_date:document.getElementById("sel_date").value,
							sel_month:document.getElementById("sel_month").value,
							sel_year:document.getElementById("sel_year").value,
							
							telephone:document.getElementById("telephone").value,
							
							passport:document.getElementById("passport").value,
							passportvidandata:document.getElementById("passportvidandata").value,
							passportvidankem:document.getElementById("passportvidankem").value,
							
							alienpassportser:document.getElementById("alienpassportser").value,
							alienpassportnom:document.getElementById("alienpassportnom").value,	
							
							address:document.getElementById("address").value,
							polis:document.getElementById("polis").value,

							sex:sex_value,
							
							session_id: session_id,
						},
						success:function(data){document.getElementById("errrror").innerHTML=data;}
					})
				// в случае ошибок в форме
				}else{
					// перебираем массив с ошибками
					for(var errorField in data.text_error){
						// выводим текст ошибок 
						$('#'+errorField+'_error').html(data.text_error[errorField]);
						// показываем текст ошибок
						$('#'+errorField+'_error').show();
						// обводим инпуты красным цветом
					   // $('#'+errorField).addClass('error_input');                      
					}
					document.getElementById("errror").innerHTML='<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>'
				}
			}
		});	
	}; 
	
	function Ajax_edit_fio_client() {
		// убираем класс ошибок с инпутов
		$('input').each(function(){
			$(this).removeClass('error_input');
		});
		// прячем текст ошибок
		$('.error').hide();
		 
		$.ajax({
			// метод отправки 
			type: "POST",
			// путь до скрипта-обработчика
			url: "ajax_test.php",
			// какие данные будут переданы
			data: {
				fname:document.getElementById("f").value,
				iname:document.getElementById("i").value,
				oname:document.getElementById("o").value
			},
			// тип передачи данных
			dataType: "json",
			// действие, при ответе с сервера
			success: function(data){
				// в случае, когда пришло success. Отработало без ошибок
				if(data.result == 'success'){   
					//alert('форма корректно заполнена');
					ajax({
						url:"client_edit_fio_f.php",
						statbox:"errrror",
						method:"POST",
						data:
						{
							id:document.getElementById("id").value,
							
							f:document.getElementById("f").value,
							i:document.getElementById("i").value,
							o:document.getElementById("o").value,
						},
						success:function(data){document.getElementById("errrror").innerHTML=data;}
					})
				// в случае ошибок в форме
				}else{
					// перебираем массив с ошибками
					for(var errorField in data.text_error){
						// выводим текст ошибок 
						$('#'+errorField+'_error').html(data.text_error[errorField]);
						// показываем текст ошибок
						$('#'+errorField+'_error').show();
						// обводим инпуты красным цветом
					   // $('#'+errorField).addClass('error_input');                      
					}
					document.getElementById("errror").innerHTML='<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>'
				}
			}
		});		
	};  
	
	function Ajax_edit_fio_user() {
		// убираем класс ошибок с инпутов
		$('input').each(function(){
			$(this).removeClass('error_input');
		});
		// прячем текст ошибок
		$('.error').hide();
		 
		$.ajax({
			// метод отправки 
			type: "POST",
			// путь до скрипта-обработчика
			url: "ajax_test.php",
			// какие данные будут переданы
			data: {
				fname:document.getElementById("f").value,
				iname:document.getElementById("i").value,
				oname:document.getElementById("o").value
			},
			// тип передачи данных
			dataType: "json",
			// действие, при ответе с сервера
			success: function(data){
				// в случае, когда пришло success. Отработало без ошибок
				if(data.result == 'success'){   
					//alert('форма корректно заполнена');
					ajax({
						url:"user_edit_fio_f.php",
						statbox:"errrror",
						method:"POST",
						data:
						{
							id:document.getElementById("id").value,
							
							f:document.getElementById("f").value,
							i:document.getElementById("i").value,
							o:document.getElementById("o").value,
						},
						success:function(data){document.getElementById("errrror").innerHTML=data;}
					})
				// в случае ошибок в форме
				}else{
					// перебираем массив с ошибками
					for(var errorField in data.text_error){
						// выводим текст ошибок 
						$('#'+errorField+'_error').html(data.text_error[errorField]);
						// показываем текст ошибок
						$('#'+errorField+'_error').show();
						// обводим инпуты красным цветом
					   // $('#'+errorField).addClass('error_input');                      
					}
					document.getElementById("errror").innerHTML='<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>'
				}
			}
		});		
	};  
	
	// !!! правильный пример AJAX
	function Ajax_change_shed() {
		
		//document.getElementById("changeShedOptionsReq").innerHTML = '<img src="img/wait.gif"> обработка...';
		
		//document.getElementById("changeShedOptionsReq").innerHTML = '';
		
		var day = document.getElementById("SelectDayShedOptions").value;
		var month = document.getElementById("SelectMonthShedOptions").value;
		var year = document.getElementById("SelectYearShedOptions").value;
		
		var ignoreshed = $("input[name=ignoreshed]:checked").val();
		if (typeof (ignoreshed) == 'undefined') ignoreshed = 0;
		
		//alert (ignoreshed);

		$.ajax({
			url:"sheduler_change_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				day:day,
				month:month,
				year:year,
				ignoreshed:ignoreshed,
			},
			cache: false,
			beforeSend: function() {
				$('#changeShedOptionsReq').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(data){
				$('#changeShedOptionsReq').html(data);
			}
		})
	};  
	
	function iWantThisDate(path){
		var iWantThisMonth = document.getElementById("iWantThisMonth").value;
		var iWantThisYear = document.getElementById("iWantThisYear").value;
													
		window.location.replace(path+'&m='+iWantThisMonth+'&y='+iWantThisYear);
	}
	
	function manageScheduler(){
		e = $('.manageScheduler');
		if(!e.is(':visible')) {
			e.show();
		}else{
			e.hide();
		}
		if (iCanManage) iCanManage = false; else iCanManage = true;
	}
	
	
	
	//Выборка стоматология
	function Ajax_show_result_stat_stom3(){
		$.ajax({
			url:"ajax_show_result_stat_stom3_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				all_time:all_time,
				datastart:document.getElementById("datastart").value,
				dataend:document.getElementById("dataend").value,
				
				all_age:all_age,
				agestart:document.getElementById("agestart").value,
				ageend:document.getElementById("ageend").value,
				
				worker:document.getElementById("search_worker").value,
				filial:document.getElementById("filial").value,
				
				pervich:document.querySelector('input[name="pervich"]:checked').value,
				insured:document.querySelector('input[name="insured"]:checked').value,
				noch:document.querySelector('input[name="noch"]:checked').value,
				
				sex:document.querySelector('input[name="sex"]:checked').value,
				wo_sex:wo_sex,

			},
			cache: false,
			beforeSend: function() {
				$('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(data){
				$('#qresult').html(data);
			}
		})
	}
	
	// Return an array of the selected opion values
	// select is an HTML select element
	function getSelectValues(select) {
		var result = [];
		var options = select && select.options;
		var opt;

		for (var i=0, iLen=options.length; i<iLen; i++) {
			opt = options[i];

			//if (opt.selected) {
				result.push(opt.value || opt.text);
			//}
		}
		return result;
	}
	
	//Выборка косметология
	function Ajax_show_result_stat_cosm_ex2(){
		
		var condition = [];
		var effect = [];
		
		var el_condition = document.getElementById("multi_d_to");
		var el_effect = document.getElementById("multi_d_to_2");

		condition = getSelectValues(el_condition);
		effect = getSelectValues(el_effect);
		
		//console.log(condition);
		//console.log(effect);
		
		$.ajax({
			url:"ajax_show_result_stat_cosm_ex2_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				all_time:all_time,
				datastart:document.getElementById("datastart").value,
				dataend:document.getElementById("dataend").value,
				
				all_age:all_age,
				agestart:document.getElementById("agestart").value,
				ageend:document.getElementById("ageend").value,
				
				worker:document.getElementById("search_worker").value,
				filial:document.getElementById("filial").value,
				
				//pervich:document.querySelector('input[name="pervich"]:checked').value,
				
				condition:condition,
				effect:effect,
				
				sex:document.querySelector('input[name="sex"]:checked').value,
				wo_sex:wo_sex,

			},
			cache: false,
			beforeSend: function() {
				$('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(data){
				$('#qresult').html(data);
			}
		})
	}
	
	//Выборка добавления пациентов
	function Ajax_show_result_stat_add_clients(){
		
		$.ajax({
			url:"ajax_show_result_stat_add_clients.php",
			global: false, 
			type: "POST", 
			data:
			{
				all_time:all_time,
				datastart:document.getElementById("datastart").value,
				dataend:document.getElementById("dataend").value,
				
				//all_age:all_age,
				//agestart:document.getElementById("agestart").value,
				//ageend:document.getElementById("ageend").value,
				
				worker:document.getElementById("search_worker").value,
				//filial:document.getElementById("filial").value,
				filial:99,
				
				//pervich:document.querySelector('input[name="pervich"]:checked').value,

				//sex:document.querySelector('input[name="sex"]:checked').value,
				//wo_sex:wo_sex,

			},
			cache: false,
			beforeSend: function() {
				$('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(data){
				$('#qresult').html(data);
			}
		})
	}
	
	// !!!!
	//$(document).ready(function () {
		$('#showDiv1').click(function () {
			$('#div1').stop(true, true).slideToggle('slow');
			$('#div2').slideUp('slow');
		});
		$('#showDiv2').click(function () {
			$('#div2').stop(true, true).slideToggle('slow');
			$('#div1').slideUp('slow');
		});
	//});
	
	//$(document).ready(function () {
		$('#toggleDiv1').click(function () {
			$('#div1').stop(true, true).slideToggle('slow');

		});
		$('#toggleDiv2').click(function () {
			$('#div2').stop(true, true).slideToggle('slow');
		});
		$('#toggleDiv3').click(function () {
			$('#div3').stop(true, true).slideToggle('slow');
		});
	//});

	
	//Для мультисельктора косметологии
    jQuery(document).ready(function($) {
        $('#multi_d').multiselect({
            right: '#multi_d_to, #multi_d_to_2',
            rightSelected: '#multi_d_rightSelected, #multi_d_rightSelected_2',
            leftSelected: '#multi_d_leftSelected, #multi_d_leftSelected_2',
            rightAll: '#multi_d_rightAll, #multi_d_rightAll_2',
            leftAll: '#multi_d_leftAll, #multi_d_leftAll_2',
     
            search: {
                left: '<input type="text" name="q" class="form-control" placeholder="Поиск..." />'
            },
     
            moveToRight: function(Multiselect, $options, event, silent, skipStack) {
                var button = $(event.currentTarget).attr('id');
     
                if (button == 'multi_d_rightSelected') {
                    var $left_options = Multiselect.$left.find('> option:selected');
                    Multiselect.$right.eq(0).append($left_options);
     
                    if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                        Multiselect.$right.eq(0).find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$right.eq(0));
                    }
                } else if (button == 'multi_d_rightAll') {
                    var $left_options = Multiselect.$left.children(':visible');
                    Multiselect.$right.eq(0).append($left_options);
     
                    if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                        Multiselect.$right.eq(0).find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$right.eq(0));
                    }
                } else if (button == 'multi_d_rightSelected_2') {
                    var $left_options = Multiselect.$left.find('> option:selected');
                    Multiselect.$right.eq(1).append($left_options);
     
                    if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                        Multiselect.$right.eq(1).find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$right.eq(1));
                    }
                } else if (button == 'multi_d_rightAll_2') {
                    var $left_options = Multiselect.$left.children(':visible');
                    Multiselect.$right.eq(1).append($left_options);
     
                    if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                        Multiselect.$right.eq(1).eq(1).find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$right.eq(1));
                    }
                }
            },
     
            moveToLeft: function(Multiselect, $options, event, silent, skipStack) {
                var button = $(event.currentTarget).attr('id');
     
                if (button == 'multi_d_leftSelected') {
                    var $right_options = Multiselect.$right.eq(0).find('> option:selected');
                    Multiselect.$left.append($right_options);
     
                    if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                        Multiselect.$left.find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$left);
                    }
                } else if (button == 'multi_d_leftAll') {
                    var $right_options = Multiselect.$right.eq(0).children(':visible');
                    Multiselect.$left.append($right_options);
     
                    if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                        Multiselect.$left.find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$left);
                    }
                } else if (button == 'multi_d_leftSelected_2') {
                    var $right_options = Multiselect.$right.eq(1).find('> option:selected');
                    Multiselect.$left.append($right_options);
     
                    if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                        Multiselect.$left.find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$left);
                    }
                } else if (button == 'multi_d_leftAll_2') {
                    var $right_options = Multiselect.$right.eq(1).children(':visible');
                    Multiselect.$left.append($right_options);
     
                    if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                        Multiselect.$left.find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$left);
                    }
                }
            }
        });
    });
