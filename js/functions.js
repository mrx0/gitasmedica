	//попытка показать контекстное меню
	function contextMenuShow(ind, key, event, mark){
		//alert(mark);
		
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		// Получаем элемент на котором был совершен клик:
		var target = $(event.target);

		// Добавляем класс selected-html-element что бы наглядно показать на чем именно мы кликнули (исключительно для тестирования):
		target.addClass('selected-html-element');
		
		$.ajax({
			url:"context_menu_show_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				mark: mark,
				ind: ind,
				key: key,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
				//alert(res.data);
				
				if (mark == 'zapis_options'){
					res.data = $('#zapis_options'+ind+'').html();
				}
				
				// Создаем меню:
				var menu = $('<div/>', {
					class: 'context-menu' // Присваиваем блоку наш css класс контекстного меню:
				})
				.css({
					left: event.pageX+'px', // Задаем позицию меню на X
					top: event.pageY+'px' // Задаем позицию меню по Y
				})
				.appendTo('body') // Присоединяем наше меню к body документа:
				.append( // Добавляем пункты меню:
					$('<ul/>').append(res.data)
				);
				
				
				if ((mark == 'insure') || (mark == 'insureItem')){
					menu.css({
						'height': '300px',
						'overflow-y': 'scroll',
					});
				}
					
				menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
		
			}
		});
	}


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
				
				//sel_date:document.getElementById("sel_date").value,
				//sel_month:document.getElementById("sel_month").value,
				//sel_year:document.getElementById("sel_year").value,
				
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
	
	function Ajax_del_client(session_id) {
		var id = document.getElementById("id").value;
		
		ajax({
			url:"client_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
				session_id: session_id,
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('client.php?id='+id);
					//alert('client.php?id='+id);
				}, 100);
			}
		})
	}; 
	
	function Ajax_del_pricelistgroup(id, session_id) {
		
		//var id = document.getElementById("id").value;
		if ($("#deleteallin").prop("checked")){
			var deleteallin = 1;
		}else{
			var deleteallin = 0;
		}
		
		ajax({
			url:"pricelistgroup_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
				deleteallin:deleteallin,
				session_id: session_id,
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('pricelistgroup.php?id='+id);
					//alert('client.php?id='+id);
				}, 100);
			}
		})
	}; 
	
	//Удаление позиции прайса
	function Ajax_del_pricelistitem(id) {
		
		ajax({
			url:"pricelistitem_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('pricelistitem.php?id='+id);
					//alert('client.php?id='+id);
				}, 100);
			}
		})
	}; 
	
	//Удаление позиции прайса страховой
	function Ajax_del_pricelistitem_insure(id, insure) {
		
		ajax({
			url:"pricelistitem_insure_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
				insure: insure,
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('insure_price.php?id='+insure);
					//alert('client.php?id='+id);
				}, 100);
			}
		})
	}; 
	
	//Заполнить прайс
	function Ajax_insure_price_fill(id) {
		
		ajax({
			url:"insure_price_fill_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
				group: document.getElementById("group").value,
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				/*setTimeout(function () {
					window.location.replace('pricelistitem.php?id='+id);
					//alert('client.php?id='+id);
				}, 100);*/
			}
		})
	}; 
	
	//Очисить прайс
	function Ajax_insure_price_clear(id) {
		
		ajax({
			url:"insure_price_clear_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				/*setTimeout(function () {
					window.location.replace('pricelistitem.php?id='+id);
					//alert('client.php?id='+id);
				}, 100);*/
			}
		})
	}; 
	
	function Ajax_del_insure(id) {
		
		ajax({
			url:"insure_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('insure.php?id='+id);
					//alert('client.php?id='+id);
				}, 100);
			}
		})
	}; 
	
	function Ajax_reopen_client(session_id, id) {
		//var id = document.getElementById("id").value;
		
		ajax({
			url:"client_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
				session_id: session_id,
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('client.php?id='+id);
					//alert('client.php?id='+id);
				}, 100);
			}
		})
	}; 
	
	function Ajax_reopen_pricelistitem(id) {
		//var id = document.getElementById("id").value;
		
		ajax({
			url:"pricelistitem_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('pricelistitem.php?id='+id);
					//alert('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	}; 
	
	function Ajax_reopen_insure(id) {
		//var id = document.getElementById("id").value;
		
		ajax({
			url:"insure_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('insure.php?id='+id);
					//alert('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	}; 
	
	function Ajax_reopen_pricelistgroup(session_id, id) {
		//var id = document.getElementById("id").value;
		
		ajax({
			url:"pricelistgroup_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
				session_id: session_id,
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('pricelistgroup.php?id='+id);
					//alert('pricelistgroup.php?id='+id);
				}, 100);
			}
		})
	}; 
	//Перемещения косметологии другому пациенту
	function Ajax_cosm_move(session_id, id) {
		//var id = document.getElementById("id").value;
		
		ajax({
			url:"cosm_move_f.php",
			method:"POST",
			data:
			{
				id: id,
				session_id: session_id,
			},
			success:function(data){
				setTimeout(function () {
					window.location.replace('client.php?id='+id);
				}, 100);
			}
		})
	}; 
	//Перемещение всего другому пациенту
	function Ajax_move_all(id) {
		//var id = document.getElementById("id").value;
		var name = document.getElementById("search_client").value;
		
		var rys = false;
		
		var rys = confirm("Вы хотите перенести записи другому пациенту. \nЭто невозможно будет исправить \n\nВы уверены?");

		if (rys){
			$.ajax({
				url:"move_all_f.php",
				global: false, 
				type: "POST", 
				data:
				{
					id: id,
					client: name,
				},
				cache: false,
				beforeSend: function() {
					$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
				},
				success:function(data){
					$('#errrror').html(data);
					/*setTimeout(function () {
						window.location.replace('client.php?id='+id);
					}, 100);*/
				}
			})
		}
	}; 
	//Редактировать ФИО пациента
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
				$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(data){
				$('#errrror').html(data);
			}
		})
	}; 

	function Ajax_edit_insure(id) {
		// убираем класс ошибок с инпутов
		$('input').each(function(){
			$(this).removeClass('error_input');
		});
		// прячем текст ошибок
		$('.error').hide();
		 
		var name = document.getElementById("name").value;
		var contract = document.getElementById("contract").value;
		var contacts = document.getElementById("contacts").value;
		 
		$.ajax({
			// метод отправки 
			type: "POST",
			// путь до скрипта-обработчика
			url: "ajax_test.php",
			// какие данные будут переданы
			data: {
				name:name,
			},
			// тип передачи данных
			dataType: "json",
			// действие, при ответе с сервера
			success: function(data){
				// в случае, когда пришло success. Отработало без ошибок
				if(data.result == 'success'){   
					//alert('форма корректно заполнена');
					ajax({
						url:"insure_edit_f.php",
						statbox:"errrror",
						method:"POST",
						data:
						{
							id:id,
							
							name:name,
							contract:contract,
							contacts:contacts,
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
					document.getElementById("errrror").innerHTML='<div class="query_neok">Ошибка, что-то заполнено не так.</div>'
				}
			}
		});	
	}; 
	
	
	// !!! правильный пример AJAX
	function Ajax_add_priceitem(session_id) {

		var pricename = document.getElementById("pricename").value;
		var price = document.getElementById("price").value;
		var group = document.getElementById("group").value;
		var iWantThisDate2 = document.getElementById("iWantThisDate2").value;
		
		$.ajax({
			url:"add_priceitem_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				pricename:pricename,
				price:price,
				group:group,
				iWantThisDate2:iWantThisDate2,
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
	
	//Добавить в прайс страховой
	function Ajax_add_insure_priceitem() {

		var pricename = document.getElementById("pricename").value;
		var price = document.getElementById("price").value;
		var group = document.getElementById("group").value;
		var iWantThisDate2 = document.getElementById("iWantThisDate2").value;
		
		$.ajax({
			url:"add_priceitem_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				pricename:pricename,
				price:price,
				group:group,
				iWantThisDate2:iWantThisDate2,
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
	
	// Добавим группу прайса
	function Ajax_add_pricegroup(session_id) {

		var groupname = document.getElementById("groupname").value;
		var group = document.getElementById("group").value;

		$.ajax({
			url:"add_pricegroup_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				groupname:groupname,
				group:group,
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
	
	function Ajax_edit_pricelistitem(id, session_id) {

		var pricelistitemname = document.getElementById("pricelistitemname").value;
		var group = document.getElementById("group").value;

		$.ajax({
			url:"pricelistitem_edit_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				pricelistitemname:pricelistitemname,
				session_id:session_id,
				group:group,
				id: id,
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
	
	function Ajax_edit_pricelistgroup(id, session_id) {

		var pricelistgroupname = document.getElementById("pricelistgroupname").value;
		var group = document.getElementById("group").value;

		$.ajax({
			url:"pricelistgroup_edit_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				pricelistgroupname:pricelistgroupname,
				session_id:session_id,
				group:group,
				id: id,
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
	
	function Ajax_edit_price(id, session_id) {

		var price = document.getElementById("price").value;
		var iWantThisDate2 = document.getElementById("iWantThisDate2").value;

		$.ajax({
			url:"priceprice_edit_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				session_id:session_id,
				price:price,
				iWantThisDate2:iWantThisDate2,
				id: id,
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
	
	function Ajax_edit_price_insure(id, insure) {

		var price = document.getElementById("price").value;
		var iWantThisDate2 = document.getElementById("iWantThisDate2").value;

		$.ajax({
			url:"priceprice_insure_edit_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				price: price,
				iWantThisDate2: iWantThisDate2,
				id: id,
				insure: insure,
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
		
		e4 = $('.managePriceList');
		if(e4.is(':visible')) {
			e4.hide();
		}else{
			e4.show();
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
	
	//Добавление записи
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
	
	//Редактирование записи
	function Ajax_edit_TempZapis(type) {
		 
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
		
		var id = document.getElementById("zapis_id").value;
		
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
			url: "edit_zapis_day_f.php",
			// какие данные будут переданы
			data: {
				type:"scheduler_stom",
				id:id,
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
		var type = document.getElementById("type").value;
		
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
		var next_time_end_rez = 0;
		
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
				
				type:type,
				
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

	//Смена пароля
	function changePass(id) {
		
		var rys = confirm("Вы хотите сменить пароль. \n\nВы уверены?");

		if (rys){
			ajax({
				url:"change_pass_f.php",
				//statbox:"errrror",
				method:"POST",
				data:
				{
					id: id,
				},
				success:function(data){
					alert(data);
				}
			})
		}
	};
	
	//Подсчёт суммы для счёта
	function calculateInvoice (invoice_type){
		
		var Summ = 0;
		var SummIns = 0;
		
		var insure = 0;
		var insureapprove = 0;
		
		document.getElementById("calculateInvoice").innerHTML = Summ;
		if (invoice_type == 5){
			document.getElementById("calculateInsInvoice").innerHTML = SummIns;
		}

		$(".invoiceItemPrice").each(function() {
			
			//console.log(document.getElementById($(this).parent().find('[insure]')).getAttribute('insure'));
			//console.log($(this).prev().prev().attr('insure'));
			//alert($(this).attr("insure"));
			
			if (invoice_type == 5){
				//получаем значение страховой
				insure = $(this).prev().prev().attr('insure');
				//console.log(insure);
			
				//получаем значение согласования
				insureapprove = $(this).prev().attr('insureapprove');
			}
			
			//получаем значение гарантии
			var guarantee = $(this).next().next().next().next().attr('guarantee');
			//console.log(insure);
			
			//Цена
			var cost = Number(this.innerHTML);
					
			var ind = $(this).attr('ind');
			var key = $(this).attr('key');
			//alert(ind);
			//alert(key);
			//alert(cost);
				
			//обновляем цену в сессии как можем 
			$.ajax({
				url: 'add_price_price_id_in_item_invoice_f.php',
				global: false, 
				type: "POST", 
				dataType: "JSON",
				data:
				{
					client: document.getElementById("client").value,
					zapis_id: document.getElementById("zapis_id").value,
					filial: document.getElementById("filial").value,
					worker: document.getElementById("worker").value,
					
					invoice_type: invoice_type,
					
					ind: ind,
					key: key,
					
					price: cost,
				},
				cache: false,
				beforeSend: function() {
					//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
				},
				// действие, при ответе с сервера
				success: function(data){
					//if(data.result == "success"){  
						//alert(data.data);
						//$('#invoice_rezult').html(data.data);
						
					//}else{
						//alert('error');
					//	$('#errror').html(data.data);
					//}
				}
			});
			
			//коэффициент
			var spec_koeff = Number($(this).parent().find('.spec_koeffInvoice').html());

			//скидка акция
			var discount = $(this).next().next().next().attr('discount');
			//alert(discount);
						
			//взяли количество
			var quantity = Number($(this).parent().find('[type=number]').val());
					
			//вычисляем стоимость
			var stoim = quantity * (cost +  cost / 100 * spec_koeff)
						
			//с учетом скидки акции
			stoim = stoim - (stoim / 100 * discount);
			stoim = Math.ceil(stoim/10) * 10			
				
			//прописываем стоимость
			if (guarantee == 0){
				$(this).next().next().next().next().next().html(stoim);
			}
						
			if (guarantee == 0){
				if (insure != 0){
					if (insureapprove != 0){
						SummIns += stoim;
					}
				}else{
					Summ += stoim;
				}
			}
			
		});
		
		document.getElementById("calculateInvoice").innerHTML = Summ;
		if (SummIns > 0){
			document.getElementById("calculateInsInvoice").innerHTML = SummIns;
		}
		
	};
	
	//Окрасить кнопки с зубами
	function colorizeTButton (t_number_active){
		$(".sel_tooth").each(function() {
			this.style.background = '';
		});
		$(".sel_toothp").css({'background': ""});
		
		if (t_number_active == 99){
			$(".sel_toothp").css({'background': "#83DB53"});
		}else{
			$(".sel_tooth").each(function() {
				if (Number(this.innerHTML) == t_number_active){
					this.style.background = '#83DB53';
				}
			});
		}
	}
	
	//Функция заполняет результат счета из сессии
	function fillInvoiseRez(){
		
		var invoice_type = document.getElementById("invoice_type").value;
		
		var link = "fill_invoice_stom_from_session_f.php";
		if (invoice_type == 6){
			link = "fill_invoice_cosm_from_session_f.php";
		}
		$.ajax({
			url: link,
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
					
					// !!!
					calculateInvoice(invoice_type);
					
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}
			}
		});
		//$('#errror').html('Результат');
		//calculateInvoice();
	}
	
	// что-то как-то я хз, типа добавляем в сессию новый зуб
	function addInvoiceInSession(t_number){
		
		colorizeTButton(t_number);
			
		//Отправляем в сессию
		$.ajax({
			url:"add_invoice_in_session_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				t_number: t_number,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
			},
			cache: false,
			beforeSend: function() {
				//$(\'#errrror\').html("<div style=\'width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);\'><img src=\'img/wait.gif\' style=\'float:left;\'><span style=\'float: right;  font-size: 90%;\'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();
				
				if(data.result == "success"){  
					//$(\'#errror\').html(data.data);

					
				}else{
					$('#errror').html(data.data);
				}

			}
		})
	}
	//меняет кол-во позиции
	function changeQuantityInvoice(zub, itemId, dataObj){
		//alert(dataObj.value);
		//console.log(this);
		
		var invoice_type = document.getElementById("invoice_type").value;
		
		//количество
		var quantity = dataObj.value;
		//alert(quantity);
		
		$.ajax({
			url:"add_quantity_price_id_in_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				key: itemId,
				zub: zub,
				
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
				
				quantity: quantity,
				invoice_type: invoice_type,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();
				
				//$('#errror').html(data);
				//if(data.result == "success"){
				//	alert(data.data);
					
				//	colorizeTButton (data.t_number_active);
					
					/*$(".sel_tooth").each(function() {
						if (Number(this.innerHTML) == data.t_number_active){
							this.style.background = '#83DB53';
						}else{
							this.style.background = '';
						}
					});*/
				//}
				/*else{
					//alert('error');
					$('#errror').html(data.data);
				}*/

			}
		});
	}
	
	//Удалить текущую позицию
	function deleteInvoiceItem(zub, dataObj){
		//alert(dataObj.getAttribute("invoiceitemid"));

		//номер позиции
		var itemId = dataObj.getAttribute("invoiceitemid");
		var target = 'item';
		
		if ((itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
			target = 'zub';
		}
		
		$.ajax({
			url:"delete_invoice_item_from_session_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				key: itemId,
				zub: zub,
				
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
				
				target: target,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				//$('#errror').html(data);
				if(data.result == "success"){
					//alert(111);
					
					colorizeTButton (data.t_number_active);
					
					/*$(".sel_tooth").each(function() {
						if (Number(this.innerHTML) == data.t_number_active){
							this.style.background = '#83DB53';
						}else{
							this.style.background = '';
						}
					});*/
				}
				/*else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
				

			}
		});
	}	

	//Удалить текущий диагноз МКБ
	function deleteMKBItem(zub){

		$.ajax({
			url:"delete_mkb_item_from_session_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				zub: zub,
				
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				//$('#errror').html(data);
				//if(data.result == "success"){
					//alert(111);
					
					//colorizeTButton (data.t_number_active);
					
					/*$(".sel_tooth").each(function() {
						if (Number(this.innerHTML) == data.t_number_active){
							this.style.background = '#83DB53';
						}else{
							this.style.background = '';
						}
					});*/
				//}
				/*else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});
	}	

	//Изменить коэффициент специалиста у всех
	function spec_koeffInvoice(spec_koeff){
		
		var invoice_type = document.getElementById("invoice_type").value;
		
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		$.ajax({
			url:"add_spec_koeff_price_id_in_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				spec_koeff: spec_koeff,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
				
				invoice_type: invoice_type,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});
		//$(".invoiceItemPrice").each(function() {
			
			//this.innerHTML = Number(this.innerHTML) + Number(this.innerHTML) / 100 * koeff
			
			//написали стоимость позиции
			//$(this).next().next().next().html(quantity * Number(this.innerHTML));
			
			
		//});
		
	}	

	//Изменить гарантию у всех
	function guaranteeInvoice(guarantee){
		
		var invoice_type = document.getElementById("invoice_type").value;
		
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		$.ajax({
			url:"add_guarantee_in_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				guarantee: guarantee,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
				
				invoice_type: invoice_type,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});
		//$(".invoiceItemPrice").each(function() {
			
			//this.innerHTML = Number(this.innerHTML) + Number(this.innerHTML) / 100 * koeff
			
			//написали стоимость позиции
			//$(this).next().next().next().html(quantity * Number(this.innerHTML));
			
			
		//});
		
	}	
	//Изменить согласование у всех
	function insureApproveInvoice(approve){
		
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		$.ajax({
			url:"add_insure_approve_in_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				approve: approve,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});
		//$(".invoiceItemPrice").each(function() {
			
			//this.innerHTML = Number(this.innerHTML) + Number(this.innerHTML) / 100 * koeff
			
			//написали стоимость позиции
			//$(this).next().next().next().html(quantity * Number(this.innerHTML));
			
			
		//});
		
	}	

	//Изменить скидку у всех
	function discountInvoice(discount){
		//alert(discount);
		
		var invoice_type = document.getElementById("invoice_type").value;
		
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		$.ajax({
			url:"add_discount_price_id_in_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				discount: discount,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
				
				invoice_type: invoice_type,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});
		//$(".invoiceItemPrice").each(function() {
			
			//this.innerHTML = Number(this.innerHTML) + Number(this.innerHTML) / 100 * koeff
			
			//написали стоимость позиции
			//$(this).next().next().next().html(quantity * Number(this.innerHTML));
			
			
		//});
		
	}	

	//Изменить страховую у всех
	function insureInvoice(insure){
		
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		$.ajax({
			url:"add_insure_price_id_in_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				insure: insure,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});
		//$(".invoiceItemPrice").each(function() {
			
			//this.innerHTML = Number(this.innerHTML) + Number(this.innerHTML) / 100 * koeff
			
			//написали стоимость позиции
			//$(this).next().next().next().html(quantity * Number(this.innerHTML));
			
			
		//});
		
	}	

	//Изменить страховую у этого зуба
	function insureItemInvoice(zub, key, insure){
		
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		$.ajax({
			url:"add_insure_price_id_in_item_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				zub: zub,
				key: key,
				insure: insure,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});
		//$(".invoiceItemPrice").each(function() {
			
			//this.innerHTML = Number(this.innerHTML) + Number(this.innerHTML) / 100 * koeff
			
			//написали стоимость позиции
			//$(this).next().next().next().html(quantity * Number(this.innerHTML));
			
			
		//});
		
	}	

	//Изменить согласование у этого зуба
	function insureApproveItemInvoice(zub, key, approve){
		
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		$.ajax({
			url:"add_insure_approve_price_id_in_item_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				zub: zub,
				key: key,
				approve: approve,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});
		//$(".invoiceItemPrice").each(function() {
			
			//this.innerHTML = Number(this.innerHTML) + Number(this.innerHTML) / 100 * koeff
			
			//написали стоимость позиции
			//$(this).next().next().next().html(quantity * Number(this.innerHTML));
			
			
		//});
		
	}	

	//Изменить гарантию у этого зуба
	function guaranteeItemInvoice(zub, key, guarantee){
		
		var invoice_type = document.getElementById("invoice_type").value;
		
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		$.ajax({
			url:"add_guarantee_price_id_in_item_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				zub: zub,
				key: key,
				guarantee: guarantee,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
				
				invoice_type: invoice_type,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});
		//$(".invoiceItemPrice").each(function() {
			
			//this.innerHTML = Number(this.innerHTML) + Number(this.innerHTML) / 100 * koeff
			
			//написали стоимость позиции
			//$(this).next().next().next().html(quantity * Number(this.innerHTML));
			
			
		//});
		
	}	

	//Изменить Коэффициент у этого зуба
	function spec_koeffItemInvoice(zub, key, spec_koeff){
		
		var invoice_type = document.getElementById("invoice_type").value;
		
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		$.ajax({
			url:"add_spec_koeff_price_id_in_item_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				zub: zub,
				key: key,
				spec_koeff: spec_koeff,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
				
				invoice_type: invoice_type,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});
		//$(".invoiceItemPrice").each(function() {
			
			//this.innerHTML = Number(this.innerHTML) + Number(this.innerHTML) / 100 * koeff
			
			//написали стоимость позиции
			//$(this).next().next().next().html(quantity * Number(this.innerHTML));
			
			
		//});
		
	}	

	//Изменить скидка акция у этого зуба
	function discountItemInvoice(zub, key, discount){
		
		var invoice_type = document.getElementById("invoice_type").value;
		
		//alert(discount);
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		$.ajax({
			url:"add_discount_price_id_in_item_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				zub: zub,
				key: key,
				discount: discount,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
				
				invoice_type: invoice_type,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});
		//$(".invoiceItemPrice").each(function() {
			
			//this.innerHTML = Number(this.innerHTML) + Number(this.innerHTML) / 100 * koeff
			
			//написали стоимость позиции
			//$(this).next().next().next().html(quantity * Number(this.innerHTML));
			
			
		//});
		
	}	

	//Выбор зуба из таблички 
	function toothInInvoice(t_number){

		//alert (t_number);
		$.ajax({
			url:"add_invoice_in_session_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				t_number: t_number,
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				if(data.result == "success"){  
					//$('#errror').html(data.data);
					
				}else{
					$('#errror').html(data.data);
				}
			}
		})
				
		colorizeTButton(t_number);
	}
			
	//Добавить позицию из прайса в счет
	function checkPriceItem(price_id, type){
		//alert(100);
		
		var link = "add_price_id_stom_in_invoice_f.php";
		if (type == 6){
			link = "add_price_id_cosm_in_invoice_f.php";
		}
		$.ajax({
			url: link,
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				price_id: price_id,
				client: document.getElementById("client").value,
				client_insure: document.getElementById("client_insure").value,
				zapis_id: document.getElementById("zapis_id").value,
				zapis_insure: document.getElementById("zapis_insure").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();

				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});

	}; 
	
	//Добавить позицию из МКБ в акт
	function checkMKBItem(mkb_id){
		//alert(100);
		$.ajax({
			url:"add_mkb_id_in_invoice_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				mkb_id: mkb_id,
				client: document.getElementById("client").value,
				client_insure: document.getElementById("client_insure").value,
				zapis_id: document.getElementById("zapis_id").value,
				zapis_insure: document.getElementById("zapis_insure").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				
				fillInvoiseRez();
				
				/*if(data.result == "success"){  
					//alert(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//alert('error');
					$('#errror').html(data.data);
				}*/
			}
		});

	}; 
	
	//Полностью чистим счёт
	function clearInvoice(){
		
		var rys = false;
		
		var rys = confirm("Очистить?");

		if (rys){
			$.ajax({
				url:"invoice_clear_f.php",
				global: false, 
				type: "POST", 
				dataType: "JSON",
				data:
				{
					client: document.getElementById("client").value,
					zapis_id: document.getElementById("zapis_id").value,
				},
				cache: false,
				beforeSend: function() {
					//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
				},
				// действие, при ответе с сервера
				success: function(data){
					
					fillInvoiseRez();
					
					colorizeTButton();
				}
			});
			
		}
	}; 
	
	// !!! Перенесли отсюда документ реади в инвойс_адд
	
	//Tree
	$(document).ready(function(){
		/*
		$(".ul-dropfree").find("li:has(ul)").prepend('<div class="drop"></div>');
		$(".ul-dropfree .drop").click(function() {
			if ($(this).nextAll("ul").css('display')=='none') {
				$(this).nextAll("ul").slideDown(400);
				$(this).css({'background-position':"-11px 0"});
			} else {
				$(this).nextAll("ul").slideUp(400);
				$(this).css({'background-position':"0 0"});
			}
		});
		$(".ul-dropfree").find("ul").slideUp(400).parents("li").children("div.drop").css({'background-position':"0 0"});
		*/
		$(".ul-drop").find("li:has(ul)").prepend('<div class="drop"></div>');
		$(".ul-drop .drop").click(function() {
			if ($(this).nextAll("ul").css('display')=='none') {
				$(this).nextAll("ul").slideDown(400);
				$(this).prev("div").css({'background-position':"-11px 0"});
				$(this).css({'background-position':"-11px 0"});
			} else {
				$(this).nextAll("ul").slideUp(400);
				$(this).prev("div").css({'background-position':"0 0"});
				$(this).css({'background-position':"0 0"});
			}
		});
		$(".ul-drop").find("ul").slideUp(400).parents("li").children("div.drop").css({'background-position':"0 0"});

		$(".lasttreedrophide").click(function(){
			$("#lasttree").find("ul").slideUp(400).parents("li").children("div.drop").css({'background-position':"0 0"});
		});
		$(".lasttreedropshow").click(function(){
			$("#lasttree").find("ul").slideDown(400).parents("li").children("div.drop").css({'background-position':"-11px 0"});
		});
	});
	
	//Тест контекстного меню
	$(document).ready(function() {
		
		$(document).click(function(e){
			var elem = $(".context-menu"); 
			var elem2 = $("#spec_koeff"); 
			var elem3 = $("#insure"); 
			var elem4 = $("#guarantee"); 
			var elem5 = $("#insure_approve"); 
			var elem6 = $("#discount"); 
			if(e.target != elem[0]&&!elem.has(e.target).length &&
			e.target != elem2[0]&&!elem2.has(e.target).length && 
			e.target != elem3[0]&&!elem3.has(e.target).length && 
			e.target != elem4[0]&&!elem4.has(e.target).length && 
			e.target != elem5[0]&&!elem5.has(e.target).length && 
			e.target != elem6[0]&&!elem6.has(e.target).length){
				elem.hide(); 
			} 
		});
		
		// Вешаем слушатель события нажатие кнопок мыши для всего документа:
		$("#spec_koeff").click(function(event) {

			// Проверяем нажата ли именно правая кнопка мыши:
			if (event.which === 1)  {
				contextMenuShow(0, 0, event, 'spec_koeff');
			}
		});
		// Вешаем слушатель события нажатие кнопок мыши для всего документа:
		$("#guarantee").click(function(event) {

			// Проверяем нажата ли именно правая кнопка мыши:
			if (event.which === 1)  {
				contextMenuShow(0, 0, event, 'guarantee');
			}
		});
		// Вешаем слушатель события нажатие кнопок мыши для всего документа:
		$("#insure").click(function(event) {

			// Проверяем нажата ли именно правая кнопка мыши:
			if (event.which === 1)  {
				//alert(1);
				contextMenuShow(0, 0, event, 'insure');
			}
		});
		// Вешаем слушатель события нажатие кнопок мыши для всего документа:
		$("#insure_approve").click(function(event) {

			// Проверяем нажата ли именно правая кнопка мыши:
			if (event.which === 1)  {
				//alert(1);
				contextMenuShow(0, 0, event, 'insure_approve');
			}
		});
		// Вешаем слушатель события нажатие кнопок мыши для всего документа:
		$("#discounts").click(function(event) {

			// Проверяем нажата ли именно правая кнопка мыши:
			if (event.which === 1)  {
				//alert(71);
				contextMenuShow(0, 0, event, 'discounts');
			}
		});
		// Вешаем слушатель события нажатие кнопок мыши для всего документа:
		$(".change_filial").click(function(event) {

			// Проверяем нажата ли именно правая кнопка мыши:
			if (event.which === 1)  {
				//alert(71);
				contextMenuShow(0, 0, event, 'change_filial');
			}
		});
	});

	function changeUserFilial(filial){
		ajax({
			url:"Change_user_session_filial.php",
			//statbox:"status_notes",
			method:"POST",
			data:
			{
				data: filial,
			},
			success:function(data){
				//document.getElementById("status_notes").innerHTML=data;
				//alert("Ok");
				location.reload();
			}
		});
	}
	
	//Показываем блок с суммами и кнопками Для наряда
	function showInvoiceAdd(invoice_type){
		$('#overlay').show();
		
		var Summ = document.getElementById("calculateInvoice").innerHTML;
		var SummIns = 0;
		var SummInsBlock = '';
		
		if (invoice_type == 5){
			SummIns = document.getElementById("calculateInsInvoice").innerHTML;
			SummInsBlock = '<div>Страховка: <span class="calculateInsInvoice">'+SummIns+'</span> руб.</div>';
		}
		
		// Создаем меню:
		var menu = $('<div/>', {
			class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
		})
		.appendTo('#overlay')
		.append(
			$('<div/>')
			.css({
				"height": "100%",
				"border": "1px solid #AAA",
				"position": "relative",
			})
			.append('<span style="margin: 5px;"><i>Проверьте сумму и нажмите сохранить</i></span>')
			.append(
				$('<div/>')
				.css({
					"position": "absolute",
					"width": "100%",
					"margin": "auto",
					"top": "-10px",
					"left": "0",
					"bottom": "0",
					"right": "0",
					"height": "50%",
				})
				.append('<div style="margin: 10px;">К оплате: <span class="calculateInvoice">'+Summ+'</span> руб.</div>'+SummInsBlock)
			)
			.append(
				$('<div/>')
				.css({
					"position": "absolute",
					"bottom": "2px",
					"width": "100%",
				})
				.append('<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_add()">'+
						'<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
				)
			)
		);

		menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

	}

	//Добавляем в базу наряд из сессии
	function Ajax_invoice_add(){
		
		var invoice_type = document.getElementById("invoice_type").value;
		
		var Summ = document.getElementById("calculateInvoice").innerHTML;
		var SummIns = 0;
		
		if (invoice_type == 5){
			SummIns = document.getElementById("calculateInsInvoice").innerHTML;
		}
		
		$.ajax({
			url:"invoice_add_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
				
				summ: Summ,
				summins: SummIns,
				
				invoice_type: invoice_type,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
				//alert(res);
				$('.center_block').remove();
				$('#overlay').hide();
				
				if(res.result == "success"){  
					$('#data').hide();
					$('#invoices').html('<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Добавлен новый наряд</li>'+
										'<li class="cellsBlock" style="width: auto;">'+
										'<a href="invoice.php?id='+res.data+'" class="cellName ahref">'+
											'<b>Наряд #'+res.data+'</b>'+
										'</a>'+
										'</li>');
				}else{
					$('#errror').html(res.data);
				}
			}
		});
	}
	