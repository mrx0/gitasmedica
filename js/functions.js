
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
									
							fo: document.getElementById("fo").value,
							io: document.getElementById("io").value,
							oo: document.getElementById("oo").value,
									
							comment:document.getElementById("comment").value,
								
							card:document.getElementById("card").value,
								
							therapist:document.getElementById("search_client2").value,
							therapist2:document.getElementById("search_client4").value,
							
							sel_date:document.getElementById("sel_date").value,
							sel_month:document.getElementById("sel_month").value,
							sel_year:document.getElementById("sel_year").value,
							
							telephone:document.getElementById("telephone").value,
							htelephone:document.getElementById("htelephone").value,
							
							telephoneo:document.getElementById("telephoneo").value,
							htelephoneo:document.getElementById("htelephoneo").value,
							
							passport:document.getElementById("passport").value,
							passportvidandata:document.getElementById("passportvidandata").value,
							passportvidankem:document.getElementById("passportvidankem").value,
							
							alienpassportser:document.getElementById("alienpassportser").value,
							alienpassportnom:document.getElementById("alienpassportnom").value,							
							
							address:document.getElementById("address").value,
							
							polis:document.getElementById("polis").value,
							polisdata:document.getElementById("polisdata").value,
							insurecompany:document.getElementById("insurecompany").value,
							
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
							
							fo: document.getElementById("fo").value,
							io: document.getElementById("io").value,
							oo: document.getElementById("oo").value,
							
							comment:document.getElementById("comment").value,
							
							card:document.getElementById("card").value,
							
							therapist:document.getElementById("search_client2").value,
							therapist2:document.getElementById("search_client4").value,
							
							sel_date:document.getElementById("sel_date").value,
							sel_month:document.getElementById("sel_month").value,
							sel_year:document.getElementById("sel_year").value,
							
							telephone:document.getElementById("telephone").value,
							htelephone:document.getElementById("htelephone").value,
							
							telephoneo:document.getElementById("telephoneo").value,
							htelephoneo:document.getElementById("htelephoneo").value,
							
							passport:document.getElementById("passport").value,
							passportvidandata:document.getElementById("passportvidandata").value,
							passportvidankem:document.getElementById("passportvidankem").value,
							
							alienpassportser:document.getElementById("alienpassportser").value,
							alienpassportnom:document.getElementById("alienpassportnom").value,	
							
							address:document.getElementById("address").value,
							
							polis:document.getElementById("polis").value,
							polisdata:document.getElementById("polisdata").value,
							insurecompany:document.getElementById("insurecompany").value,

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
	function Ajax_add_insure(session_id) {

		var name = document.getElementById("name").value;
		var contract = document.getElementById("contract").value;
		var contacts = document.getElementById("contacts").value;

		$.ajax({
			url:"add_insure_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				name:name,
				contract:contract ,
				contacts:contacts,
				session_id:session_id,
			},
			cache: false,
			beforeSend: function() {
				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(data){
				$('#errror').html(data);
			}
		})
	};  
	
	// !!! правильный пример AJAX
	function Ajax_add_priceitem(session_id) {

		var pricename = document.getElementById("pricename").value;
		var price = document.getElementById("price").value;

		$.ajax({
			url:"add_priceitem_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				pricename:pricename,
				price:price,
				session_id:session_id,
			},
			cache: false,
			beforeSend: function() {
				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(data){
				$('#errror').html(data);
			}
		})
	};  
	
	function Ajax_edit_service(session_id) {

		var servicename = document.getElementById("servicename").value;

		$.ajax({
			url:"serviceitem_edit_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				servicename:servicename,
				session_id:session_id,
			},
			cache: false,
			beforeSend: function() {
				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(data){
				$('#errror').html(data);
			}
		})
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
	
	function iWantThisDate2(path){
		var iWantThisDate2 = document.getElementById("iWantThisDate2").value;
		var ThisDate = iWantThisDate2.split('.') 
		
		window.location.replace(path+'&d='+ThisDate[0]+'&m='+ThisDate[1]+'&y='+ThisDate[2]);
	}
	
	function manageScheduler(){
		e = $('.manageScheduler');
		if(!e.is(':visible')) {
			e.show();
		}else{
			e.hide();
		}
		
		e2 = $('.nightSmena');
		if(!e2.is(':visible')) {
			e2.show();
		}else{
			e2.hide();
		}
		
		e3 = $('.fa-info-circle');
		if(e3.is(':visible')) {
			e3.hide();
		}else{
			e3.show();
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
	
	//Для Отсутствующие зубы
	function Ajax_show_result_stat_stom4(){
		$.ajax({
			url:"ajax_show_result_stat_stom4_f.php",
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
	
	function Ajax_show_result_stat_client_finance(){
		
		$.ajax({
			url:"ajax_show_result_stat_client_finance.php",
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
				
				//worker:document.getElementById("search_worker").value,
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

	
	// !!! Для сортировки таблиц ТЕСТ
	// var grid = document.getElementById('grid');
	var grid = document.getElementsByClassName('grid');
	//console.log(grid);
	
	var myFunction = function() {
		sortGrid(this.getAttribute('data-sort'), this.getAttribute('data-sort-cell'), this.getAttribute('data-type'));
	};
	
	for (var i = 0; i < grid.length; i++){
		grid[i].addEventListener('click', myFunction, false);
	}
	
    /*grid.onclick = function(e) {
		sortGrid(e.target.getAttribute('data-sort'), e.target.getAttribute('data-sort-cell'), e.target.getAttribute('data-type'));
    };*/

	function sortGrid(dataSort, cellNum, type) {
		// Составить масси
		var div = document.getElementById(dataSort);
		var elems = div.getElementsByTagName('li');
		var elemsArr = [].slice.call(elems);
		//console.log(elemsArr);
		
		// определить функцию сравнения, в зависимости от типа
		var compare;

		switch (type) {
			case 'number':
				compare = function(rowA, rowB) {
					return rowA.children[cellNum].innerHTML.toLowerCase() - rowB.children[cellNum].innerHTML.toLowerCase();
				};
			break;
			case 'string':
				compare = function(rowA, rowB) {
					return rowA.children[cellNum].innerHTML.toLowerCase() > rowB.children[cellNum].innerHTML.toLowerCase() ? 1 : -1;
				};
			break;
		}

		// сортировать
		elemsArr.sort(compare);
		
		// Убрать старое из большого DOM документа для лучшей производительности
		while (div.firstChild) {
			div.removeChild(div.firstChild);
		}

		// добавить результат в нужном порядке
		// они автоматически будут убраны со старых мест и вставлены в правильном порядке
		for (var i = 0; i < elemsArr.length; i++) {
			div.appendChild(elemsArr[i]);
		}
		//div.appendChild(tbody);
	}
	
	//Добавить аванс ИЛИ платёж
	function Ajax_finance_debt_add(client, session_id) {
		// убираем класс ошибок с инпутов
		$("input").each(function(){
			$(this).removeClass("error_input");
		});
		// прячем текст ошибок
		$(".error").hide();
		 
		$.ajax({
			// метод отправки 
			global: false, 
			type: "POST", 
			// путь до скрипта-обработчика
			url: "ajax_test.php",
			// какие данные будут переданы
			data: {
				summ:document.getElementById("summ").value,
			},
			cache: false,
			// тип передачи данных
			dataType: "json",
			beforeSend: function() {
				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				// в случае, когда пришло success. Отработало без ошибок
				if(data.result == "success"){   
					//alert("форма корректно заполнена");
					
					var type = document.getElementById("type").value;
					
					if (type == 3){
						var uri = 'finance_prepayment_add_f.php';
					}
					if (type == 4){
						var uri = 'finance_debt_add_f.php';
					}
					
					$.ajax({
						url: uri,
						statbox:"status",
						global: false, 
						type: "POST", 
						data:
						{
							client: client,
							summ:document.getElementById("summ").value,
							type:type,
							
							date_expires:document.getElementById("dataend").value,
							
							comment:document.getElementById("comment").value,
							
							session_id: session_id,
						},
						cache: false,
						beforeSend: function() {
							$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
						},
						success:function(data){
							document.getElementById("status").innerHTML=data;
						}
					})
				// в случае ошибок в форме
				}else{
					// перебираем массив с ошибками
					for(var errorField in data.text_error){
						// выводим текст ошибок 
						$("#"+errorField+"_error").html(data.text_error[errorField]);
						// показываем текст ошибок
						$("#"+errorField+"_error").show();
						// обводим инпуты красным цветом
					   // $("#"+errorField).addClass("error_input");                      
					}
					document.getElementById("errror").innerHTML='<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>'
				}
			}
		});						
	};  
	
	//Редактировать аванас ИЛИ платёж
	function Ajax_finance_debt_edit(id, session_id) {
		// убираем класс ошибок с инпутов
		$("input").each(function(){
			$(this).removeClass("error_input");
		});
		// прячем текст ошибок
		$(".error").hide();
		 
		$.ajax({
			global: false, 
			type: "POST", 
			// путь до скрипта-обработчика
			url: "ajax_test.php",
			// какие данные будут переданы
			data: {
				summ:document.getElementById("summ").value,
			},
			cache: false,
			beforeSend: function() {
				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// тип передачи данных
			dataType: "json",
			// действие, при ответе с сервера
			success: function(data){
				// в случае, когда пришло success. Отработало без ошибок
				if(data.result == "success"){   
					//alert("форма корректно заполнена");
					$.ajax({
						url:"finance_dp_edit_f.php",
						statbox:"status",
						global: false, 
						type: "POST", 
						data:
						{
							id: id,
							
							summ: document.getElementById("summ").value,
							
							date_expires:document.getElementById("dataend").value,
							
							comment: document.getElementById("comment").value,
							
							session_id: session_id,
						},
						cache: false,
						beforeSend: function() {
							$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
						},
						success:function(data){document.getElementById("status").innerHTML=data;}
					})
					// в случае ошибок в форме
				}else{
					// перебираем массив с ошибками
					for(var errorField in data.text_error){
					// выводим текст ошибок 
					$("#"+errorField+"_error").html(data.text_error[errorField]);
					// показываем текст ошибок
					$("#"+errorField+"_error").show();
					// обводим инпуты красным цветом
					 // $("#"+errorField).addClass("error_input");                      
					}
					document.getElementById("errror").innerHTML="<span style='color: red'>Ошибка, что-то заполнено не так.</span>"
				}
			}
		});
	}; 
	
	//Закрыть (Полное погашение) аванас ИЛИ платёж
	function Ajax_finance_dp_repayment_add(id) {
		// убираем класс ошибок с инпутов
		$("input").each(function(){
			$(this).removeClass("error_input");
		});
		// прячем текст ошибок
		$(".error").hide();
		 
		$.ajax({
			global: false, 
			type: "POST", 
			// путь до скрипта-обработчика
			url: "ajax_test.php",
			// какие данные будут переданы
			data: {
				summ:document.getElementById("summ").value,
			},
			cache: false,
			beforeSend: function() {
				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// тип передачи данных
			dataType: "json",
			// действие, при ответе с сервера
			success: function(data){
				// в случае, когда пришло success. Отработало без ошибок
				if(data.result == "success"){   
					//alert("форма корректно заполнена");
					$.ajax({
						url:"finance_dp_repayment_add_f.php",
						statbox:"status",
						global: false, 
						type: "POST", 
						data:
						{
							id: id,
							comment: document.getElementById("comment").value,
							summ: document.getElementById("summ").value,

						},
						cache: false,
						beforeSend: function() {
							$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
						},
						success:function(data){document.getElementById("status").innerHTML=data;}
					})
					// в случае ошибок в форме
				}else{
					// перебираем массив с ошибками
					for(var errorField in data.text_error){
					// выводим текст ошибок 
					$("#"+errorField+"_error").html(data.text_error[errorField]);
					// показываем текст ошибок
					$("#"+errorField+"_error").show();
					// обводим инпуты красным цветом
					 // $("#"+errorField).addClass("error_input");                      
					}
					document.getElementById("errror").innerHTML="<span style='color: red'>Ошибка, что-то заполнено не так.</span>"
				}
			}
		});
	}; 
	
	//Редактировать погашение
	function Ajax_finance_dp_repayment_edit(id) {
		// убираем класс ошибок с инпутов
		$("input").each(function(){
			$(this).removeClass("error_input");
		});
		// прячем текст ошибок
		$(".error").hide();
		 
		$.ajax({
			global: false, 
			type: "POST", 
			// путь до скрипта-обработчика
			url: "ajax_test.php",
			// какие данные будут переданы
			data: {
				summ:document.getElementById("summ").value,
			},
			cache: false,
			beforeSend: function() {
				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// тип передачи данных
			dataType: "json",
			// действие, при ответе с сервера
			success: function(data){
				// в случае, когда пришло success. Отработало без ошибок
				if(data.result == "success"){   
					//alert("форма корректно заполнена");
					$.ajax({
						url:"finance_dp_repayment_edit_f.php",
						statbox:"status",
						global: false, 
						type: "POST", 
						data:
						{
							id: id,
							comment: document.getElementById("comment").value,
							summ: document.getElementById("summ").value,

						},
						cache: false,
						beforeSend: function() {
							$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
						},
						success:function(data){document.getElementById("status").innerHTML=data;}
					})
					// в случае ошибок в форме
				}else{
					// перебираем массив с ошибками
					for(var errorField in data.text_error){
					// выводим текст ошибок 
					$("#"+errorField+"_error").html(data.text_error[errorField]);
					// показываем текст ошибок
					$("#"+errorField+"_error").show();
					// обводим инпуты красным цветом
					 // $("#"+errorField).addClass("error_input");                      
					}
					document.getElementById("errror").innerHTML="<span style='color: red'>Ошибка, что-то заполнено не так.</span>"
				}
			}
		});
	}; 
		
	function Ajax_add_TempZapis(type) {
		 
		// получение данных из полей
		//var type = document.getElementById("type").value;
		
		var filial = $("#filial").val();
		var author = $("#author").val();
		var year = $("#year").val();
		var month = $("#month").val();
		var day = $("#day").val();
		
		var patient = $("#search_client").val();
		//var contacts = $("#contacts").val();
		var contacts = 0;
		var description = $("#description").val();
		
		var start_time = $("#start_time").val();
		var wt = $("#wt").val();
		
		var kab = document.getElementById("kab").innerHTML;

		var worker = $("#search_client2").val();
		//alert(worker);
		if((typeof worker == "undefined") || (worker == "")) worker = 0;
		//alert(worker);
		
		if ($("#pervich").prop("checked")){
			var pervich = 1;
		}else{
			var pervich = 0;
		}
		if ($("#insured").prop("checked")){
			var insured = 1;
		}else{
			var insured = 0;
		}
		if ($("#noch").prop("checked")){
			var noch = 1;
		}else{
			var noch = 0;
		}
		
		$.ajax({
			global: false, 
			type: "POST", 
			// путь до скрипта-обработчика
			url: "edit_schedule_day_f.php",
			// какие данные будут переданы
			data: {
				type:"scheduler_stom",
				author:author,
				filial:filial,
				kab:kab,
				day:day,
				month:month,
				year:year,
				start_time:start_time,
				wt:wt,
				worker:worker,
				description:description,
				contacts:contacts,
				patient:patient,
				
				pervich:pervich,
				insured:insured,
				noch:noch,
				
				type:type
			},
			cache: false,
			beforeSend: function() {
				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			dataType: "json",
			// действие, при ответе с сервера
			success: function(data){
				if(data.result == "success"){  
					document.getElementById("errror").innerHTML=data.data;
					setTimeout(function () {
						location.reload()
					}, 100);
				}else{
					document.getElementById("errror").innerHTML=data.data;
				}
			}
		});		
	};
	
	function Ajax_TempZapis_edit_Enter(id, enter) {
		if (enter == 8){
			var rys = confirm("Вы хотите удалить запись. \nЕё невозможно будет восстановить. \n\nВы уверены?");
		}else{
			var rys = true;
		}
		if (rys){
			$.ajax({
				//statbox:SettingsScheduler,
				// метод отправки 
				type: "POST",
				// путь до скрипта-обработчика
				url: "ajax_tempzapis_edit_enter_f.php",
				// какие данные будут переданы
				data: {
					id:id,
					enter:enter,
					datatable: "zapis"
				},
				// действие, при ответе с сервера
				success: function(data){
					//document.getElementById("req").innerHTML=data;
					//window.location.href = "";
					setTimeout(function () {
						location.reload()
					}, 100);
				}
			});	
		}
	};


	function Ajax_TempZapis_edit_OK(id, office) {
		 
		$.ajax({
			//statbox:SettingsScheduler,
			// метод отправки 
			type: "POST",
			// путь до скрипта-обработчика
			url: "ajax_tempzapis_edit_OK_f.php",
			// какие данные будут переданы
			data: {
				id:id,
				office:office,
				datatable: "zapis"
			},
			// действие, при ответе с сервера
			success: function(data){
				//document.getElementById("req").innerHTML=data;
				//window.location.href = "";
				setTimeout(function () {
					location.reload()
				}, 100);
			}
		});		
	};
	
	function PriemTimeCalc(){
		var work_time_h = Number(document.getElementById("work_time_h").value);
		var work_time_m = Number(document.getElementById("work_time_m").value);
		
		var start_time = work_time_h*60+work_time_m;
		
		document.getElementById("start_time").value = start_time;
		
		//var start_time = Number(document.getElementById("start_time").value);
		var change_hours = Number(document.getElementById("change_hours").value);
		var change_minutes = Number(document.getElementById("change_minutes").value);
		
		var day = Number(document.getElementById("day").value);
		var month = Number(document.getElementById("month").value);
		var year = Number(document.getElementById("year").value);
		
		var filial = Number(document.getElementById("filial").value);
		var kab = document.getElementById("kab").innerHTML;
		
		//alert(kab);
		//alert(wt);
		//alert(start_time);
		//alert(start_time+wt);

		if (change_minutes > 59){
			change_minutes = 59;
			document.getElementById("change_minutes").value = 59;
		}
		if (change_hours > 12){
			change_hours = 11;
			document.getElementById("change_hours").value = 11;
		}
		if ((change_hours == 0) && (change_minutes == 0)){
			change_minutes = 5;
			document.getElementById("change_minutes").value = 5;
		}
			
		
		var next_time_start_rez = 0;
		
		$.ajax({
			dataType: "json",
			async: false,
			// метод отправки 
			type: "POST",
			// путь до скрипта-обработчика
			url: "get_next_zapis.php",
			// какие данные будут переданы
			data: {
				day:day,
				month:month,
				year:year,
				
				filial:filial,
				kab:kab,
				
				start_time:start_time,
				wt:change_hours*60+change_minutes,
				
				datatable:"zapis"
			},
			// действие, при ответе с сервера
			success: function(next_zapis_data){
				//alert (next_zapis_data.next_time_start);
				//document.getElementById("kab").innerHTML=nex_zapis_data;
				next_time_start_rez = next_zapis_data.next_time_start;
				next_time_end_rez = next_zapis_data.next_time_end;
				//next_zapis_data;
				
			}
		});		
						
		//alert (next_time_start_rez);
		//alert (next_time_end_rez);
						
		//alert(change_hours);
		//alert(change_minutes);
		
		var end_time = start_time+change_hours*60+change_minutes;
		
		//if (end_time > 1260){
			//alert(\'Перебор\');
		//}
		
		//alert(start_time+' == '+next_time_start_rez);
		
		if (next_time_start_rez != 0){
			//alert(next_time_start_rez);
			
			//if ((end_time > next_time_start_rez) || (start_time == next_time_start_rez)){
			if (((end_time > next_time_start_rez) && (end_time < next_time_end_rez)) || ((start_time >= next_time_start_rez) && (start_time < next_time_end_rez))){
				//alert(next_time_start_rez);
				document.getElementById("exist_zapis").innerHTML='<span style="color: red">Дальше есть запись</span>';
				
				var raznica_vremeni = Math.abs(next_time_start_rez - start_time);
				
				document.getElementById("change_hours").value = raznica_vremeni/60|0;
				document.getElementById("change_minutes").value = raznica_vremeni%60;
				
				change_hours = raznica_vremeni/60|0;
				change_minutes = raznica_vremeni%60;
				
				end_time = start_time+change_hours*60+change_minutes;
				
				document.getElementById("Ajax_add_TempZapis").disabled = true; 
			}else{
			//if (end_time < next_time_start_rez){
				document.getElementById("exist_zapis").innerHTML='';
				document.getElementById("Ajax_add_TempZapis").disabled = false; 
			}
		}else{
			document.getElementById("exist_zapis").innerHTML='';
			document.getElementById("Ajax_add_TempZapis").disabled = false; 
		}
					
		var real_time_h_end = end_time/60|0;
		if (real_time_h_end > 23) real_time_h_end = real_time_h_end - 24;
		var real_time_m_end = end_time%60;
						
		//var real_time_h_end = (end_time + Number(wt))/60|0;
		//var real_time_m_end = (end_time + Number(wt))%60;
						
		if (real_time_m_end < 10) real_time_m_end = '0'+real_time_m_end;
						
		document.getElementById("work_time_h_end").innerHTML=real_time_h_end;
		document.getElementById("work_time_m_end").innerHTML=real_time_m_end;
						
		document.getElementById("wt").value=change_hours*60+change_minutes;
	}
	
	//События при наведении/убирании мыши СуперТест!
	document.body.onmouseover = document.body.onmouseout = handler;

	function handler(event) {

		/*function str(el) {
			if (!el) return "null"
			return el.className || el.tagName;
		}*/

		e = $('#ShowDescrTempZapis');
		
		if (event.type == 'mouseover') {
			if (event.target.className == 'cellZapisVal'){
				var id = $(this).attr('clientid');
				
				//if(!e.is(':visible')) {
					e.show();
				//}else{
				//	e.hide();
				//}
			}
		}
		
		if (event.type == 'mouseout') {
			e.hide();
			/*if (event.target.className == 'cellZapisVal'){
				var id = $(this).attr('clientid');
				event.target.style.background = '';
			}*/
		}
	}
