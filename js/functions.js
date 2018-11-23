	//Размер объекта
	Object.size = function(obj) {
		var size = 0, key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) size++;
		}
		return size;
	};

	function hideAllErrors (){
        // убираем класс ошибок с инпутов
        $('input').each(function(){
            $(this).removeClass('error_input');
        });

        // прячем текст ошибок
        $('.error').hide();
        $('#errror').html('');
	}

	//Форматирование числа в красивый вид
    function number_format( number, decimals = 0, dec_point = '.', thousands_sep = ',' ) {

        let sign = number < 0 ? '-' : '';

        let s_number = Math.abs(parseInt(number = (+number || 0).toFixed(decimals))) + "";
        let len = s_number.length;
        let tchunk = len > 3 ? len % 3 : 0;

        let ch_first = (tchunk ? s_number.substr(0, tchunk) + thousands_sep : '');
        let ch_rest = s_number.substr(tchunk)
            .replace(/(\d\d\d)(?=\d)/g, '$1' + thousands_sep);
        let ch_last = decimals ?
            dec_point + (Math.abs(number) - s_number)
                .toFixed(decimals)
                .slice(2) :
            '';

        return sign + ch_first + ch_rest + ch_last;
    }

    //Для поиска сертификата из модального окна
    $('#search_cert').bind("change keyup input click", function() {

        //var $this = $(this);
        var val = $(this).val();
        //console.log(val);

        if (val.length > 1){
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

    //Блок с прогрессом ожидания
    function blockWhileWaiting (show){
    	if (show){
            $('#overlay').show();

            $('#overlay').append( "<div id='waiting' style='padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 1);'><img src='img/wait.gif' style='float:left;'><span class='loadingMessage' style='font-size: 90%;'> обработка...</span></div>" );
            //$('#waiting').html("");
		}else {
            $('#overlay').html('');
            $('#overlay').hide();
        }
	}

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
				key: key
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
                        //'<input type="text" name="changePriceItem" id="changePriceItem" class="form-control" value="'+Number(target.html())+'" onkeyup="changePriceItem(this.val(), '+start_price+');">'+
						'<div style="display: inline;" onclick="priceItemInvoice('+ind+', '+key+', $(\'#changePriceItem\').val(), '+start_price+')">Ok</div>'+
						'</li>';

				}
				//Регуляция конечной цены
				if (mark == 'priceItemItog'){

				    var itog_price = Number(target.html());
				    var manual_itog_price = Number(target.attr("manual_itog_price"));

                    manual_itog_price = itog_price;

					var min_itog_price = manual_itog_price - 10;
					var max_itog_price = manual_itog_price + 2;

					if (min_itog_price < 1) min_itog_price = 1;


					res.data =
                        '<li style="font-size: 10px;">'+
                        'Введите цену (от '+min_itog_price+' до '+max_itog_price+')'+
                        '</li>'+
						'<li>'+
                        '<input type="number" name="changePriceItogItem" id="changePriceItogItem" class="form-control" size="3" min="'+min_itog_price+'"  max="'+max_itog_price+'" value="'+itog_price+'" class="mod">'+
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
                 $("#status").html(data);
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
				fname: $("#f").val(),
				iname: $("#i").val(),
				oname: $("#o").val(),

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
							f:  $("#f").val(),
							i:  $("#i").val(),
							o:  $("#o").val(),

							fo:  $("#fo").val(),
							io:  $("#io").val(),
							oo:  $("#oo").val(),

							comment: $("#comment").val(),

							card: $("#card").val(),

							therapist: $("#search_client2").val(),
							therapist2: $("#search_client4").val(),

							sel_date: $("#sel_date").val(),
							sel_month: $("#sel_month").val(),
							sel_year: $("#sel_year").val(),

							telephone: $("#telephone").val(),
							htelephone: $("#htelephone").val(),

							telephoneo: $("#telephoneo").val(),
							htelephoneo: $("#htelephoneo").val(),

							passport: $("#passport").val(),
							passportvidandata: $("#passportvidandata").val(),
							passportvidankem: $("#passportvidankem").val(),

							alienpassportser: $("#alienpassportser").val(),
							alienpassportnom: $("#alienpassportnom").val(),

							address: $("#address").val(),

							polis: $("#polis").val(),
							polisdata: $("#polisdata").val(),
							insurecompany: $("#insurecompany").val(),

							sex:sex_value,

							session_id: session_id,
						},
						success:function(data){
							$("#errrror").html(data);
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
					 $("#errror").html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>')
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
							id: $("#id").val(),

							fo:  $("#fo").val(),
							io:  $("#io").val(),
							oo:  $("#oo").val(),

							comment: $("#comment").val(),

							card: $("#card").val(),

							therapist: $("#search_client2").val(),
							therapist2: $("#search_client4").val(),

							sel_date: $("#sel_date").val(),
							sel_month: $("#sel_month").val(),
							sel_year: $("#sel_year").val(),

							telephone: $("#telephone").val(),
							htelephone: $("#htelephone").val(),

							telephoneo: $("#telephoneo").val(),
							htelephoneo: $("#htelephoneo").val(),

							passport: $("#passport").val(),
							passportvidandata: $("#passportvidandata").val(),
							passportvidankem: $("#passportvidankem").val(),

							alienpassportser: $("#alienpassportser").val(),
							alienpassportnom: $("#alienpassportnom").val(),

							address: $("#address").val(),

							polis: $("#polis").val(),
							polisdata: $("#polisdata").val(),
							insurecompany: $("#insurecompany").val(),

							sex:sex_value,

							session_id: session_id
						},
						success:function(data){
							$("#errrror").html(data);
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
					 $("#errror").html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>')
				}
			}
		});
	};

	function Ajax_del_client(session_id) {
		var id =  $("#id").val();

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
				 $("#errrror").html(data);
				setTimeout(function () {
					window.location.replace('client.php?id='+id);
					//console.log('client.php?id='+id);
				}, 100);
			}
		})
	};

	function Ajax_del_pricelistgroup(id, session_id) {

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
				 $("#errrror").html(data);
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
				 $("#errrror").html(data);
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
				 $("#errrror").html(data);
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
                // $("#errrror").html(data);
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
				group:  $("#group").val()
			},
			success:function(data){
				 $("#errrror").html(data);
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
				id2:  $("#insurecompany").val()
			},
			success:function(data){
				 $("#errrror").html(data);
			}
		})
	}

	//Очистить прайс
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
				 $("#errrror").html(data);
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
				id: id
			},
			success:function(data){
				 $("#errrror").html(data);
				setTimeout(function () {
					window.location.replace('insure.php?id='+id);
					//console.log('client.php?id='+id);
				}, 100);
			}
		})
	}

	//Удаление блокировка лаборатории
	function Ajax_del_labor(id) {

		ajax({
			url:"labor_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id
			},
			success:function(data){
				 $("#errrror").html(data);
				setTimeout(function () {
					window.location.replace('labor.php?id='+id);
					//console.log('client.php?id='+id);
				}, 100);
			}
		})
	}

	//Удаление блокировка сертификата
	function Ajax_del_cert(id) {

        $.ajax({
			url:"cert_del_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
			data:
			{
				id: id
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
                     $("#errrror").html(res.data);
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
                     $("#errrror").html(res.data);
                    setTimeout(function () {
                        window.location.replace('invoice.php?id=' + id);
                        //console.log('client.php?id='+id);
                    }, 100);
                }else{
                    //console.log(2);
                     $("#errrror").html(res.data);
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

	//Редактирование времени наряда
	function Ajax_invoice_close_time_edit(invoice_id) {

        $.ajax({
			url:"invoice_close_time_edit_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
			data:
			{
                invoice_id: invoice_id,
				new_time: $("#datanew").val()
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
                        window.location.replace('invoice.php?id=' + invoice_id);
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
				$("#errrror").html(data);
				/*setTimeout(function () {
					window.location.replace('order.php?id='+id);
					//console.log('client.php?id='+id);
				}, 4000);*/
			}
		})
	};

	//Удаление блокировка ордера по-любому
	function Ajax_del_order_anyway(id, client_id) {

        var rys = false;

        rys = confirm("Вы собираетесь удалить ордер задним числом. \nЭто невозможно будет исправить \n\nВы уверены?");

        if (rys) {

            $.ajax({
                url: "order_del_anyway_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    client_id: client_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success: function (res) {
                	//console.log(data);
                    $("#errrror").html(res.data);
                    if (res.result == 'success') {
                        setTimeout(function () {
                            window.location.replace('order.php?id=' + id);
                            //console.log('client.php?id='+id);
                        }, 2000);
                    }
                }
            })
        }
	};

	function Ajax_reopen_client(session_id, id) {

		ajax({
			url:"client_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
				session_id: session_id
			},
			success:function(data){
				// $("#errrror").html(data);
				setTimeout(function () {
					window.location.replace('client.php?id='+id);
					//console.log('client.php?id='+id);
				}, 100);
			}
		})
	}

	function Ajax_reopen_pricelistitem(id) {

		ajax({
			url:"pricelistitem_reopen_f.php",
			method:"POST",
			data:
			{
				id: id
			},
			success:function(data){
				// $("#errrror").html(data);
				setTimeout(function () {
					window.location.replace('pricelistitem.php?id='+id);
					//console.log('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	}

	//разблокировка страховой
	function Ajax_reopen_insure(id) {

		ajax({
			url:"insure_reopen_f.php",
			method:"POST",
			data:
			{
				id: id
			},
			success:function(data){
				// $("#errrror").html(data);
				setTimeout(function () {
					window.location.replace('insure.php?id='+id);
					//console.log('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	}

	//разблокировка лаборатории
	function Ajax_reopen_labor(id) {

		ajax({
			url:"labor_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
			},
			success:function(data){
				// $("#errrror").html(data);
				setTimeout(function () {
					window.location.replace('labor.php?id='+id);
					//console.log('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	}

	//разблокировка сертификата
	function Ajax_reopen_cert(id) {

		ajax({
			url:"cert_reopen_f.php",
			method:"POST",
			data:
			{
				id: id
			},
			success:function(data){
				// $("#errrror").html(data);
				setTimeout(function () {
					window.location.replace('certificate.php?id='+id);
					//console.log('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	}

	//разблокировка наряда
	function Ajax_reopen_invoice(id, client_id) {

		ajax({
			url:"invoice_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
                client_id: client_id,
			},
			success:function(data){
				// $("#errrror").html(data);
				setTimeout(function () {
					window.location.replace('invoice.php?id='+id);
					//console.log('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	};
	//разблокировка ордера
	function Ajax_reopen_order(id, client_id) {

		ajax({
			url:"order_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
                client_id: client_id,
			},
			success:function(data){
				// $("#errrror").html(data);
				setTimeout(function () {
					window.location.replace('order.php?id='+id);
					//console.log('pricelistitem.php?id='+id);
				}, 100);
			}
		})
	};

	function Ajax_reopen_pricelistgroup(session_id, id) {

		ajax({
			url:"pricelistgroup_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
				session_id: session_id,
			},
			success:function(data){
				// $("#errrror").html(data);
				setTimeout(function () {
					window.location.replace('pricelistgroup.php?id='+id);
					//console.log('pricelistgroup.php?id='+id);
				}, 100);
			}
		})
	};
	//Перемещения косметологии другому пациенту
	function Ajax_cosm_move(session_id, id) {

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

		var name =  $("#search_client").val();

		var rys = false;

		rys = confirm("Вы хотите перенести записи другому пациенту. \nЭто невозможно будет исправить \n\nВы уверены?");

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

        var name =  $("#search_client").val();

		var rys = false;

		rys = confirm("Вы хотите перенести запись другому пациенту. \nЭто невозможно будет исправить \n\nВы уверены?");

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
				fname: $("#f").val(),
				iname: $("#i").val(),
				oname: $("#o").val()
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
							id: $("#id").val(),

							f: $("#f").val(),
							i: $("#i").val(),
							o: $("#o").val(),
						},
						success:function(data){ $("#errrror").html(data);}
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
					 $("#errror").html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>')
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
				fname: $("#f").val(),
				iname: $("#i").val(),
				oname: $("#o").val()
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
							id: $("#id").val(),

							f: $("#f").val(),
							i: $("#i").val(),
							o: $("#o").val(),
						},
						success:function(data){ $("#errrror").html(data);}
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
					 $("#errror").html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>')
				}
			}
		});
	};

	// !!! правильный пример AJAX
	function Ajax_add_insure(session_id) {

		var name =  $("#name").val();
		var contract =  $("#contract").val();
		var contacts =  $("#contacts").val();

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

		var name =  $("#name").val();
		var contract =  $("#contract").val();
		var contacts =  $("#contacts").val();

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

		var name =  $("#name").val();
		var contract =  $("#contract").val();
		var contract2 =  $("#contract2").val();
		var contacts =  $("#contacts").val();

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
							contract2:contract2,
							contacts:contacts,
						},
						success:function(data){ $("#errrror").html(data);}
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
					 $("#errrror").html('<div class="query_neok">Ошибка, что-то заполнено не так.</div>')
				}
			}
		});
	};


	function Ajax_edit_labor(id) {
		// убираем класс ошибок с инпутов
		$hideAllErrors ();

		var name =  $("#name").val();
		var contract =  $("#contract").val();
		var contacts =  $("#contacts").val();

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
						success:function(data){ $("#errrror").html(data);}
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
					 $("#errrror").html('<div class="query_neok">Ошибка, что-то заполнено не так.</div>')
				}
			}
		});
	};


	// !!! правильный пример AJAX
	function Ajax_add_priceitem(session_id) {

		var pricename = $("#pricename").val();
		var category_id = $("#category_id").val();
		var pricecode = $("#pricecode").val();
		var price = $("#price").val();
		var price2 = $("#price2").val();
		var price3 = $("#price3").val();
		var group = $("#group").val();
		var iWantThisDate2 = $("#iWantThisDate2").val();

		$.ajax({
			url:"add_priceitem_f.php",
			global: false,
			type: "POST",
			data:
			{
				pricename:pricename,
                category_id:category_id,
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

		var pricename =  $("#pricename").val();
		var price =  $("#price").val();
		var group =  $("#group").val();
		var iWantThisDate2 =  $("#iWantThisDate2").val();

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

		var groupname =  $("#groupname").val();
		var group =  $("#group").val();

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

		var pricelistitemname = $("#pricelistitemname").val();
		var pricelistitemcode = $("#pricelistitemcode").val();
		var group = $("#group").val();
		var category_id = $("#category_id").val();

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
                category_id:category_id,
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

		var pricelistgroupname =  $("#pricelistgroupname").val();
		var group =  $("#group").val();

		$.ajax({
			url:"pricelistgroup_edit_f.php",
			global: false,
			type: "POST",
			data:
			{
				pricelistgroupname:pricelistgroupname,
				session_id:session_id,
				group:group,
				id: id
			},
			cache: false,
			beforeSend: function() {
				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(data){
				$('#errror').html(data);
			}
		})
	}

	function Ajax_edit_price(id, session_id) {

		var price =  $("#price").val();
		var price2 =  $("#price2").val();
		var price3 =  $("#price3").val();
		var iWantThisDate2 =  $("#iWantThisDate2").val();

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
				id: id
			},
			cache: false,
			beforeSend: function() {
				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(data){
				$('#errror').html(data);
			}
		})
	}

	function Ajax_edit_price_insure(id, insure) {

		var price =  $("#price").val();
		var price2 =  $("#price2").val();
		var price3 =  $("#price3").val();
		var iWantThisDate2 =  $("#iWantThisDate2").val();

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
				insure: insure
			},
			cache: false,
			beforeSend: function() {
				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(data){
				$('#errror').html(data);
			}
		})
	}

	//Удаляем позицию в истории цен
    function deletePriceHistory(id) {

        var Data = {
            id:id
        };

        var link = "deletePriceHistory.php";

        var rys = false;

        rys = confirm("Вы собираетесь удалить промежуточную цену.\n\nВы уверены?");

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",

                data:Data,

                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        //console.log(res);
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

	// !!! правильный пример AJAX
	function Ajax_change_shed() {

		var day = $("#SelectDayShedOptions").val();
		var month = $("#SelectMonthShedOptions").val();
		var year = $("#SelectYearShedOptions").val();

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
				ignoreshed:ignoreshed
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

        blockWhileWaiting (true);

		var iWantThisMonth =  $("#iWantThisMonth").val();
		var iWantThisYear =  $("#iWantThisYear").val();

		window.location.replace(path+'&m='+iWantThisMonth+'&y='+iWantThisYear);
	}

	function iWantThisDate2(path){

        blockWhileWaiting (true);

		var iWantThisDate2 =  $("#iWantThisDate2").val();
		var ThisDate = iWantThisDate2.split('.');

		window.location.replace(path+'&d='+ThisDate[0]+'&m='+ThisDate[1]+'&y='+ThisDate[2]);
	}

	//переход к прайсу страховой
    function iWantThisInsurePrice(){
        var insure_id =  $("#insurecompany").val();
		if (insure_id != 0){
            window.location.replace('insure_price.php?id='+insure_id);
		}
    }

    //Функция пунта управления
	function manageScheduler(doc_name){
    	//console.log(doc_name);

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

        var link = "ajax_add_some_settings_in_session.php";

        var reqData = {
            manage: iCanManage,
            doc_name: doc_name
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
            	console.log(res);

            }
        })
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
				datastart: $("#datastart").val(),
				dataend: $("#dataend").val(),

				all_age:all_age,
				agestart: $("#agestart").val(),
				ageend: $("#ageend").val(),

				worker: $("#search_worker").val(),
				filial: $("#filial").val(),

				pervich:document.querySelector('input[name="pervich"]:checked').value,
				insured:document.querySelector('input[name="insured"]:checked').value,
				noch:document.querySelector('input[name="noch"]:checked').value,

				sex:document.querySelector('input[name="sex"]:checked').value,
				wo_sex:wo_sex,

                age:document.querySelector('input[name="age"]:checked').value,
				wo_age:wo_age

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
                    datastart:  $("#datastart").val(),
                    dataend:  $("#dataend").val(),

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

    //Выборка отчёта по записи
    function Ajax_show_result_main_report_zapis(){

        blockWhileWaiting (true);

        var link = "ajax_show_result_main_report_zapis_f.php";

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

        var reqData = {
            //all_time: all_time,
            all_time: 0,
            datastart:  $("#datastart").val(),
            dataend:  $("#dataend").val(),

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
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                $('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                //console.log(res);

                if (res.result == "success") {
                    //console.log(res.query);
                    //console.log(res.data);

                    $('#qresult').html('Всего: ' + res.data.length + '<br>' +
					'Первичных: <span id="res_pervich">0</span><br>' +
					'Ночных: <span id="res_noch">0</span><br>' +
					'Страховых: <span id="res_insured">0</span><br>' +
					'<span id="res_temp"></span><br>' +
					'');

                    var pervich = 0;
                    var noch = 0;
                    var insured = 0;

                    var noch_pervich = 0;
                    var noch_insured = 0;
                    var insured_pervich = 0;

                    //массив пациентов
                    var clients_arr = [];

                    res.data.forEach(function(element) {

                        //showZapisRezult2($journal, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, 0, true, false, $dop);

						//Вывод на экран
                        /*link = "showZapisRezult3.php";

                        reqData = {
                            data: element
						};

                        $.ajax({
                            url: link,
                            global: false,
                            type: "POST",
                            //dataType: "JSON",
                            data: reqData,
                            cache: false,
                            //async: false,
                            beforeSend: function() {
                                //$('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                            },
                            success:function(res){
                            	//console.log(res);

                                $('#res_temp').append(res);
                            }
                        });*/

                        //$('#qresult').append(element.id + '<br>');

                        if (element.pervich == 1) {
                            pervich++;
                        }
						if (element.noch == 1){
							noch++

							if (element.pervich == 1){
                                noch_pervich++;
							}
							if (element.insured == 1){
                                noch_insured++;
							}
						}
						if (element.insured == 1) {
                            insured++;

                            if (element.pervich == 1){
                                insured_pervich++;
                            }
                        }
                        //console.log(element.patient);

                        //Хочу собрать массив пациентов
                        //console.log(clients_arr.indexOf(element.patient));

                        if (clients_arr.indexOf(element.patient) == -1) {
                            clients_arr.push(element.patient);
                        }else{

                        }

                    });
                    console.log(clients_arr.length);

                    $('#res_pervich').html(pervich);
                    $('#res_noch').html(noch);
                    $('#res_insured').html(insured);

                    if (noch_pervich != 0){
                        $('#res_noch').append('. Из них первичные: ' + noch_pervich);
					}
                    if (noch_insured != 0){
                        $('#res_noch').append('. Из них страховые: ' + noch_insured);
					}
                    if (insured_pervich != 0){
                        $('#res_insured').append('. Из них первичные: ' + insured_pervich);
					}

                    //console.log('Done');

                    blockWhileWaiting (false);

                    //$('#qresult').html('Ok');

                } else {
                    $('#qresult').html(res.data);

                    blockWhileWaiting (false);
                }


            }
        })
    }

    //Выборка отчёта по категориям
    function Ajax_show_result_main_report_category(){

        blockWhileWaiting (true);

        var link = "ajax_show_result_main_report_category_f.php";

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

        var reqData = {
            //all_time: all_time,
            all_time: 0,
            datastart:  $("#datastart").val(),
            dataend:  $("#dataend").val(),

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
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            //dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                $('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                //console.log(res);
                $('#qresult').html(res);

                /*if (res.result == "success") {
                    //console.log(res.query);
                    //console.log(res.data);

                    $('#qresult').html('Всего: ' + res.data.length + '<br>' +
					'Первичных: <span id="res_pervich">0</span><br>' +
					'Ночных: <span id="res_noch">0</span><br>' +
					'Страховых: <span id="res_insured">0</span><br>' +
					'<span id="res_temp"></span><br>' +
					'');

                    var pervich = 0;
                    var noch = 0;
                    var insured = 0;

                    var noch_pervich = 0;
                    var noch_insured = 0;
                    var insured_pervich = 0;

                    //массив пациентов
                    var clients_arr = [];

                    res.data.forEach(function(element) {*/

                        //showZapisRezult2($journal, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, 0, true, false, $dop);

						//Вывод на экран
                        /*link = "showZapisRezult3.php";

                        reqData = {
                            data: element
						};

                        $.ajax({
                            url: link,
                            global: false,
                            type: "POST",
                            //dataType: "JSON",
                            data: reqData,
                            cache: false,
                            //async: false,
                            beforeSend: function() {
                                //$('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                            },
                            success:function(res){
                            	//console.log(res);

                                $('#res_temp').append(res);
                            }
                        });*/

                        //$('#qresult').append(element.id + '<br>');

                /*        if (element.pervich == 1) {
                            pervich++;
                        }
						if (element.noch == 1){
							noch++

							if (element.pervich == 1){
                                noch_pervich++;
							}
							if (element.insured == 1){
                                noch_insured++;
							}
						}
						if (element.insured == 1) {
                            insured++;

                            if (element.pervich == 1){
                                insured_pervich++;
                            }
                        }*/
                        //console.log(element.patient);

                        //Хочу собрать массив пациентов
                        //console.log(clients_arr.indexOf(element.patient));

                /*        if (clients_arr.indexOf(element.patient) == -1) {
                            clients_arr.push(element.patient);
                        }else{

                        }

                    });
                    console.log(clients_arr.length);

                    $('#res_pervich').html(pervich);
                    $('#res_noch').html(noch);
                    $('#res_insured').html(insured);

                    if (noch_pervich != 0){
                        $('#res_noch').append('. Из них первичные: ' + noch_pervich);
					}
                    if (noch_insured != 0){
                        $('#res_noch').append('. Из них страховые: ' + noch_insured);
					}
                    if (insured_pervich != 0){
                        $('#res_insured').append('. Из них первичные: ' + insured_pervich);
					}*/

                    //console.log('Done');

                    blockWhileWaiting (false);



                    //Показываем график
                	showChart ();

                    //$('#qresult').html('Ok');

                /*} else {
                    $('#qresult').html(res.data);

                    blockWhileWaiting (false);
                }
*/

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
                    datastart:  $("#datastart").val(),
                    dataend:  $("#dataend").val(),

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
                    invoiceInsure: invoiceInsure

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

        var insureTrue = $("input[id=insureTrue]:checked").val();
        if (insureTrue === undefined){
            insureTrue = 0;
        }

        $.ajax({
            url:"ajax_show_result_stat_invoice_f.php",
            global: false,
            type: "POST",
            data:
                {
                    all_time:all_time,
                    datastart:  $("#datastart").val(),
                    dataend:  $("#dataend").val(),

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
                    insureTrue: insureTrue

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
                    datastart:  $("#datastart").val(),
                    dataend:  $("#dataend").val(),

                    //worker: $("#search_worker").val(),
                    insure:  $("#insure_sel").val(),
                    filial:  $("#filial").val(),

                    zapisAll: zapisAll,
                    zapisArrive: zapisArrive,
                    zapisNotArrive: zapisNotArrive,
                    zapisError: zapisError,
                    zapisNull: zapisNull

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

        $.ajax({
            url:"ajax_repare_insure_xls_f.php",
            global: false,
            type: "POST",
            data:
                {
                    all_time:all_time,
                    showError:showError,
                    datastart:  $("#datastart").val(),
                    dataend:  $("#dataend").val(),

                    //worker: $("#search_worker").val(),
                    insure:  $("#insure_sel").val(),
                    filial:  $("#filial").val(),

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
				datastart: $("#datastart").val(),
				dataend: $("#dataend").val(),

				all_age:all_age,
				agestart: $("#agestart").val(),
				ageend: $("#ageend").val(),

				worker: $("#search_worker").val(),
				filial: $("#filial").val(),

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
        //console.log(select);

		var result = [];
		var options = select && select.options;
        //console.log(options);

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
		var el_effect =  document.getElementById("multi_d_to_2");
        //console.log(el_effect);

		condition = getSelectValues(el_condition);
		//console.log(condition);
		effect = getSelectValues(el_effect);
        //console.log(effect);

		$.ajax({
			url:"ajax_show_result_stat_cosm_ex2_f.php",
			global: false,
			type: "POST",
			data:
			{
				all_time: all_time,
				datastart: $("#datastart").val(),
				dataend: $("#dataend").val(),

				all_age: all_age,
				agestart: $("#agestart").val(),
				ageend: $("#ageend").val(),

				worker: $("#search_worker").val(),
				filial: $("#filial").val(),

				//pervich:document.querySelector('input[name="pervich"]:checked').value,

				condition: condition,
				effect: effect,

				sex: document.querySelector('input[name="sex"]:checked').value,
				wo_sex: wo_sex

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
				datastart: $("#datastart").val(),
				dataend: $("#dataend").val(),

				worker: $("#search_worker").val(),
				filial:99,

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
				datastart: $("#datastart").val(),
				dataend: $("#dataend").val(),

				filial:99,

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

    //Долги авансы новые
	function Ajax_show_result_stat_client_finance2(){

		var reqData = {
            //Кто создал запись
            creator:$("#search_worker").val(),
            //Пациент
            client:$("#search_client").val(),
            //К кому запись
            worker:$("#search_client4").val(),
            filial:$("#filial").val(),
		};
		//console.log($("#msg_input").html());

		//Запрос к базе и получение лога и вывод
		$.ajax({
			url:"ajax_show_result_stat_client_finance2.php",
			global: false,
			type: "POST",
			//dataType: "JSON",

			data:reqData,

			cache: false,
			beforeSend: function() {
                $('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			success:function(res){
            	$('#qresult').html(res);
			}
		});

	}

	$('#showDiv1').click(function () {
		$('#div1').stop(true, true).slideToggle('slow');
		$('#div2').slideUp('slow');
	});
	$('#showDiv2').click(function () {
		$('#div2').stop(true, true).slideToggle('slow');
		$('#div1').slideUp('slow');
	});

	$('#toggleDiv1').click(function () {
		$('#div1').stop(true, true).slideToggle('slow');

	});
	$('#toggleDiv2').click(function () {
		$('#div2').stop(true, true).slideToggle('slow');
	});
	$('#toggleDiv3').click(function () {
		$('#div3').stop(true, true).slideToggle('slow');
	});


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
				summ: $("#summ").val()
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

					var type =  $("#type").val();

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
							summ: $("#summ").val(),
							type:type,

							date_expires: $("#dataend").val(),

							comment: $("#comment").val(),

							session_id: session_id
						},
						cache: false,
						beforeSend: function() {
							$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
						},
						success:function(data){
							 $("#status").html(data);
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
					 $("#errror").html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>')
				}
			}
		});
	}

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
				summ: $("#summ").val()
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
							summ:  $("#summ").val(),
							date_expires: $("#dataend").val(),
							comment:  $("#comment").val(),
							session_id: session_id
						},
						cache: false,
						beforeSend: function() {
							$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
						},
						success:function(data){ $("#status").html(data);}
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
					 $("#errror").html("<span style='color: red'>Ошибка, что-то заполнено не так.</span>")
				}
			}
		});
	}

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
				summ: $("#summ").val()
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
							comment:  $("#comment").val(),
							summ:  $("#summ").val()
						},
						cache: false,
						beforeSend: function() {
							$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
						},
						success:function(data){ $("#status").html(data);}
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
					 $("#errror").html("<span style='color: red'>Ошибка, что-то заполнено не так.</span>")
				}
			}
		});
	}

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
				summ: $("#summ").val()
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
							comment:  $("#comment").val(),
							summ:  $("#summ").val()

						},
						cache: false,
						beforeSend: function() {
							$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
						},
						success:function(data){ $("#status").html(data);}
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
					 $("#errror").html("<span style='color: red'>Ошибка, что-то заполнено не так.</span>")
				}
			}
		});
	};

	//Добавление записи
	function Ajax_add_TempZapis() {
        // получение данных из полей

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

		var kab =  $("#kab").html();

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
                 $("#Ajax_add_TempZapis").disabled = true;

				$('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			dataType: "json",
			// действие, при ответе с сервера
			success: function(data){
                //Разблокируем кнопку OK
                setTimeout(function () {
                	 $("#Ajax_add_TempZapis").disabled = false;
                }, 200);
				if(data.result == "success"){
					 $("#errror").html(data.data);
					setTimeout(function () {
                        //console.log(window.location.href);

                        //window.location.replace(window_location_href+"#tabs-4");

                        location.reload();
					}, 50);
				}else{
					 $("#errror").html(data.data);
				}
			}
		});
	};

	//Редактирование записи
	function Ajax_edit_TempZapis(type) {

		// получение данных из полей
		//var type =  $("#type").val();

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

		var id =  $("#zapis_id").val();

		var kab =  $("#kab").html();

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
					 $("#errror").html(data.data);
					setTimeout(function () {
						location.reload()
					}, 100);
				}else{
					 $("#errror").html(data.data);
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
				// $("#req").html(data);
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

		//var start_time = Number( $("#start_time").val());
		var change_hours = Number($("#change_hours").val());
		var change_minutes = Number($("#change_minutes").val());

		var day = Number($("#day").val());
		var month = Number($("#month").val());
		var year = Number($("#year").val());

		var filial = Number($("#filial").val());
		var zapis_id = Number($("#zapis_id").val());
		var kab = $("#kab").html();

		//var wt = Number($("#wt").val());
		var wt = change_hours*60+change_minutes;

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

                 $("#Ajax_add_TempZapis").disabled = true;

				next_time_start_rez = res.next_time_start;
				next_time_end_rez = res.next_time_end;

                var end_time = start_time + change_hours*60 + change_minutes;

                var real_time_h_end = end_time/60|0;
                if (real_time_h_end > 23) real_time_h_end = real_time_h_end - 24;
                var real_time_m_end = end_time%60;

                if (real_time_m_end < 10) real_time_m_end = '0'+real_time_m_end;

                $("#work_time_h_end").html(real_time_h_end);
                $("#work_time_m_end").html(real_time_m_end);

                $("#wt").val(change_hours*60 + change_minutes);

                if (next_time_start_rez != 0){
                    if (
                        (start_time <= next_time_start_rez) && (end_time > next_time_start_rez)
					){

                        $("#exist_zapis").html('<span style="color: red">Записи не могут пересекаться</span><br>');

                         $("#Ajax_add_TempZapis").disabled = true;

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

                                $("#Ajax_add_TempZapis").disabled = true;

                                next_time_start_rez = res.next_time_start;
                                next_time_end_rez = res.next_time_end;

                                var end_time = start_time + change_hours*60 + change_minutes;

                                var real_time_h_end = end_time/60|0;
                                if (real_time_h_end > 23) real_time_h_end = real_time_h_end - 24;
                                var real_time_m_end = end_time%60;

                                if (real_time_m_end < 10) real_time_m_end = '0'+real_time_m_end;

                                $("#work_time_h_end").html(real_time_h_end);
                                $("#work_time_m_end").html(real_time_m_end);

                                $("#wt").val(change_hours*60 + change_minutes);

                                if (next_time_start_rez != 0){
                                    if (
                                        ((start_time < next_time_end_rez) && (start_time >= next_time_start_rez))
                                    ){
                                        $("#exist_zapis").html('<span style="color: red">Записи не могут пересекаться</span><br>');

                                         $("#Ajax_add_TempZapis").disabled = true;
                                    }else{

                                        $("#exist_zapis").html('');
                                         $("#Ajax_add_TempZapis").disabled = false;
                                    }
                                }else{
                                    $("#exist_zapis").html('');
                                     $("#Ajax_add_TempZapis").disabled = false;
                                }
                            }
                        });
                    }
                }else{
                    $("#exist_zapis").html('');
                     $("#Ajax_add_TempZapis").disabled = false;
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
					id: id
				},
				success:function(data){
					alert(data);
				}
			})
		}
	};

	//Подсчёт суммы для счёта
	function calculateInvoice (invoice_type, changeItogPrice){
		//console.log("calculateInvoice");

		var Summ = 0;
		var SummIns = 0;

		var insure = 0;
		var insureapprove = 0;

		//var discount = Number( $("#discountValue").html());

		var link = 'add_price_price_id_in_item_invoice_f.php';

		if (invoice_type == 88){
            link = 'add_price_price_id_in_item_invoice_free_f.php';
		}
        //console.log(link);

        $("#calculateInvoice").html(Summ);

		if (invoice_type == 5){
			$("#calculateInsInvoice").html(SummIns);
		}

		$(".invoiceItemPrice").each(function() {

            var invoiceItemPriceItog = 0;

			if (invoice_type == 5){
				//получаем значение страховой
				insure = $(this).prev().prev().attr('insure');
				//console.log(insure);

				//получаем значение согласования
				insureapprove = $(this).prev().attr('insureapprove');
			}

			//получаем значение гарантии
			var guarantee = $(this).next().next().next().next().attr('guarantee');

            //получаем значение подарка
            var gift = $(this).next().next().next().next().attr('gift');
            //console.log(gift);

			//Цена
			var cost = Number($(this).attr('price'));

			var ind = $(this).attr('ind');
			var key = $(this).attr('key');

			//обновляем цену в сессии как можем
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
					worker: $("#worker").val(),

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

            //суммируем сумму в итоги
            if ((guarantee == 0) && (gift == 0)) {
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

            if (ishod_price == 0) {
            	//2018.03.13 попытка разобраться с гарантийной ценой для зарплаты
                //if (guarantee != 1) {
                    $(this).parent().find('.invoiceItemPriceItog').html(stoim);
                //}
            }

            if (changeItogPrice) {
                //прописываем стоимость этой позиции
                if ((guarantee == 0) && (gift == 0)) {

                    $(this).parent().find('.invoiceItemPriceItog').html(stoim);
                }
            }
            //console.log("calculateInvoice --> changeItogPrice ---->");
            //console.log(invoiceItemPriceItog);

            if (changeItogPrice) {
				//console.log(changeItogPrice);

                var link2 = "add_manual_itog_price_id_in_item_invoice_f.php";

                if (invoice_type == 88){
                    link2 = 'add_manual_itog_price_id_in_item_invoice_free_f.php';
                }

                $.ajax({
                    url: link2,
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
                    }
                });

            }
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

	//Смена исполнителя для расчета
    function changeWorkerInCalculate (){

        var link = "search_user_f.php";

        var reqData = {
            workerFIO: $("#search_client2").val(),
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function (res) {
                //console.log(res);

               $("#worker").val(res.data.id);
            }
        });
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

		var invoice_type =  $("#invoice_type").val();
		//console.log(invoice_type);

		var link = "fill_invoice_stom_from_session_f.php";
		if (invoice_type == 6){
			link = "fill_invoice_cosm_from_session_f.php";
		}
		if (invoice_type == 88){
			link = "fill_invoice_free_from_session_f.php";
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
				worker: $("#worker").val(),
                invoice_type: invoice_type
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
					//console.log(res.data2);
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
	}

	//Функция заполняет результат расчета из сессии
	function fillCalculateRez(){

		var invoice_type = $("#invoice_type").val();
        //console.log(invoice_type);

		var link = "fill_calculate_stom_from_session_f.php";
		if (invoice_type == 6){
			link = "fill_calculate_cosm_from_session_f.php";
		}
		if (invoice_type == 88){
			link = "fill_calculate_free_from_session_f.php";
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
				filial: $("#filial2").val(),
				worker: $("#worker").val(),
                invoice_type: invoice_type

			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
                //console.log(res);

				if(res.result == "success"){
					//console.log(res.data);
					$('#calculate_rezult').html(res.data);
					//$('#calculate_rezult').append(res.data);

					// !!!
                    calculateCalculate();

				}else{
					//console.log(res.data);
					$('#errror').html(res.data);
				}
				// !!! скролл надо замутить сюда $('#invoice_rezult').scrollTop();
			}
		});
		//$('#errror').html('Результат');
		//calculateInvoice();
	}

	// что-то как-то я хз, типа добавляем в сессию новый зуб (наряд)
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
				client: $("#client").val(),
				zapis_id: $("#zapis_id").val(),
				filial: $("#filial").val(),
				worker: $("#worker").val()
			},
			cache: false,
			beforeSend: function() {
				//$(\'#errrror\').html("<div style=\'width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);\'><img src=\'img/wait.gif\' style=\'float:left;\'><span style=\'float: right;  font-size: 90%;\'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){

                fillInvoiseRez(true);

				if(res.result == "success"){
					//$(\'#errror\').html(rez.data);


				}else{
					$('#errror').html(res.data);
				}

			}
		})
	}

	//меняет кол-во позиции
	function changeQuantityInvoice(ind, itemId, dataObj){
		//console.log(dataObj.val());
		//console.log(this);

		var invoice_type = $("#invoice_type").val();

		var link = "add_quantity_price_id_in_invoice_f.php";

		if (invoice_type == 88){
            link = "add_quantity_price_id_in_invoice_free_f.php";
		}
        //console.log(invoice_type);

		//количество
		var quantity = dataObj.value;
		//console.log(quantity);

		$.ajax({
			url: link,
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

			}
		});
	}

	//Для измения цены +1
	function invPriceUpDownOne(ind, itemId, price, start_price, up_down){
		//console.log(dataObj.value);
		//console.log(this);

		var invoice_type = $("#invoice_type").val();

		var link = 'add_price_up_down_one_price_id_in_invoice_f.php';

		if (invoice_type == 88){
            link = 'add_price_up_down_one_price_id_in_invoice_free_f.php';
		}

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
			url: link,
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

				invoice_type: invoice_type
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
	function deleteInvoiceItem(ind, dataObj){
		//console.log(dataObj.getAttribute("invoiceitemid"));

        var invoice_type = $("#invoice_type").val();

        var link = "delete_invoice_item_from_session_f.php";

        if (invoice_type == 88){
            link = "delete_invoice_free_item_from_session_f.php";
        }

		//номер позиции
		var itemId = dataObj.getAttribute("invoiceitemid");
		var target = 'item';

		//if ((itemId == 0) || (itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
		if ((itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
			target = 'ind';
		}
		//console.log(zub);
		//console.log(target);

		$.ajax({
			url: link,
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

				target: target
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){

                fillInvoiseRez(true);

				//$('#errror').html(data);
				if(res.result == "success"){
					//console.log(111);

					colorizeTButton (res.t_number_active);

				}
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
			target = 'ind';
		}

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

				target: target
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

				client:  $("#client").val(),
				zapis_id:  $("#zapis_id").val(),
				filial:  $("#filial").val(),
				worker:  $("#worker").val(),
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

				client:  $("#client").val(),
				zapis_id:  $("#zapis_id").val(),
				filial:  $("#filial").val(),
				worker:  $("#worker").val(),
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

        var link = "add_spec_koeff_price_id_in_invoice_f.php";
        if (invoice_type == 88){
            link = "add_spec_koeff_price_id_in_invoice_free_f.php";
        }

		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();

		$.ajax({
			url: link,
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

			}
		});

	}

	//Изменить гарантию у всех
	function guaranteeInvoice(guarantee){

		var invoice_type =  $("#invoice_type").val();

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
				client:  $("#client").val(),
				zapis_id:  $("#zapis_id").val(),
				filial:  $("#filial").val(),
				worker:  $("#worker").val(),

				invoice_type: invoice_type
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
	//Изменить гарантию или подарок у всех
	function giftOrGiftInvoice(guaranteeOrGift){
		//console.log(guaranteeOrGift);

		var invoice_type = $("#invoice_type").val();

		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();

		$.ajax({
			url:"add_guarantee_gift_in_invoice_f.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                guaranteeOrGift: guaranteeOrGift,
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

                fillInvoiseRez(true);

			}
		});
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
				client:  $("#client").val(),
				zapis_id:  $("#zapis_id").val(),
				filial:  $("#filial").val(),
				worker:  $("#worker").val()
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

	//Изменить скидку у всех
	function discountInvoice(discount){
		//console.log(discount);

        var invoice_type = $("#invoice_type").val();

        var link = "add_discount_price_id_in_invoice_f.php";
        if (invoice_type == 88){
            link = "add_discount_price_id_in_invoice_free_f.php";
        }

		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
				discount: discount,
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

                // $("#discountValue").html(Number(discount));

                fillInvoiseRez(true);

			}
		});
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
				client:  $("#client").val(),
				zapis_id:  $("#zapis_id").val(),
				filial:  $("#filial").val(),
				worker:  $("#worker").val(),
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
				client:  $("#client").val(),
				zapis_id:  $("#zapis_id").val(),
				filial:  $("#filial").val(),
				worker:  $("#worker").val(),
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
				client:  $("#client").val(),
				zapis_id:  $("#zapis_id").val(),
				filial:  $("#filial").val(),
				worker:  $("#worker").val(),
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

	//Изменить гарантию у этого зуба
	function guaranteeItemInvoice(zub, key, guarantee){

		var invoice_type =  $("#invoice_type").val();

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
				client:  $("#client").val(),
				zapis_id:  $("#zapis_id").val(),
				filial:  $("#filial").val(),
				worker:  $("#worker").val(),

				invoice_type: invoice_type,
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

	//Изменить гарантию и подарок у этого зуба
	function guaranteeGiftItemInvoice(zub, key, guaranteeOrGift){

		var invoice_type = $("#invoice_type").val();

		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();

		$.ajax({
			url:"add_guarantee_gift_price_id_in_item_invoice_f.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
				zub: zub,
				key: key,
                guaranteeOrGift: guaranteeOrGift,
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

                fillInvoiseRez(true);

			}
		});
	}

    //меняет категорию позиции
    function changeItemPerCatInvoice(ind, itemId, catValue){
        //console.log(catValue);
        //console.log(this);

        var invoice_type = $("#invoice_type").val();

        var link = "add_percent_cat_id_in_invoice_f.php";

        if (invoice_type == 88){
            link = "add_percent_cats_id_in_invoice_free_f.php";
        }
        //console.log(invoice_type);

        //категория
        //var category = catValue;
        //console.log(category);

        $.ajax({
            url: link,
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

                    percent_cats: catValue,
                    invoice_type: invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                console.log(res);

                fillInvoiseRez(true);

            }
        });
    }




    //Изменить категорию процентов
    function fl_changeItemPercentCat(ind, key, percent_cats){

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
                    percent_cats: percent_cats,
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

            }
        });
    }


	//Изменить Коэффициент у этого зуба
	function spec_koeffItemInvoice(ind, key, spec_koeff){

		var invoice_type = $("#invoice_type").val();

		var link = "add_spec_koeff_price_id_in_item_invoice_f.php";

		if (invoice_type == 88){
            link = "add_spec_koeff_price_id_in_item_invoice_free_f.php";
		}
		//console.log(link);

		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
				ind: ind,
				key: key,
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
			success: function(res){
                //console.log(res);

                fillInvoiseRez(true);

			}
		});
	}

	//Изменить скидка акция у этого зуба
	function discountItemInvoice(ind, key, discount){

		var invoice_type = $("#invoice_type").val();

		var link = 'add_discount_price_id_in_item_invoice_f.php';

		if (invoice_type == 88){
            link = "add_discount_price_id_in_item_invoice_free_f.php";
		}

		//console.log(discount);
		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();

		$.ajax({
			url: link,
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

			}
		});
	}

	//Изменить цену у этого зуба
	function priceItemInvoice(ind, key, price, start_price){

		var invoice_type =  $("#invoice_type").val();

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

				client:  $("#client").val(),
				zapis_id:  $("#zapis_id").val(),
				filial:  $("#filial").val(),
				worker:  $("#worker").val(),

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

			}
		});
	}

	//Изменить итоговую цену у этой позиции
	function priceItemItogInvoice(ind, key, price, manual_itog_price){

		var invoice_type = $("#invoice_type").val();

		var link = "add_manual_itog_price_id_in_item_invoice_f.php";

		if (invoice_type == 88){
            link = "add_manual_itog_price_id_in_item_invoice_free_f.php";
		}

		/*console.log(ind);
		console.log(key);*/

        var min_price = manual_itog_price - 10;
        var max_price = manual_itog_price + 2;

        if (min_price < 0) min_price = 0;

        if (isNaN(price)) price = max_price;
		if (price < min_price) price = min_price;
		if (price > max_price) price = max_price;

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();

		$.ajax({
			url: link,
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

				fillInvoiseRez(false);

			}
		});


	}

	//Выбор позиции из таблички в наряде
	function toothInInvoice(t_number){

        var invoice_type = $("#invoice_type").val();

        var link = "add_invoice_in_session_f.php";
        if (invoice_type == 88){
            link = "add_invoice_free_in_session_f.php";
        }

		//console.log (t_number);
		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
				t_number: t_number,
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
		if (type == 88){
			link = "add_price_id_free_in_invoice_f.php";
		}
		//console.log(link);

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
				price_id: price_id,
				client: $("#client").val(),
				client_insure: $("#client_insure").val(),
				zapis_id: $("#zapis_id").val(),
				zapis_insure: $("#zapis_insure").val(),
				filial: $("#filial").val(),
				worker: $("#worker").val()
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
                //console.log(res.data);

                fillInvoiseRez(true);

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
				client:  $("#client").val(),
				client_insure:  $("#client_insure").val(),
				zapis_id:  $("#zapis_id").val(),
				zapis_insure:  $("#zapis_insure").val(),
				filial:  $("#filial").val(),
				worker:  $("#worker").val()
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

	};

	//Полностью чистим счёт
	function clearInvoice(){

		var rys = false;

		rys = confirm("Очистить?");

		if (rys){
			$.ajax({
				url:"invoice_clear_f.php",
				global: false,
				type: "POST",
				dataType: "JSON",
				data:
				{
					client: $("#client").val(),
					zapis_id: $("#zapis_id").val()
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

	// !!! Перенесли отсюда документ_реади в инвойс_адд


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
				// $("#status_notes").html(data);
				//console.log("Ok");
				location.reload();
			}
		});
	}

	//Сменить категории процентов в сессии пользователя
	function fl_changePercentCat(percent_cats){

        var invoice_type = $("#invoice_type").val();

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url:"fl_add_percent_cat_in_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            //dataType: "JSON",
            data:
                {
                    percent_cats: percent_cats,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type:invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);
                //console.log(res.data);

                fillInvoiseRez(true);

            }
        });

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

		if (invoice_type == 88){
            buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_free_add(\'add\')">';
		}

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

	//Показываем блок с суммами и кнопками Для закрытия наряда
	function showInvoiceClose(invoice_id){
		//console.log(mode);
        var rys = false;

        rys = confirm("Закрыть работу?");

        if (rys) {

            var link = "invoice_close_f.php";

            var reqData = {
                invoice_id: invoice_id
            };

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: reqData,
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success: function (res) {
                    //console.log (res);

                    if (res.result == "success") {
                        setTimeout(function () {
                            window.location.href = "invoice.php?id="+invoice_id;
                        }, 200);
                    } else {

                    }
                }
            })
        }
	}

	//Показываем блок с суммами и кнопками Для окрытия наряда
	function showInvoiceOpen(invoice_id){
		//console.log(mode);
        var rys = false;

        rys = confirm("Снять отметку о звершении работы?");

        if (rys) {

            var link = "invoice_open_f.php";

            var reqData = {
                invoice_id: invoice_id
            };

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: reqData,
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success: function (res) {
                    //console.log (res);

                    if (res.result == "success") {
                        setTimeout(function () {
                            window.location.href = "invoice.php?id="+invoice_id;
                        }, 200);
                    } else {

                    }
                }
            })
        }
	}

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
		// Показываем меню с небольшим стандартным эффектом jQuery.
		menu.show();

	}

    //Показываем блок с суммами и кнопками Для ордера
    function showOrderAdd(mode){
        //console.log(mode);

        var Summ = $("#summ").val();
        var SummType = $("#summ_type").val();
        var filial = $("#filial").val();

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    summ: Summ,
                    summ_type: SummType,
                    filial: filial
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){
                    $('#overlay').show();

                    if (mode == 'add'){
                        Ajax_order_add('add');
                    }

                    if (mode == 'edit'){
                        Ajax_order_add('edit');
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
                    $("#errror").html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');
                }
            }
        })
    }

    //Показываем блок с суммами и кнопками Для РАСХОДНОГО ордера
    function showGiveOutCashAdd(mode){
        //console.log(mode);

        var Summ = $("#summ").val();
        var type = $("#type").val();
        var filial = $("#filial").val();

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    summ: Summ,
                    filial: filial
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){
                    $('#overlay').show();

                    if (mode == 'add'){
                        Ajax_GiveOutCash_add('add');
                    }

                    if (mode == 'edit'){
                        Ajax_GiveOutCash_add('edit');
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
                    $("#errror").html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');
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

        $('#overlay').show();

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
								'<div id="search_cert_input_target">'+
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
            //console.log(this.html());
            $(this).html('');
        });

         $("#errror").html('');

        var search_client2 =  $("#search_client2").val();
        var lab =  $("#lab").val();
        var descr =  $("#descr").val();
        var comment =  $("#comment").val();

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
                     $("#errror").html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>')
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
				invoice_id: invoice_id
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

	//Добавляем/редактируем в базу наряд из сессии "пустой"
	function Ajax_invoice_free_add(mode){
		//console.log(mode);

        $('#errror').html('');

		var invoice_id = 0;

		var link = "invoice_free_add_f.php";

		if (mode == 'edit'){
			link = "invoice_free_edit_f.php";
			invoice_id = $("#invoice_id").val();
		}

		var invoice_type = $("#invoice_type").val();

		var Summ = $("#calculateInvoice").html();
		var SummIns = 0;

		var SummInsStr = '';

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                client: $("#search_client").val(),
				date_in: $("#iWantThisDate2").val(),
				filial: $("#filial").val(),
				worker: $("#search_client4").val(),

				summ: Summ,
				summins: SummIns,

				invoice_type: invoice_type,
				invoice_id: invoice_id
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
				client_id: client,
				zapis_id: $("#zapis_id2").val(),
				invoice_id: invoice_id,
				filial_id: $("#filial2").val(),
				worker_id: $("#worker").val(),

				summ: Summ,
				summins: SummIns,

				invoice_type: invoice_type,
                calculate_id: calculate_id
			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){
				console.log(res);

				$('.center_block').remove();
				$('#overlay').hide();

				if(res.result == "success"){
                    if (mode == 'reset') {
                        location.reload();
                    }else {
                        $('#data').hide();
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
                cell_date: $('#iWantThisDate2').val(),
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

	//Удалим продажу сертификата
	function Ajax_cert_celling_del(id){

        var rys = false;

        rys = confirm("Вы собираетесь отменить продажу сертификата.\nВы уверены?");

        if (rys) {

            var link = "cert_cell_dell_f.php";

            var Data = {
                cert_id: id
            };

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: Data,
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {

					 if(res.result == "success"){
					    //$('#data').hide();
					    $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
					    '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Продажа отменена</li>'+
					    '</ul>');
					    setTimeout(function () {
                            location.reload();
					    }, 100);
					 }
					 if(res.result == "error"){
					    $('#errror').html(res.data);
					 }
                }
            });
        }
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

	}

	//Добавляем/редактируем в базу ордер
	function Ajax_order_add(mode){
		//console.log(mode);

        var order_id = 0;

		var link = "order_add_f.php";

		var paymentStr = '';

		if (mode == 'edit'){
			link = "edit_order_f.php";
            order_id = $("#order_id").val();
		}

        var Summ = $("#summ").val();
        //var SummType =  $("#summ_type").val();
        var SummType = document.querySelector('input[name="summ_type"]:checked').value;
        var office_id = $("#filial").val();

		var client_id = $("#client_id").val();
		//var order_id =  $("#order_id").val();
		//console.log(invoice_id);
		var date_in = $("#date_in").val();
		//console.log(date_in);

        var comment = $("#comment").val();
        //console.log(comment);

        var org_pay = $("input[name=org_pay]:checked").val();

        if (org_pay === undefined){
            org_pay = 0;
        }

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
                org_pay: org_pay,

                order_id: order_id
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

	//Добавляем/редактируем в базу ордер
	function Ajax_GiveOutCash_add(mode){
		//console.log(mode);

        var giveoutcash_id = 0;

		var link = "fl_give_out_cash_add_f.php";

		//var paymentStr = '';

		if (mode == 'edit'){
			link = "fl_give_out_cash_edit_f.php";
            giveoutcash_id = $("#giveoutcash_id").val();
		}

        var Summ = $("#summ").val();
        //var SummType =  $("#summ_type").val();
        var type = $("#type").val();
        var office_id = $("#filial").val();

		//var client_id = $("#client_id").val();
		//var order_id =  $("#order_id").val();
		//console.log(invoice_id);
		var date_in = $("#date_in").val();
		//console.log(date_in);

        var comment = $("#comment").val();
        //console.log(comment);

        /*if (giveoutcash_id != 0){
            paymentStr = '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
                '<a href= "payment_add.php?invoice_id='+order_id+'" class="b">Оплатить наряд #'+order_id+'</a>'+
                '</li>';
		}*/

		$.ajax({
			url: link,
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                office_id: office_id,
				summ: Summ,
                type: type,
                date_in: date_in,
                comment: comment,

                giveoutcash_id: giveoutcash_id
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
											'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Добавлен/отредактирован расходный ордер</li>'+
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
                        					/*paymentStr+*/
					                        '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
						                        '<a href="stat_cashbox.php" class="b">Касса</a>'+
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
            lab_order_id = $("#lab_order_id").val();
		}

        var client_id = $("#client_id").val();

        var search_client2 = $("#search_client2").val();
        var lab = $("#lab").val();
        var descr = $("#descr").val();
        var comment = $("#comment").val();

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

                status: status

			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){

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

                status: status

			},
			cache: false,
			beforeSend: function() {
				//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
			},
			// действие, при ответе с сервера
			success: function(res){

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
		var client_fio =  $("#search_client").val();
		if (client_fio != ''){
			window.location.replace('client_add.php?fio='+ $("#search_client").val());
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

        rys = confirm("Вы хотите удалить позиции из прайса страховой. \n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "del_items_from_insure_price_f.php",
                global: false,
                type: "POST",
                data: {
                    items: checkedItems(),
                    insure_id: insure_id
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

        var group =  $("#group").val();
        //console.log(group);

        var rys = false;

        rys = confirm("Вы хотите переместить выбранные позиции в группу. \n\nВы уверены?");
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
            var elem8 = $("#gift");
            var elem9 = $("#guaranteegift");

            if(e.target != elem[0]&&!elem.has(e.target).length &&
                e.target != elem2[0]&&!elem2.has(e.target).length &&
                e.target != elem3[0]&&!elem3.has(e.target).length &&
                e.target != elem4[0]&&!elem4.has(e.target).length &&
                e.target != elem5[0]&&!elem5.has(e.target).length &&
                e.target != elem6[0]&&!elem6.has(e.target).length &&
                e.target != elem7[0]&&!elem7.has(e.target).length &&
                e.target != elem8[0]&&!elem8.has(e.target).length &&
                e.target != elem9[0]&&!elem9.has(e.target).length){
                elem.hide();
            }
        });

        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
        /*$("#spec_koeff").click(function(event) {
        	//console.log(1);

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'spec_koeff');
            }
        });*/

        $("body").on("click", "#spec_koeff", function(event){
            //console.log(1);

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'spec_koeff');
            }
        });

        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
        /*$("#guarantee").click(function(event) {

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'guarantee');
            }
        });*/

        /*$("body").on("click", "#guarantee", function(){

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'guarantee');
            }
        });*/

        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
        /*$("#gift").click(function(event) {

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'gift');
            }
        });*/

        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
        /*$("#guaranteegift").click(function(event) {

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'guaranteegift');
            }
        });*/

        $("body").on("click", "#guaranteegift", function(event){

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'guaranteegift');
            }
        });

        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
        /*$("#insure").click(function(event) {

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                //console.log(1);
                contextMenuShow(0, 0, event, 'insure');
            }
        });*/

        $("body").on("click", "#insure", function(event){

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                //console.log(1);
                contextMenuShow(0, 0, event, 'insure');
            }
        });

        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
