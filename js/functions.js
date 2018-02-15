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

        if ((val.length > 1) && !isNaN(val[val.length - 1])){
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
                    	//console.log(res.data);
                        $(".search_result_cert").html(res.data).fadeIn(); //Выводим полученые данные в списке
                    }else{
                        //console.log(res.data);
					}
                },
				error:function(){
                	//console.log(12);
				}
            });
        }else{
            $("#search_result_cert").hide();
        }
    });

	//Для изменения цены вручную
    function changePriceItem(newPrice, start_price){
        //console.log(newPrice);
        //console.log(start_price);

    };

	//попытка показать контекстное меню
	function contextMenuShow(ind, key, event, mark){
		//console.log(mark);

		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();
		
		// Получаем элемент на котором был совершен клик:
		var target = $(event.target);

        //console.log(target.attr('start_price'));

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
				//console.log(res.data);

				//для записи
				if (mark == 'zapis_options'){
					res.data = $('#zapis_options'+ind+'').html();
				}
				//Регуляция цены
				if (mark == 'priceItem'){

				    var start_price = Number(target.attr('start_price'));

					res.data =
                        '<li style="font-size: 10px;">'+
                        'Введите новую цену (не менее '+start_price+')'+
                        '</li>'+
						'<li>'+
                        //'<input type="number" name="changePriceItem" id="changePriceItem" class="form-control" size="2" min="'+start_price+'" value="'+Number(target.html())+'" class="mod" onchange="priceItemInvoice('+ind+', '+key+', $(this).val(), '+start_price+');">'+
                        '<input type="number" name="changePriceItem" id="changePriceItem" class="form-control" size="2" min="'+start_price+'" value="'+Number(target.html())+'" class="mod">'+
                        //'<input type="text" name="changePriceItem" id="changePriceItem" class="form-control" value="'+Number(target.html())+'" onkeyup="changePriceItem(this.value, '+start_price+');">'+
						'<div style="display: inline;" onclick="priceItemInvoice('+ind+', '+key+', $(\'#changePriceItem\').val(), '+start_price+')">Ok</div>'+
						'</li>';

				}
				//Регуляция конечной цены
				if (mark == 'priceItemItog'){

				    var itog_price = Number(target.html());
				    var manual_itog_price = Number(target.attr("manual_itog_price"));
				    /*console.log(itog_price);
				    console.log(manual_itog_price);*/
				    //console.log(target.attr("manual_itog_price"));
				    //console.log(Math.floor(itog_price / 10) * 10);
				    //console.log(Math.floor((itog_price) / 10) * 10 +10);

                    manual_itog_price = itog_price;

					/*var min_itog_price = Math.floor(itog_price / 10) * 10;
					var max_itog_price = min_itog_price + 10;*/
					/*var min_itog_price = itog_price - 2;
					var max_itog_price = itog_price + 2;*/
					var min_itog_price = manual_itog_price - 2;
					var max_itog_price = manual_itog_price + 2;

					if (min_itog_price < 1) min_itog_price = 1;


					res.data =
                        '<li style="font-size: 10px;">'+
                        'Введите цену (от '+min_itog_price+' до '+max_itog_price+')'+
                        '</li>'+
						'<li>'+
                        //'<input type="number" name="changePriceItem" id="changePriceItem" class="form-control" size="2" min="'+start_price+'" value="'+Number(target.html())+'" class="mod" onchange="priceItemInvoice('+ind+', '+key+', $(this).val(), '+start_price+');">'+
                        '<input type="number" name="changePriceItogItem" id="changePriceItogItem" class="form-control" size="3" min="'+min_itog_price+'"  max="'+max_itog_price+'" value="'+itog_price+'" class="mod">'+
                        //'<input type="text" name="changePriceItem" id="changePriceItem" class="form-control" value="'+Number(target.html())+'" onkeyup="changePriceItem(this.value, '+start_price+');">'+
						'<div style="display: inline;" onclick="priceItemItogInvoice('+ind+', '+key+', $(\'#changePriceItogItem\').val(), '+manual_itog_price+')">Ok</div>'+
						'</li>';

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
                // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
				menu.show();
		
			}
		});
	}

    //для сбора чекбоксов в массив
    function itemExistsChecker2 (cboxArray, cboxValue) {

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

    function checkedItems2 (){

        var cboxArray = [];

        $('input[type="checkbox"]').each(function() {
            var cboxValue = $(this).val();
            //console.log($(this).attr("id"));

            if ($(this).attr("id") != 'fired') {
                if ($(this).prop("checked")) {
                    cboxArray = itemExistsChecker2(cboxArray, cboxValue);
                }
            }
        });

        return cboxArray;
    }


    //Редактирование сотрудника
    function Ajax_user_edit(worker_id) {

        var fired = $("input[name=fired]:checked").val();
        if((typeof fired == "undefined") || (fired == "")) fired = 0;

        var org = 0;
        var permissions = $('#permissions').val();
        var contacts = $('#contacts').val();

        //console.log(checkedItems2());

        $.ajax({
            url:"user_edit_f.php",
            global: false,
            type: "POST",
            data:
                {
                    worker_id: worker_id,
                    org: org,
                    permissions: permissions,
                    contacts: contacts,
                    fired: fired,
                    specializations:checkedItems2(),

                },
            cache: false,
            beforeSend: function() {
                // $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                document.getElementById("status").innerHTML=data;
            }
        })
    };

	//Добавляем нового клиента
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
					//console.log('форма корректно заполнена');
					ajax({
						url:"client_add_f.php",
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
				
				sel_date: $("#sel_date").val(),
				sel_month: $("#sel_month").val(),
				sel_year: $("#sel_year").val(),
				
				sex: sex_value,
			},
			// тип передачи данных
			dataType: "json",
			// действие, при ответе с сервера
			success: function(data){
				// в случае, когда пришло success. Отработало без ошибок
				if(data.result == 'success'){   
					//console.log('форма корректно заполнена');
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
					//console.log('client.php?id='+id);
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
				session_id: session_id
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('pricelistgroup.php?id='+id);
					//console.log('client.php?id='+id);
				}, 100);
			}
		})
	}
	
	//Удаление позиции прайса
	function Ajax_del_pricelistitem(id) {
		
		ajax({
			url:"pricelistitem_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('pricelistitem.php?id='+id);
					//console.log('client.php?id='+id);
				}, 100);
			}
		})
	}
	
	//Удаление позиции прайса страховой
	function Ajax_del_pricelistitem_insure(id, insure) {
		
		ajax({
			url:"pricelistitem_insure_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
				insure: insure
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('insure_price.php?id='+insure);
					//console.log('client.php?id='+id);
				}, 100);
			}
		})
	}
	
	//Добавление позиции основного прайса в страховой
	function Ajax_add_pricelistitem_insure(id, insure) {

        $.ajax({
            url:"pricelistitem_insure_add_f.php",
            global: false,
            type: "POST",
            data:
                {
                    id: id,
                    insure: insure
                },
            cache: false,
            beforeSend: function() {
               // $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                //document.getElementById("errrror").innerHTML=data;
                setTimeout(function () {
                    window.location.replace('');
                    //console.log('client.php?id='+id);
                }, 100);
            }
        })
	}

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
					//console.log('client.php?id='+id);
				}, 100);*/
			}
		})
	}
	
	//Скопировать прайс
	function Ajax_insure_price_copy(id) {

		ajax({
			url:"insure_price_copy_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
				id2: document.getElementById("insurecompany").value
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				/*setTimeout(function () {
					window.location.replace('pricelistitem.php?id='+id);
					//console.log('client.php?id='+id);
				}, 100);*/
			}
		})
	}

	//Очисить прайс
	function Ajax_insure_price_clear(id) {
		
		ajax({
			url:"insure_price_clear_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				/*setTimeout(function () {
					window.location.replace('pricelistitem.php?id='+id);
					//console.log('client.php?id='+id);
				}, 100);*/
			}
		})
	}

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
					//console.log('client.php?id='+id);
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
					//console.log('client.php?id='+id);
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
                    //console.log(1);
                    $('#data').html(res.data);
                    setTimeout(function () {
                        window.location.replace('certificate.php?id=' + id);
                        //console.log('client.php?id='+id);
                    }, 100);
                }else{
                    //console.log(2);
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
                client_id: client_id
			},
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
			success:function(res){
                if(res.result == 'success') {
                	//console.log(1);
                    document.getElementById("errrror").innerHTML = res.data;
                    setTimeout(function () {
                        window.location.replace('invoice.php?id=' + id);
                        //console.log('client.php?id='+id);
                    }, 100);
                }else{
                    //console.log(2);
                    document.getElementById("errrror").innerHTML = res.data;
				}
			}
		})
	};

	//Редактирование времени наряда
	function Ajax_invoice_time_edit(id) {

        $.ajax({
			url:"invoice_time_edit_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
			data:
			{
				id: id,
				new_create_time: $("#datanew").val()
			},
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
			success:function(res){
                //console.log(res);
                if(res.result == 'success') {
                	//console.log(1);
                   $("#errrror").html(res.data);
                    setTimeout(function () {
                        window.location.replace('invoice.php?id=' + id);
                        //console.log('client.php?id='+id);
                    }, 500);
                }else{
                    //console.log(2);
                    $("#errrror").html(res.data);
				}
			}
		})
	}

	//Удаление блокировка ордера
	function Ajax_del_order(id, client_id) {

		ajax({
			url:"order_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id,
                client_id: client_id
			},
			success:function(data){
				document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('order.php?id='+id);
					//console.log('client.php?id='+id);
				}, 2000);
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
				session_id: session_id
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('client.php?id='+id);
					//console.log('client.php?id='+id);
				}, 100);
			}
		})
	}
	
	function Ajax_reopen_pricelistitem(id) {
		//var id = document.getElementById("id").value;
		
		ajax({
			url:"pricelistitem_reopen_f.php",
			method:"POST",
			data:
			{
				id: id
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('pricelistitem.php?id='+id);
					//console.log('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	}

	//разблокировка страховой
	function Ajax_reopen_insure(id) {
		//var id = document.getElementById("id").value;
		
		ajax({
			url:"insure_reopen_f.php",
			method:"POST",
			data:
			{
				id: id
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('insure.php?id='+id);
					//console.log('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	}

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
					//console.log('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	}

	//разблокировка сертификата
	function Ajax_reopen_cert(id) {
		//var id = document.getElementById("id").value;

		ajax({
			url:"cert_reopen_f.php",
			method:"POST",
			data:
			{
				id: id
			},
			success:function(data){
				//document.getElementById("errrror").innerHTML=data;
				setTimeout(function () {
					window.location.replace('certificate.php?id='+id);
					//console.log('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	}

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
					//console.log('pricelistitem.php?id='+id);
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
					//console.log('pricelistitem.php?id='+id);
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
					//console.log('pricelistgroup.php?id='+id);
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
					//console.log('форма корректно заполнена');
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
					//console.log('форма корректно заполнена');
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
	//Добавить лабораторию
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
	//Добавить объявление
	function Ajax_add_announcing(mode) {

        var announcing_id = 0;

        var link = "announcing_add_f.php";

        if (mode == 'edit'){
            link = "announcing_add_f.php";
            announcing_id = $("#announcing_id").val();
        }

		var announcing_type = $("#announcing_type").val();
		var theme = $("#theme").val();
		var comment = $("#comment").val();
		var filial = $("#filial").val();
		var workers_type = $("#workers_type").val();
		//console.log(announcing_type);

		$.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
			{
                announcing_type:announcing_type,
                comment:comment,
                filial:filial,
                workers_type:workers_type,
                theme: theme
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(res){
                if(res.result == "success") {
                    $('#data').html(res.data);
                    setTimeout(function () {
                        window.location.replace('index.php');
                    }, 1000)
                }else{
					$('#errror').html(data);
                }
			}
		})
	};

	//Добавить новую задачу
	function Ajax_add_ticket(mode) {

        var ticket_id = 0;

        var link = "ticket_add_f.php";

        if (mode == 'edit'){
            link = "ticket_edit_f.php";
            ticket_id = $("#ticket_id").val();
        }

		var descr = $("#descr").val();
		var plan_date = $("#iWantThisDate2").val();
		var workers = $("#postCategory").val();
        var workers_type = $("#workers_type").val();
        var filial = $("#filial").val();
		//console.log(ticket_type);

        var certData = {
            descr: descr,
            plan_date: plan_date,
            workers: workers,
            workers_type: workers_type,
            filial: filial,
            ticket_id: ticket_id
        };

		$.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: certData,
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(res){
            	//console.log (res);

                if(res.result == "success") {
                    $('#data').html(res.data);
                    setTimeout(function () {
                        window.location.replace('tickets.php');
                    }, 800)
                }else{
					$('#errror').html(res.data);
                    //$('#descr').css({'border-color': 'red'});
                }
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
                    //console.log('форма корректно заполнена');
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
                                //console.log('success');
                                $('#data').html(data.data);
                            }else{
                                //console.log('error');
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
                    //console.log('success');
                    $('#data').html(data.data);
                }else{
                    //console.log('error');
                    $('#errror').html(data.data);
                    $('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу специализацию
    function Ajax_specialization_add(mode){

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

            data:
			{
				name: name,
			},

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(data){
                //console.log('success1');
                if(data.result == 'success') {
                    //console.log('success');
                    $('#data').html(data.data);
                    setTimeout(function () {
                        window.location.replace('specializations.php');
                    }, 100);
                }else{
                    //console.log('error');
                    $('#errror').html(data.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу категории процентов
    function Ajax_cat_add(mode){

        var link = "fl_percent_cat_add_f.php";

        if (mode == 'edit'){
            link = "fl_percent_cat_edit_f.php";
        }
		//console.log(link);

        var cat_name = $('#cat_name').val();
        var work_percent = $('#work_percent').val();
        var material_percent = $('#material_percent').val();
        var personal_id = $('#personal_id').val();
        /*console.log(cat_name);
        console.log(work_percent);
        console.log(material_percent);
        console.log(personal_id);*/

        // убираем класс ошибок с инпутов
        $("input").each(function(){
            $(this).removeClass("error_input");
        });
        // прячем текст ошибок
        $(".error").hide();

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data:{
                cat_name: cat_name,
                work_percent: work_percent,
                material_percent: material_percent,
                personal_id: personal_id
            },

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){
                    //console.log(data.result);
                    $.ajax({
                        url: link,
                        global: false,
                        type: "POST",
                        dataType: "JSON",

                        data:
                            {
                                cat_name: cat_name,
                                work_percent: work_percent,
                                material_percent: material_percent,
                                personal_id: personal_id
                            },

                        cache: false,
                        beforeSend: function() {
                            //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                        },
                        // действие, при ответе с сервера
                        success:function(data){
                            //console.log(data.data);

                            if(data.result == 'success') {
                                //console.log('success');
                                $('#data').html(data.data);
                                setTimeout(function () {
                                    //window.location.replace('specializations.php');
                                }, 100);
                            }else{
                                //console.log('error');
                                $('#errror').html(data.data);
                                //$('#errrror').html('');
                            }
                        }
                    });
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

	//!!! тут очередная "правильная" ф-ция
    //Промежуточная функция добавления/редактирования сертификата
    function showCertAdd(id, mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var num = $('#num').val();
        var nominal = $('#nominal').val();

        var certData = {
            num:num,
            nominal:nominal
        };

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

    function Ajax_change_expiresTime(id){
        //console.log(id);

        var link = "cert_change_expiresTime.php";

        var dataCertEnd = $('#dataCertEnd').val();
        var dataCertEnd_arr = dataCertEnd.split('.');

        if ((dataCertEnd_arr[2] == undefined) ||
			(dataCertEnd_arr[1] == undefined) ||
			(dataCertEnd_arr[0] == undefined) ||
			(dataCertEnd_arr[2]+"-"+dataCertEnd_arr[1]+"-"+dataCertEnd_arr[0] == '0000-00-00')) {

            alert('Что-то пошло не так');

        }else{

            var certData = {
                cert_id: id,
                dataCertEnd: dataCertEnd_arr[2] + "-" + dataCertEnd_arr[1] + "-" + dataCertEnd_arr[0]
            };

            console.log(dataCertEnd_arr[2]+"-"+dataCertEnd_arr[1]+"-"+dataCertEnd_arr[0]);

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: certData,

                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log("fillInvoiseRez---------->");
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    }

                    // !!! скролл надо замутить сюда $('#invoice_rezult').scrollTop();
                }
            });
        }
    }

	function Ajax_edit_insure(id) {
		//убираем класс ошибок с инпутов
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
					//console.log('форма корректно заполнена');
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
					//console.log('форма корректно заполнена');
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

		//console.log (ignoreshed);

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

        var typeW = document.querySelector('input[name="typeW"]:checked').value;

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

        var fullAll = $("input[id=fullAll]:checked").val();
        if (fullAll === undefined){
            fullAll = 0;
        }

        var fullWOInvoice = $("input[id=fullWOInvoice]:checked").val();
        if (fullWOInvoice === undefined){
            fullWOInvoice = 0;
        }

        var fullWOTask = $("input[id=fullWOTask]:checked").val();
        if (fullWOTask === undefined){
            fullWOTask = 0;
        }

        var fullOk = $("input[id=fullOk]:checked").val();
        if (fullOk === undefined){
            fullOk = 0;
        }

        var statusAll = $("input[id=statusAll]:checked").val();
        if (statusAll === undefined){
            statusAll = 0;
        }

        var statusPervich = $("input[id=statusPervich]:checked").val();
        if (statusPervich === undefined){
            statusPervich = 0;
        }

        var statusInsure = $("input[id=statusInsure]:checked").val();
        if (statusInsure === undefined){
            statusInsure = 0;
        }

        var statusNight = $("input[id=statusNight]:checked").val();
        if (statusNight === undefined){
            statusNight = 0;
        }

        var statusAnother = $("input[id=statusAnother]:checked").val();
        if (statusAnother === undefined){
            statusAnother = 0;
        }

        var invoiceAll = $("input[id=invoiceAll]:checked").val();
        if (invoiceAll === undefined){
            invoiceAll = 0;
        }

        var invoicePaid = $("input[id=invoicePaid]:checked").val();
        if (invoicePaid === undefined){
            invoicePaid = 0;
        }

        var invoiceNotPaid = $("input[id=invoiceNotPaid]:checked").val();
        if (invoiceNotPaid === undefined){
            invoiceNotPaid = 0;
        }

        var invoiceInsure = $("input[id=invoiceInsure]:checked").val();
        if (invoiceInsure === undefined){
            invoiceInsure = 0;
        }

        var patientUnic = $("input[id=patientUnic]:checked").val();
        if (patientUnic === undefined){
            patientUnic = 0;
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

                    //Кто создал запись
                    creator:$("#search_worker").val(),
                    //Пациент
                    client:$("#search_client").val(),
                    //К кому запись
                    worker:$("#search_client4").val(),
                    filial:$("#filial").val(),

                    typeW:typeW,

                    zapisAll: zapisAll,
                    zapisArrive: zapisArrive,
                    zapisNotArrive: zapisNotArrive,
                    zapisError: zapisError,
                    zapisNull: zapisNull,

                    fullAll: fullAll,
                    fullWOInvoice: fullWOInvoice,
                    fullWOTask: fullWOTask,
                    fullOk: fullOk,

                    statusAll: statusAll,
                    statusPervich: statusPervich,
                    statusInsure: statusInsure,
                    statusNight: statusNight,
                    statusAnother: statusAnother,

                    invoiceAll: invoiceAll,
                    invoicePaid: invoicePaid,
                    invoiceNotPaid: invoiceNotPaid,
                    invoiceInsure: invoiceInsure,

                    patientUnic: patientUnic

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

    //Выборка статистики расчётов
    function Ajax_show_result_stat_calculate(){

        var typeW = document.querySelector('input[name="typeW"]:checked').value;

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

        var fullAll = $("input[id=fullAll]:checked").val();
        if (fullAll === undefined){
            fullAll = 0;
        }

        var fullWOInvoice = $("input[id=fullWOInvoice]:checked").val();
        if (fullWOInvoice === undefined){
            fullWOInvoice = 0;
        }

        var fullWOTask = $("input[id=fullWOTask]:checked").val();
        if (fullWOTask === undefined){
            fullWOTask = 0;
        }

        var fullOk = $("input[id=fullOk]:checked").val();
        if (fullOk === undefined){
            fullOk = 0;
        }

        var statusAll = $("input[id=statusAll]:checked").val();
        if (statusAll === undefined){
            statusAll = 0;
        }

        var statusPervich = $("input[id=statusPervich]:checked").val();
        if (statusPervich === undefined){
            statusPervich = 0;
        }

        var statusInsure = $("input[id=statusInsure]:checked").val();
        if (statusInsure === undefined){
            statusInsure = 0;
        }

        var statusNight = $("input[id=statusNight]:checked").val();
        if (statusNight === undefined){
            statusNight = 0;
        }

        var statusAnother = $("input[id=statusAnother]:checked").val();
        if (statusAnother === undefined){
            statusAnother = 0;
        }

        var invoiceAll = $("input[id=invoiceAll]:checked").val();
        if (invoiceAll === undefined){
            invoiceAll = 0;
        }

        var invoicePaid = $("input[id=invoicePaid]:checked").val();
        if (invoicePaid === undefined){
            invoicePaid = 0;
        }

        var invoiceNotPaid = $("input[id=invoiceNotPaid]:checked").val();
        if (invoiceNotPaid === undefined){
            invoiceNotPaid = 0;
        }

        var invoiceInsure = $("input[id=invoiceInsure]:checked").val();
        if (invoiceInsure === undefined){
            invoiceInsure = 0;
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

                    //Кто создал запись
                    creator:$("#search_worker").val(),
                    //Пациент
                    client:$("#search_client").val(),
                    //К кому запись
                    worker:$("#search_client4").val(),
                    filial:$("#filial").val(),

                    typeW:typeW,

                    zapisAll: zapisAll,
                    zapisArrive: zapisArrive,
                    zapisNotArrive: zapisNotArrive,
                    zapisError: zapisError,
                    zapisNull: zapisNull,

                    fullAll: fullAll,
                    fullWOInvoice: fullWOInvoice,
                    fullWOTask: fullWOTask,
                    fullOk: fullOk,

                    statusAll: statusAll,
                    statusPervich: statusPervich,
                    statusInsure: statusInsure,
                    statusNight: statusNight,
                    statusAnother: statusAnother,

                    invoiceAll: invoiceAll,
                    invoicePaid: invoicePaid,
                    invoiceNotPaid: invoiceNotPaid,
                    invoiceInsure: invoiceInsure,

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

    //Выборка статистики  нарядов
    function Ajax_show_result_stat_invoice(){

        //var typeW = document.querySelector('input[name="typeW"]:checked').value;

        var paidAll = $("input[id=paidAll]:checked").val();
        if (paidAll === undefined){
            paidAll = 0;
        }
        var paidTrue = $("input[id=paidTrue]:checked").val();
        if (paidTrue === undefined){
            paidTrue = 0;
        }
        var paidNot = $("input[id=paidNot]:checked").val();
        if (paidNot === undefined){
            paidNot = 0;
        }

        /*var zapisError = $("input[id=zapisError]:checked").val();
        if (zapisError === undefined){
            zapisError = 0;
        }*/

        var insureTrue = $("input[id=insureTrue]:checked").val();
        if (insureTrue === undefined){
            insureTrue = 0;
        }

        /*var fullAll = $("input[id=fullAll]:checked").val();
        if (fullAll === undefined){
            fullAll = 0;
        }

        var fullWOInvoice = $("input[id=fullWOInvoice]:checked").val();
        if (fullWOInvoice === undefined){
            fullWOInvoice = 0;
        }

        var fullWOTask = $("input[id=fullWOTask]:checked").val();
        if (fullWOTask === undefined){
            fullWOTask = 0;
        }

        var fullOk = $("input[id=fullOk]:checked").val();
        if (fullOk === undefined){
            fullOk = 0;
        }

        var statusAll = $("input[id=statusAll]:checked").val();
        if (statusAll === undefined){
            statusAll = 0;
        }

        var statusPervich = $("input[id=statusPervich]:checked").val();
        if (statusPervich === undefined){
            statusPervich = 0;
        }

        var statusInsure = $("input[id=statusInsure]:checked").val();
        if (statusInsure === undefined){
            statusInsure = 0;
        }

        var statusNight = $("input[id=statusNight]:checked").val();
        if (statusNight === undefined){
            statusNight = 0;
        }

        var statusAnother = $("input[id=statusAnother]:checked").val();
        if (statusAnother === undefined){
            statusAnother = 0;
        }*/

        $.ajax({
            url:"ajax_show_result_stat_invoice_f.php",
            global: false,
            type: "POST",
            data:
                {
                    all_time:all_time,
                    datastart: document.getElementById("datastart").value,
                    dataend: document.getElementById("dataend").value,

                    //Кто создал запись
                    creator:$("#search_worker").val(),
                    //Пациент
                    client:$("#search_client").val(),
                    //К кому запись
                    //worker:$("#search_client4").val(),
                    filial:$("#filial").val(),

                    //typeW:typeW,

                    paidAll: paidAll,
                    paidTrue: paidTrue,
                    paidNot: paidNot,
                    //zapisError: zapisError,
                    insureTrue: insureTrue,

                    /*fullAll: fullAll,
                    fullWOInvoice: fullWOInvoice,
                    fullWOTask: fullWOTask,
                    fullOk: fullOk,

                    statusAll: statusAll,
                    statusPervich: statusPervich,
                    statusInsure: statusInsure,
                    statusNight: statusNight,
                    statusAnother: statusAnother,*/

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

	//Кнопка "ясно" в объявлениях на главной странице
    $('.iUnderstand').click(function () {

    	var thisObj = $(this);
    	//кнопка "Развернуть"
    	var anotherObj =  thisObj.parent().prev();
    	//Заголовок / тема
    	var anotherObj2 =  thisObj.parent().prev().prev().prev();
    	var announcingID = thisObj.attr("announcingID");

        $.ajax({
            url: "announcing_change_readmark_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data: {
                announcingID: announcingID,
            },
            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function (res) {
                if(res.result == "success"){

                    $('#infoDiv').html(res.data);
                    $('#infoDiv').show();

                    thisObj.remove();

                    setTimeout(function() {
                        $('#topic_'+announcingID).hide('slow');
                        $('#infoDiv').hide('slow');
                        $('#infoDiv').html();

                        anotherObj.show();
                        anotherObj2.removeClass("blink1");
                    }, 500);

                    //location.reload();
                }

            }
        });
    });

	//Кнопка "Развернуть" в объявлениях на главной странице
    $('.showMeTopic').click(function () {

    	var thisObj = $(this);
    	var announcingID = thisObj.attr("announcingID");
        $('#topic_'+announcingID).show();
        thisObj.hide();
        return false;
    });


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
					//console.log("форма корректно заполнена");

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
					//console.log("форма корректно заполнена");
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
					//console.log("форма корректно заполнена");
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
					//console.log("форма корректно заполнена");
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
	function Ajax_add_TempZapis() {
        // получение данных из полей
		//var type = document.getElementById("type").value;

		var type = $("#type").val();

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
		//console.log(worker);
		if((typeof worker == "undefined") || (worker == "")) worker = 0;
		//console.log(worker);

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

        //var window_location_href_arr = (window.location.href).split("#");
        //var window_location_href = window_location_href_arr[0];

		$.ajax({
			global: false,
			type: "POST",
			// путь до скрипта-обработчика
			url: "edit_schedule_day_f.php",
			// какие данные будут переданы
			data: {
				//type:"scheduler_stom",
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
                //Разблокируем кнопку OK
                setTimeout(function () {
                	document.getElementById("Ajax_add_TempZapis").disabled = false;
                }, 200);
				if(data.result == "success"){
					document.getElementById("errror").innerHTML=data.data;
					setTimeout(function () {
                        //console.log(window.location.href);

                        //window.location.replace(window_location_href+"#tabs-4");

                        location.reload();
					}, 50);
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
		//console.log(worker);
		if((typeof worker == "undefined") || (worker == "")) worker = 0;
		//console.log(worker);

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

            var certData = {
                id:id,
                enter:enter,
                datatable: "zapis"
            }


			$.ajax({
				url: "ajax_tempzapis_edit_enter_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",

				data: certData,

                cache: false,
                beforeSend: function() {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
				success: function(res){
					//console.log(res.data);

                    if(res.result == 'success') {
                        setTimeout(function () {
                            location.reload()
                        }, 100);
                    }else{
                        if(res.search_error == 1){
                        	alert(res.data);
						}
					}
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
		//console.log();

		var type = $("#type").val();

		var work_time_h = Number($("#work_time_h").val());
		var work_time_m = Number($("#work_time_m").val());

		var start_time = work_time_h*60+work_time_m;
        //console.log(start_time);

        $("#start_time").val(start_time);

		//var start_time = Number(document.getElementById("start_time").value);
		var change_hours = Number($("#change_hours").val());
		var change_minutes = Number($("#change_minutes").val());

		var day = Number($("#day").val());
		var month = Number($("#month").val());
		var year = Number($("#year").val());

        /*console.log(day);
        console.log(month);
        console.log(year);*/

		var filial = Number($("#filial").val());
		var zapis_id = Number($("#zapis_id").val());
		var kab = $("#kab").html();

		//var wt = Number($("#wt").val());
		var wt = change_hours*60+change_minutes;

		//console.log(zapis_id);
		//console.log(kab);
		//console.log(wt);
		//console.log(start_time);
		//console.log(start_time+wt);
        //console.log(change_hours*60+change_minutes);

		if (change_minutes > 55){
			change_minutes = 55;
            $("#change_minutes").val(55);
		}
		if (change_hours > 12){
			change_hours = 11;
            $("#change_hours").val(11);
		}
		if ((change_hours == 0) && (change_minutes == 0)){
			change_minutes = 5;
            $("#change_minutes").val(5);
		}

		var next_time_start_rez = 0;
		var next_time_end_rez = 0;
		var query = '';
		var idz = '';

        var certData = {
            zapis_id: zapis_id,

            day: day,
            month: month,
            year: year,

            filial: filial,
            kab: kab,

            start_time: start_time,
            wt: wt,

            type: type,

            datatable:"zapis",

			direction: "next"
        };

        //Проверим записи после

        $.ajax({
			url: "get_next_zapis.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data:certData,

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){

                document.getElementById("Ajax_add_TempZapis").disabled = true;

				//console.log(res);
				//console.log (res.next_time_start);
				//document.getElementById("kab").innerHTML=res;

				next_time_start_rez = res.next_time_start;
				next_time_end_rez = res.next_time_end;
				//next_zapis_data;

                //Работаем с результатами ajax
                //console.log (next_time_start_rez);
                //console.log (next_time_end_rez);

                /*console.log('----------------');
                console.log (res.query);
                console.log (res.zapis_id);
                console.log (res.idz);
                console.log (certData.direction+' -> '+res.id);*/

                var end_time = start_time + change_hours*60 + change_minutes;

                //console.log(start_time);
                //console.log(end_time);

                //if (end_time > 1260){
                //console.log(\'Перебор\');
                //}

                //console.log(start_time+' == '+next_time_start_rez);

                var real_time_h_end = end_time/60|0;
                if (real_time_h_end > 23) real_time_h_end = real_time_h_end - 24;
                var real_time_m_end = end_time%60;

                //var real_time_h_end = (end_time + Number(wt))/60|0;
                //var real_time_m_end = (end_time + Number(wt))%60;

                if (real_time_m_end < 10) real_time_m_end = '0'+real_time_m_end;

                $("#work_time_h_end").html(real_time_h_end);
                $("#work_time_m_end").html(real_time_m_end);

                $("#wt").val(change_hours*60 + change_minutes);

                if (next_time_start_rez != 0){
                    //console.log(next_time_start_rez);

                    //if ((end_time > next_time_start_rez) || (start_time == next_time_start_rez)){
                    if (
                        (start_time <= next_time_start_rez) && (end_time > next_time_start_rez)
					){
                        //console.log(next_time_start_rez);

                        $("#exist_zapis").html('<span style="color: red">Записи не могут пересекаться</span><br>');

                        //var raznica_vremeni = Math.abs(next_time_start_rez - start_time);

                        //$("#change_hours").val(raznica_vremeni/60|0);
                        //$("#change_minutes").val(raznica_vremeni%60);

                        //change_hours = raznica_vremeni/60|0;
                        //change_minutes = raznica_vremeni%60;

                        //end_time = start_time+change_hours*60+change_minutes;

                        document.getElementById("Ajax_add_TempZapis").disabled = true;

                    }else{

                        //Теперь проверим записи до

                        certData.direction = "prev";

                        $.ajax({
                            url: "get_next_zapis.php",
                            global: false,
                            type: "POST",
                            dataType: "JSON",

                            data:certData,

                            cache: false,
                            beforeSend: function() {
                                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                            },
                            success:function(res){

                                document.getElementById("Ajax_add_TempZapis").disabled = true;

                                //console.log(res);
                                //console.log (res.next_time_start);
                                //document.getElementById("kab").innerHTML=res;

                                next_time_start_rez = res.next_time_start;
                                next_time_end_rez = res.next_time_end;
                                //next_zapis_data;

                                //Работаем с результатами ajax
                                //console.log (next_time_start_rez);
                                //console.log (next_time_end_rez);

                                //console.log (res.query);
                                //console.log (certData.direction+' -> '+res.id);

                                var end_time = start_time + change_hours*60 + change_minutes;

                                //console.log(start_time);
                                //console.log(end_time);

                                //if (end_time > 1260){
                                //console.log(\'Перебор\');
                                //}

                                //console.log(start_time+' == '+next_time_start_rez);

                                var real_time_h_end = end_time/60|0;
                                if (real_time_h_end > 23) real_time_h_end = real_time_h_end - 24;
                                var real_time_m_end = end_time%60;

                                //var real_time_h_end = (end_time + Number(wt))/60|0;
                                //var real_time_m_end = (end_time + Number(wt))%60;

                                if (real_time_m_end < 10) real_time_m_end = '0'+real_time_m_end;

                                $("#work_time_h_end").html(real_time_h_end);
                                $("#work_time_m_end").html(real_time_m_end);

                                $("#wt").val(change_hours*60 + change_minutes);

                                if (next_time_start_rez != 0){
                                    //console.log(next_time_start_rez);

                                    //if ((end_time > next_time_start_rez) || (start_time == next_time_start_rez)){
                                    if (
                                        ((start_time < next_time_end_rez) && (start_time >= next_time_start_rez))
                                    ){
                                        //console.log(next_time_start_rez);

                                        $("#exist_zapis").html('<span style="color: red">Записи не могут пересекаться</span><br>');

                                        //var raznica_vremeni = Math.abs(next_time_start_rez - start_time);

                                        //$("#change_hours").val(raznica_vremeni/60|0);
                                        //$("#change_minutes").val(raznica_vremeni%60);

                                        //change_hours = raznica_vremeni/60|0;
                                        //change_minutes = raznica_vremeni%60;

                                        //end_time = start_time+change_hours*60+change_minutes;

                                        document.getElementById("Ajax_add_TempZapis").disabled = true;
                                    }else{
                                        //if (end_time < next_time_start_rez){
                                        $("#exist_zapis").html('');
                                        document.getElementById("Ajax_add_TempZapis").disabled = false;
                                    }
                                }else{
                                    $("#exist_zapis").html('');
                                    document.getElementById("Ajax_add_TempZapis").disabled = false;
                                }
                            }
                        });


                        //if (end_time < next_time_start_rez){
                        /*$("#exist_zapis").html('');
                        document.getElementById("Ajax_add_TempZapis").disabled = false;*/
                    }
                }else{
                    $("#exist_zapis").html('');
                    document.getElementById("Ajax_add_TempZapis").disabled = false;
                }
			}
		});
	}

	function PriemTimeCalcChangeDate(){
        //console.log($("#month_date").val());

        var IWantDateArr = $("#month_date").val().split('.');
        //console.log(IWantDateArr);

        $("#day").val(Number(IWantDateArr[0]));
        $("#month").val(Number(IWantDateArr[1]));
        $("#year").val(Number(IWantDateArr[2]));

        PriemTimeCalc();
	}

	//События при наведении/убирании мыши !!! СуперТест!
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
	function calculateInvoice (invoice_type, changeItogPrice){

		var Summ = 0;
		var SummIns = 0;

		var insure = 0;
		var insureapprove = 0;

		//var discount = Number(document.getElementById("discountValue").innerHTML);

        $("#calculateInvoice").html(Summ);
        
		if (invoice_type == 5){
			$("#calculateInsInvoice").html(SummIns);
		}

		$(".invoiceItemPrice").each(function() {

            var invoiceItemPriceItog = 0;

			//console.log(document.getElementById($(this).parent().find('[insure]')).getAttribute('insure'));
			//console.log($(this).prev().prev().attr('insure'));
			//console.log($(this).attr("insure"));

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
			//var cost = Number(this.innerHTML);
			var cost = Number($(this).attr('price'));
			//console.log(cost);

			var ind = $(this).attr('ind');
			var key = $(this).attr('key');
			//console.log(ind);
			//console.log(key);
			//console.log(cost);

			//обновляем цену в сессии как можем
			$.ajax({
				url: 'add_price_price_id_in_item_invoice_f.php',
				global: false,
				type: "POST",
				dataType: "JSON",
				data:
				{
					client: $("#client").value,
					zapis_id: $("#zapis_id").value,
					filial: $("#filial").value,
					worker: $("#worker").value,

					invoice_type: invoice_type,

					ind: ind,
					key: key,

					price: cost
				},
				cache: false,
				beforeSend: function() {
					//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
				},
				// действие, при ответе с сервера
				success: function(res){
					//if(data.result == "success"){
						//console.log(data.data);
						//$('#invoice_rezult').html(data.data);

					//}else{
						//console.log('error');
					//	$('#errror').html(data.data);
					//}
				}
			});

			//коэффициент

			//скидка акция
			var discount = $(this).next().next().next().attr('discount');
			//console.log(discount);

			//взяли количество
			var quantity = Number($(this).parent().find('[type=number]').val());

			//вычисляем стоимость
			//var stoim = quantity * (cost +  cost * spec_koeff / 100);
			var stoim = quantity * cost	;

			//с учетом скидки акции, но если не страховая
            if (insure == 0) {
                stoim = stoim - (stoim * discount / 100);

            	//Убрали округление 2017.08.09
           		//stoim = Math.round(stoim / 10) * 10;
                //Изменили округление 2017.08.10
           		stoim = Math.round(stoim);
            }

            if (!changeItogPrice) {
                stoim = Number($(this).parent().find('.invoiceItemPriceItog').html());
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

            var invoiceItemPriceItog = stoim;
            var ishod_price = Number($(this).parent().find('.invoiceItemPriceItog').html());

            /*console.log(ishod_price);
            console.log(invoiceItemPriceItog);
            console.log(stoim);
            console.log(changeItogPrice);
            console.log("//////////////////////");*/

            if (ishod_price == 0) {
                if (guarantee != 1) {
                    $(this).parent().find('.invoiceItemPriceItog').html(stoim);
                }
            }

            if (changeItogPrice) {
                //прописываем стоимость этой позиции
                if (guarantee == 0) {
                    //$(this).next().next().next().next().next().html(stoim);
                    //$(this).next().next().next().next().html(stoim);

                    $(this).parent().find('.invoiceItemPriceItog').html(stoim);
                }
            }
            //console.log("calculateInvoice --> changeItogPrice ---->");
            //console.log(invoiceItemPriceItog);

            if (changeItogPrice) {
				//console.log(changeItogPrice);

				/*var min_itog_price = Math.floor(invoiceItemPriceItog / 10) * 10;
				var max_itog_price = min_itog_price + 10;
				if (min_itog_price < 1) min_itog_price = 1;*/

				//priceItemItogInvoice (ind, key, invoiceItemPriceItog, min_itog_price, max_itog_price)

                $.ajax({
                    url: "add_manual_itog_price_id_in_item_invoice_f.php",
                    global: false,
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        ind: ind,
                        key: key,
                        price: invoiceItemPriceItog,
                        manual_itog_price: stoim,

                        client: $("#client").val(),
                        zapis_id: $("#zapis_id").val(),
                        filial: $("#filial").val(),
                        worker: $("#worker").val(),

                        invoice_type: invoice_type
                    },
                    cache: false,
                    beforeSend: function () {
                        //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                    },
                    // действие, при ответе с сервера
                    success: function (res) {
                        //console.log(res);

                        //fillInvoiseRez();

                        //$(this).parent().find('.invoiceItemPriceItog').html(invoiceItemPriceItog);

                    }
                });

            }


            //обновляем итоговую цену в сессии как можем
            /*$.ajax({
                url: 'add_price_itog_price_id_in_item_invoice_f.php',
                global: false,
                type: "POST",
                dataType: "JSON",
                data:
                    {
                        client: $("#client").val(),
                        zapis_id: $("#zapis_id").val(),
                        filial: $("#filial").val(),
                        worker: $("#worker").val(),

                        invoice_type: invoice_type,

                        ind: ind,
                        key: key,

                        price: invoiceItemPriceItog
                    },
                cache: false,
                beforeSend: function() {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function(data){
                    //console.log(data);
                    //if(data.result == "success"){
                    //console.log(data.data);
                    //$('#invoice_rezult').html(data.data);

                    //}else{
                    //console.log('error');
                    //	$('#errror').html(data.data);
                    //}

                    //$(this).parent().find('.invoiceItemPriceItog').html(invoiceItemPriceItog);

                }
            });*/


		});

        //Summ = Math.round(Summ - (Summ * discount / 100));

        //Убрали округление 2017.08.09
        //Summ = Math.round(Summ/10) * 10;
        //Изменили округление 2017.08.10
        Summ = Math.round(Summ);

        //SummIns = Math.round(SummIns - (SummIns * discount / 100));
		//страховые не округляем
        //SummIns = Math.round(SummIns/10) * 10;

        $("#calculateInvoice").html(Summ);

		if (SummIns > 0){
			$("#calculateInsInvoice").html(SummIns);
		}

        /*console.log(Summ);
        console.log("<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>");*/

	};

	//Подсчёт суммы для расчёта
	function calculateCalculate (){

		var Summ = 0;

		$(".invoiceItemPriceItog").each(function() {

            Summ += Number($(this).html());
            //console.log(Summ);
        });

		$("#calculateSumm").html(Summ);

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
	function fillInvoiseRez(changeItogPrice){

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
				client: $("#client").val(),
				zapis_id: $("#zapis_id").val(),
				filial: $("#filial").val(),
				worker: $("#worker").val()
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
                //console.log("fillInvoiseRez---------->");
                //console.log(res);

				if(res.result == "success"){
					$('#invoice_rezult').html(res.data);

					// !!!
					calculateInvoice(invoice_type, changeItogPrice);

				}else{
					//console.log('error');
					$('#errror').html(res.data);
				}

				// !!! скролл надо замутить сюда $('#invoice_rezult').scrollTop();
			}
		});
		//$('#errror').html('Результат');
		//calculateInvoice();
	}

	//Функция заполняет результат расчета из сессии
	function fillCalculateRez(){

		var invoice_type = $("#invoice_type").val();
        //console.log(invoice_type);

		var link = "fill_calculate_stom_from_session_f.php";
		if (invoice_type == 6){
			link = "fill_calculate_cosm_from_session_f.php";
		}
        //console.log(link);

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
				client: $("#client").val(),
				zapis_id: $("#zapis_id2").val(),
				filial: $("#filial").val(),
				worker: $("#worker").val()

			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
                //console.log(res.data2);

				if(res.result == "success"){
					//console.log(res.data);
					$('#calculate_rezult').html(res.data);
					//$('#calculate_rezult').append(res.data);

					// !!!
                    calculateCalculate();

				}else{
					console.log(res.data);
					$('#errror').html(res.data);
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

                fillInvoiseRez(true);

				if(data.result == "success"){
					//$(\'#errror\').html(data.data);


				}else{
					$('#errror').html(data.data);
				}

			}
		})
	}
	//меняет кол-во позиции
	function changeQuantityInvoice(ind, itemId, dataObj){
		//console.log(dataObj.value);
		//console.log(this);

		var invoice_type = $("#invoice_type").val();

		//количество
		var quantity = dataObj.value;
		//console.log(quantity);

		$.ajax({
			url:"add_quantity_price_id_in_invoice_f.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
				key: itemId,
                ind: ind,

				client: $("#client").val(),
				zapis_id: $("#zapis_id").val(),
				filial: $("#filial").val(),
				worker: $("#worker").val(),

				quantity: quantity,
				invoice_type: invoice_type,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
                //console.log(res);

                fillInvoiseRez(true);

				//$('#errror').html(data);
				//if(data.result == "success"){
				//	console.log(data.data);

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
					//console.log('error');
					$('#errror').html(data.data);
				}*/

			}
		});
	}

	//Для измения цены +1
	function invPriceUpDownOne(ind, itemId, price, start_price, up_down){
		//console.log(dataObj.value);
		//console.log(this);

		var invoice_type = document.getElementById("invoice_type").value;

        if (up_down == 'up'){
            price = Number(price) + 1;
        }
        if (up_down == 'down'){
            price = Number(price) - 1;
        }

        if (isNaN(price)) price = start_price;
        if (price <= start_price) price = start_price;

		//console.log(price);

		$.ajax({
			url:"add_price_up_down_one_price_id_in_invoice_f.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
				key: itemId,
				ind: ind,

                price: price,
                start_price: start_price,

				client: $("#client").val(),
				zapis_id: $("#zapis_id").val(),
				filial: $("#filial").val(),
				worker: $("#worker").val(),

				invoice_type: invoice_type,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
				//console.log(data);

                fillInvoiseRez(true);

			}
		});
	}

	//Удалить текущую позицию
	function deleteInvoiceItem(zub, dataObj){
		//console.log(dataObj.getAttribute("invoiceitemid"));

		//номер позиции
		var itemId = dataObj.getAttribute("invoiceitemid");
		var target = 'item';

		//if ((itemId == 0) || (itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
		if ((itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
			target = 'zub';
		}
		//console.log(zub);
		//console.log(target);

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

                fillInvoiseRez(true);

				//$('#errror').html(data);
				if(data.result == "success"){
					//console.log(111);

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
					//console.log('error');
					$('#errror').html(data.data);
				}*/


			}
		});
	}

	//Удалить текущую позицию в расчете
	function deleteCalculateItem(ind, dataObj){
		//console.log($(dataObj).parent().remove());
        //$(dataObj).parent().remove();

		//номер позиции
		var itemId = dataObj.getAttribute("invoiceitemid");
		var target = 'item';

		//if ((itemId == 0) || (itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
		if ((itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
			target = 'zub';
		}
		//console.log(zub);
		//console.log(target);

		$.ajax({
			url:"fl_delete_calculate_item_from_session_f.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
				key: itemId,
                ind: ind,

				client: $("#client").val(),
				zapis_id: $("#zapis_id2").val(),
				filial: $("#filial").val(),
				worker: $("#worker").val(),

				target: target,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){

				fillCalculateRez();

				//$('#errror').html(data);
				if(data.result == "success"){
					//console.log(111);

					//colorizeTButton (data.t_number_active);

					/*$(".sel_tooth").each(function() {
						if (Number(this.innerHTML) == data.t_number_active){
							this.style.background = '#83DB53';
						}else{
							this.style.background = '';
						}
					});*/
				}else{
					//console.log('error');
					$('#errror').html(data.data);
				}


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

                fillInvoiseRez(true);

				//$('#errror').html(data);
				//if(data.result == "success"){
					//console.log(111);

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
					//console.log('error');
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

                fillInvoiseRez(true);

			}
		});
	}

	//Изменить коэффициент специалиста у всех
	function spec_koeffInvoice(spec_koeff){
		//console.log(spec_koeff);

		var invoice_type = $("#invoice_type").val();

		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();

		$.ajax({
			url:"add_spec_koeff_price_id_in_invoice_f.php",
			global: false,
			type: "POST",
			//dataType: "JSON",
			data:
			{
				spec_koeff: spec_koeff,
				client: $("#client").val(),
				zapis_id: $("#zapis_id").val(),
				filial: $("#filial").val(),
				worker: $("#worker").val(),

				invoice_type: invoice_type
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
                //$('#errror').html(data);

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
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

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
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

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
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
		//console.log(discount);

		var invoice_type = $("#invoice_type").val();

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

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
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

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
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

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
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

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
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

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
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

    //Изменить категорию процентов
    function fl_changeItemPercentCat(ind, key, percent_cat){

        var invoice_type = $("#invoice_type").val();

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        //$('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        //$('.context-menu').remove();

        $.ajax({
            url:"fl_add_percent_cat_in_item_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    ind: ind,
                    key: key,
                    percent_cat: percent_cat,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id2").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type: invoice_type,
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){
                //console.log(data);

                fillCalculateRez();

                /*if(data.result == "success"){
                 //console.log(data.data);
                 $('#invoice_rezult').html(data.data);
                 }else{
                 //console.log('error');
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
			//dataType: "JSON",
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

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
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
	function discountItemInvoice(ind, key, discount){

		var invoice_type = $("#invoice_type").val();

		//console.log(discount);
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
				ind: ind,
				key: key,
				discount: discount,
				client: $("#client").val(),
				zapis_id: $("#zapis_id").val(),
				filial: $("#filial").val(),
				worker: $("#worker").val(),

				invoice_type: invoice_type,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
				//console.log(res);

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
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

	//Изменить цену у этого зуба
	function priceItemInvoice(ind, key, price, start_price){

		var invoice_type = document.getElementById("invoice_type").value;

		/*console.log(ind);
		console.log(key);
		console.log(price);
		console.log(start_price);*/

        if (isNaN(price)) price = start_price;
		if (price <= start_price) price = start_price;

		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();

		$.ajax({
			url:"add_manual_price_id_in_item_invoice_f.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                ind: ind,
				key: key,
                price: price,

                start_price: start_price,

				client: document.getElementById("client").value,
				zapis_id: document.getElementById("zapis_id").value,
				filial: document.getElementById("filial").value,
				worker: document.getElementById("worker").value,

				invoice_type: invoice_type
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
				//console.log(res);

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
					$('#errror').html(data.data);
				}*/
			}
		});
		//$(".invoiceItemPrice").each(function() {

			//this.innerHTML = Number(this.innerHTML) + Number(this.innerHTML) / 100 * koeff

			//написали стоимость позиции
			//$(this).next().next().next().html(quantity * Number(this.innerHTML));


		//});*/

	}

	//Изменить итоговую цену у этой позиции
	function priceItemItogInvoice(ind, key, price, manual_itog_price){

		var invoice_type = $("#invoice_type").val();

		/*console.log(ind);
		console.log(key);*/

        var min_price = manual_itog_price - 2;
        var max_price = manual_itog_price + 2;

        if (min_price < 0) min_price = 0;

        if (isNaN(price)) price = max_price;
		if (price < min_price) price = min_price;
		if (price > max_price) price = max_price;

        //console.log(min_price);
        //console.log(max_price);
        //console.log(price);

		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();

		$.ajax({
			url:"add_manual_itog_price_id_in_item_invoice_f.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                ind: ind,
				key: key,
                price: price,
                manual_itog_price: manual_itog_price,

				client: $("#client").val(),
				zapis_id: $("#zapis_id").val(),
				filial: $("#filial").val(),
				worker: $("#worker").val(),

				invoice_type: invoice_type
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
				//console.log("priceItemItogInvoice----------->");
                //console.log(res);
				//console.log(price);

				fillInvoiseRez(false);

			}
		});


	}

	//Выбор зуба из таблички
	function toothInInvoice(t_number){

		//console.log (t_number);
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

                fillInvoiseRez(true);

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
		//console.log(100);

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
				worker: document.getElementById("worker").value
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){
             	//$('#errror').html(data);

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
					$('#errror').html(data.data);
				}*/
			}
		});

	};

	//Добавить позицию из МКБ в акт
	function checkMKBItem(mkb_id){
		//console.log(100);
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
				worker: document.getElementById("worker").value
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(data){

                fillInvoiseRez(true);

				/*if(data.result == "success"){
					//console.log(data.data);
					$('#invoice_rezult').html(data.data);
				}else{
					//console.log('error');
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

                    fillInvoiseRez(true);

					colorizeTButton();
				}
			});

		}
	};

	// !!! Перенесли отсюда документ реади в инвойс_адд


	//Сменить филиал в сессии пользователя
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
				//console.log("Ok");
				location.reload();
			}
		});
	}

	//Сменить филиал в сессии пользователя
	function fl_changePercentCat(percent_cat){

        var invoice_type = $("#invoice_type").val();

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url:"fl_add_percent_cat_in_invoice_f.php",
            global: false,
            type: "POST",
            //dataType: "JSON",
            data:
                {
                    percent_cat: percent_cat,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id2").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type:invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){
                //console.log(data);
                fillCalculateRez();

				/*if(data.result == "success"){
				 //console.log(data.data);
				 $('#invoice_rezult').html(data.data);
				 }else{
				 //console.log('error');
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

	//Показываем блок с суммами и кнопками Для наряда
	function showInvoiceAdd(invoice_type, mode){
		//console.log(mode);
		$('#overlay').show();
		
		var Summ = $("#calculateInvoice").html();
		var SummIns = 0;
		var SummInsBlock = '';
		
		if (invoice_type == 5){
			SummIns = $("#calculateInsInvoice").html();
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

	//Показываем блок с суммами и кнопками Для расчета
	/*function showCalculateAdd(invoice_type, mode){
		//console.log(mode);
		$('#overlay').show();

		var Summ = document.getElementById("calculateInvoice").innerHTML;
		var SummIns = 0;
		var SummInsBlock = '';

		if (invoice_type == 5){
			SummIns = document.getElementById("calculateInsInvoice").innerHTML;
			SummInsBlock = '<div>Страховка: <span class="calculateInsInvoice">'+SummIns+'</span> руб.</div>';
		}

		var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_calculate_add(\'add\')">';


		if (mode == 'edit'){
			buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_calculate_add(\'edit\')">';
		}

		// Создаем меню:
		var menu = $('<div/>', {
			class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
		}).css({
			"height": "100px",
		})
		.appendTo('#overlay')
		.append(
			$('<div/>')
			.css({
				"height": "100%",
				"border": "1px solid #AAA",
				"position": "relative",
			})
			.append('<span style="margin: 5px;"><i>Это действие нельзя будет отменить. Вы уверены?</i></span>')
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
				//.append('<div style="margin: 10px;">Сумма: <span class="calculateInvoice">'+Summ+'</span> руб.</div>'+SummInsBlock)
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

	}*/

	//Показываем блок с суммами и кнопками Для расчета
	function showCalculateAdd(invoice_type, mode){
		//console.log(mode);
		$('#overlay').show();

		var Summ = $("#calculateInvoice").html();
		var SummIns = 0;
		var SummInsBlock = '';

		if (invoice_type == 5){
			SummIns = $("#calculateInsInvoice").html();
			SummInsBlock = '<div>Страховка: <span class="calculateInsInvoice">'+SummIns+'</span> руб.</div>';
		}

		var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_calculate_add(\'add\')">';


		if (mode == 'edit'){
			buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_calculate_add(\'edit\')">';
		}

		if (mode == 'reset'){
			buttonsStr = '<input type="button" class="b" value="Сбросить" onclick="Ajax_calculate_add(\'reset\')">';
		}

		// Создаем меню:
		var menu = $('<div/>', {
			class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
		}).css({
			"height": "100px",
		})
		.appendTo('#overlay')
		.append(
			$('<div/>')
			.css({
				"height": "100%",
				"border": "1px solid #AAA",
				"position": "relative",
			})
			.append('<span style="margin: 5px;"><i>Подтверждение действия</i></span>')
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
				//.append('<div style="margin: 10px;">Сумма: <span class="calculateInvoice">'+Summ+'</span> руб.</div>'+SummInsBlock)
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
        //console.log(mode);

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
        //console.log(id);
        hideAllErrors ();

        var cell_price = $('#cell_price').val();
        //console.log(cell_price);

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
                	//console.log(1);
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
        //console.log(id);
        //hideAllErrors ();

        //var search_cert_input = $('#search_cert_input').html();
		//console.log(search_cert_input);

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
        //console.log(mode);

        $('.error').each(function(){
            //console.log(this.innerHTML);
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
		//console.log(mode);
		
		var invoice_id = 0;
		
		var link = "invoice_add_f.php";
		
		if (mode == 'edit'){
			link = "invoice_edit_f.php";
			invoice_id = $("#invoice_id").val();
		}
		
		var invoice_type = $("#invoice_type").val();
		
		var Summ = $("#calculateInvoice").html();
		var SummIns = 0;
		
		var SummInsStr = '';
		
		if (invoice_type == 5){
			SummIns = $("#calculateInsInvoice").html();
			SummInsStr = '<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">'+
							'Страховка:<br>'+
							'<span class="calculateInsInvoice" style="font-size: 13px">'+SummIns+'</span> руб.'+
						'</div>';
		}
		
		var client = $("#client").val();
		
		$.ajax({
			url: link,
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data:
			{
				client: client,
				zapis_id: $("#zapis_id").val(),
				filial: $("#filial").val(),
				worker: $("#worker").val(),
				
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
				//console.log(res);
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

	//Добавляем/редактируем в базу расчет
	function Ajax_calculate_add(mode){
		//console.log(mode);

		var calculate_id = 0;

		var link = "fl_calculate_add_f.php";

		if (mode == 'edit'){
			link = "fl_calculate_edit_f.php";
            calculate_id = $("#invoice_id").val();
		}

		if (mode == 'reset'){
			link = "fl_calculate_reset_f.php";
            calculate_id = $("#invoice_id").val();
		}

		var invoice_type = $("#invoice_type").val();
		//console.log (invoice_type);

		var Summ = $("#calculateInvoice").html();
        //console.log (Summ);

		var SummIns = 0;

		var SummInsStr = '';

		if (invoice_type == 5){
			SummIns = $("#calculateInsInvoice").html();
            //console.log (SummIns);

			SummInsStr = '<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">'+
							'Страховка:<br>'+
							'<span class="calculateInsInvoice" style="font-size: 13px">'+SummIns+'</span> руб.'+
						'</div>';
		}

		var client = $("#client").val();
		var invoice_id = $("#invoice_id").val();

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
				client: client,
				zapis_id: $("#zapis_id2").val(),
				invoice_id: invoice_id,
				filial: $("#filial2").val(),
				worker: $("#worker").val(),

				summ: Summ,
				summins: SummIns,

				invoice_type: invoice_type,
                calculate_id: calculate_id,
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
				//console.log(res);

				$('.center_block').remove();
				$('#overlay').hide();

				if(res.result == "success"){
                    if (mode == 'reset') {
                        location.reload();
                    }else {
                        $('#data').hide();
                        /*$('#invoices').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">' +
                            '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Добавлен/отредактирован наряд</li>' +
                            '<li class="cellsBlock" style="width: auto;">' +
                            '<a href="invoice.php?id=' + res.data + '" class="cellName ahref">' +
                            '<b>Наряд #' + res.data + '</b><br>' +
                            '</a>' +
                            '<div class="cellName">' +
                            '<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">' +
                            'Сумма:<br>' +
                            '<span class="calculateInvoice" style="font-size: 13px">' + Summ + '</span> руб.' +
                            '</div>' +
                            SummInsStr +
                            '</div>' +
                            '</li>' +
                            '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">' +
                            '<a href="payment_add.php?invoice_id=' + res.data + '" class="b">Оплатить</a>' +
                            '</li>' +
                            '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">' +
                            '<a href="add_order.php?client_id=' + client + '&invoice_id=' + res.data + '" class="b">Добавить приходный ордер</a>' +
                            '</li>' +
                            '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">' +
                            '<a href="finance_account.php?client_id=' + client + '" class="b">Управление счётом</a>' +
                            '</li>' +
                            '</ul>');*/
                        window.location.replace('invoice.php?id='+invoice_id+'');
                    }
				}else{
					$('#errror').html(res.data);
				}
			}
		});
	}

	//Продаём сертификат по базе
	function Ajax_cert_cell(id, cell_price, office_id){

        var summ_type = document.querySelector('input[name="summ_type"]:checked').value;
        //console.log(summ_type);

		$.ajax({
			url: "cert_cell_f.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                cert_id: id,
                cell_price: cell_price,
                office_id: office_id,
                summ_type: summ_type
            },
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
				//console.log(res);
				$('.center_block').remove();
				$('#overlay').hide();

				if(res.result == "success"){
					//$('#data').hide();
					$('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
											'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Сертификат продан</li>'+
									'</ul>');
                    setTimeout(function () {
                        window.location.replace('certificate.php?id='+id+'');
                        //console.log('client.php?id='+id);
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
				//console.log(res);
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
				//console.log(res);
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
		//console.log(mode);

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
		//console.log(invoice_id);
		var date_in = document.getElementById("date_in").value;
		//console.log(date_in);

        var comment = document.getElementById("comment").value;
        //console.log(comment);

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
				//console.log(res);
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
		//console.log(mode);

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
				//console.log(res);
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
		//console.log(status);

		var link = "labOrderStatusChange_f.php";

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                lab_order_id: lab_order_id,

                status: status,

			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
				//console.log(res);
				//$('.center_block').remove();
				//$('#overlay').hide();

				if(res.result == "success"){
					//$('#data').hide();
					window.location.replace('');
				}else{
				    console.log('error');
					$('#errror').html(res.data);
				}
			}
		});
	}

	//Меняем статус онлайн записи
	function changeOnlineZapisStatus(online_zapis_id, status){
		//console.log(status);

		var link = "changeOnlineZapisStatus_f.php";

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                online_zapis_id:online_zapis_id,

                status: status,

			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
				//console.log(res);
				//$('.center_block').remove();
				//$('#overlay').hide();

				if(res.result == "success"){
					//$('#data').hide();
					window.location.replace('');
				}else{
				    console.log('error');
					$('#errror').html(res.data);
				}
			}
		});
	}

	//Для перехода в добавление нового клиента из записи
	$('#add_client_fio').click(function () {
		var client_fio = document.getElementById("search_client").value;
		if (client_fio != ''){
			window.location.replace('client_add.php?fio='+document.getElementById("search_client").value);
		}else{
			window.location.replace('client_add.php');
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
                        //console.log('client.php?id='+id);
                    }, 100);
                }
            })
        }
    }
	//перемещение выбранных позиций прайса в группу
    function moveCheckedItems (){
		//console.log(880);

        var group = document.getElementById("group").value;
        //console.log(group);

        var rys = false;

        var rys = confirm("Вы хотите переместить выбранные позиции в группу. \n\nВы уверены?");
		//console.log(885);

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
                        //console.log('client.php?id='+id);
                    }, 100);
                }
            })
        }
    }

	//Показать меню для перемещение выбранных позиций прайса в группу
    function showMoveCheckedItems (){

        //console.log(mode);
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

    //Подгрузка записи с сайта при каждой загрузке страницы и остальное
	$(document).ready(function() {

        //Tree
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


        //Тест контекстного меню
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
                //console.log(1);
                contextMenuShow(0, 0, event, 'insure');
            }
        });
        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
        $("#insure_approve").click(function(event) {

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                //console.log(1);
                contextMenuShow(0, 0, event, 'insure_approve');
            }
        });
        //Скидки Вешаем слушатель события нажатие кнопок мыши для всего документа:
        $("#discounts").click(function(event) {

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                //console.log(71);
                contextMenuShow(0, 0, event, 'discounts');
            }
        });
        //для категорий процентов
		/*$("#percent_cats").click(function(event) {

		 // Проверяем нажата ли именно правая кнопка мыши:
		 if (event.which === 1)  {
		 //console.log(71);
		 contextMenuShow(0, 0, event, 'percent_cats');
		 }
		 });*/
        //Для прикрепления к филиалу
        $(".change_filial").click(function(event) {

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                //console.log(71);
                contextMenuShow(0, 0, event, 'change_filial');
            }
        });
        //Для отображения списка молочных зубов
        $('#teeth_moloch').click(function(event) {

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                //console.log(71);
                contextMenuShow(0, 0, event, 'teeth_moloch');
            }
        });
        //Для отображения меню изменения статуса
        $('#lab_order_status').click(function(event) {

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                //console.log(71);

                var lab_order_id = document.getElementById("lab_order_id").value;
                var status_now = document.getElementById("status_now").value;
                //console.log(status_now);

                contextMenuShow(lab_order_id, status_now, event, 'lab_order_status');
            }
        });




		//типы пользователей
		//var types = [5, 6, 10];

		//for (var key in types) {

			//Надо же хоть что-то передать...
            var reqData = {
                type: 5,
            }

            //Запрос к базе онлайн записи и выгрузка
            $.ajax({
                url:"get_zapis2.php",
                global: false,
                type: "POST",
                dataType: "JSON",

                data:reqData,

                cache: false,
                beforeSend: function() {
                },
                success:function(res){
                    //console.log(res);

                    if(res.result == 'success'){
                    	if (res.data > 0) {
                            $(".have_new-zapis").show();
                            $(".have_new-zapis").html(res.data);
                        }
                    }else{

                    }
                }
            });


            //Запрос есть ли новые объявления
            $.ajax({
                url:"get_topic2.php",
                global: false,
                type: "POST",
                dataType: "JSON",

                data:reqData,

                cache: false,
                beforeSend: function() {
                },
                success:function(res){
                    //console.log(res);

                    if(res.result == 'success'){
                    	//console.log(res);

                    	if (res.data > 0) {
                            //console.log(res.data);

                            $(".have_new-topic").show();
                            $(".have_new-topic").html(res.data);
                        }
                    }else{

                    }
                }
            });

             //Запрос есть ли новые тикеты
            $.ajax({
                url:"get_ticket2.php",
                global: false,
                type: "POST",
                dataType: "JSON",

                data:reqData,

                cache: false,
                beforeSend: function() {
                },
                success:function(res){
                    //console.log(res);

                    if(res.result == 'success'){
                    	//console.log(res);

                    	if (res.data > 0) {
                            //console.log(res.data);

                            $(".have_new-ticket").show();
                            $(".have_new-ticket").html(res.data);
                        }
                    }else{

                    }
                }
            });

        //}
	});

	//Для фильтра в косметологии для подсчета элементов
    $('input.filterInCosmet').keyup(function() {
    	count = 0;
    	$('.cosmBlock').each(function() {
			if ($(this).css('display') != 'none'){
				count++;
            }
        });
    	//console.log(count);
        $('.countCosmBlocks').html(count);
	});

	//Закрываем тикет
    function Ajax_ticket_done(id, workers_exist){
        //console.log(id);

        var link = "ajax_ticket_done.php";

		var certData = {
			ticket_id: id,
            workers_exist: workers_exist,
            last_comment: $("#ticket_last_comment").val()
		};

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data: certData,

			cache: false,
			beforeSend: function () {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function (res) {
				//console.log(res);

				if (res.result == "success") {
					location.reload();
				}
			}
		});
    }

    //Возвращаем тикет в работу
    function Ajax_ticket_restore(id){
        var rys = false;

        var rys = confirm("Вы cобираетесь вернуть тикет в работу. \n\nВы уверены?");

        if (rys){

            var link = "ajax_ticket_restore.php";

            var certData = {
                ticket_id: id
            };

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: certData,

                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    }
                }
            })
        }
	}

    //Удаляем тикет
    function Ajax_delete_ticket(id){
        //console.log(id);

        var link = "ajax_ticket_delete.php";

        var certData = {
            ticket_id: id,
            last_comment: $("#ticket_last_comment").val()
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: certData,

            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function (res) {
                //console.log(res);

                if (res.result == "success") {
                    location.reload();
                }
            }
        });
    }

    //Разблокировка тикета
    function Ajax_reopen_ticket(id) {

    	var link = "ticket_reopen_f.php";

        var certData = {
            ticket_id: id
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: certData,

            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function (res) {
                //console.log(res);

                if (res.result == "success") {
                    location.reload();
                }
            }
        });
    }

    //Получим логи для тикета
    function getLogForTicket(id) {

        var reqData = {
            ticket_id: id,
        }

        //Запрос к базе и получение лога и вывод
        $.ajax({
            url:"ticket_get_log_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data:reqData,

            cache: false,
            beforeSend: function() {
            },
            success:function(res){
                //console.log(res);

                if(res.result == 'success'){
					$("#ticket_change_log").html(res.data);
                }else{

                }
            }
        });
    }

    //Получим коменты для тикета
    function getCommentsForTicket(id) {
        var reqData = {
            ticket_id: id
        }

        //Запрос к базе и получение лога и вывод
        $.ajax({
            url:"ticket_get_comments_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data:reqData,

            cache: false,
            beforeSend: function() {
            },
            success:function(res){
                //console.log(res);

                if(res.result == 'success'){

                    $("#ticket_comments").html(res.data);

                    //скролл !!! scroll
                    //document.querySelector('#ticket_comments').scrollTop = document.querySelector('#ticket_comments').scrollHeight;
                    var height= $("#ticket_comments").height();
                    //console.log(height);
                    $("#chat").animate({"scrollTop":height}, 100);
                    /*$("#chat").animate({scrollTop: 0}, 100);*/
                }else{

                }
            }
        });
    }

	//Добавляем новый коммент в тикет
    function Add_newComment_inTicket(id) {
        var reqData = {
            ticket_id: id,
            descr: $("#msg_input").html()
        };
        //console.log($("#msg_input").html());

        //Запрос к базе и получение лога и вывод
        $.ajax({
            url:"ticket_add_comments_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data:reqData,

            cache: false,
            beforeSend: function() {
            },
            success:function(res){
                //console.log(res);
                //$("#ticket_change_log").html(res);

                if(res.result == 'success'){
                    $("#msg_input").html(res.data);
                    getCommentsForTicket(id);
                }else{

                }
            }
        });
    }

	//Прочитали все тикеты
    function iReadAllOfTickts(worker_id) {
        var rys = false;

        var rys = confirm("Пометить все тикеты как прочитаные?");

        if (rys) {

            var reqData = {
                worker_id: worker_id
            };
            //console.log($("#msg_input").html());

            //Запрос к базе и получение лога и вывод
            $.ajax({
                url: "ticket_i_read_all_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",

                data: reqData,

                cache: false,
                beforeSend: function () {
                },
                success: function (res) {
                    //console.log(res);
                    //$("#data").html(res.data);

                    if (res.result == 'success') {
                     //location.reload();
                    } else {

                    }
                }
            });
        }
    }

    //Открываем модальные окна (закрываем тикет)
    $('.open_modal_ticket_done, .open_modal_ticket_delete').live('click', function(event){
        event.preventDefault(); // вырубаем стандартное поведение
        var div = $(this).attr('href'); // возьмем строку с селектором у кликнутой ссылки

		if ($("#workers_exist").val() != 'true'){
            $("#workers_exist_warn").html('Так как задаче не назначены исполнители, назначены будете вы.');
		}

        $('#overlay').fadeIn(400, //показываем оверлэй
            function(){ // после окончания показывания оверлэя
                $(div) // берем строку с селектором и делаем из нее jquery объект
                    .css('display', 'block')
                    .animate({opacity: 1, top: '50%'}, 200); // плавно показываем
            });
    });

    //Открываем модальные окна (удаляем тикет)
    /*$('.open_modal_ticket_delete').live('click', function(event){
        event.preventDefault(); // вырубаем стандартное поведение
        var div = $(this).attr('href'); // возьмем строку с селектором у кликнутой ссылки

		if ($("#workers_exist").val() != 'true'){
            $("#workers_exist_warn").html('Так как задаче не назначены исполнители, назначены будете вы.');
		}

        $('#overlay').fadeIn(400, //показываем оверлэй
            function(){ // после окончания показывания оверлэя
                $(div) // берем строку с селектором и делаем из нее jquery объект
                    .css('display', 'block')
                    .animate({opacity: 1, top: '50%'}, 200); // плавно показываем
            });
    });*/

    //скрываем модальные окна
    $("#modal1, #modal2, #modal_ticket_done, #modal_ticket_delete") // все модальные окна
        .animate({opacity: 0, top: '45%'}, 50, // плавно прячем
    function(){ // после этого
        $(this).css('display', 'none');
        $('#overlay').fadeOut(50); // прячем подложку
    }
    );

    //Закрыть модальные окна
    $('.modal_close, #overlay').click( function(){ // ловим клик по крестику или оверлэю
        $("#modal1, #modal2, #modal_ticket_done, #modal_ticket_delete") // все модальные окна
            .animate({opacity: 0, top: '45%'}, 200, // плавно прячем
                function(){ // после этого
                    $(this).css('display', 'none');
                    $('#overlay').fadeOut(400); // прячем подложку
                }
            );
    });

