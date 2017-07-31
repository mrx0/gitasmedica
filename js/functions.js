	function hideAllErrors (){
        // убираем класс ошибок с инпутов
        $('input').each(function(){
            $(this).removeClass('error_input');
        });

        // прячем текст ошибок
        $('.error').hide();
        $('#errror').html('');
	}


    //Для поиска сертификата из модального окна
    $('#search_cert').bind("change keyup input click", function() {

        //var $this = $(this);
        var val = $(this).val();
        //console.log(val);

        if ((val.length > 3) && !isNaN(val[val.length - 1])){
            $.ajax({
                url:"FastSearchCert.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data:{
					num:val,
				},
                cache: false,
                beforeSend: function() {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success:function(res){
                    if(res.result == 'success') {
                    	//alert(res.data);
                        $(".search_result_cert").html(res.data).fadeIn(); //Выводим полученые данные в списке
                    }else{
                        //alert(res.data);
					}
                },
				error:function(){
                	//alert(12);
				}
            });
        }else{
            $("#search_result_cert").hide();
        }
    });

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
				
				//для записи
				if (mark == 'zapis_options'){
					res.data = $('#zapis_options'+ind+'').html();
				}
				//для молочных
				if (mark == 'teeth_moloch'){
					res.data = $('#teeth_moloch_options').html();
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
        hideAllErrors ();
		 
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
        hideAllErrors ();
		 
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
	
	//Добавление позиции основного прайса в страховой
	function Ajax_add_pricelistitem_insure(id, insure) {

        $.ajax({
            url:"pricelistitem_insure_add_f.php",
            global: false,
            type: "POST",
            data:
                {
                    id: id,
                    insure: insure,
                },
            cache: false,
            beforeSend: function() {
               // $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                //document.getElementById("errrror").innerHTML=data;
                setTimeout(function () {
                    window.location.replace('');
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
	
	//Скопировать прайс
	function Ajax_insure_price_copy(id) {

		ajax({
			url:"insure_price_copy_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
				id2: document.getElementById("insurecompany").value,
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

	//Удаление блокировка страховой
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

	//Удаление блокировка лаборатории
	function Ajax_del_labor(id) {

		ajax({
			url:"labor_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('labor.php?id='+id);
					//alert('client.php?id='+id);
				}, 100);
			}
		})
	};

	//Удаление блокировка сертификата
	function Ajax_del_cert(id) {

        $.ajax({
			url:"cert_del_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
			data:
			{
				id: id,
			},
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
                if(res.result == 'success') {
                    //alert(1);
                    $('#data').html(res.data);
                    setTimeout(function () {
                        window.location.replace('certificate.php?id=' + id);
                        //alert('client.php?id='+id);
                    }, 100);
                }else{
                    //alert(2);
                    document.getElementById("errrror").innerHTML = res.data;
                }
            }
		})
	};

	//Удаление блокировка наряда
	function Ajax_del_invoice(id, client_id) {

        $.ajax({
			url:"invoice_del_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
			data:
			{
				id: id,
                client_id: client_id,
			},
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
			success:function(res){
                if(res.result == 'success') {
                	//alert(1);
                    document.getElementById("errrror").innerHTML = res.data;
                    setTimeout(function () {
                        window.location.replace('invoice.php?id=' + id);
                        //alert('client.php?id='+id);
                    }, 100);
                }else{
                    //alert(2);
                    document.getElementById("errrror").innerHTML = res.data;
				}
			}
		})
	};

	//Удаление блокировка ордера
	function Ajax_del_order(id, client_id) {

		ajax({
			url:"order_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
                client_id: client_id,
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('order.php?id='+id);
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

	//разблокировка страховой
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

	//разблокировка лаборатории
	function Ajax_reopen_labor(id) {
		//var id = document.getElementById("id").value;

		ajax({
			url:"labor_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('labor.php?id='+id);
					//alert('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	};

	//разблокировка сертификата
	function Ajax_reopen_cert(id) {
		//var id = document.getElementById("id").value;

		ajax({
			url:"cert_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('certificate.php?id='+id);
					//alert('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	};

	//разблокировка наряда
	function Ajax_reopen_invoice(id, client_id) {
		//var id = document.getElementById("id").value;

		ajax({
			url:"invoice_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
                client_id: client_id,
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('invoice.php?id='+id);
					//alert('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	};
	//разблокировка ордера
	function Ajax_reopen_order(id, client_id) {
		//var id = document.getElementById("id").value;

		ajax({
			url:"order_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
                client_id: client_id,
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('order.php?id='+id);
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
					setTimeout(function () {
						window.location.replace('client.php?id='+id);
					}, 100);
				}
			})
		}
	}; 
	//Перемещение записи другому
	function Ajax_edit_zapis_change_client(zapis_id, client_id) {

        var name = document.getElementById("search_client").value;

		var rys = false;

		var rys = confirm("Вы хотите перенести запись другому пациенту. \nЭто невозможно будет исправить \n\nВы уверены?");

		if (rys){
			$.ajax({
				url:"edit_zapis_change_client_f.php",
				global: false,
				type: "POST",
				data:
				{
                    zapis_id: zapis_id,
                    client_id: client_id,
					new_client: name,
				},
				cache: false,
				beforeSend: function() {
					$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
				},
				success:function(data){
					$('#errrror').html(data);
					setTimeout(function () {
						window.location.replace('client.php?id='+client_id);
					}, 100);
				}
			})
		}
	};
	//Редактировать ФИО пациента
	function Ajax_edit_fio_client() {
		// убираем класс ошибок с инпутов
        hideAllErrors ();
		 
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
        hideAllErrors ();
		 
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

	function Ajax_add_labor(session_id) {

		var name = document.getElementById("name").value;
		var contract = document.getElementById("contract").value;
		var contacts = document.getElementById("contacts").value;

		$.ajax({
			url:"labor_add_f.php",
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

	//Добавить сертификат
	/*function Ajax_add_cert(session_id) {
        // убираем ошибки
        hideAllErrors ();

		var num = document.getElementById("num").value;
		var nominal = document.getElementById("nominal").value;

        $.ajax({
            // метод отправки
            type: "POST",
            // путь до скрипта-обработчика
            url: "ajax_test.php",
            // какие данные будут переданы
            data: {
                num:num,
                nominal:nominal,
            },
            // тип передачи данных
            dataType: "json",
            // действие, при ответе с сервера
            success: function(data){
                // в случае, когда пришло success. Отработало без ошибок
                if(data.result == 'success'){
                    //alert('форма корректно заполнена');
                    $.ajax({
                        url:"cert_add_f.php",
                        global: false,
                        type: "POST",
                        dataType: "JSON",
                        data:
                            {
                                num:num,
                                nominal:nominal,
                                session_id:session_id,
                            },
                        cache: false,
                        beforeSend: function() {
                            $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                        },
                        success:function(data){
                            if(data.result == 'success') {
                                //alert('success');
                                $('#data').html(data.data);
                            }else{
                                //alert('error');
                                $('#errror').html(data.data);
                                $('#errrror').html('');
							}
                        }
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
                    document.getElementById("errror").innerHTML='<div class="query_neok">Ошибка, что-то заполнено не так.</div>'
                }
            }
        });
	};*/


    //Добавляем/редактируем в базу сертификат
    function  Ajax_cert_add(id, mode, certData){

        var link = "cert_add_f.php";

        if (mode == 'edit'){
            link = "cert_edit_f.php";
        }

        certData['cert_id'] = id;

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data:certData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(data){
                if(data.result == 'success') {
                    //alert('success');
                    $('#data').html(data.data);
                }else{
                    //alert('error');
                    $('#errror').html(data.data);
                    $('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу специализацию
    function  Ajax_specialization_add(mode){

        var link = "specialization_add_f.php";

        if (mode == 'edit'){
            link = "specialization_edit_f.php";
        }

        var name = $('#name').val();

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data:name,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(data){
                if(data.result == 'success') {
                    //alert('success');
                    $('#data').html(data.data);
                }else{
                    //alert('error');
                    $('#errror').html(data.data);
                    $('#errrror').html('');
                }
            }
        });
    }

	//!!! тут очередная "правильная" ф-ция
    //Промежуточная функция добавления/редактирования сертификата
    function showCertAdd(id, mode){
        //alert(mode);

        // убираем ошибки
        hideAllErrors ();

        var num = $('#num').val();
        var nominal = $('#nominal').val();

        var certData = {
            num:num,
            nominal:nominal,
        }

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data:certData,

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){

                    Ajax_cert_add(id, mode, certData);

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
                    $('#errror').html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');
                }
            }
        })
    }





	function Ajax_edit_insure(id) {
		// убираем класс ошибок с инпутов
        hideAllErrors ();
		 
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
	
	
	function Ajax_edit_labor(id) {
		// убираем класс ошибок с инпутов
		$hideAllErrors ();

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
						url:"labor_edit_f.php",
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
		var pricecode = document.getElementById("pricecode").value;
		var price = document.getElementById("price").value;
		var price2 = document.getElementById("price2").value;
		var price3 = document.getElementById("price3").value;
		var group = document.getElementById("group").value;
		var iWantThisDate2 = document.getElementById("iWantThisDate2").value;
		
		$.ajax({
			url:"add_priceitem_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				pricename:pricename,
                pricecode:pricecode,
				price:price,
				price2:price2,
				price3:price3,
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
		var pricelistitemcode = document.getElementById("pricelistitemcode").value;
		var group = document.getElementById("group").value;

		$.ajax({
			url:"pricelistitem_edit_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				pricelistitemname:pricelistitemname,
                pricelistitemcode:pricelistitemcode,
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
		var price2 = document.getElementById("price2").value;
		var price3 = document.getElementById("price3").value;
		var iWantThisDate2 = document.getElementById("iWantThisDate2").value;

		$.ajax({
			url:"priceprice_edit_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				session_id:session_id,
				price:price,
				price2:price2,
				price3:price3,
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
		var price2 = document.getElementById("price2").value;
		var price3 = document.getElementById("price3").value;
		var iWantThisDate2 = document.getElementById("iWantThisDate2").value;

		$.ajax({
			url:"priceprice_insure_edit_f.php",
			global: false, 
			type: "POST", 
			data:
			{
				price: price,
				price2: price2,
				price3: price3,
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

	//переход к прайсу страховой
    function iWantThisInsurePrice(){
        var insure_id = document.getElementById("insurecompany").value;
		if (insure_id != 0){
            window.location.replace('insure_price.php?id='+insure_id);
		}
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
		e5 = $('.cellManage');
		e6 = $('#DIVdelCheckedItems');

		if((e4.is(':visible')) || (e5.is(':visible')) || (e6.is(':visible'))) {
			e4.hide();
			//e5.children().remove();
            e5.hide();
            e6.hide();
		}else{
			e4.show();
            e5.show();
            e6.show();
            //e5.append('<span style="font-size: 80%; color: #777;"><input type="checkbox" name="propDel[]" value="1"> пометить на удаление</span>');
            //меняет цвет
			//e5.parent().css({"background-color": "#ffbcbc"});
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

                age:document.querySelector('input[name="age"]:checked').value,
				wo_age:wo_age,

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

    //Выборка статистики  записи
    function Ajax_show_result_stat_zapis(){

        var zapisAll = $("input[id=zapisAll]:checked").val();
        if (zapisAll === undefined){
            zapisAll = 0;
        }
        var zapisArrive = $("input[id=zapisArrive]:checked").val();
        if (zapisArrive === undefined){
            zapisArrive = 0;
        }
        var zapisNotArrive = $("input[id=zapisNotArrive]:checked").val();
        if (zapisNotArrive === undefined){
            zapisNotArrive = 0;
        }

        var zapisError = $("input[id=zapisError]:checked").val();
        if (zapisError === undefined){
            zapisError = 0;
        }

        var zapisNull = $("input[id=zapisNull]:checked").val();
        if (zapisNull === undefined){
            zapisNull = 0;
        }

        $.ajax({
            url:"ajax_show_result_stat_zapis_f.php",
            global: false,
            type: "POST",
            data:
                {
                    all_time:all_time,
                    datastart: document.getElementById("datastart").value,
                    dataend: document.getElementById("dataend").value,

                    worker:document.getElementById("search_worker").value,
                    filial: document.getElementById("filial").value,

                    zapisAll: zapisAll,
                    zapisArrive: zapisArrive,
                    zapisNotArrive: zapisNotArrive,
                    zapisError: zapisError,
                    zapisNull: zapisNull,

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

    //Выборка статистики страховых
    function Ajax_show_result_stat_insure(){

        var zapisAll = $("input[id=zapisAll]:checked").val();
        if (zapisAll === undefined){
            zapisAll = 0;
        }
        var zapisArrive = $("input[id=zapisArrive]:checked").val();
        if (zapisArrive === undefined){
            zapisArrive = 0;
        }
        var zapisNotArrive = $("input[id=zapisNotArrive]:checked").val();
        if (zapisNotArrive === undefined){
            zapisNotArrive = 0;
        }

        var zapisError = $("input[id=zapisError]:checked").val();
        if (zapisError === undefined){
            zapisError = 0;
        }

        var zapisNull = $("input[id=zapisNull]:checked").val();
        if (zapisNull === undefined){
            zapisNull = 0;
        }

        $.ajax({
            url:"ajax_show_result_stat_insure_f.php",
            global: false,
            type: "POST",
            data:
                {
                    all_time:all_time,
                    datastart: document.getElementById("datastart").value,
                    dataend: document.getElementById("dataend").value,

                    //worker:document.getElementById("search_worker").value,
                    insure: document.getElementById("insure_sel").value,
                    filial: document.getElementById("filial").value,

                    zapisAll: zapisAll,
                    zapisArrive: zapisArrive,
                    zapisNotArrive: zapisNotArrive,
                    zapisError: zapisError,
                    zapisNull: zapisNull,

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

    //Подготовка файла xls для выгрузки
    function Ajax_repare_insure_xls(){

        /*var zapisAll = $("input[id=zapisAll]:checked").val();
        if (zapisAll === undefined){
            zapisAll = 0;
        }
        var zapisArrive = $("input[id=zapisArrive]:checked").val();
        if (zapisArrive === undefined){
            zapisArrive = 0;
        }
        var zapisNotArrive = $("input[id=zapisNotArrive]:checked").val();
        if (zapisNotArrive === undefined){
            zapisNotArrive = 0;
        }

        var zapisError = $("input[id=zapisError]:checked").val();
        if (zapisError === undefined){
            zapisError = 0;
        }

        var zapisNull = $("input[id=zapisNull]:checked").val();
        if (zapisNull === undefined){
            zapisNull = 0;
        }*/

        $.ajax({
            url:"ajax_repare_insure_xls_f.php",
            global: false,
            type: "POST",
            data:
                {
                    all_time:all_time,
                    showError:showError,
                    datastart: document.getElementById("datastart").value,
                    dataend: document.getElementById("dataend").value,

                    //worker:document.getElementById("search_worker").value,
                    insure: document.getElementById("insure_sel").value,
                    filial: document.getElementById("filial").value,

                    /*zapisAll: zapisAll,
                    zapisArrive: zapisArrive,
                    zapisNotArrive: zapisNotArrive,
                    zapisError: zapisError,
                    zapisNull: zapisNull,*/

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
		// Составить массив
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
                //Блокируем кнопку OK
                document.getElementById("Ajax_add_TempZapis").disabled = true;
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
		var zapis_id = Number(document.getElementById("zapis_id").value);
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
		var query = '';
		var idz = '';

		$.ajax({
			dataType: "json",
			async: false,
			// метод отправки 
			type: "POST",
			// путь до скрипта-обработчика
			url: "get_next_zapis.php",
			// какие данные будут переданы
			data: {
				id: zapis_id,

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
				//alert(next_zapis_data);
				//alert (next_zapis_data.next_time_start);
				//document.getElementById("kab").innerHTML=nex_zapis_data;
				next_time_start_rez = next_zapis_data.next_time_start;
				next_time_end_rez = next_zapis_data.next_time_end;
				//next_zapis_data;
                query = next_zapis_data.query;
                idz = next_zapis_data.id;

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
				document.getElementById("exist_zapis").innerHTML='<span style="color: red">Дальше есть запись</span><br>';

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

		//var discount = Number(document.getElementById("discountValue").innerHTML);

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
			//var guarantee = $(this).next().next().next().attr('guarantee');
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
			
			//коэффициентinvoiceItemPrice

			//скидка акция
			var discount = $(this).next().next().next().attr('discount');
			//alert(discount);
						
			//взяли количество
			var quantity = Number($(this).parent().find('[type=number]').val());
					
			//вычисляем стоимость
			//var stoim = quantity * (cost +  cost * spec_koeff / 100);
			var stoim = quantity * cost	;

			//с учетом скидки акции, но если не страховая
            if (insure == 0) {
                stoim = stoim - (stoim * discount / 100);
            }
            if (insure == 0) {
           		stoim = Math.round(stoim / 10) * 10;
            }

            //console.log(stoim);
			//console.log($(this).parent().find('.invoiceItemPriceItog').html(stoim));

            //суммируем сумму в итоги
            if (guarantee == 0){
                if (insure != 0){
                    if (insureapprove != 0){
                        SummIns += stoim;
                    }
                }else{
                    Summ += stoim;
                }
            }

			//прописываем стоимость
			if (guarantee == 0){
				//$(this).next().next().next().next().next().html(stoim);
				//$(this).next().next().next().next().html(stoim);
                $(this).parent().find('.invoiceItemPriceItog').html(stoim);
			}

		});

        //Summ = Math.round(Summ - (Summ * discount / 100));
        Summ = Math.round(Summ/10) * 10;
        //SummIns = Math.round(SummIns - (SummIns * discount / 100));
		//страховые не округляем
        //SummIns = Math.round(SummIns/10) * 10;

		document.getElementById("calculateInvoice").innerHTML = Summ;
		if (SummIns > 0){
			document.getElementById("calculateInsInvoice").innerHTML = SummIns;
		}
		
	};
	
	//Подсчёт суммы для счёта с учетом сертификата
	function calculatePaymentCert (){

		var SummCert = 0;
		var rezSumm = 0;

		var leftToPay = Number($("#leftToPay").html());

        $(".cert_pay").each(function() {
            SummCert += Number($(this).html());
		});

        if (SummCert > leftToPay){
            rezSumm = leftToPay;
		}else{
            rezSumm = SummCert;
		}

        $("#summ").html(rezSumm);

	}

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
				// !!! скролл надо замутить сюда $('#invoice_rezult').scrollTop();
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
		
		//if ((itemId == 0) || (itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
		if ((itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
			target = 'zub';
		}
		//alert(zub);
		//alert(target);
		
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

	//Удалить все диагнозы МКБ
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

	//Удалить текущий диагноз МКБ
	function deleteMKBItemID(ind, key){

		$.ajax({
			url:"delete_mkb_item_id_from_session_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				ind: ind,
				key: key,
				
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

			}
		});
	}	

	//Изменить коэффициент специалиста у всех
	function spec_koeffInvoice(spec_koeff){
		//alert(spec_koeff);

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
                //$('#errror').html(data);

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

                //document.getElementById("discountValue").innerHTML = Number(discount);

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
	function spec_koeffItemInvoice(ind, key, spec_koeff){
		
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
				ind: ind,
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
			//dataType: "JSON",
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
             	//$('#errror').html(data);

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
			var elem7 = $("#lab_order_status");

			if(e.target != elem[0]&&!elem.has(e.target).length &&
			e.target != elem2[0]&&!elem2.has(e.target).length && 
			e.target != elem3[0]&&!elem3.has(e.target).length && 
			e.target != elem4[0]&&!elem4.has(e.target).length && 
			e.target != elem5[0]&&!elem5.has(e.target).length && 
			e.target != elem6[0]&&!elem6.has(e.target).length &&
			e.target != elem7[0]&&!elem7.has(e.target).length){
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
		//Для прикрепления к филиалу
		$(".change_filial").click(function(event) {

			// Проверяем нажата ли именно правая кнопка мыши:
			if (event.which === 1)  {
				//alert(71);
				contextMenuShow(0, 0, event, 'change_filial');
			}
		});
		//Для отображения списка молочных зубов
		$('#teeth_moloch').click(function(event) {

			// Проверяем нажата ли именно правая кнопка мыши:
			if (event.which === 1)  {
				//alert(71);
				contextMenuShow(0, 0, event, 'teeth_moloch');
			}
		});
		//Для отображения меню изменения статуса
		$('#lab_order_status').click(function(event) {

			// Проверяем нажата ли именно правая кнопка мыши:
			if (event.which === 1)  {
				//alert(71);

                var lab_order_id = document.getElementById("lab_order_id").value;
                var status_now = document.getElementById("status_now").value;
                //alert(status_now);

				contextMenuShow(lab_order_id, status_now, event, 'lab_order_status');
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
	function showInvoiceAdd(invoice_type, mode){
		//alert(mode);
		$('#overlay').show();
		
		var Summ = document.getElementById("calculateInvoice").innerHTML;
		var SummIns = 0;
		var SummInsBlock = '';
		
		if (invoice_type == 5){
			SummIns = document.getElementById("calculateInsInvoice").innerHTML;
			SummInsBlock = '<div>Страховка: <span class="calculateInsInvoice">'+SummIns+'</span> руб.</div>';
		}
		
		var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_add(\'add\')">';
						
		
		if (mode == 'edit'){
			buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_add(\'edit\')">';
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
				.append(buttonsStr+
						'<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
				)
			)
		);

		menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

	}

    //Показываем блок с суммами и кнопками Для ордера
    function showOrderAdd(mode){
        //alert(mode);

        var Summ = document.getElementById("summ").value;
        var SummType = document.getElementById("summ_type").value;
        var filial = document.getElementById("filial").value;

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    summ:Summ,
                    summ_type:SummType,
                    filial:filial,
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){
                    $('#overlay').show();

                    //var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_order_add(\'add\')">';

                    /*if (mode == 'edit'){
                        buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_order_add(\'edit\')">';
                    }*/

                    if (mode == 'add'){
                        Ajax_order_add('add');
                    }

                    if (mode == 'edit'){
                        Ajax_order_add('edit');
                    }

                    // Создаем меню:
                    /*var menu = $('<div/>', {
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
                                        .append('<div style="margin: 10px;">Сумма: <span class="calculateInvoice">'+Summ+'</span> руб.</div>')
                                )
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "bottom": "2px",
                                            "width": "100%",
                                        })
                                        .append(buttonsStr+
                                            '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                                        )
                                )
                        );

                    menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
                    */

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
        })
    }


   //Показываем блок с суммами и кнопками Для сертификата
    function showCertCell(id){
        //alert(id);
        hideAllErrors ();

        var cell_price = $('#cell_price').val();
        //alert(cell_price);

        var office_id = $('#office_id').val();

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    cell_price: cell_price
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){
                    $('#overlay').show();

                    var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_cert_cell('+id+', '+cell_price+', '+office_id+')">';

                    /*if (mode == 'edit'){
                        buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_order_add(\'edit\')">';
                    }*/

                    /*if (mode == 'add'){
                        Ajax_order_add('add');
                    }

                    if (mode == 'edit'){
                        Ajax_order_add('edit');
                    }*/

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
                                        .append('<div style="margin: 10px;">Сумма: <span class="calculateInvoice">'+cell_price+'</span> руб.</div>')
                                )
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "bottom": "2px",
                                            "width": "100%",
                                        })
                                        .append(buttonsStr+
                                            '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                                        )
                                )
                        );

                    menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

                // в случае ошибок в форме
                }else{
                	//alert(1);
                    // перебираем массив с ошибками
                    for(var errorField in data.text_error){
                        // выводим текст ошибок
                        $('#'+errorField+'_error').html(data.text_error[errorField]);
                        // показываем текст ошибок
                        $('#'+errorField+'_error').show();
                        // обводим инпуты красным цветом
                        // $('#'+errorField).addClass('error_input');
                    }
                    $('#errror').html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');
                }
            }
        })
    }

    //Показываем блок для поиска и добавления сертификата
    function showCertPayAdd(){
        //alert(id);
        //hideAllErrors ();

        //var search_cert_input = $('#search_cert_input').html();
		//alert(search_cert_input);

        $('#overlay').show();

        //var buttonsStr = '<input type="button" class="b" value="Добавить" onclick="Ajax_cert_add_pay()">';
        var buttonsStr = '';

        // Создаем меню:
        var menu = $('<div/>', {
            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
        }).css({
            "height": "250px"
        })
            .appendTo('#overlay')
            .append(
                $('<div/>')
                    .css({
                        "height": "100%",
                        "border": "1px solid #AAA",
                        "position": "relative",
                    })
                    .append('<span style="margin: 0;"><i></i></span>')
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "width": "100%",
                                "margin": "auto",
                                "top": "-90px",
                                "left": "0",
                                "bottom": "0",
                                "right": "0",
                                "height": "50%",
                            })
                            .append(
                                //search_cert_input
								'<div id="search_cert_input_target">'+
								//	'<input type="text" size="30" name="searchdata" id="search_cert" placeholder="Наберите номер сертификата" value="" class="who"  autocomplete="off" style="width: 90%;">'+
            					//'<ul id="search_result" class="search_result"></ul>'+
								'</div>'
							)
                    )
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "bottom": "2px",
                                "width": "100%",
                            })
                            .append(buttonsStr+
                                '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'#search_cert_input\').append($(\'#search_cert_input_target\').children()); $(\'.center_block\').remove(); $(\'#search_result_cert\').html(\'\'); $(\'#search_cert\').val(\'\');">'
                            )
                    )
            );

        $('#search_cert_input_target').append($('#search_cert_input').children());

        menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

    }


    //Промежуточная функция добавления заказа в лабораторию
    function showLabOrderAdd(mode){
        //alert(mode);

        $('.error').each(function(){
            //alert(this.innerHTML);
            this.innerHTML = '';
        });

        document.getElementById("errror").innerHTML = '';

        var search_client2 = document.getElementById("search_client2").value;
        var lab = document.getElementById("lab").value;
        var descr = document.getElementById("descr").value;
        var comment = document.getElementById("comment").value;

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    search_client2:search_client2,
                    lab:lab,
                    descr:descr
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){
                    //$('#overlay').show();

                    //var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_order_add(\'add\')">';

                    /*if (mode == 'edit'){
                        buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_order_add(\'edit\')">';
                    }*/

                    if (mode == 'add'){
                        Ajax_lab_order_add('add');
                    }

                    if (mode == 'edit'){
                        Ajax_lab_order_add('edit');
                    }

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
        })
    }


	//Добавляем/редактируем в базу наряд из сессии
	function Ajax_invoice_add(mode){
		//alert(mode);
		
		var invoice_id = 0;
		
		var link = "invoice_add_f.php";
		
		if (mode == 'edit'){
			link = "invoice_edit_f.php";
			invoice_id = document.getElementById("invoice_id").value;
		}
		
		var invoice_type = document.getElementById("invoice_type").value;
		
		var Summ = document.getElementById("calculateInvoice").innerHTML;
		var SummIns = 0;
		
		var SummInsStr = '';
		
		if (invoice_type == 5){
			SummIns = document.getElementById("calculateInsInvoice").innerHTML;
			SummInsStr = '<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">'+
							'Страховка:<br>'+
							'<span class="calculateInsInvoice" style="font-size: 13px">'+SummIns+'</span> руб.'+
						'</div>';
		}
		
		var client = document.getElementById("client").value;
		
		$.ajax({
			url: link,
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				client: client,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,
				
				summ: Summ,
				summins: SummIns,
				
				invoice_type: invoice_type,
				invoice_id: invoice_id,
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
					$('#invoices').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
											'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Добавлен/отредактирован наряд</li>'+
											'<li class="cellsBlock" style="width: auto;">'+
												'<a href="invoice.php?id='+res.data+'" class="cellName ahref">'+
													'<b>Наряд #'+res.data+'</b><br>'+
												'</a>'+
												'<div class="cellName">'+
													'<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">'+
														'Сумма:<br>'+
														'<span class="calculateInvoice" style="font-size: 13px">'+Summ+'</span> руб.'+
													'</div>'+
													SummInsStr+
												'</div>'+
											'</li>'+
											'<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
												'<a href="payment_add.php?invoice_id='+res.data+'" class="b">Оплатить</a>'+
											'</li>'+
											'<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
												'<a href="add_order.php?client_id='+client+'&invoice_id='+res.data+'" class="b">Добавить приходный ордер</a>'+
											'</li>'+
											'<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
												'<a href="finance_account.php?client_id='+client+'" class="b">Управление счётом</a>'+
											'</li>'+
										'</ul>');
				}else{
					$('#errror').html(res.data);
				}
			}
		});
	}

	//Продаём сертификат по базе
	function Ajax_cert_cell(id, cell_price, office_id){


		$.ajax({
			url: "cert_cell_f.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                cert_id: id,
                cell_price: cell_price,
                office_id: office_id
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
					//$('#data').hide();
					$('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
											'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Сертификат продан</li>'+
									'</ul>');
                    setTimeout(function () {
                        window.location.replace('certificate.php?id='+id+'');
                        //alert('client.php?id='+id);
                    }, 100);
				}else{
					$('#errror').html(res.data);
				}
			}
		});
	}

	//Добавим сертификат сертификат в оплату
	function Ajax_cert_add_pay(id){

        $('#overlay').hide();
        $('#search_cert_input').append($('#search_cert_input_target').children());
        $('.center_block').remove();
        $('#search_result_cert').html('');
        $('#search_cert').val('');

        //$('.have_money_or_not').show();
        $('#certs_result').show();
        $('#showCertPayAdd_button').hide();

		$.ajax({
			url: "FastSearchCertOne.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                id: id,
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
					//$('#data').hide();
                    $('#certs_result').append(res.data);

                    calculatePaymentCert ();

				}else{
					//$('#errror').html(res.data);
				}
			}
		});
	}

	//Очистить все сертификаты
	function certsResultDel(){

        /*$('#overlay').hide();
        $('#search_cert_input').append($('#search_cert_input_target').children());
        $('.center_block').remove();
        $('#search_result_cert').html('');
        $('#search_cert').val('');*/

        //$('.have_money_or_not').show();
        $('#certs_result').hide();
        $('#showCertPayAdd_button').show();

        $('#certs_result').html(
			'<tr>'+
				'<td><span class="lit_grey_text">Номер</span></td>'+
					'<td><span class="lit_grey_text">Номинал</span></td>'+
					'<td><span class="lit_grey_text">К оплате (остаток)</span></td>'+
				'<td style="text-align: center;"><i class="fa fa-times" aria-hidden="true" title="Удалить"></i></td>'+
            '</tr>'
		);

        $('#summ').html(0);

		/*$.ajax({
			url: "FastSearchCertOne.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                id: id,
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
					//$('#data').hide();
                    $('#certs_result').append(res.data);
				}else{
					//$('#errror').html(res.data);
				}
			}
		});*/
	}

	//Добавляем/редактируем в базу ордер
	function Ajax_order_add(mode){
		//alert(mode);

        var order_id = 0;

		var link = "order_add_f.php";

		var paymentStr = '';

		if (mode == 'edit'){
			link = "edit_order_f.php";
            order_id = document.getElementById("order_id").value;
		}

        var Summ = document.getElementById("summ").value;
        //var SummType = document.getElementById("summ_type").value;
        var SummType = document.querySelector('input[name="summ_type"]:checked').value;
        var office_id = document.getElementById("filial").value;

		var client_id = document.getElementById("client_id").value;
		//var order_id = document.getElementById("order_id").value;
		//alert(invoice_id);
		var date_in = document.getElementById("date_in").value;
		//alert(date_in);

        var comment = document.getElementById("comment").value;
        //alert(comment);

        if (order_id != 0){
            paymentStr = '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
                '<a href= "payment_add.php?invoice_id='+order_id+'" class="b">Оплатить наряд #'+order_id+'</a>'+
                '</li>';
		}

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                client_id: client_id,
                office_id: office_id,
				summ: Summ,
                summtype: SummType,
                date_in: date_in,
                comment: comment,

                order_id: order_id,
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
					//$('#data').hide();
					$('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
											'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Добавлен/отредактирован приходный ордер</li>'+
											'<li class="cellsBlock" style="width: auto;">'+
												'<a href="order.php?id='+res.data+'" class="cellName ahref">'+
													'<b>Ордер #'+res.data+'</b><br>'+
												'</a>'+
												'<div class="cellName">'+
													'<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">'+
														'Сумма:<br>'+
														'<span class="calculateInvoice" style="font-size: 13px">'+Summ+'</span> руб.'+
													'</div>'+
												'</div>'+
											'</li>'+
                        					paymentStr+
					                        '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
						                        '<a href="finance_account.php?client_id='+client_id+'" class="b">Управление счётом</a>'+
					                        '</li>'+
										'</ul>');
				}else{
					$('#errror').html(res.data);
				}
			}
		});
	}

	//Добавляем/редактируем в базу заказ в лабораторию
	function Ajax_lab_order_add(mode){
		//alert(mode);

        var lab_order_id = 0;

		var link = "lab_order_add_f.php";

		if (mode == 'edit'){
			link = "lab_order_edit_f.php";
            lab_order_id = document.getElementById("lab_order_id").value;
		}

        var client_id = document.getElementById("client_id").value;

        var search_client2 = document.getElementById("search_client2").value;
        var lab = document.getElementById("lab").value;
        var descr = document.getElementById("descr").value;
        var comment = document.getElementById("comment").value;

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                client_id:client_id,

                worker: search_client2,
                lab: lab,
                descr: descr,
                comment: comment,

                lab_order_id: lab_order_id,
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
					//$('#data').hide();
					$('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
											'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Добавлен/отредактирован заказ в лабораторию</li>'+
											'<li class="cellsBlock" style="width: auto;">'+
												'<a href="lab_order.php?id='+res.data+'" class="cellName ahref">'+
													'<b>Заказ #'+res.data+'</b><br>'+
												'</a>'+
											'</li>'+
										'</ul>');
				}else{
					$('#errror').html(res.data);
				}
			}
		});
	}

	//Меняем статус заказа в лаборатории
	function labOrderStatusChange(lab_order_id, status){
		//alert(status);

		var link = "labOrderStatusChange_f.php";

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                lab_order_id:lab_order_id,

                status: status,

			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
				//alert(res);
				//$('.center_block').remove();
				//$('#overlay').hide();

				if(res.result == "success"){
					//$('#data').hide();
					window.location.replace('');
				}else{
					//$('#errror').html(res.data);
				}
			}
		});
	}

	//Для перехода в добавление нового клиента из записи
	$('#add_client_fio').click(function () {
		var client_fio = document.getElementById("search_client").value;
		if (client_fio != ''){
			window.location.replace('add_client.php?fio='+document.getElementById("search_client").value);
		}else{
			window.location.replace('add_client.php');
		}
	});

    //для сбора чекбоксов в массив
    function itemExistsChecker(cboxArray, cboxValue) {

        var len = cboxArray.length;
        if (len > 0) {
            for (var i = 0; i < len; i++) {
                if (cboxArray[i] == cboxValue) {
                    return true;
                }
            }
        }

        cboxArray.push(cboxValue);

        return (cboxArray);
    }

    function checkedItems (){

        var cboxArray = [];

        $('input[type="checkbox"]').each(function() {
            var cboxValue = $(this).val();

            if ( $(this).prop("checked")){
                cboxArray = itemExistsChecker(cboxArray, cboxValue);
            }

        });

       return cboxArray;
	}
	//Удаление выбранных позиций из прайса страховой
    function delCheckedItems (insure_id){

        var rys = false;

        var rys = confirm("Вы хотите удалить позиции из прайса страховой. \n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "del_items_from_insure_price_f.php",
                global: false,
                type: "POST",
                data: {
                    items: checkedItems(),
                    insure_id: insure_id,
                },
                cache: false,
                beforeSend: function () {
                    // $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success: function (data) {
                    $('#errrror').html(data);
                    setTimeout(function () {
                        window.location.replace('');
                        //alert('client.php?id='+id);
                    }, 100);
                }
            })
        }
    }
	//перемещение выбранных позиций прайса в группу
    function moveCheckedItems (){
		//alert(880);

        var group = document.getElementById("group").value;
        //alert(group);

        var rys = false;

        var rys = confirm("Вы хотите переместить выбранные позиции в группу. \n\nВы уверены?");
		//alert(885);

        if (rys) {
            $.ajax({
                url: "move_items_in_group_insure_price_f.php",
                global: false,
                type: "POST",
                data: {
                    group: group,
                    items: checkedItems(),
                },
                cache: false,
                beforeSend: function () {
                    $('#overlay').hide();
                    $('.center_block').remove();
                    $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success: function (data) {
                    $('#errrror').html(data);
                    setTimeout(function () {
                        window.location.replace('');
                        //alert('client.php?id='+id);
                    }, 100);
                }
            })
        }
    }

	//Показать меню для перемещение выбранных позиций прайса в группу
    function showMoveCheckedItems (){

        //alert(mode);
        $('#overlay').show();

        var buttonsStr = '<input type="button" class="b" value="Применить" onclick="moveCheckedItems()">';

        var tree = '';

        $.ajax({
            url: "show_tree.php",
            global: false,
            type: "POST",
            data: {

            },
            cache: false,
            beforeSend: function () {
                // $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success: function (data) {
                tree = data;

                // Создаем меню:
                var menu = $('<div/>', {
                    class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
                })
					.css({
                	    "height": "200px",
                	})
                    .appendTo('#overlay')
                    .append(
                        $('<div/>')
                            .css({
                                "height": "100%",
                                "border": "1px solid #AAA",
                                "position": "relative",
                            })
                            .append('<span style="margin: 5px;"><i>Выберите группу</i></span>')
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
                                    .append('<div style="margin: 10px;">'+tree+'</div>')
                            )
                            .append(
                                $('<div/>')
                                    .css({
                                        "position": "absolute",
                                        "bottom": "2px",
                                        "width": "100%",
                                    })
                                    .append(buttonsStr+
                                        '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                                    )
                            )
                    );

                menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню


            }
        })
    }