/*        $("#insure_approve").click(function(event) {

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                //console.log(1);
                contextMenuShow(0, 0, event, 'insure_approve');
            }
        });*/

        $("body").on("click", "#insure_approve", function(event){

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                //console.log(1);
                contextMenuShow(0, 0, event, 'insure_approve');
            }
        });

        //Скидки Вешаем слушатель события нажатие кнопок мыши для всего документа:
        /*$("#discounts").click(function(event) {

            // Проверяем нажата ли именно правая кнопка мыши:
            if (event.which === 1)  {
                //console.log(71);
                contextMenuShow(0, 0, event, 'discounts');
            }
        });*/

        $("body").on("click", "#discounts", function(event){

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

                var lab_order_id =  $("#lab_order_id").val();
                var status_now =  $("#status_now").val();
                //console.log(status_now);

                contextMenuShow(lab_order_id, status_now, event, 'lab_order_status');
            }
        });

		//Надо же хоть что-то передать...
		var reqData = {
			type: 5,
		}

		//Запрос к базе онлайн записи и выгрузка
		$.ajax({
			url:"get_zapis3.php",
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

        rys = confirm("Вы cобираетесь вернуть тикет в работу. \n\nВы уверены?");

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

        rys = confirm("Пометить все тикеты как прочитаные?");

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



    //Функция открыть скрытый див по его id
	function toggleSomething (divID){
        $(divID).toggle('normal');
	}

	//Открываем в новом окне url
    function iOpenNewWindow(url, name, options){

		//Небольшой костыль из-за хрома, в котором не работает .focus();
        if (typeof openedWindow !== 'undefined'){

			if (navigator.userAgent.indexOf('Chrome/') > 0) {
				if (openedWindow) {
					openedWindow.close();
					openedWindow = null;
				}
			}
        }

        openedWindow = window.open(url, name, options);
        openedWindow.focus();

        //WaitForCloseWindow(openedWindow);

        return openedWindow;
    }

    //Получаем, показываем направления
    function getRemovesfunc(worker_id){
        //console.log (worker_id);

    	var link = "removes_get_f.php";

		var reqData = {
            worker_id: worker_id
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
				$("#removes").html("<div style='width: 120px; height: 32px; padding: 5px 10px 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...<br>загрузка</span></div>");
            },
            success:function(res){
                //console.log (res);

                if(res.result == "success") {
                    $("#removes").html(res.data);
                }else{
                	//Показываем ошибку в консоли
                    console.log (res.data);
                }
            }
        })
	}

	//Получаем, показываем напоминания
    function getNotesfunc(worker_id){

    	var link = "notes_get_f.php";

		var reqData = {
            worker_id: worker_id
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
				$("#notes").html("<div style='width: 120px; height: 32px; padding: 5px 10px 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...<br>загрузка</span></div>");
            },
            success:function(res){
                //console.log (res);

                if(res.result == "success") {
                    $("#notes").html(res.data);
                }else{
                }
            }
        })
	}

	//Получаем, показываем записи в карточке клиента
    function getZapisfunc(client_id){

    	var link = "zapis_get_f.php";

		var reqData = {
            client_id: client_id
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
				$("#zapis").html("<div style='width: 120px; height: 32px; padding: 5px 10px 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...<br>загрузка</span></div>");
            },
            success:function(res){
                //console.log (res);

                if(res.result == "success") {
                    $("#zapis").html(res.data);
                }else{
                }
            }
        })
	}

	//Получаем, показываем движение денег в карточке клиента
    function getClientMoney(client_id){

    	var link = "money_get_f.php";

		var reqData = {
            client_id: client_id
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
				$("#giveMeYourMoney").html("<div style='width: 120px; height: 32px; padding: 5px 10px 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...<br>загрузка</span></div>");
            },
            success:function(res){
                //console.log (res);

                if(res.result == "success") {
                    $("#giveMeYourMoney").html(res.data);
                }else{
                }
            }
        })
	}

	//Редактирование напоминание
    function Change_notes_stomat(id, type, worker_id, thisObj) {
    	//console.log(thisObj.parent().parent().html());

		var note = thisObj.parent().parent().html();

        var link = "Change_notes_stomat.php";

        var reqData = {
            id: id,
            type: type,
            worker_id: worker_id
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
            },
            success:function(res){
                //console.log (res);

                if(res.result == "success") {
                    $("#notes_change").show();
                    $("#notes_change").html(res.data);
                    $("#notes_change_note").html('<li class="cellsBlock">'+note+'</li>');
                }else{
                }
            }
        })
    }

    //Закрыть напоминание
    function Close_notes_stomat(id, worker_id) {

        var link = "Close_notes_stomat_f.php";

        var reqData = {
            id: id
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
            },
            success:function(res){
                if(res.result == "success") {
                    getNotesfunc (worker_id);
                }else{
                }
            }
        })
    }

    //Обновить изменить напоминание
    function Ajax_change_notes_stomat(id, worker_id) {

        var link = "Change_notes_stomat_f.php";

        var reqData = {
            id:id,
            change_notes_months: $("#change_notes_months").val(),
            change_notes_days: $("#change_notes_days").val(),
            change_notes_type: $("#change_notes_type").val()
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
            },
            success:function(res){
                //console.log (res);

                if(res.result == "success") {
                    getNotesfunc (worker_id);
                }else{
                }
            }
        })
    }

    //Закрыть направление
    function Close_removes_stomat(id, worker_id) {

        var link = "Close_removes_stomat_f.php";

        var reqData = {
            id: id
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
            },
            success:function(res){
                //console.log (res);

                if(res.result == "success") {
                    //console.log (res.data);

                    getRemovesfunc(worker_id);
                }else{
                }
            }
        })
    }

    //showGiveOutCashAdd('add');




	/*Для круговой диаграммы*/
    var randomScalingFactor = function() {
        return Math.round(Math.random() * 100);
    };

	/*document.getElementById('randomizeData').addEventListener('click', function() {
        config.data.datasets.forEach(function(dataset) {
            dataset.data = dataset.data.map(function() {
                return randomScalingFactor();
            });
        });

        window.myPie.update();
    });*/

    var colorNames = Object.keys(window.chartColors);

    /*document.getElementById('addDataset').addEventListener('click', function() {
        var newDataset = {
            backgroundColor: [],
            data: [],
            label: 'New dataset ' + config.data.datasets.length,
        };

        for (var index = 0; index < config.data.labels.length; ++index) {
            newDataset.data.push(randomScalingFactor());

            var colorName = colorNames[index % colorNames.length];
            var newColor = window.chartColors[colorName];
            newDataset.backgroundColor.push(newColor);
        }

        config.data.datasets.push(newDataset);
        window.myPie.update();
    });*/

    /*document.getElementById('removeDataset').addEventListener('click', function() {
        config.data.datasets.splice(0, 1);
        window.myPie.update();
    });*/


    function showChart() {

        var mainData = [];
        var mainLabel = [];

		$('.categoryItem').each(function() {
			//console.log($(this).attr('nameCat'));

			if ($(this).attr('percentCat').replace(',', '.') > 2) {

                //Массив данных
                mainData.push($(this).attr('percentCat').replace(',', '.'));

                //Массив названий
                mainLabel.push($(this).attr('nameCat'));
            }
		});

		//console.log(mainData);
		//console.log(mainLabel);

        var config = {
            type: 'pie',
            data: {
                datasets: [{
                    data: mainData,
                    backgroundColor: [
                        window.chartColors.red,
                        window.chartColors.orange,
                        window.chartColors.yellow,
                        window.chartColors.green,
                        window.chartColors.cyan,
                        window.chartColors.blue,
                        window.chartColors.indigo,
                    ],
                    label: 'Dataset 1'
                }],
                labels: mainLabel
            },
            options: {
                responsive: true
            }
        };

        var ctx = document.getElementById('chart-area').getContext('2d');

        window.myPie = new Chart(ctx, config);

    };


