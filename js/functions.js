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
        $('#errrror').html('');
	}

	//Функция для работы с GET
	function urlGetWork(addIt, deleteIt){

        var get_data_str = "";

		var params = window
            .location
            .search
            .replace("?","")
            .split("&")
            .reduce(
                function(p,e){
                    var a = e.split('=');
                    p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                    return p;
                },
                {}
            );
        //console.log(params);

        for (key in params) {
        	//console.log(key.length);

			//Если ключ есть и он не undef
            if (key.length > 0) {
            	if (deleteIt.indexOf(key) == -1){
                	get_data_str = get_data_str + "&" + key + "=" + params[key];
                }
            }
        }

        //!!! Дописать про добавление параметров

        //console.log(get_data_str);

		//Переходим по новой ссылке, удалив перед этим первый символ, который у нас "&"
        document.location.href = "?"+get_data_str.slice(1);
	}

	//Сегодняшняя дата (сегодня)
	function getTodayDate (){
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();

        if(dd<10) {
            dd = '0'+dd
        }

        if(mm<10) {
            mm = '0'+mm
        }

        today = dd + '.' + mm + '.' + yyyy;

        return today;
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

	//Задаёт цвет в зависимости от заполнения от 0 до 100% данных (от красного к зеленому)
    function Colorize (data, alpha){
		//console.log(data);
		//console.log(typeof (data));

        var red, green;
        //var stepSize = 5;
        //var counter=0;

        //console.log('data < 50: '+(data < 50));
        if (data < 50){
            red = 255;
            green = data * 5 + 5;
        }

        //console.log('data == 50: '+(data == 50));
        if (data == 50){
            red = 255 - (data - 50) * 5;
            green = 255;
        }

        //console.log('data > 50: '+(data > 50));
        if (data > 50){
            red = 0;
            green = 255;
        }
        // console.log(red);
        // console.log(green);
        // console.log('--------');

        return ' rgba('+red+', '+green+', 0, '+alpha+')';
    }

    //Рандомный случайный цвет
    function randColor() {
        let r = Math.floor(Math.random() * (256)),
            g = Math.floor(Math.random() * (256)),
            b = Math.floor(Math.random() * (256)),
            color = '#' + r.toString(16) + g.toString(16) + b.toString(16);

        return color;
    }

    //Рандомный случайный цвет из списка
    function randColor2() {
        Colors = {};
        Colors.names = {
            aqua: "#00ffff",
            blue: "#0000ff",
            brown: "#a52a2a",
            darkblue: "#00008b",
            darkcyan: "#008b8b",
            darkgrey: "#a9a9a9",
            darkgreen: "#006400",
            darkkhaki: "#bdb76b",
            darkmagenta: "#8b008b",
            darkolivegreen: "#556b2f",
            darkorange: "#ff8c00",
            darkorchid: "#9932cc",
            darkred: "#8b0000",
            darksalmon: "#e9967a",
            darkviolet: "#9400d3",
            fuchsia: "#ff00ff",
            gold: "#ffd700",
            green: "#008000",
            indigo: "#4b0082",
             lime: "#00ff00",
            magenta: "#ff00ff",
            maroon: "#800000",
            navy: "#000080",
            olive: "#808000",
            orange: "#ffa500",
            purple: "#800080",
            violet: "#800080",
            red: "#ff0000",
            yellow: "#ffff00"
        };

        let color;
        let count = 0;
        for (let prop in Colors.names)
            if (Math.random() < 1/++count)
                color = prop;

        return color;
    }


    //Функция для создания бланка платежки с QR кодом в формате PDF
    function createPayBlankPdfQRCode(){

	    //получим все данные со странички, проверим их и поедем дальше
        //Сам человек или опекун
        var thisFIO = document.querySelector('input[name="thisFIO"]:checked').value;

        //Если опекун
        if (thisFIO == 1){
            var fio = $("#fioo").val();
        }else{
            var fio = $("#fio").val();
        }

        //Длина строки JS
        //Если заполнены ФИО
        if (fio.length > 0){
            //Адрес
            var address = $("#address").val();

            //Если заполнен адрес
            if (address.length > 0){
                //Назначение платежа/коммент
                var comment = $("#comment").val();

                if (comment.length > 0){
                    //Сумма
                    var rub = $("#rub").val();
                    var kop = $("#kop").val();
                    // console.log(Number(rub));
                    // console.log(Number(kop));
                    // console.log(isNaN(rub));
                    // console.log(isNaN(kop));

                    //Если сумма число
                    if ((!isNaN(rub) && !isNaN(kop)) && (Number(rub) + Number(kop) > 0)){
                        //console.log(Number(rub) + Number(kop)/100);

                        //Если выбрали организацию
                        var org_id = $("#SelectOrg").val();

                        if (org_id != 0) {

                            var inn = $("#inn").html();
                            var kpp = $("#kpp").html();
                            var org_full_name = $("#org_full_name").html();
                            var bik = $("#bik").html();
                            var ks = $("#ks").html();
                            var bank_name = $("#bank_name").html();
                            var rs = $("#rs").html();
                            var payerinn = $("#payerinn").val();

                            //console.log('pay_blank_pdf_qr_create.php?inn=' + inn + '&kpp=' + kpp + '&org_full_name=' + org_full_name + '&bik=' + bik + '&ks=' + ks + '&bank_name=' + bank_name + '&rs=' + rs + '&fio=' + fio + '&address=' + address + '&comment=' + comment + '&summ=' + (Number(rub) + Number(kop)));
                            window.open('pay_blank_pdf_qr_create.php?inn=' + inn + '&kpp=' + kpp + '&org_full_name=' + org_full_name + '&bik=' + bik + '&ks=' + ks + '&bank_name=' + bank_name + '&rs=' + rs + '&fio=' + fio + '&address=' + address + '&comment=' + comment + '&rub=' + (Number(rub) + '&kop=' + Number(kop)) + '&payerinn=' + payerinn);
                        }else{
                            alert ("Выберите Юр. лицо");
                        }

                    }else{
                        alert ("Проверьте сумму");
                    }

                }else{
                    alert ("Заполните назначение платежа");
                }
            }
            else{
                alert ("Проверьте заполнение адреса");
            }

        }else{
            alert ("Проверьте заполнение ФИО");
        }
    }

    //Вывод данных сессии в консоль
    function fromSessionInConsole(){

        var link = "from_session_in_console.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:{a:1},
            cache: false,
            beforeSend: function() {
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);
            }
        });
	}

	//Расчет возврата средств
	function calculateRefundSumm(){

        var Summ = 0;
        var salaryDeductionSumm = 0;
        var msg = '';

        //Отметка отмечены ли все
        var all_checked = true;
        var some_checked = false;

        $(".position_check").each(function(){
        	
            var checked_status = $(this).is(":checked");

            if (checked_status){
                Summ += Number($(this).parent().parent().find(".invoiceItemPriceItog").html());
                salaryDeductionSumm += Number($(this).parent().parent().find(".salaryDeductionItemPriceItog").html());
                some_checked = true;
            }else{
                all_checked = false;
			}
		});

		if (Summ > Number($("#calculateInvoice").html())){
            Summ = Number($("#calculateInvoice").html());
            msg += '<span class="query_neok" style="padding-top: 0">Ошибка #35. Нельзя вернуть сумму больше чем было оплачено.</span>';
        }

        //Выводим сумму возврата
        if (Summ > 0) {
            $("#refundSumm").html(Summ);
        }else{
            $("#refundSumm").html(0);
            if (some_checked) {
                msg += '<span class="query_neok" style="padding-top: 0">Ошибка #36. Некорректная сумма на возврат.</span>';
            }
		}

		//Выводим сумму вычета из ЗП
        if (salaryDeductionSumm > 0) {
            $("#salaryDeductionSumm").html(number_format(salaryDeductionSumm, 0, '.', ''));
        }else{
            $("#salaryDeductionSumm").html(0);
            if (some_checked) {
                msg += '<span class="query_neok" style="padding-top: 0">Ошибка #37. Некорректная сумма вычета.</span>';
            }
		}

        $("#errror").html(msg);

		if (!all_checked){
            $(".all_position_check").prop("checked", false);
		}else{
            $(".all_position_check").prop("checked", true);
		}
	}

	//Выбор checkbox в возврате средств
    $("body").on("click", ".all_position_check", function(){
    	//console.log('all_position_check');

        var checked_status = $(this).is(":checked");

        $(".position_check").each(function() {
            if (checked_status){
            	//console.log($(this).is(':disabled'));
				if (!$(this).is(':disabled')) {
                    $(this).prop("checked", true);
                    $(this).parent().parent().css({"background-color": "rgba(131, 219, 83, 0.5)"});
                }
            }else{
                //console.log($(this).is(':disabled'));
                $(this).prop("checked", false);
                $(this).parent().parent().css({"background-color": "rgb(255, 255, 255);"});
            }
        });

        calculateRefundSumm();
    });

    //Рабочий пример клика на элементе после подгрузки загрузки его в DOM
    $("body").on("click", ".position_check", function(){

        var checked_status = $(this).prop("checked");
        //console.log(checked_status);

        var add_status = 0;

        //Меняем цвет блока
        if (checked_status){
            $(this).parent().parent().css({"background-color": "rgba(131, 219, 83, 0.5)"});
        }else{
            $(this).parent().parent().css({"background-color": "rgb(255, 255, 255);"});
        }

        calculateRefundSumm();
    });

    //Рабочий пример клика на элементе после подгрузки загрузки его в DOM
    $("body").on("click", "#selectTabelForSalaryDeduction", function(){
    	//console.log($(this).attr("worker_id"));

		if ($(this).attr("worker_id") > 0) {
            selectTabelForSalaryDeduction($(this).attr("worker_id"));
        }

    });

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
                    //console.log(res);

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

    //Для поиска сертификата именного из модального окна
    $('#search_cert_name').bind("change keyup input click", function() {

        //var $this = $(this);
        var val = $(this).val();
        //console.log(val);

        if (val.length > 1){
            $.ajax({
                url:"FastSearchCertName.php",
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
                    //console.log(res);

                    if(res.result == 'success') {
                        //console.log(res.data);

                        $(".search_result_cert_name").html(res.data).fadeIn(); //Выводим полученые данные в списке
                    }else{
                        //console.log(res.data);
                    }
                },
                error:function(){
                    //console.log(12);
                }
            });
        }else{
            $("#search_result_cert_name").hide();
        }
    });

    //Для поиска абонемента из модального окна
    $('#search_abon').bind("change keyup input click", function() {

        //var $this = $(this);
        var val = $(this).val();
        //console.log(val);

        if (val.length > 1){
            $.ajax({
                url:"FastSearchAbon.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data:{
					num: val
				},
                cache: false,
                beforeSend: function() {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success:function(res){
                    //console.log(res);

                    if(res.result == 'success') {
                    	//console.log(res.data);

                        $(".search_result_abon").html(res.data).fadeIn(); //Выводим полученые данные в списке
                    }else{
                        //console.log(res.data);
					}
                },
				error:function(){
                	//console.log(12);
				}
            });
        }else{
            $("#search_result_abon").hide();
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
		//console.log(event);
		//console.log(ind);

		// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
		$('*').removeClass('selected-html-element');
		// Удаляем предыдущие вызванное контекстное меню:
		$('.context-menu').remove();

		//Запретить или разрешить показывать меню и отключить при этом стандартное контекстное меню
        var finShow = true;
		
		// Получаем элемент на котором был совершен клик:
		var target = $(event.target);
        //console.log(target.attr('start_price'));
        //console.log(target.html());

		var dopReq = {};

        if (mark == 'consRepAdm') {
            var date = (target.html().replace(/\s{2,}/g, ''));
            //console.log(date);

			var filial_id = target.attr('filial_id');
            //console.log(filial_id);

            dopReq.date = date;
            dopReq.filial_id = filial_id;
            //console.log(dopReq);
        }

        if (mark == 'sclad_cat') {
            //console.log(target.attr('id'));

            ind = 0;

            if ((target.attr('id') !== undefined) && (target.attr('id') !== 'sclad_cat_rezult')) {
                ind = target.attr('id').split('_')[1];
                //console.log(ind);
            }

            if (target.attr('id') !== 'sclad_cat_rezult') {
                key = 'dop';
            }
        }

        if (mark == 'sclad_item') {
            //console.log(target.closest('tr').attr('id'));

            ind = 0;

            if (target.closest('tr').attr('id') !== undefined) {
                event.preventDefault();

                ind = target.closest('tr').attr('id').split('_')[1];;
                //console.log(ind);

            }else{
                finShow = false;
            }

            // if ((target.attr('id') !== undefined) && (target.attr('id') !== 'sclad_cat_rezult')) {
            //     ind = target.attr('id').split('_')[1];
            //     //console.log(ind);
            // }
            //
            // if (target.attr('id') !== 'sclad_cat_rezult') {
            //     key = 'dop';
            // }
        }

        if (mark == 'price_cat') {
            //console.log(target.attr('id'));

            ind = 0;

            if ((target.attr('id') !== undefined) && (target.attr('id') !== 'price_cat_rezult')) {
                ind = target.attr('id').split('_')[1];
                //console.log(ind);
            }

            if (target.attr('id') !== 'price_cat_rezult') {
                key = 'dop';
            }
        }

        if (mark == 'price_item') {
            //console.log(target.closest('tr').attr('id'));

            ind = 0;

            if (target.closest('tr').attr('id') !== undefined) {
                event.preventDefault();

                ind = target.closest('tr').attr('id').split('_')[1];;
                //console.log(ind);

            }else{
                finShow = false;
            }

            // if ((target.attr('id') !== undefined) && (target.attr('id') !== 'price_cat_rezult')) {
            //     ind = target.attr('id').split('_')[1];
            //     //console.log(ind);
            // }
            //
            // if (target.attr('id') !== 'price_cat_rezult') {
            //     key = 'dop';
            // }
        }

        if ((mark == 'insure') || (mark == 'insureItem')){
            dopReq.client_insure = $("#client_insure").val();
        }

		// Добавляем класс selected-html-element что бы наглядно показать на чем именно мы кликнули (исключительно для тестирования):
		target.addClass('selected-html-element');

        var reqData = {
            mark: mark,
            ind: ind,
            key: key,

            dop: dopReq
        };
        //console.log(reqData);

		$.ajax({
			url:"context_menu_show_f.php",
			global: false, 
			type: "POST", 
			dataType: "JSON",
			data: reqData,
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
					var max_itog_price = manual_itog_price + 10;

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
                if (finShow) {
                    //event.preventDefault();
                    menu.show();
                }
		
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
            //console.log($(this).attr("name"));

            if ($(this).attr("name") == 'specializations[]'){
                if ($(this).attr("id") != 'fired') {
                    if ($(this).prop("checked")) {
                        cboxArray = itemExistsChecker2(cboxArray, cboxValue);
                    }
                }
            }
        });

        return cboxArray;
    }


    //Редактирование сотрудника
    function Ajax_user_edit(worker_id) {

        // var fired = $("input[name=fired]:checked").val();
        // if((typeof fired == "undefined") || (fired == "")) fired = 0;

        var org = 0;
        var permissions = $('#permissions').val();
        var contacts = $('#contacts').val();
        var category = $('#SelectCategory').val();
        if((typeof category == "undefined") || (category == "")) category = 0;
        // console.log(category);
        // console.log(checkedItems2());

        var status = $('#w_status').val();

        var filial = $('#SelectFilial').val();

        var spec_prikaz8 = $("input[name=spec_prikaz8]:checked").val();
        if (spec_prikaz8 === undefined){
            spec_prikaz8 = 0;
        }

        var spec_oklad = $("input[name=spec_oklad]:checked").val();
        if (spec_oklad === undefined){
            spec_oklad = 0;
        }

        var spec_work_6days = $("input[name=spec_work_6days]:checked").val();
        if (spec_work_6days === undefined){
            spec_work_6days = 0;
        }

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
                    //fired: fired,
                    status: status,
                    specializations: checkedItems2(),
                    category: category,
                    filial: filial,

                    sel_date: $("#sel_date").val(),
                    sel_month: $("#sel_month").val(),
                    sel_year: $("#sel_year").val(),

                    spec_prikaz8: spec_prikaz8,
                    spec_oklad: spec_oklad,
                    spec_work_6days: spec_work_6days
                },
            cache: false,
            beforeSend: function() {
                // $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                $("#status").html(data);
                window.location.replace('user.php?id='+worker_id+'');
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

				sex: sex_value
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

                            email: $("#email").val(),
                            no_sms: $("input[name=no_sms]:checked").val(),

                            inn: $("#inn").val(),

							passport: $("#passport").val(),
							passportvidandata: $("#passportvidandata").val(),
							passportvidankem: $("#passportvidankem").val(),

							alienpassportser: $("#alienpassportser").val(),
							alienpassportnom: $("#alienpassportnom").val(),

							address: $("#address").val(),

							polis: $("#polis").val(),
							polisdata: $("#polisdata").val(),
							insurecompany: $("#insurecompany").val(),

							sex: sex_value,

							session_id: session_id
						},
						success:function(data){
							$("#errrror").html(data);
							//$("#errrror").html(data);
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

                            email: $("#email").val(),
                            no_sms: $("input[name=no_sms]:checked").val(),

                            inn: $("#inn").val(),

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

	//Удаление блокировка филиала
	function Ajax_del_filial(id) {

		ajax({
			url:"filial_del_f.php",
			statbox:"errrror",
			method:"POST",
			data:
			{
				id: id
			},
			success:function(data){
				 $("#errrror").html(data);
				setTimeout(function () {
                    //!!! переход window.location.href - это правильное использование
                    window.location.href = 'filial.php?id='+id;
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
                	//console.log(res);

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

    //Удаление выдачи
    function Ajax_del_withdraw(id, client_id) {

        ajax({
            url:"withdraw_del_f.php",
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

	//разблокировка филиала
	function Ajax_reopen_filial(id) {

		ajax({
			url:"filial_reopen_f.php",
			method:"POST",
			data:
			{
				id: id,
			},
			success:function(data){
				// $("#errrror").html(data);
				setTimeout(function () {
					//window.location.replace('labor.php?id='+id);
					//console.log('pricelistitem.php?id='+id);

                    window.location.href = 'filial.php?id='+id;


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
					new_client: name
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
                announcing_type: announcing_type,
                comment: comment,
                filial: filial,
                workers_type: workers_type,
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

		// var descr = $("#descr").val();
		// var plan_date = $("#iWantThisDate2").val();
		// var workers = $("#postCategory").val();
        // var workers_type = $("#workers_type").val();
        // var filial = $("#filial").val();
		//console.log(ticket_type);

        let certData = {
            descr: $("#descr").val(),
            plan_date: $("#iWantThisDate2").val(),
            workers: $("#postCategory").val(),
            workers_type: $("#workers_type").val(),
            filial: $("#filial").val(),
            ticket_id: $("#filial").val()
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
    function Ajax_cert_add(id, mode, certData){

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

    //Добавляем/редактируем в базу сертификат именной
    function Ajax_cert_name_add(id, mode, certData){

        var link = "cert_name_add_f.php";

        if (mode == 'edit'){
            link = "cert_name_edit_f.php";
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

    //Добавляем/редактируем в базу абонемент
    function Ajax_abon_add(id, mode, reqData){
    	//console.log(mode);

        var link = "abon_add_f.php";

        if (mode == 'edit'){
            link = "abon_edit_f.php";
        }

        reqData['abon_id'] = id;

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data: reqData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(data){
            	//console.log(data);

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
        var permission = $('#permissions').val();

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data:
			{
				name: name,
                permission: permission
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
    function Ajax_cat_add(id, mode){

        //убираем ошибки
        hideAllErrors ();

        var link = "fl_percent_cat_add_f.php";

        if (mode == 'edit'){
            link = "fl_percent_cat_edit_f.php";
        }
		//console.log(link);

        // var cat_name = $('#cat_name').val();
        // var work_percent = $('#work_percent').val();
        // var material_percent = $('#material_percent').val();
        // var summ_special = $('#summ_special').val();
        // var personal_id = $('#personal_id').val();

        if ($('#personal_id').val() == 0) {
            $("#personal_id_error").html('<span style="color: red">В этом поле ошибка</span>');
            $("#personal_id_error").show();
        }else{

            let reqData = {
                cat_name: $('#cat_name').val(),
                work_percent: $('#work_percent').val(),
                material_percent: $('#material_percent').val(),
                summ_special: $('#summ_special').val(),
                personal_id: $('#personal_id').val(),
                cat_id: id
            };
            // console.log(reqData);

            // убираем класс ошибок с инпутов
            $("input").each(function () {
                $(this).removeClass("error_input");
            });
            // прячем текст ошибок
            $(".error").hide();

            //проверка данных на валидность
            $.ajax({
                url: "ajax_test.php",
                global: false,
                type: "POST",
                dataType: "JSON",

                data: {cat_name: $('#cat_name').val()},

                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success: function (data) {
                    if (data.result == 'success') {
                        //console.log(data.result);
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
                            success: function (data) {
                                //console.log(data.data);

                                if (data.result == 'success') {
                                    //console.log('success');
                                    $('#data').html(data.data);
                                    setTimeout(function () {
                                        //window.location.replace('specializations.php');
                                    }, 100);
                                } else {
                                    //console.log('error');
                                    $('#errror').html(data.data);
                                    //$('#errrror').html('');
                                }
                            }
                        });
                        // в случае ошибок в форме
                    } else {
                        // перебираем массив с ошибками
                        for (var errorField in data.text_error) {
                            // выводим текст ошибок
                            $('#' + errorField + '_error').html(data.text_error[errorField]);
                            // показываем текст ошибок
                            $('#' + errorField + '_error').show();
                            // обводим инпуты красным цветом
                            // $('#'+errorField).addClass('error_input');
                        }
                        $('#errror').html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');
                    }
                }
            })
        }
    }

    //Редактируем норму часов
    function Ajax_normahours_add(id, mode){

        //убираем ошибки
        hideAllErrors ();

        let link = "fl_normahours_add_f.php";

        if (mode == 'edit'){
            link = "fl_normahours_edit_f.php";
        }
		//console.log(link);

        if ($('#norma_hours').val() == 0) {
            $("#norma_hours_error").html('<span style="color: red">В этом поле ошибка</span>');
            $("#norma_hours_error").show();
        }else{

            let reqData = {
                count: $('#norma_hours').val(),
                norma_id: id
            };
            // console.log(reqData);


            // убираем класс ошибок с инпутов
            $("input").each(function () {
                $(this).removeClass("error_input");
            });
            // прячем текст ошибок
            $(".error").hide();

            //проверка данных на валидность
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
                    //console.log(data.data);

                    if (res.result == 'success') {
                        $('#data').html(res.data);

                        setTimeout(function () {
                            window.location.href = 'normahours_item.php?id='+id;
                        }, 100);
                    } else {
                        //console.log('error');
                        $('#errror').html(res.data);
                        //$('#errrror').html('');
                    }
                }
            });
        }
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

	//!!! тут очередная "правильная" ф-ция
    //Промежуточная функция добавления/редактирования сертификата именного
    function showCertNameAdd(id, mode){
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

                    Ajax_cert_name_add(id, mode, certData);

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

    //Промежуточная функция добавления/редактирования абонемента
    function showAbonAdd(id, mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var num = $('#num').val();
        var abon_type = document.querySelector('input[name="abon_type"]:checked');
        if (abon_type != null) {
            //console.log(abon_type.value);

            var reqData = {
                num: num,
                abon_type: abon_type.value
            };
            //console.log(reqData);

            //проверка данных на валидность
            $.ajax({
                url:"ajax_test.php",
                global: false,
                type: "POST",
                dataType: "JSON",

                data: reqData,

                cache: false,
                beforeSend: function() {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success:function(data){
                    if(data.result == 'success'){

                        Ajax_abon_add(id, mode, reqData);

                        //в случае ошибок в форме
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
        }else{
            //console.log(abon_type);

            $('#errror').html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');
		}
    }

    function Ajax_change_expiresTime(type ,id){
        //console.log(id);
        //console.log(type);

        if (type == 'cert') {
            var link = "cert_change_expiresTime.php";
        }
        if (type == 'abon') {
            var link = "abon_change_expiresTime.php";
        }


        var dataCertEnd = $('#dataCertEnd').val();
        var dataCertEnd_arr = dataCertEnd.split('.');

        if ((dataCertEnd_arr[2] == undefined) ||
			(dataCertEnd_arr[1] == undefined) ||
			(dataCertEnd_arr[0] == undefined) ||
			(dataCertEnd_arr[2]+"-"+dataCertEnd_arr[1]+"-"+dataCertEnd_arr[0] == '0000-00-00')) {

            alert('Что-то пошло не так');

        }else{

            var reqData = {
                id: id,
                dataCertEnd: dataCertEnd_arr[2] + "-" + dataCertEnd_arr[1] + "-" + dataCertEnd_arr[0]
            };

            //console.log(dataCertEnd_arr[2]+"-"+dataCertEnd_arr[1]+"-"+dataCertEnd_arr[0]);

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
							contacts:contacts
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
		hideAllErrors ();

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

	//Редактируем филиал
	function Ajax_edit_filial(id) {
		// убираем класс ошибок с инпутов
		hideAllErrors ();

        let link = "filial_edit_f.php";

        let reqData = {
            id: id,
            name: $("#name").val(),
            name2: $("#name2").val(),
            address: $("#address").val(),
            contacts: $("#contacts").val(),
            org: $("#org").val()
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            //dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                //console.log(res.data);

                $("#errrror").html(res);
            }
        })
	};


	// !!! правильный пример AJAX
	function Ajax_add_priceitem(session_id) {

		var pricename = $("#pricename").val();
		var category_id = $("#category_id").val();
		var pricecode = $("#pricecode").val();
		var pricecodemkb = $("#pricecodemkb").val();
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
                pricecodemkb:pricecodemkb,
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
		var pricelistitemcodemkb = $("#pricelistitemcodemkb").val();
		var group = $("#group").val();
		var category_id = $("#category_id").val();

		$.ajax({
			url:"pricelistitem_edit_f.php",
			global: false,
			type: "POST",
			data:
			{
				pricelistitemname: pricelistitemname,
                pricelistitemcode: pricelistitemcode,
                pricelistitemcodemkb: pricelistitemcodemkb,
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

                setTimeout(function () {
                    location.reload();

                }, 500);
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

                setTimeout(function () {
                    location.reload();

                }, 500);
			}
		})
	}

	//Удаляем позицию в истории цен
    function deletePriceHistory(id, insure = 0) {

        var Data = {
            id: id,
            insure: insure
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

	//Меняем фактический график по шаблону планового
	//!!!пример массив checkbox
	function Ajax_change_shed(type) {
	    //console.log(type);

		var day = $("#SelectDayShedOptions").val();
		var month = $("#SelectMonthShedOptions").val();
		var year = $("#SelectYearShedOptions").val();

		var ignoreshed = $("input[name=ignoreshed]:checked").val();
		if (typeof (ignoreshed) == 'undefined') ignoreshed = 0;
        //console.log (ignoreshed);

		var all_fililas_chckd = $("input[name=fullAll]:checked").val();
        if (typeof (all_fililas_chckd) == 'undefined') all_fililas_chckd = 0;
        //console.log (all_fililas_chckd);

        var filials_chckd_arr = [];

        //if (all_fililas_chckd == 0) {
            $("input[name='filials_chckd[]']:checked").each(function () {
                filials_chckd_arr.push(parseInt($(this).val()));
            });
        //}
        //console.log (filials_chckd_arr);

		$.ajax({
			url:"sheduler_change_f.php",
			global: false,
			type: "POST",
			data:
			{
				day: day,
				month: month,
				year: year,
				ignoreshed: ignoreshed,
                all_fililas_chckd: all_fililas_chckd,
                filials_arr: filials_chckd_arr,
                type: type
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

	//
	function iWantThisDate(path){

        blockWhileWaiting (true);

		var iWantThisMonth =  $("#iWantThisMonth").val();
		var iWantThisYear =  $("#iWantThisYear").val();

		window.location.replace(path+'&m='+iWantThisMonth+'&y='+iWantThisYear);
	}

	//
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
	function manageScheduler(doc_name) {
        //console.log(doc_name);

        e = $('.manageScheduler');
        if (!e.is(':visible')) {
            e.show();
        } else {
            e.hide();
        }

        e2 = $('.nightSmena');
        if (!e2.is(':visible')) {
            e2.show();
        } else {
            e2.hide();
        }

        e3 = $('.fa-info-circle');
        if (e3.is(':visible')) {
            e3.hide();
        } else {
            e3.show();
        }


        e4 = $('.managePriceList');
        e5 = $('.cellManage');
        e6 = $('#DIVdelCheckedItems');

        if ((e4.is(':visible')) || (e5.is(':visible')) || (e6.is(':visible'))) {
            e4.hide();
            //e5.children().remove();
            e5.hide();
            e6.hide();
        } else {
            e4.show();
            e5.show();
            e6.show();
            //e5.append('<span style="font-size: 80%; color: #777;"><input type="checkbox" name="propDel[]" value="1"> пометить на удаление</span>');
            //меняет цвет
            //e5.parent().css({"background-color": "#ffbcbc"});
        }

        if (iCanManage) {
            iCanManage = false;
        } else {
        	iCanManage = true;
    	}

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
            	//console.log(res.data);

            	if (res.data == "true"){
					$("#manageMessage").html("Управление <span style='color: green;'>включено</span>");
					//выключаем контекстное меню
                    document.oncontextmenu = function() {return false;};
				}else{
                    $("#manageMessage").html("Управление <span style='color: red;'>выключено</span>");
                    //включаем контекстное меню
                    document.oncontextmenu = function() {};
				}
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

        var reqData = {
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
        };
        //console.log(reqData);

        $.ajax({
            url:"ajax_show_result_stat_zapis_f.php",
            global: false,
            type: "POST",
            data: reqData,
            cache: false,
            beforeSend: function() {
                $('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                $('#qresult').html(data);
            }
        })
    }

    //Выборка тех, у кого была консультация и они больше не пришли
    function Ajax_show_result_stat_lost_pervich(){
        $('#q2result').html('');

        let typeW = document.querySelector('input[name="typeW"]:checked').value;

        // var zapisAll = $("input[id=zapisAll]:checked").val();
        // if (zapisAll === undefined){
        //     zapisAll = 0;
        // }
        // // var zapisArrive = $("input[id=zapisArrive]:checked").val();
        // // if (zapisArrive === undefined){
        // //     zapisArrive = 0;
        // // }
        // // var zapisNotArrive = $("input[id=zapisNotArrive]:checked").val();
        // // if (zapisNotArrive === undefined){
        // //     zapisNotArrive = 0;
        // // }
        // //
        // // var zapisError = $("input[id=zapisError]:checked").val();
        // // if (zapisError === undefined){
        // //     zapisError = 0;
        // // }
        //
        // var zapisNull = $("input[id=zapisNull]:checked").val();
        // if (zapisNull === undefined){
        //     zapisNull = 0;
        // }

        // var fullAll = $("input[id=fullAll]:checked").val();
        // if (fullAll === undefined){
        //     fullAll = 0;
        // }
        //
        // var fullWOInvoice = $("input[id=fullWOInvoice]:checked").val();
        // if (fullWOInvoice === undefined){
        //     fullWOInvoice = 0;
        // }
        //
        // var fullWOTask = $("input[id=fullWOTask]:checked").val();
        // if (fullWOTask === undefined){
        //     fullWOTask = 0;
        // }
        //
        // var fullOk = $("input[id=fullOk]:checked").val();
        // if (fullOk === undefined){
        //     fullOk = 0;
        // }
        //
        // var statusAll = $("input[id=statusAll]:checked").val();
        // if (statusAll === undefined){
        //     statusAll = 0;
        // }
        //
        // var statusPervich = $("input[id=statusPervich]:checked").val();
        // if (statusPervich === undefined){
        //     statusPervich = 0;
        // }
        //
        // var statusInsure = $("input[id=statusInsure]:checked").val();
        // if (statusInsure === undefined){
        //     statusInsure = 0;
        // }
        //
        // var statusNight = $("input[id=statusNight]:checked").val();
        // if (statusNight === undefined){
        //     statusNight = 0;
        // }
        //
        // var statusAnother = $("input[id=statusAnother]:checked").val();
        // if (statusAnother === undefined){
        //     statusAnother = 0;
        // }
        //
        // var invoiceAll = $("input[id=invoiceAll]:checked").val();
        // if (invoiceAll === undefined){
        //     invoiceAll = 0;
        // }
        //
        // var invoicePaid = $("input[id=invoicePaid]:checked").val();
        // if (invoicePaid === undefined){
        //     invoicePaid = 0;
        // }
        //
        // var invoiceNotPaid = $("input[id=invoiceNotPaid]:checked").val();
        // if (invoiceNotPaid === undefined){
        //     invoiceNotPaid = 0;
        // }
        //
        // var invoiceInsure = $("input[id=invoiceInsure]:checked").val();
        // if (invoiceInsure === undefined){
        //     invoiceInsure = 0;
        // }

        let patientUnic = $("input[id=patientUnic]:checked").val();
        if (patientUnic === undefined){
            patientUnic = 0;
        }

        let reqData = {
            all_time: all_time,
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

            // zapisAll: zapisAll,
            // zapisArrive: zapisArrive,
            // zapisNotArrive: zapisNotArrive,
            // zapisError: zapisError,
            // zapisNull: zapisNull,
            //
            // fullAll: fullAll,
            // fullWOInvoice: fullWOInvoice,
            // fullWOTask: fullWOTask,
            // fullOk: fullOk,
            //
            // statusAll: statusAll,
            // statusPervich: statusPervich,
            // statusInsure: statusInsure,
            // statusNight: statusNight,
            // statusAnother: statusAnother,
            //
            // invoiceAll: invoiceAll,
            // invoicePaid: invoicePaid,
            // invoiceNotPaid: invoiceNotPaid,
            // invoiceInsure: invoiceInsure,

            patientUnic: patientUnic
        };
        //console.log(reqData);

        $.ajax({
            url:"ajax_show_result_stat_lost_pervich_f.php",
            global: false,
            type: "POST",
            data: reqData,
            cache: false,
            beforeSend: function() {
                $('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                $('#qresult').html(data);
                //$('#q2result').html('');

                Ajax_show_result_stat_lost_pervich_analiz();
            }
        })
    }

    //Функция анализа записей первичек
    function Ajax_show_result_stat_lost_pervich_analiz(){

        $(".zapis_id").each(function(){
            //console.log($(this).val());

            let zapis_id = $(this).val();

            let link = "lost_pervich_analiz.php";

            let reqData = {
                zapis_id: zapis_id
            };

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                //dataType: "JSON",
                data: reqData,
                cache: false,
                beforeSend: function() {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success:function(res){
                    //console.log(res);
                    //  $('#q2result').append(zapis_id);
                    // $('#q2result').append(res);

                    //console.log(res.data);

                    getInvoiceByZapis(res);

                    $('#q2result').html('');
                }
            })


        })

    }

    //Функция берёт наряды по записи
    function getInvoiceByZapis(zapis_id){

	    let link = "get_inoive_by_zapis_f.php";

        let reqData = {
            zapis_id: zapis_id
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
                //console.log(res.data);

                $('#q2result').append(res.data);
            }
        })
    }

    //Выборка статистики по скидкам и наценкам
    function Ajax_show_result_stat_percents(){

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
            all_time: all_time,
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
        //console.log(reqData);

        $.ajax({
            url:"ajax_show_result_stat_percents_f.php",
            global: false,
            type: "POST",
            data: reqData,
            cache: false,
            beforeSend: function() {
                $('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                $('#qresult').html(data);
            }
        })
    }

    //Выборка нарядов в лаборатории
    function Ajax_show_result_stat_lab_order(){

        var status = document.querySelector('input[name="status"]:checked').value;

        var reqData = {
            all_time: all_time,
            datastart:  $("#datastart").val(),
            dataend:  $("#dataend").val(),

            //Кто создал запись
            //creator:$("#search_worker").val(),
            //Пациент
            client:$("#search_client").val(),
            //К кому запись
            worker:$("#search_client4").val(),
            labor:$("#labor").val(),
            filial:$("#filial").val(),

            status:status
        };
        console.log(reqData);

        $.ajax({
            url:"ajax_show_result_stat_labor_order_f.php",
            global: false,
            type: "POST",
            data: reqData,
            cache: false,
            beforeSend: function() {
                $('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                $('#qresult').html(data);
            }
        })
    }

    //Выборка нарядов
    function Ajax_show_result_stat_invoice2(){

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
        };
        console.log(reqData);

        // $.ajax({
        //     url:"ajax_show_result_stat_zapis2_f.php",
        //     global: false,
        //     type: "POST",
        //     data: reqData,
        //     cache: false,
        //     beforeSend: function() {
        //         $('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
        //     },
        //     success:function(data){
        //         $('#qresult').html(data);
        //     }
        // })
    }



    // !!!!  Выборка отчета лабораторий
    function Ajax_show_result_stat_labor(){

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
            percent_cat:$("#percent_cat").val(),
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

                    blockWhileWaiting (false);
                    //Показываем график
                	//showChart ();

            }
        })
    }

    //Выборка отчёта по категориям 2
    function Ajax_show_result_main_report_category2(){

        blockWhileWaiting (true);

        var link = "ajax_show_result_main_report_category2_f.php";

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

                    setTimeout(function() {

                        document.location.reload(true);

                    }, 1000);

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

        var pervich = $("#pervich").val();
        //console.log(pervich);

		if (pervich > 0) {

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

            var kab = $("#kab").html();

            var worker = $("#search_client2").val();
            //console.log(worker);
            if ((typeof worker == "undefined") || (worker == "")) worker = 0;
            //console.log(worker);

			/*if ($("#pervich").prop("checked")){
			 var pervich = 1;
			 }else{
			 var pervich = 0;
			 }*/

            if ($("#insured").prop("checked")) {
                var insured = 1;
            } else {
                var insured = 0;
            }
            if ($("#noch").prop("checked")) {
                var noch = 1;
            } else {
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
                    author: author,
                    filial: filial,
                    kab: kab,
                    day: day,
                    month: month,
                    year: year,
                    start_time: start_time,
                    wt: wt,
                    worker: worker,
                    description: description,
                    contacts: contacts,
                    patient: patient,

                    pervich: pervich,
                    insured: insured,
                    noch: noch,

                    type: type
                },
                cache: false,
                beforeSend: function () {
                    //Блокируем кнопку OK
                    $("#Ajax_add_TempZapis").disabled = true;

                    $('#errror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                dataType: "json",
                // действие, при ответе с сервера
                success: function (data) {
                    //Разблокируем кнопку OK
                    setTimeout(function () {
                        $("#Ajax_add_TempZapis").disabled = false;
                    }, 200);
                    if (data.result == "success") {
                        $("#errror").html(data.data);
                        setTimeout(function () {
                            //console.log(window.location.href);

                            //window.location.replace(window_location_href+"#tabs-4");

                            //location.reload();

                            urlGetWork([], ["client_id"]);

                        }, 50);
                    } else {
                        $("#errror").html(data.data);
                    }
                }
            });
        }else{
            $("#pervich_status").html("<span style=\"color: red\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\" style=\"font-size: 100%;\"></i> Не выбрано - необходимо заполнить </span>");
		}
	};

	//Редактирование записи
	function Ajax_edit_TempZapis(type) {
		// получение данных из полей
		//var type =  $("#type").val();

        var pervich = $("#pervich").val();
        //console.log(pervich);

        if (pervich > 0) {

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

			/*if ($("#pervich").prop("checked")){
				var pervich = 1;
			}else{
				var pervich = 0;
			}*/


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
        }else{
            $("#pervich_status").html("<span style=\"color: red\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\" style=\"font-size: 100%;\"></i> Не выбрано - необходимо заполнить </span>");
        }
	};

	function Ajax_TempZapis_edit_Enter(id, enter) {

		if (enter == 8){
			var rys = confirm("Вы хотите удалить запись. \n\nВы уверены?");
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
                //console.log(insureapprove);
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
                //console.log(insure);

                stoim = stoim - (stoim * discount / 100);

            	//Убрали округление 2017-08-09
           		//stoim = Math.round(stoim / 10) * 10;
                //Изменили округление 2017-08-10
           		stoim = Math.round(stoim);
            }

            if (!changeItogPrice) {
                stoim = Number($(this).parent().find('.invoiceItemPriceItog').html());
			}

            //суммируем сумму в итоги
            if ((guarantee == 0) && (gift == 0)) {
                if ((typeof insure != "undefined") && (typeof insureapprove != "undefined")) {
                    if (insure != 0) {
                        //console.log(typeof(insure));

                        if (insureapprove != 0) {
                            SummIns += stoim;
                        }
                    } else {
                        Summ += stoim;
                    }
                } else {
                    Summ += stoim;
                }
            }

            var invoiceItemPriceItog = stoim;
            var ishod_price = Number($(this).parent().find('.invoiceItemPriceItog').html());

            if (ishod_price == 0) {
            	//2018-03-13 попытка разобраться с гарантийной ценой для зарплаты
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
        //Убрали округление 2017-08-09
        //Summ = Math.round(Summ/10) * 10;
        //Изменили округление 2017-08-10
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

	//Подсчёт суммы для счёта с учетом абонемента
	function calculatePaymentAbon (){

	    //Общее кол-во минут, оставшееся для использования на всех абонементах
		var SummAbon = 0;
		//В итоге, сколько можно срезать минут с абонемента
		var rezSumm = 0;

		//Минуты, которые хотим исползовать сейчас
		var min_count = Number($("#min_count").val());
        //console.log(SummAbon);

        $(".abon_pay").each(function() {
            SummAbon += Number($(this).html());
		});
        //console.log(SummAbon);

        //Если общая сумма минут больше чем хотим использовать
        if (SummAbon > min_count){
            rezSumm = min_count;
		}else{
            rezSumm = SummAbon;
		}
		//console.log(rezSumm);

        $("#summ").html(rezSumm);

	}

	//Смена исполнителя для расчета
    function changeWorkerInCalculate (){

        var link = "search_user_f.php";

        var reqData = {
            workerFIO: $("#search_client2").val()
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

                if(res.result == "success") {
                    $("#worker").val(res.data.id);
                    $("#calculate_type").val(res.data.permissions);

                    //console.log(res.msg);
                    $('#errrror').html(res.msg);

                    fillCalculateRez();
                }else{
                    //console.log(res.msg);
                    $('#errrror').html(res.msg);
				}
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
        //console.log('Ok');

		var invoice_type =  $("#invoice_type").val();
		//console.log(invoice_type);

		var link = "fill_invoice_stom_from_session_f.php";

		if ((invoice_type == 6) || (invoice_type == 10) || (invoice_type == 7)){
			link = "fill_invoice_cosm_from_session_f.php";
		}
		// if (invoice_type == 88){
		// 	//link = "fill_invoice_free_from_session_f.php";
		// 	link = "fill_invoice_cosm_from_session_f.php";
		// }

        var adv = $("#adv").val();
		//console.log(adv);


        //Время записи (для корректировки цен)
        var ztime = $("#ztime").val();

        if (ztime == undefined){
            ztime = 0;
        }
        //console.log(ztime);

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
                client_insure: $("#client_insure").val(),
                adv: adv,
                ztime: ztime
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

		if ((invoice_type == 6) || (invoice_type == 10) || (invoice_type == 7)){
			link = "fill_calculate_cosm_from_session_f.php";
		}
		// if (invoice_type == 88){
		// 	link = "fill_calculate_free_from_session_f.php";
		// }
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

	///Удалить текущую позицию
	function deleteInvoiceItem(ind, dataObj){
		//console.log(dataObj.getAttribute("invoiceitemid"));

        var invoice_type = $("#invoice_type").val();

        var link = "delete_invoice_item_from_session_f.php";

        // if (invoice_type == 88){
        //     link = "delete_invoice_free_item_from_session_f.php";
        // }

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

	//Изменить челюсть (верхняя/нижняя) у всех
	function jawselectInvoice(jaw_select){
		//console.log(discount);

        var invoice_type = $("#invoice_type").val();

        var link = "add_jawselect_price_id_in_invoice_f.php";
        if (invoice_type == 88){
            link = "add_jawselect_price_id_in_invoice_free_f.php";
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
                jaw_select: jaw_select,
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

	//Изменить челюсть (верхняя/нижняя) у этого зуба
	function jawselectItemInvoice(ind, key, jaw_select){

		var invoice_type = $("#invoice_type").val();

		var link = 'add_jawselect_price_id_in_item_invoice_f.php';

		if (invoice_type == 88){
            link = "add_jawselect_price_id_in_item_invoice_free_f.php";
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
                jaw_select: jaw_select,
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
        // if (invoice_type == 88){
        //     link = "add_invoice_free_in_session_f.php";
        // }

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
		//console.log('Ok');

		var link = "add_price_id_stom_in_invoice_f.php";

		if ((type == 6) || (type == 10) || (type == 7)){
			link = "add_price_id_cosm_in_invoice_f.php";
		}
		if (type == 88){
			//link = "add_price_id_free_in_invoice_f.php";
            link = "add_price_id_cosm_in_invoice_f.php";
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
				worker: $("#worker").val(),

                type: type
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
				data: filial
			},
			success:function(data){
				// $("#status_notes").html(data);
				//console.log("Ok");
				location.reload();
			}
		});
	}

	//Сменить филиал в оплате
	function changePaymentFilial(payment_id, filial_id){
		ajax({
			url:"change_payment_filial.php",
			//statbox:"status_notes",
			method:"POST",
			data:
			{
                payment_id: payment_id,
                filial_id: filial_id
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
	function showInvoiceAdd(invoice_type, mode, adv){
		//console.log(mode);
		//console.log(adv);

		$('#overlay').show();

		var Summ = $("#calculateInvoice").html();
		var SummIns = 0;
		var SummInsBlock = '';

		if (invoice_type == 5){
			SummIns = $("#calculateInsInvoice").html();
			SummInsBlock = '<div>Страховка: <span class="calculateInsInvoice">'+SummIns+'</span> руб.</div>';
		}

		var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_add(\'add\', '+adv+')">';

		// if (invoice_type == 88){
         //    buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_free_add(\'add\')">';
		// }
        //
		// if (invoice_type == 7){
         //    buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_add(\'add\')">';
		// }

		if (mode == 'edit'){
			buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_add(\'edit\', '+adv+')">';
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

    //Показываем блок с суммами и кнопками для возврата
    function showRefundAdd(mode){
        //console.log(mode);

		hideAllErrors();

		//Сумма к возврату
        var refundSumm = Number($("#refundSumm").html());
        //console.log(refundSumm);

        //Сумма на которую оплатили наряд
        var payedSumm = Number($("#calculateInvoice").html());
        //console.log(payedSumm);

        //Комментарий
        var comment = $("#comment").val();
        //console.log(comment);

		//Сумма, к вычету из ЗП
        var salaryDeductionSumm = Number($("#salaryDeductionSumm").html());
        //console.log(salaryDeductionSumm);

        //Отметка, будет ли вычет из ЗП
        var salaryDeductionCheck = $("input[name=salary_deduction_checkbox]:checked").val();
        if(typeof salaryDeductionCheck == "undefined") salaryDeductionCheck = 0;
        //console.log(salaryDeductionCheck);

        //Табель в который надо будет добавить вычет
        var tabel = $("#selectedTabelID").val();
        //console.log(tabel);

        if (refundSumm > 0) {
            if (comment.length > 0) {
            	 if ((salaryDeductionCheck == 1) && (tabel == 0)){
                     $("#errror").html('<span class="query_neok" style="padding-top: 0">Ошибка #41. Не выбран табель, куда добавить вычет.</span>');
				 }else{
                     if ((salaryDeductionCheck == 1) && (salaryDeductionSumm <= 0)){
                         $("#errror").html('<span class="query_neok" style="padding-top: 0">Ошибка #42. Выбран вычет из ЗП, Сумма при этом некорректна.</span>');
					 }else {
                         var salaryDeductionStr = 'Вычета из ЗП <b>не отмечено</b>';

                         if ((salaryDeductionCheck == 1) && (tabel != 0) && (salaryDeductionSumm > 0)) {
                             salaryDeductionStr = '<div style="margin: 5px 10px;">Будет оформлен вычет из ЗП<br> на сумму: <span class="calculateInvoice">' + salaryDeductionSumm + '</span> руб.</div><div style="margin: 5px 10px;">Вычет будет добавлен в <b>Табель #'+tabel+'</b></div>';
                         }

                         $('#overlay').show();

                         var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_refund_add(' + refundSumm + ', ' + payedSumm + ', \'' + comment + '\', ' + salaryDeductionCheck + ', ' + tabel + ', ' + salaryDeductionSumm + ')">';

                         // Создаем меню:
                         var menu = $('<div/>', {
                             class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
                         }).css({"height": "221px"})
                             .appendTo('#overlay')
                             .append(
                                 $('<div/>')
                                     .css({
                                         "height": "100%",
                                         "border": "1px solid #AAA",
                                         "position": "relative"
                                     })
                                     .append('<span style="margin: 5px;"><i>Проверьте и нажмите сохранить</i></span>')
                                     .append(
                                         $('<div/>')
                                             .css({
                                                 "position": "absolute",
                                                 "width": "100%",
                                                 "margin": "auto",
                                                 "top": "-50px",
                                                 "left": "0",
                                                 "bottom": "0",
                                                 "right": "0",
                                                 "height": "50%"
                                             })
                                             .append('<div style="margin: 10px;">Сумма к возврату: <span class="calculateInvoice">' + refundSumm + '</span> руб.</div>')
                                             .append(salaryDeductionStr)
                                     )
                                     .append(
                                         $('<div/>')
                                             .css({
                                                 "position": "absolute",
                                                 "bottom": "2px",
                                                 "width": "100%",
                                             })
                                             .append(buttonsStr +
                                                 '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                                             )
                                     )
                             );

                         menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
                     }
                 }
            } else {
                $("#errror").html('<span class="query_neok" style="padding-top: 0">Ошибка #39. Заполните комментарий.</span>');
            }
        }else{
            $("#errror").html('<span class="query_neok" style="padding-top: 0">Ошибка #40.  Некорректная сумма на возврат.</span>');
        }
    }

    //Показываем блок с суммами и кнопками для добавления возврата денег
    function showWithdrawAdd (mode){
        //console.log(mode);

		hideAllErrors();

        var Summ = $("#summ").val();
        var SummType = $("#summ_type").val();
        var filial = $("#filial").val();
        var comment = $("#comment").val();

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
                    filial: filial,
                    comment: comment
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){
                    //$('#overlay').show();

                    if (mode == 'add'){
                        Ajax_withdraw_add('add');
                    }

                    // if (mode == 'edit'){
                    //     Ajax_order_add('edit');
                    // }

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
        Summ =  Summ.replace(',', '.');
        //console.log(Summ);
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
                    summ: Summ
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

        var filial_id = $('#filial_id').val();

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

                    var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_cert_cell('+id+', '+cell_price+', '+filial_id+')">';

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
                                    "position": "relative"
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
                                            "height": "50%"
                                        })
                                        .append('<div style="margin: 10px;">Сумма: <span class="calculateInvoice">'+cell_price+'</span> руб.</div>')
                                )
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "bottom": "2px",
                                            "width": "100%"
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

	//Показываем блок с суммами и кнопками Для абонемента
    function showAbonCell(id){
        //console.log(id);
        hideAllErrors ();

        var cell_price = $('#cell_price').val();
        //console.log(cell_price);

        var filial_id = $('#filial_id').val();

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

                    var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_abon_cell('+id+', '+cell_price+', '+filial_id+')">';

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
                                    "position": "relative"
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
                                            "height": "50%"
                                        })
                                        .append('<div style="margin: 10px;">Сумма: <span class="calculateInvoice">'+cell_price+'</span> руб.</div>')
                                )
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "bottom": "2px",
                                            "width": "100%"
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
							).css({
                            "position": "absolute",
                            "width": "405px",
                            "z-index": "1"
                        })
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

    //Показываем блок для поиска и добавления именного сертификата
    function showCertNamePayAdd(){

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
								'<div id="search_cert_name_input_target">'+
								'</div>'
							).css({
                            "position": "absolute",
                            "width": "405px",
                            "z-index": "1"
                        })
                    )
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "bottom": "2px",
                                "width": "100%",
                            })
                            .append(buttonsStr+
                                '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'#search_cert_name_input\').append($(\'#search_cert_name_input_target\').children()); $(\'.center_block\').remove(); $(\'#search_result_cert_name\').html(\'\'); $(\'#search_cert_name\').val(\'\');">'
                            )
                    )
            );

        $('#search_cert_name_input_target').append($('#search_cert_name_input').children());

        menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

    }

    //Показываем блок для поиска и добавления абонемента
    function showAbonPayAdd(){

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
                        "position": "relative"
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
                                "height": "50%"
                            })
                            .append(
								'<div id="search_abon_input_target">'+
								'</div>'
							).css({
                            "position": "absolute",
                            "width": "405px",
                            "z-index": "1"
                        })
                    )
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "bottom": "2px",
                                "width": "100%"
                            })
                            .append(buttonsStr+
                                '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'#search_abon_input\').append($(\'#search_abon_input_target\').children()); $(\'.center_block\').remove(); $(\'#search_result_abon\').html(\'\'); $(\'#search_abon\').val(\'\');">'
                            )
                    )
            );

        $('#search_abon_input_target').append($('#search_abon_input').children());

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

    //Добавляем/редактируем в базу наряд из сессии (сама функция)
    function Ajax_invoice_add_f(invoice_type, mode, zapis_id, adv){

        let invoice_id = 0;
        let comment = "";

        let link = "invoice_add_f.php";

        if (mode == 'edit'){
            link = "invoice_edit_f.php";
            invoice_id = $("#invoice_id").val();
        }

        if (adv){
            comment = $("#comment").val();
        }

        let Summ = $("#calculateInvoice").html();
        let SummIns = 0;

        let SummInsStr = '';

        if (invoice_type == 5){
            SummIns = $("#calculateInsInvoice").html();
            SummInsStr = '<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">'+
                'Страховка:<br>'+
                '<span class="calculateInsInvoice" style="font-size: 13px">'+SummIns+'</span> руб.'+
                '</div>';
        }

        let client = $("#client").val();

        let reqData = {
            client: $("#client").val(),
            filial: $("#filial").val(),
            worker: $("#worker").val(),

            zapis_id: $("#zapis_id").val(),

            summ: Summ,
            summins: SummIns,

            invoice_type: invoice_type,
            invoice_id: invoice_id,

            adv: adv,
            comment: comment,

            cert_name_id: $("#cert_name_id").val(),
            cert_name_old_id: $("#cert_name_old_id").val()
		};

        if (zapis_id != 0) {
            reqData.zapis_id_new = zapis_id;
        }
        //console.log(reqData);

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
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                $('.center_block').remove();
                $('#overlay').hide();

                if(res.result == "success"){
                    $('#data').hide();
                    if (adv){
                        $('#invoices').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">' +
                            '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Добавлен/отредактирован предварительный расчёт</li>' +
                            '<li class="cellsBlock" style="width: auto;">' +
                            '<a href="invoice_advance.php?id=' + res.data + '" class="cellName ahref">' +
                            '<b>Пред. расчёт #' + res.data + '</b><br>' +
                            '</a>' +
                            '<div class="cellName">' +
                            '<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">' +
                            'Сумма:<br>' +
                            '<span class="calculateInvoice" style="font-size: 13px">' + Summ + '</span> руб.' +
                            '</div>' +
                            SummInsStr +
                            '</div>' +
                            '</li>' +
                            '</ul>');
                    }else {
                        $('#invoices').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">' +
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
                            '</ul>');
                    }
                }else{
                    if (mode == 'edit'){
                        alert(res.data);
                        setTimeout(function () {
                            //window.location.replace('invoice_edit.php?id=' + id);
                            location.reload();
                        }, 100);
                        $('#errror').html('<div class="query_neok">'+res.data+'</div>');
                    }else{
                        $('#errror').html(res.data);
                    }
                }
            }
        });
	}

	//Добавляем/редактируем в базу наряд из сессии
	function Ajax_invoice_add(mode, adv){
		//console.log(mode);

        let invoice_type = $("#invoice_type").val();

        if ((invoice_type == 7) && (mode != 'edit')){
        	//console.log("Добавляем запись");
            //console.log($("#scheduler_json").val());
            //console.log(JSON.parse($("#scheduler_json").val()));

            let link = "zapis_free_add_f.php";

            let reqData = JSON.parse($("#scheduler_json").val());
            //console.log(reqData);

			//Добавим запись для пациента "с улицы"
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
                // действие, при ответе с сервера
                success: function(res){
                    //console.log(res);

                    if(res.result == "success"){

                        Ajax_invoice_add_f(invoice_type, mode, res.data, adv)

                    }else{
                        $('#errror').html(res.data);
                    }
                }
            });


		}else {
            Ajax_invoice_add_f(invoice_type, mode, 0, adv)
        }
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
            //calculate_id = $("#invoice_id").val();
		}

		if (mode == 'reset'){
			link = "fl_calculate_reset_f.php";
            //calculate_id = $("#invoice_id").val();
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
		var calculate_type = $("#calculate_type").val();

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
                calculate_type: calculate_type,

                calculate_id: calculate_id
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
                        window.location.replace('invoice.php?id='+invoice_id+'');
                    }
				}else{
					$('#errror').html(res.data);
				}
			}
		});
	}

    //Добавляем/редактируем в базу возврат средств на счет
    function Ajax_refund_add(refundSumm, payedSumm, comment, salaryDeductionCheck, tabel, salaryDeductionSumm){
		//Сумма к возврату на счет
        //console.log(refundSumm);
        //Сумма на которую оплатили наряд
        //console.log(payedSumm);
        //Комментарий
        //console.log(comment);
        //Отметка, будет ли вычет из ЗП
        //console.log(salaryDeductionCheck);
		//ID табеля
        //console.log(tabel);
        //Сумма, к вычету из ЗП
        //console.log(salaryDeductionSumm);

		//Соберём все отмеченные позиции, за которые вернули деньги
		var checkedItems = {};

        $(".position_check").each(function(){

            var checked_status = $(this).is(":checked");

            if (checked_status){
                var salaryDeductionItemSumm = Number($(this).parent().parent().find(".salaryDeductionItemPriceItog").html());
                //console.log(salaryDeductionItemSumm);

                //checkedItems.push($(this).attr("item_id"));
                checkedItems[$(this).attr("item_id")] = salaryDeductionItemSumm;
            }
        });
        //console.log(checkedItems);

        var link = "fl_refund_add_f.php";

        var reqData = {
            invoice_id: $("#invoice_id").val(),
            zapis_id: $("#zapis_id").val(),
            client_id: $("#client_id").val(),
			worker_id: $("#worker_id").val(),
            refundSumm: refundSumm,
			payedSumm: payedSumm,
			comment: comment,
			salaryDeductionCheck: salaryDeductionCheck,
			tabel_id: tabel,
			salaryDeductionSumm: salaryDeductionSumm,
            checkedItems: checkedItems
        };
        //console.log(reqData);

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
            // действие, при ответе с сервера
            success: function(res){
                console.log(res);
                // $('#errror').html(res);

                $('.center_block').remove();
                $('#overlay').hide();

                if(res.result == "success"){
                    $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
                        '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Возврат средств на счёт выполнен</li>'+
                        '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
                        '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
                        '<a href="finance_account.php?client_id='+$("#client_id").val()+'" class="b">Управление счётом</a>'+
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

        var summ_type = document.querySelector('input[name="summ_type"]:checked').value;
        //console.log(summ_type);
        var expirationDate =  $('#expirationDate').val();

        var reqData = {
            cert_id: id,
            cell_price: cell_price,
            office_id: office_id,
            cell_date: $('#iWantThisDate2').val(),
            summ_type: summ_type,
            expirationDate: expirationDate
		};
        //console.log(reqData);

		$.ajax({
			url: "cert_cell_f.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data: reqData,
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

	//Продаём абонемент по базе
	function Ajax_abon_cell(id, cell_price, filial_id){

        var summ_type = document.querySelector('input[name="summ_type"]:checked').value;
        //console.log(summ_type);
        //var expirationDate =  $('#expirationDate').val();

		$.ajax({
			url: "abon_cell_f.php",
			global: false,
			type: "POST",
			dataType: "JSON",
			data:
			{
                abonement_id: id,
                cell_price: cell_price,
                filial_id: filial_id,
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
											'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Абонемент продан</li>'+
									'</ul>');
                    setTimeout(function () {
                        window.location.replace('abonement.php?id='+id+'');
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

	//Удалим выдачу именного сертификата
	function Ajax_cert_name_celling_del(id){

        let rys = false;

        rys = confirm("Вы собираетесь отменить выдачу сертификата.\nВы уверены?");

        if (rys) {

            let link = "cert_name_cell_dell_f.php";

            let Data = {
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
                    // console.log(res.data);

					 if(res.result == "success"){
					    //$('#data').hide();
					    $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
					    '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Выдача отменена</li>'+
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

	//Удалим продажу абонемента
	function Ajax_abonement_celling_del(id){

        var rys = false;

        rys = confirm("Вы собираетесь отменить продажу абонемента.\nВы уверены?");

        if (rys) {

            var link = "abonement_cell_dell_f.php";

            var Data = {
                abonement_id: id
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

	//Добавим сертификат в оплату
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

	//Добавим сертификат именной в наряд
	function Ajax_cert_name_add_pay(id, num){

        $('#overlay').hide();
        $('#search_cert_name_input').append($('#search_cert_name_input_target').children());
        $('.center_block').remove();
        $('#search_result_cert_name').html('');
        $('#search_cert_name').val('');

        //$('.have_money_or_not').show();
        $('#certNameBlockButton').hide();
        $('#certNameBlockChosen').show();

        $('#cert_name_id').val(id);

        $('#certNameBlockChosen').append('Использован <a href="certificate_name.php?id='+id+'" class="ahref" style="" target="_blank" rel="nofollow noopener"><b>именной серт-т '+num+'</b></a> ' +
            '<span style="cursor: pointer; color: red; font-size: 110%; margin-left: 10px; background-color: #CCC; padding: 0px 7px; border: 1px solid red;" onclick="certNameBlockChosen_delete('+id+')"><i class="fa fa-times" aria-hidden="true" style=""></i></span>');

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

                    calculatePaymentCert ();

				}else{
					//$('#errror').html(res.data);
				}
			}
		});*/
	}

	//Удалить именной сертификат из наряда
    function certNameBlockChosen_delete(id){

        $('#certNameBlockButton').show();
        $('#certNameBlockChosen').html('');
        $('#certNameBlockChosen').hide();

        $('#cert_name_id').val(0);
    }

	//Добавим сертификат именной к выдаче
	function Ajax_cert_name_add_cell(id){

        //$('#overlay').hide();
        //$('#search_cert_input').append($('#search_cert_input_target').children());
        //$('.center_block').remove();
        $('#search_result_fcertname2').html('');
        //$('#search_cert').val('');

        //$('.have_money_or_not').show();
        //$('#certs_result').show();
        //$('#showCertPayAdd_button').hide();

		// $.ajax({
		// 	url: "FastSearchCertOne.php",
		// 	global: false,
		// 	type: "POST",
		// 	dataType: "JSON",
		// 	data:
		// 	{
        //         id: id,
        //     },
		// 	cache: false,
		// 	beforeSend: function() {
		// 		//$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
		// 	},
		// 	// действие, при ответе с сервера
		// 	success: function(res){
		// 		//console.log(res);
		// 		$('.center_block').remove();
		// 		$('#overlay').hide();
        //
		// 		if(res.result == "success"){
		// 			//$('#data').hide();
        //             $('#certs_result').append(res.data);
        //
        //             calculatePaymentCert ();
        //
		// 		}else{
		// 			//$('#errror').html(res.data);
		// 		}
		// 	}
		// });
	}

	//Добавим абонемент в оплату
	function Ajax_abon_add_pay(id){

        $('#overlay').hide();
        $('#search_abon_input').append($('#search_abon_input_target').children());
        $('.center_block').remove();
        $('#search_result_abon').html('');
        $('#search_abon').val('');

        //$('.have_money_or_not').show();
        $('#abons_result').show();
        $('#showAbonPayAdd_button').hide();

		$.ajax({
			url: "FastSearchAbonOne.php",
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
			success: function(res){
				//console.log(res);

				$('.center_block').remove();
				$('#overlay').hide();

				if(res.result == "success"){
					//$('#data').hide();
                    $('#abons_result').append(res.data);

                    calculatePaymentAbon();

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

	//Очистить все абонементы
	function abonsResultDel(){

        $('#abons_result').hide();
        $('#showAbonPayAdd_button').show();

        $('#abons_result').html(
			'<tr>'+
				'<td><span class="lit_grey_text">Номер</span></td>'+
					'<td><span class="lit_grey_text">Всего минут</span></td>'+
					'<td><span class="lit_grey_text">Осталось</span></td>'+
				'<td style="text-align: center;"><i class="fa fa-times" aria-hidden="true" title="Удалить"></i></td>'+
            '</tr>'
		);

        $('#summ').html(0);

	}

    function Ajax_MaterialCostDelete (id){
        //console.log();

        let rys = false;

        rys = confirm("Вы хотите удалить расход материалов. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            let link = "fl_material_cost_delete_f.php";
            //console.log(link);

            let reqData = {
                id: id
            };
            //console.log(reqData);

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
                    // console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        alert(res.data);
                        //$("#overlay").hide();
                        $('#errrror').html('<div class="query_neok">' + res.data + '</div>');
                    }
                }
            });
        }
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
		var invoice_id =  $("#invoice_id").val();
		//console.log(invoice_id);
		var date_in = $("#date_in").val();
		//console.log(date_in);

        var comment = $("#comment").val();
        //console.log(comment);

        var org_pay = $("input[name=org_pay]:checked").val();

        if (org_pay === undefined){
            org_pay = 0;
        }

        if ((mode == 'add') && (invoice_id != 0)){
            paymentStr = '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
                '<a href= "payment_add.php?invoice_id='+invoice_id+'" class="b">Оплатить наряд #'+invoice_id+'</a>'+
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

	//Добавляем/редактируем в базу выдачу
	function Ajax_withdraw_add(mode){
		//console.log(mode);

        var withdraw_id = 0;

		var link = "withdraw_add_f.php";

		var paymentStr = '';

		if (mode == 'edit'){
			link = "edit_withdraw_f.php";
            withdraw_id = $("#withdraw_id").val();
		}

        var Summ = Number($("#summ").val());
        var availableBalance = Number($("#availableBalance").html());

        if (Summ > availableBalance){
			$("#errror").html('<span class="query_neok" style="padding-top: 0">Ошибка #38. Нельзя вернуть сумму больше чем доступно на балансе.</span>');
		}else {
            //console.log(availableBalance);
            //var SummType =  $("#summ_type").val();
            var SummType = document.querySelector('input[name="summ_type"]:checked').value;
            var office_id = $("#filial").val();

            var client_id = $("#client_id").val();
            //var invoice_id =  $("#invoice_id").val();
            //console.log(invoice_id);
            var date_in = $("#date_in").val();
            //console.log(date_in);

            var comment = $("#comment").val();
            //console.log(comment);

            var org_pay = $("input[name=org_pay]:checked").val();

            if (org_pay === undefined) {
                org_pay = 0;
            }

            // if (invoice_id != 0){
            //    paymentStr = '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
            //        '<a href= "payment_add.php?invoice_id='+invoice_id+'" class="b">Оплатить наряд #'+invoice_id+'</a>'+
            //        '</li>';
            // }

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    client_id: client_id,
                    office_id: office_id,
                    summ: Summ,
                    summtype: SummType,
                    date_in: date_in,
                    comment: comment,
                    org_pay: org_pay,

                    withdraw_id: withdraw_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);
                    $('.center_block').remove();
                    $('#overlay').hide();

                    if (res.result == "success") {
                        //$('#data').hide();
                        $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">' +
                            '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Добавлена выдача</li>' +
                            '<li class="cellsBlock" style="width: auto;">' +
                            '<a href="withdraw.php?id=' + res.data + '" class="cellName ahref">' +
                            '<b>Выдача #' + res.data + '</b><br>' +
                            '</a>' +
                            '<div class="cellName">' +
                            '<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">' +
                            'Сумма:<br>' +
                            '<span class="calculateInvoice" style="font-size: 13px">' + Summ + '</span> руб.' +
                            '</div>' +
                            '</div>' +
                            '</li>' +
                            paymentStr +
                            '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">' +
                            '<a href="finance_account.php?client_id=' + client_id + '" class="b">Управление счётом</a>' +
                            '</li>' +
                            '</ul>');
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
	}

	//Добавляем/редактируем в базу новое замечание сотруднику
	function Ajax_remarkToEmployee_add(mode){
		//console.log(mode);

        let remark_to_employee_id = 0;

		let link = "remark_to_employee_add_f.php";

		if (mode == 'edit'){
			link = "remark_to_employee_edit_f.php";
            remark_to_employee_id = $("#remark_to_employee_id").val();
		}

        let reqData = {
            date_in: $("#date_in").val(),
            worker: $("#search_client4").val(),
            comment: $("#comment").val()
        };
		//console.log(reqData);

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
                    $('#errror').html(res.data);
                    setTimeout(function () {
                        window.location.href = "remarks_to_employees.php";
                    }, 1000);
                } else {
                    $('#errror').html(res.data);
                }
            }
        })
	}

	function selectThisTabelForSalaryDeduction(worker_id){
		//console.log(worker_id);

        var tabelForAdding = $('input[name=tabelForAdding]:checked').val();
        //console.log(tabelForAdding);

        if (tabelForAdding != undefined){

            $("#selectedTabelForSalaryDeduction").html("<b>Табель #"+tabelForAdding+"</b> ");
            $("#selectedTabelID").val(tabelForAdding);

            blockWhileWaiting(false);
		}/*else{
            console.log(tabelForAdding);
		}*/


	}

	//Показываем меню с табелями для тогор, чтобы из них выбрать
    function menuForAddSalaryDeductionINTabel(res, worker_id){
        //console.log(res);

        //var buttonsStr = '';
        var buttonsStr = '<input type="button" class="b" value="OK" onclick="selectThisTabelForSalaryDeduction(' + worker_id + ')">';


        // Создаем меню:
        var menu = $('<div/>', {
            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
        }).css({
            "top": "-170px",
            "height": "fit-content",
            "width": "45%",
            "background-color": "rgb(195, 194, 194)"
        })
            .appendTo('#overlay')
            .append(
                $('<div/>')
                    .css({
						/*"height": "100%",*/
                        "border": "1px solid #AAA",
                        "position": "relative",
                        "background-color": "rgb(245, 245, 245)",
                        "padding": "10px"
                    })
                    //.append('<span style="margin: 5px;"><i>Новый табель</i></span>')
                    .append(
                        $('<div/>')
                            .css({
								/*"position": "absolute",*/
                                "width": "100%",
                                "margin": "auto",
                                "top": "-10px",
                                "left": "0",
                                "bottom": "0",
                                "right": "0",
                                "height": "50%"
                            })
                            .append('<div style="margin: 0px;">'+res+'</div>')
                    )
                    .append(
                        $('<div/>')
                            .css({
								/*"position": "absolute",*/
                                "bottom": "2px",
                                "width": "100%"
                            })
                            .append(buttonsStr+
                                '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                            )
                    )
            );

        menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
    }

    //Выбираем табель, куда добавим вычет
    function selectTabelForSalaryDeduction (worker_id){

        var link = "fl_getTabels_f.php";

        var reqData = {
            type_id: 0,
            worker_id: worker_id,
            filial_id: 0
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            //dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success: function (res) {
                //console.log (res);
                //console.log (res.length);

                if (res.length > 0) {
                    $('#overlay').show();

                    menuForAddSalaryDeductionINTabel(res, worker_id);
                }else{
                    //$('#errrror').html('<div class="query_neok">Ошибка #34. Ничего не выбрано. Обновите выбор РЛ</div>');
                }
            }
        })
    }

	//Добавляем/редактируем в базу расходный ордер
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
        Summ =  Summ.replace(',', '.');
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

        var additional_info = $("#additional_info").val();
        //console.log(additional_info);

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
                additional_info: additional_info,

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
                    if (mode == 'add'){


                        date_arr = date_in.split(".");
                        //console.log(date_arr);

					    $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
											'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Добавлен/отредактирован расходный ордер</li>'+
											'<li class="cellsBlock" style="width: auto;">'+
												'<div class="cellName">'+
													'<b>Расходный ордер #'+res.data+'</b><br>'+
												'</div>'+
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
                        						'<a href="fl_give_out_cash_add.php?filial_id='+office_id+'&d='+date_arr[0]+'&m='+date_arr[1]+'&y='+date_arr[2]+'" class="b">Добавить ещё</a>'+
					                        '</li>'+
										'</ul>');
                    }else{
                        location.reload();
                    }
				}else{
					$('#errror').html(res.data);
				}
			}
		});
	}

    //Удаление(блокировка) расходного ордера
    function fl_deleteGiveout_cash (order_id){

        var rys = false;

        rys = confirm("Вы хотите удалить расходный ордер. \n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "fl_delete_give_out_cash_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: order_id
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

    //Удаление(блокировка)
    function announcingDelete (ann_id, status){

        let rys = false;

        if (status == 9) {
            rys = confirm("Вы собираетесь удалить объявление. \n\nВы уверены?");
        }

        if (status == 8) {
            rys = confirm("Вы собираетесь закрыть объявление. \n\nВы уверены?");
        }

        if (status == 0) {
            rys = confirm("Восстановить объявление?");
        }

        if (rys) {
            $.ajax({
                url: "delete_announce_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    ann_id: ann_id,
                    status: status
                },
                cache: false,
                beforeSend: function () {
                    // $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success: function (data) {
                    //$('#errrror').html(data);
                    //setTimeout(function () {
                        location.reload();
                        //console.log('client.php?id='+id);
                    //}, 100);
                }
            })
        }
    }

    //Удаление из банка
    function fl_deleteInBank (item_id){

        var rys = false;

        rys = confirm("Вы собираетесь удалить документ. \n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "fl_delete_in_bank_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: item_id
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

    //Удаление АН
    function fl_deleteToDirector (item_id){

        var rys = false;

        rys = confirm("Вы собираетесь удалить документ. \n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "fl_delete_to_director_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: item_id
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

    //Удаление(блокировка) посещения солярия
    function fl_deleteSolar (id){

        var rys = false;

        rys = confirm("Вы хотите удалить документ. \n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "fl_delete_solar_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
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

    //Удаление(блокировка) релизации
    function fl_deleteRealiz (id){

        var rys = false;

        rys = confirm("Вы хотите удалить документ. \n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "fl_delete_realiz_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
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

    //Восстановление(разблокировка) расходного ордера
    function fl_reopenGiveout_cash (order_id){

        var rys = false;

        rys = confirm("Вы хотите восстановить расходный ордер. \n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "fl_reopen_give_out_cash_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: order_id
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

    //Восстановление(разблокировка) посещения солярия
    // function fl_reopenSolar (order_id){
    //
    //     var rys = false;
    //
    //     rys = confirm("Вы хотите восстановить расходный ордер. \n\nВы уверены?");
    //
    //     if (rys) {
    //         $.ajax({
    //             url: "fl_reopen_give_out_cash_f.php",
    //             global: false,
    //             type: "POST",
    //             dataType: "JSON",
    //             data: {
    //                 id: order_id
    //             },
    //             cache: false,
    //             beforeSend: function () {
    //                 // $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
    //             },
    //             success: function (data) {
    //                 $('#errrror').html(data);
    //                 setTimeout(function () {
    //                     window.location.replace('');
    //                     //console.log('client.php?id='+id);
    //                 }, 100);
    //             }
    //         })
    //     }
    // }


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

		 // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'spec_koeff');
            }
        });*/

        $("body").on("click", "#spec_koeff", function(event){
            //console.log(1);

            // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'spec_koeff');
            }
        });

        $("body").on("click", "#jaw_select", function(event){
            //console.log(1);

            // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'jaw_select');
            }
        });

        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
        /*$("#guarantee").click(function(event) {

		 // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'guarantee');
            }
        });*/

        /*$("body").on("click", "#guarantee", function(){

		 // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'guarantee');
            }
        });*/

        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
        /*$("#gift").click(function(event) {

		 // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'gift');
            }
        });*/

        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
        /*$("#guaranteegift").click(function(event) {

		 // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'guaranteegift');
            }
        });*/

        $("body").on("click", "#guaranteegift", function(event){

            // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                contextMenuShow(0, 0, event, 'guaranteegift');
            }
        });

        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
        /*$("#insure").click(function(event) {

		 // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                //console.log(1);
                contextMenuShow(0, 0, event, 'insure');
            }
        });*/

        $("body").on("click", "#insure", function(event){

            // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                //console.log(1);
                contextMenuShow(0, 0, event, 'insure');
            }
        });

        // Вешаем слушатель события нажатие кнопок мыши для всего документа:
/*        $("#insure_approve").click(function(event) {

 // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                //console.log(1);
                contextMenuShow(0, 0, event, 'insure_approve');
            }
        });*/

        $("body").on("click", "#insure_approve", function(event){

            // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                //console.log(1);
                contextMenuShow(0, 0, event, 'insure_approve');
            }
        });

        //Скидки Вешаем слушатель события нажатие кнопок мыши для всего документа:
        /*$("#discounts").click(function(event) {

		 // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                //console.log(71);
                contextMenuShow(0, 0, event, 'discounts');
            }
        });*/

        $("body").on("click", "#discounts", function(event){

            // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                //console.log(71);
                contextMenuShow(0, 0, event, 'discounts');
            }
        });

        //для категорий процентов
		/*$("#percent_cats").click(function(event) {

		 // Проверяем нажата ли именно левая кнопка мыши:
		 if (event.which === 1)  {
		 //console.log(71);
		 contextMenuShow(0, 0, event, 'percent_cats');
		 }
		 });*/
        //Для прикрепления к филиалу в текущей сессии
        $(".change_filial").click(function(event) {

            // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                //console.log(71);
                contextMenuShow(0, 0, event, 'change_filial');
            }
        });
        $(".change_payment_filial").click(function(event) {

            // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                //console.log($(this).attr("filial_id"));
                contextMenuShow($(this).attr("payment_id"), $(this).attr("filial_id"), event, 'change_payment_filial');
            }
        });

        //Для отображения списка молочных зубов
        $('#teeth_moloch').click(function(event) {

            // Проверяем нажата ли именно левая кнопка мыши:
            if (event.which === 1)  {
                //console.log(71);
                contextMenuShow(0, 0, event, 'teeth_moloch');
            }
        });
        //Для отображения меню изменения статуса
        $('#lab_order_status').click(function(event) {

            // Проверяем нажата ли именно левая кнопка мыши:
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

		 //Запрос есть ли новые заявки
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

        rys = confirm("Вы cобираетесь вернуть заявку в работу. \n\nВы уверены?");

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

	//Прочитали все заявки
    function iReadAllOfTickts(worker_id) {
        var rys = false;

        rys = confirm("Пометить все заявки как прочитаные?");

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



    //Функция открыть скрытый див по его id спрятать блок показать блок скрыть блок свернуть развернуть сворачиваем разворачиваем прятать
	function toggleSomething (divID){
        $(divID).toggle('normal');
	}
    //Функция открыть скрытый див по его class спрятать блок показать блок скрыть блок свернуть развернуть сворачиваем разворачиваем прятать
	function toggleSomethingByClass (divClass){
        $(divClass).each(function(){
            $(this).toggle('normal');
        });
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

	//Создание нового табеля - открываем в модальном окне
    function iOpenNewWindow2(url, name, options){



    }

    //Удаляем отметку косметолога
    function Ajax_del_task_cosmet(id){
        //console.log(id);

        var rys = false;

        rys = confirm("Вы хотите удалить отметку косметолога. \nВы уверены?");

        if (rys) {

            var link = "ajax_task_cosmet_del_f.php";

            var certData = {
                id: id
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
    }

    //Разблокировка (восстановление) отметки косметолога
    function Ajax_reopen_task_cosmet(id) {

        var link = "ajax_reopen_task_cosmet_f.php";

        var certData = {
            id: id
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

    //Закрыть направление
    function getEquipmentItemsForGroup(equipment_group_id) {

        var link = "getEquipmentItemsForGroup_f.php";

        var reqData = {
            equipment_group_id: equipment_group_id
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

					$('#rezult').html(res.data);
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

	//пробная для круговой диаграммы
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
                        window.chartColors.indigo
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

    //Очистить поле ввода поиска
	function clearSearchInput(){
		$("#search_clients").val("");
        $(".button_in_input").hide();
        $("#search_result_fc2").html("");
	}


	//Для графика фактического
    function ShowSettingsSchedulerFakt(filial, filial_name, kabN, year, month, day, smenaN){
        $("#ShowSettingsSchedulerFakt").show();
        $("#overlay").show();
        //alert(month_date);
        window.scrollTo(0,0);

        $("#filial_value").html(filial);
        $("#filial_name").html(filial_name);

        $("#month_date").html(day+'.'+month+'.'+year);
        $("#year").html(year);
        $("#month").html(month);
        $("#day").html(day);

        $("#kabN").html(kabN);
        $("#smenaN").html(smenaN);


        //Те, кто уже есть
        $.ajax({
            // метод отправки
            type: "POST",
            // путь до скрипта-обработчика
            url: "scheduler_workers_here_fakt.php",
            // какие данные будут переданы
            data: {
                day: day,
                month: month,
                year: year,
                kab: kabN,
                smena: smenaN,
                filial: filial,
                type: $("#type").val()
            },
            // действие, при ответе с сервера
            success: function(workers_here){
                $("#workersTodayDelete").html(workers_here);
            }
        });

        //Те, кто свободен
        $.ajax({
            // метод отправки
            type: "POST",
            // путь до скрипта-обработчика
            url: "scheduler_workers_free_fakt.php",
            // какие данные будут переданы
            data: {
                day: day,
                month: month,
                year: year,
                smena: smenaN,
                type: $("#type").val()
            },
            // действие, при ответе с сервера
            success: function(workers){
                $("#ShowWorkersHere").html(workers);
            }
        });

        //Если надо указать тип графика, то показываем галочку
        // if ($("#type").val() == 4){
        // 	$("#schedulerType").show();
        //     $("#twobytwo").prop("checked", false);
        // }
    }

    function HideSettingsSchedulerFakt(){
        $("#ShowSettingsSchedulerFakt").hide();
        $("#overlay").hide();
        var input = document.getElementsByName("DateForMove");
        for (var i=0; i<input.length; i++)  {
            if(input[i].value=="0") input[i].checked="checked";
        }

        $('.error').hide();
        document.getElementById("errror").innerHTML = '';
    }

    function ShowWorkersSmena(){
        var smena = 0;
        if ( $("#smena1").prop("checked")){
            if ( $("#smena2").prop("checked")){
                smena = 9;
            }else{
                smena = 1;
            }
        }else if ( $("#smena2").prop("checked")){
            smena = 2;
        }

        $.ajax({
            // метод отправки
            type: "POST",
            // путь до скрипта-обработчика
            url: "show_workers_free.php",
            // какие данные будут переданы
            data: {
                day: $('#day').val(),
				month: $('#month').val(),
				year: $('#year').val(),
				smena: smena,
            	type: $('type').val()
    		},
			// действие, при ответе с сервера
			success: function(workers){
                $("#ShowWorkersHere").html(workers);
			}
    	});
    }

    //Удаляем из смены
    function DeleteWorkersSmenaFakt(worker, filial, day, month, year, smena, kab, type){
        var rys = confirm("Удалить сотрудника из смены?");
        if (rys){
            $.ajax({
                // метод отправки
                type: "POST",
                // путь до скрипта-обработчика
                url: "scheduler_worker_delete_fakt.php",
                // какие данные будут переданы
                data: {
                    worker: worker,
                    filial: filial,
                    day: day,
                    month: month,
                    year: year,
                    smena: smena,
                    kab: kab,
                    type: type
                },
                // действие, при ответе с сервера
                success: function(request){
                    document.getElementById("workersTodayDelete").innerHTML=request;
                    setTimeout(function () {
                        location.reload()
                    }, 100);
                }
            });
        }
    }

    function ChangeWorkerShedulerFakt() {
		//console.log($("#twobytwo").prop("checked"));

        $(".error").hide();
        document.getElementById("errrror").innerHTML = "";

        // получение данных из полей
        var day = $("#day").html();
        var month = $("#month").html();
        var year = $("#year").html();
        var filial = $("#filial_value").html();
        var kab = $("#kabN").html();
        var smena = $("#smenaN").html();
        var type = $("#type").val();

        var worker = $("input[name=worker]:checked").val();
        if(typeof worker == "undefined") worker = 0;

        var twobytwo = 0;
        // if ($("#twobytwo").prop("checked")){
         //     twobytwo = 1;
		// }

        $.ajax({
            dataType: "json",
            //statbox:SettingsScheduler,
            // метод отправки
            type: "POST",
            // путь до скрипта-обработчика
            url: "scheduler_worker_edit_fakt_f.php",
            // какие данные будут переданы
            data: {
                day: day,
                month: month,
                year: year,
                filial: filial,
                kab: kab,
                smena: smena,
                type: type,
                worker: worker,
                twobytwo: twobytwo
            },
            // действие, при ответе с сервера
            success: function(res){
            	//console.log(res);

                //document.getElementById("errrror").innerHTML = data;
                if (res.req == "ok"){
                    // прячем текст ошибок
                    $(".error").hide();
                    document.getElementById("errrror").innerHTML = "";
                    setTimeout(function () {
                        location.reload()
                    }, 100);
                }
            }
        });
    };

    //Показать/скрыть текст внутри
    function SetVisible(obj, val){
    	//console.log(val);
    	//console.log(obj.getElementsByTagName('div').className);
        //console.log(obj.className);

        obj.getElementsByTagName('div').className = val ? "div_up_visible" : "div_up_hidden";

		if (val){
            $(obj).find('div').show();
            //$("."+obj.className).addClass("cellsBlockHover2");
		}else{
            $(obj).find('div').hide();
            //$("."+obj.className).removeClass("cellsBlockHover2");
		}
    }

    //Для изменения графика админов, ассистентов, ...  (scheduler3.php)
	// !! 2019-02-13 пока не знаю, будет писаться сразу в базу или все таки собираться в массив и потом по кнопке сохранить...
    function changeTempSchedulerSession(obj, worker_id, filial_id, day, month, year, holiday){
    	//!!!Тест выводим все аргументы функции
		//console.log(arguments);
		//console.log($(obj).attr("selectedDate"));

        //blockWhileWaiting (true);

        //маркер выделено или нет
        var selected = 0;
        var selectedDate = $(obj).attr("selectedDate");

        //Если было НЕ выбрано на этом филиале, ставим выбор
        if (selectedDate == 0){
            if ((holiday == 6) || (holiday == 7)) {
                $(obj).css('backgroundColor', 'rgba(24, 144, 54, 0.52) !important');
            }else{
                $(obj).css('backgroundColor', 'rgba(49, 239, 96, 0.52) !important');
			}

        	//меняем значение
            $(obj).attr("selectedDate", 1);

            selected = 1;
		}
        //Если было выбрано на этом филиале, снимаем выбор
        if (selectedDate == 1){
			if ((holiday == 6) || (holiday == 7)) {
                $(obj).css('backgroundColor', 'rgba(234, 123, 32, 0.15) !important');
            }else{
                $(obj).css('backgroundColor', 'rgba(255, 255, 255, 0.15) !important');
			}
            //меняем значение
            $(obj).attr("selectedDate", 0);

            selected = 0;
		}
        //Если было выбрано на другом филиале, но не выбрано на этом, ставим выбор
        if (selectedDate == 2){
			if ((holiday == 6) || (holiday == 7)) {
                $(obj).css('backgroundColor', 'rgba(130, 34, 35, 0.52) !important');
            }else{
                $(obj).css('backgroundColor', 'rgba(236, 107, 107, 0.52) !important');
			}
            //меняем значение
            $(obj).attr("selectedDate", 3);

            selected = 3;
		}
        //Если было выбрано на другом филиале, и выбрано на этом, снимаем выбор
        if (selectedDate == 3){
			if ((holiday == 6) || (holiday == 7)) {
                $(obj).css('backgroundColor', 'rgba(35, 137, 146, 0.52) !important');
            }else{
                $(obj).css('backgroundColor', 'rgba(49, 224, 239, 0.52) !important');
			}
            //меняем значение
            $(obj).attr("selectedDate", 2);

            selected = 2;
		}

        //Добавляем в сессию
        var link = "add_temp_scheduler_in_session_f.php";

        var reqData = {
            worker_id: worker_id,
            filial_id: filial_id,
            day: day,
            month: month,
            year: year,
            selected: selected
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

				if (res.isset){
                    //console.log (res);

                    $("#ShowSettingsSchedulerFakt").show();
				}else{
                    $("#ShowSettingsSchedulerFakt").hide();
				}

                blockWhileWaiting (false);
            }

        });
    }

    //Отменение всех изменений
    //Для изменения графика админов, ассистентов, ...  (scheduler3.php)
	function cancelChangeTempScheduler(){
        blockWhileWaiting (true);

        //Чистим сессионный переменную
        var link = "cancel_temp_scheduler_in_session_f.php";

        var reqData = {
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
                    $("#ShowSettingsSchedulerFakt").hide();
                    location.reload();
                }


                blockWhileWaiting (false);
            }

        });
	}

    //Сохранение всех изменений
    //Для изменения графика админов, ассистентов, ...  (scheduler3.php)
	function Ajax_tempScheduler_scheduler3_add(filial_id, month, year){
        blockWhileWaiting (true);

        //Чистим сессионную переменную
        var link = "ajax_scheduler3_add_f.php";

        var reqData = {
            filial_id: filial_id,
            month: month,
            year: year,
            type: $("#type").val()
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
                    //console.log (res);

                    $("#ShowSettingsSchedulerFakt").hide();
                    location.reload();
                }

                blockWhileWaiting (false);
            }

        });
    }

    //Функция возвращает, сколько денег с какого филиала надо будет снять при выплате ЗП - для fl_paidout_in_tabel_add.php
    function tabelSubtractionPercent(tabel_id, tabel_type, paidout_type, summ, paidout_summ_tabel){
		//console.log(summ);
		//console.log(paidout_summ_tabel);

		hideAllErrors();

		if (Number(summ) > Number(paidout_summ_tabel)) {
            $("#paidout_summ_error").html('Вы собираетесь выдать больше, чем указано в табеле (' + paidout_summ_tabel + ' руб.).');
            $("#paidout_summ_error").show();
            // $("#showPaidoutAddbutton").hide();
		}
		//Иногда мы хотим выдать больше, поэтому else закомментили
		//else {

            var link = "tabel_subtraction_percent2_f.php";

            var reqData = {
                tabel_id: tabel_id,
                summ: summ,
                paidout_summ_tabel: paidout_summ_tabel,
                tabel_type: tabel_type,
                paidout_type: paidout_type
            };
            //console.log(reqData);

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                //dataType: "JSON",
                data: reqData,
                cache: false,
                beforeSend: function () {
                    $("#tabelFilialSubtraction").html("<div style='width: 120px; height: 32px; padding: 5px 10px 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...<br>загрузка</span></div>");
                },
                success: function (res) {
                    //console.log (res);
                    $("#tabelFilialSubtraction").html(res);

                    $("#showPaidoutAddbutton").show();

                    //
                    // if(res.result == "success") {
                    //     $("#tabelFilialSubtraction").html(res);
                    // }else{
                    //     //Показываем ошибку в консоли
                    //     console.log (res);
                    // }
                }
            })
        // }

        //Спрячем кнопку провести, если суммы не сойдутся
        if ((Number(summ) > Number(paidout_summ_tabel)) || (Number(summ) == 0)) {
            $("#addDeployButton").hide();
        }else{
            $("#addDeployButton").show();
        }
	}

    //Добавляем коэффициент в табель
    function koeffInTabelAdd(tabel_id, minus){
        //console.log();

        hideAllErrors();

        let koeff = $("#koeff").val();
        // console.log(koeff);

        if (isNaN(koeff)){
            $("#koeff").val(0);
        }else{
            if (koeff < 0){
                $("#koeff").val(value * -1);
            }else{
                if (koeff == ""){
                    $("#koeff").val(0);
                }else{
                    if (koeff === undefined){
                        $("#koeff").val(0);
                    }else{
                        //Всё норм
                        //console.log("Всё норм")

                        var link = "fl_koeff_in_tabel_add_f.php";

                        var reqData = {
                            tabel_id: tabel_id,
                            minus: minus,
                            koeff: koeff
                        };
                        //console.log(reqData);

                        $.ajax({
                            url: link,
                            global: false,
                            type: "POST",
                            dataType: "JSON",
                            data: reqData,
                            cache: false,
                            beforeSend: function () {

                            },
                            success: function (res) {
                                //console.log (res);

                                $('.center_block').remove();
                                $('#overlay').hide();

                                location.reload();

                                // if (res.result == 'success') {
                                //     // console.log (res);
                                //
                                //     //Если категория, перезагрузим их
                                //     if (type == 'category') {
                                //         getScladCategories();
                                //     }
                                //
                                //     //Если позиция, загрузим позиции этой категории
                                //     if (type == 'item') {
                                //         getScladItems(targetId, 0, 1000, false, true, targetId);
                                //     }
                                //
                                // } else {
                                //     $("#existCatItem").html(res.data);
                                //     $("#existCatItem").show();
                                // }
                            }
                        })

                    }
                }
            }
        }





        //         $.ajax({
        //             url: link,
        //             global: false,
        //             type: "POST",
        //             dataType: "JSON",
        //             data: reqData,
        //             cache: false,
        //             beforeSend: function () {
        //
        //             },
        //             success: function (res) {
        //                 //console.log (res);
        //
        //                 $('.center_block').remove();
        //                 $('#overlay').hide();
        //
        //                 if (res.result == 'success') {
        //                     // console.log (res);
        //
        //                     //Если категория, перезагрузим их
        //                     if (type == 'category') {
        //                         getScladCategories();
        //                     }
        //
        //                     //Если позиция, загрузим позиции этой категории
        //                     if (type == 'item') {
        //                         getScladItems(targetId, 0, 1000, false, true, targetId);
        //                     }
        //
        //                 } else {
        //                     $("#existCatItem").html(res.data);
        //                     $("#existCatItem").show();
        //                 }
        //             }
        //         })
        //     }else{
        //         $("#existCatItem").html('<span style="color: red; font-weight: bold;">Выберите единицы измерения</span>');
        //         $("#existCatItem").show();
        //     }
        // }else{
        //     $("#existCatItem").html('<span style="color: red; font-weight: bold;">Ничего не ввели</span>');
        //     $("#existCatItem").show();
        // }

    }

    function changeSettings (option, value){
        var link = "change_settings_f.php";

        var reqData = {
            option: option,
            value: value
        };
        //console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                location.reload();

            }
        })
    }

    //Показываем блок для добавления коэффициента в табель
    function showKoeffInTabelAdd(tabel_id, minus=false, koeff){
        // console.log(type);

        let descr = 'Добавить коэффициент ';
        let mark = '';

        if (minus){
            mark = ' -';
        }else{
            mark = ' +';
        }

        $('#overlay').show();

        let buttonsStr = '<input type="button" class="b" value="Ok" onclick="koeffInTabelAdd('+tabel_id+', \''+minus+'\');">';

        // Создаем меню:
        let menu = $('<div/>', {
            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
        })
            .css({
                "height": "150px"
            })
            .appendTo('#overlay')
            .append(
                $('<div/>')
                    .css({
                        "height": "100%",
                        "border": "1px solid #AAA",
                        "position": "relative"
                    })
                    .append('<span style="margin: 5px;"><i>'+descr+' '+mark+'</i></span>')
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
                                "height": "50%"
                            })
                            .append('<div style="margin-top: 3px;"><span style="font-size:90%; color: #333; ">Введите значение</span><br>'+mark+'<input type="number" name="koeff" id="koeff" class="form-control" size="2" min="0" max="99" value="'+koeff+'" class="mod" style="text-align: center;">%')
                            // .append(unit_select)
                            // .append(res.data)
                    )
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "bottom": "2px",
                                "width": "100%"
                            })
                            .append('<div id="existCatItem" class="error"></div>')
                            .append(buttonsStr+
                                '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove(); ">'
                            )
                    )
            );

        menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

    }

	//!!! пример работы пауза между нажатиями
    //$('.paidout_summ2'). on("keyup", function() {
    $("body").on("keyup change", ".paidout_summ2", function (e) {
    	//console.log(e.keyCode);

		//Если только цифры, delete, backspase
        if (((e.keyCode >= 48) && (e.keyCode <= 57)) || ((e.keyCode >= 96) && (e.keyCode <= 105)) || ((e.keyCode == 8) || (e.keyCode == 46))) {
            //$this - хранит ссылку на объект нашего <input>
            var $this = $(this);
            //пауза между нажатиями, чтобы срабатывал обработчик только если пауза между нажатиями
            //больше указанного
            var $delay = 450;

            clearTimeout($this.data('timer'));

            var
                summ = $(this).val(),
                tabel_id = $(this).attr("tabel_id"),
            	paidout_summ_tabel = $(this).attr("paidout_summ_tabel"),
                tabel_type = $("#tabel_type").val();
            	paidout_type = $("#paidout_type").val();

            if (summ.length > 2) {

                $this.data('timer', setTimeout(function () {
                    $this.removeData('timer');

                    tabelSubtractionPercent(tabel_id, tabel_type, paidout_type, summ, paidout_summ_tabel);

                }, $delay));
            }
        }
    });

    //Функция отслеживает, если меняют цифры в филиалах
	function filialsSubtractionsChange (){

        //Сумма со всех филиалов - Факт
        var summ = 0;
        //Сумма со всех филиалов -план
        var iWantMyMoney = Number($("#iWantMyMoney").val());

        $(".filial_subtraction").each(function (){
            summ += Number($(this).val());
        })
        //console.log(summ);
        //console.log(iWantMyMoney);

        if (summ < iWantMyMoney){
            $("#fil_sub_msg").html("Осталось распределить: <span style='font-size: 110%; font-weight: bold; color: red;'>" + (iWantMyMoney - summ) + "</span> руб.");
            $("#showPaidoutAddbutton").hide();
        }else {
            if (summ > iWantMyMoney){
                $("#fil_sub_msg").html("Распределили больше на: <span style='font-size: 110%; font-weight: bold; color: red;'>" + (summ - iWantMyMoney) + "</span> руб.");
                $("#showPaidoutAddbutton").hide();
            }else{
                $("#fil_sub_msg").html("");
                $("#showPaidoutAddbutton").show();
            }
        }
	}

    //Изменение цифр в филиалах
    $("body").on("keyup", ".filial_subtraction", function (e) {
        //console.log(e.keyCode);

        //Если только цифры, delete, backspase
        if (((e.keyCode >= 48) && (e.keyCode <= 57)) || ((e.keyCode >= 96) && (e.keyCode <= 105)) || ((e.keyCode == 8) || (e.keyCode == 46))) {
            //$this - хранит ссылку на объект нашего <input>
            // var $this = $(this);
            // //пауза между нажатиями, чтобы срабатывал обработчик только если пауза между нажатиями
            // //больше указанного
            // var $delay = 450;
            //
            // clearTimeout($this.data('timer'));
            //
            // var
            //     summ = $(this).val(),
            //     tabel_id = $(this).attr("tabel_id");
            //
            // if (summ.length > 2) {
            //
            //     $this.data('timer', setTimeout(function () {
            //         $this.removeData('timer');
            //
            //         tabelSubtractionPercent(tabel_id, summ);
            //
            //     }, $delay));
            // }

            filialsSubtractionsChange();
        }
    });

    //Функция применят суммы вычета с филиала в указанный филиал
    function allSubtractionInHere(filial_id, summ){
    	$(".filial_subtraction").each(function() {
    		if ($(this).attr("filial_id") == filial_id){
                $(this).val(summ);
			}else{
                $(this).val(0);
			}
		})

        filialsSubtractionsChange();
	}

	//Функция считает стоимость посолярию
    function CalculateSolar(min_count, min_price, discount, realiz_summ, onlyRealiz){
        //Всего за загар
        var fin_price = (min_price * min_count)/100 * (100 - discount);

        $("#finPrice").val(fin_price);

        if (onlyRealiz){
            $("#allSumm").html(Number(realiz_summ));
        }else{
            $("#allSumm").html(fin_price + Number(realiz_summ));
        }
    }

    //Изменить статус рассрочки
    function changeInstallmentStatus(client_id, installment_status_now, reload= false){
        //console.log (arguments);

        let link = "installment_status_change_f.php";

        let reqData = {
            client_id: client_id,
            installment_status_now: installment_status_now
        };
        //console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                if (res.result == 'success') {
                    if (reload) {
                        location.reload();
                    }else{
                        //Удаляем из документа, чтобы не перезагружать таблицу
                        $('#cl_data_main_'+client_id).remove();
                        $('#user_options_'+client_id).remove();
                        $('#cl_data_user_'+client_id).css('backgroundColor', 'yellow');
                    }
                }
            }
        })
    }

    //Изменить статус рассрочки v2.0
    function changeInstallmentStatus2(installment_id, client_id, invoice_id, installment_status_now, reload= false){

        let link = "installment_change_f.php";

        let reqData = {
            installment_id: installment_id,
            client_id: client_id,
            invoice_id: invoice_id,
            installment_status_now: installment_status_now
        };
        //console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                if (res.result == 'success') {
                    if (reload) {
                        location.reload();
                    }else{
                        //Удаляем из документа, чтобы не перезагружать таблицу
                        //$('#cl_data_main_'+client_id).remove();
                        //$('#user_options_'+client_id).remove();
                        $('#cl_data_user_'+client_id).css('backgroundColor', 'rgb(254 255 116 / 60%)');
                        // $('#cl_data_main_'+client_id).css('backgroundColor', 'yellow');
                        $('#changeInstallmentStatus2_btn_'+ invoice_id).html('Закрыто');
                    }
                }
            }
        })
    }

    //Показываем типа график платежей должника
    function getPayments4Installments (client_id, date_in){
        //console.log (arguments);

        let link = "get_payments_for_installments_f.php";

        let reqData = {
            client_id: client_id,
            date_in: date_in
        };
        // console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                if (res.result == 'success') {
                    $("#client_orders_by_period_" + client_id).html(res.data);

                    //Прокрутка вправо до конца
                    let elem = document.querySelector('#client_orders_by_period_' + client_id);
                    elem.scrollLeft = elem.scrollWidth;

                    if (res.current_month_payment == 0){
                        $('#current_month_payment_'+client_id).show();
                    }

               }
            }
        })

    }

    //Функция для рассчета рассрочки
    function installmentCalculate (summ, m_count){
        // console.log(summ);
        // console.log(m_count);
        // console.log(summ*100);
        // console.log((summ*100 - summ*100 % m_count) / m_count);
        // console.log(summ*100 % m_count);

        let rezult = '';

        let payment = (summ - summ % m_count) / m_count;

        for (let i = 1; i <= m_count; i++){

            rezult += '<div style="display: table-cell; width: 83px; min-width: 83px; font-size: 80%; border: 1px solid #BFBCB5; background: lawngreen; padding: 10px;">';
            rezult += '<div style="margin-bottom: 5px;">'+i+' месяц';
            rezult += '</div>';
            rezult += '<div>';

            if (i != m_count) {
                //console.log(i + ' мес. => ' + payment)

                rezult += '<span style="font-weight: bold">'+payment+' руб.</span>';
            }else{
                //console.log(i + ' мес. => ' + (payment + summ % m_count))

                rezult += '<span style="font-weight: bold">'+(payment + summ % m_count)+' руб.</span>';
            }
            rezult += '</div>';
            rezult += '</div>';
        }
        //console.log('-------------------------------');



        // console.log(rezult);
        $("#installment_calculate").html(rezult);
    }

    //Добавляем/редактируем в базу наряд из сессии
    function Ajax_installment_add(mode){
        //console.log(mode);

        // let invoice_id = $("#invoice4installment").html();
        // let month_start = $("#iWantThisMonth").val();
        // let year_start = $("#iWantThisYear").val();

        let link = "installment_add_f.php";

        let reqData = {
            client_id: $("#client_id").val(),
            invoice_id: $("#invoice4installment").html(),
            summ: $("#installment_summ").html(),
            month_start: $("#iWantThisMonth").val(),
            year_start: $("#iWantThisYear").val()
        };
        //console.log(reqData);

        //Добавим запись
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
            // действие, при ответе с сервера
            success: function(res){
                console.log(res);

                if(res.result == "success"){

                    location.reload();

                }else{
                    $('#errror').html(res.data);
                }
            }
        });
    }




    //Функция блокирует вкладки
    function disableTabs (permission, worker){
        //console.log($('#tabs_w'+permission+'_'+worker+' .notes_count2'));

        var tabs_id_arr = [];
        // tabs_id_arr.push(0);


        $('#tabs_w'+permission+'_'+worker+' .notes_count3').each(function(){

            var filial_id = $(this).attr('filial_id');
            //console.log(filial_id);
            //console.log(($('#tabs_notes2_'+permission+'_'+worker+'_'+filial_id).css("display") == 'none'));//
            //console.log(($('#tabs_notes_'+permission+'_'+worker+'_'+filial_id).css("display") == 'none'));

            if (($('#tabs_notes2_'+permission+'_'+worker+'_'+filial_id).css("display") == 'none')
                &&
                ($('#tabs_notes_'+permission+'_'+worker+'_'+filial_id).css("display") == 'none'))
            {
                var index = $('#tabs_w'+permission+'_'+worker+' a[href="#tabs-'+permission+'_'+worker+'_'+filial_id+'"]').parent().index();
                tabs_id_arr.push (index);
            }

            $('#tabs_w'+permission+'_'+worker).tabs( "option", "disabled", tabs_id_arr );

        })

        // if (($('#tabs_notes2_'+permission+'_'+worker+'_'+office).css("display") == 'none')
        // if (($('#tabs_notes3_'+permission+'_'+worker+'_'+office).css("display") == 'none')

        // if (($('#tabs_w'+permission+'_'+worker+' .notes_count2').css("display") == 'none')
        //     &&
        //     ($('#tabs_w'+permission+'_'+worker+' .notes_count3').css("display") == 'none'))
        // {
        //     var office = $('#tabs_w'+permission+'_'+worker+' .notes_count3').attr('filial_id');
        //     //console.log(office);
        //
        //     var index = $('#tabs_w'+permission+'_'+worker+' a[href="#tabs-'+permission+'_'+worker+'_'+office+'"]').parent().index();
        //     tabs_id_arr.push (index);
        // }
        //
        // $('#tabs_w'+permission+'_'+worker).tabs( "option", "disabled", tabs_id_arr );

    }

    //Добавление посещения стоматолога
    function Ajax_add_task_stomat() {

        let link = "add_task_stomat_f.php";

        let arrayRemoveAct = new Array();
        let arrayRemoveWorker = new Array();

        $(".remove_add_search").each(function() {
            if (($(this).attr("id")).indexOf("td_title") != -1){
                let IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
                arrayRemoveAct[IndexArr] = document.getElementById($(this).attr("id")).value;
            }
            if (($(this).attr("id")).indexOf("td_worker") != -1){
                let IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
                arrayRemoveWorker [IndexArr] = document.getElementById($(this).attr("id")).value;
            }
        });

        let reqData = {
            zapis_id: $("#zapis").val(),

            complaints: $("#complaints").val(),
            objectively: $("#objectively").val(),
            diagnosis: $("#diagnosis").val(),
            therapy: $("#therapy").val(),
            recommended: $("#recommended").val(),

            comment: $("#comment").val(),

            notes: $("#add_notes_show").val(),
            remove: $("#add_remove_show").val(),

            removeAct: JSON.stringify(arrayRemoveAct),
            removeWork: JSON.stringify(arrayRemoveWorker),

            add_notes_type: $("#add_notes_type").val(),
            add_notes_months: $("#add_notes_months").val(),
            add_notes_days: $("#add_notes_days").val()
        };
        //console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            //dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                $("#status").html(res);
            }
        })
    }

    //Редактирование посещения стоматолога
    function Ajax_edit_task_stomat(){

        var link = "edit_task_stomat_f.php";

        var add_notes_type = 0;
        var add_notes_months = 0;
        var add_notes_days = 0;

        var notes_val = 0;
        var remove_val = 0;

        if ($("#add_notes_show")){
            if ($("#add_notes_show").prop("checked")){
                notes_val = 1;
                add_notes_type = $("#add_notes_type").val();
                add_notes_months = $("#add_notes_months").val();
                add_notes_days = $("#add_notes_days").val();
            }
        }

        if ($("#add_remove_show").prop("checked")){
            remove_val = 1;
        }

        var arrayRemoveAct = new Array();
        var arrayRemoveWorker = new Array();

        $(".remove_add_search").each(function() {
            if (($(this).attr("id")).indexOf("td_title") != -1){
                var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
                arrayRemoveAct[IndexArr] = document.getElementById($(this).attr("id")).value;
            }
            if (($(this).attr("id")).indexOf("td_worker") != -1){
                var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
                arrayRemoveWorker [IndexArr] = document.getElementById($(this).attr("id")).value;
            }
        });

        var reqData = {
            id: $("#id").val(),
            client_id: $("#client").val(),

            complaints: $("#complaints").val(),
            objectively: $("#objectively").val(),
            diagnosis: $("#diagnosis").val(),
            therapy: $("#therapy").val(),
            recommended: $("#recommended").val(),

            comment: $("#comment").val(),

            sel_date: $("#sel_date").val(),
            sel_month: $("#sel_month").val(),
            sel_year: $("#sel_year").val(),

            sel_seconds: $("#sel_seconds").val(),
            sel_minutes: $("#sel_minutes").val(),
            sel_hours: $("#sel_hours").val(),

            notes: notes_val,
            remove: remove_val,

            removeAct: JSON.stringify(arrayRemoveAct),
            removeWork: JSON.stringify(arrayRemoveWorker),

            add_notes_type: add_notes_type,
            add_notes_months: add_notes_months,
            add_notes_days: add_notes_days,

            client: $("#client").val()
        };
        //console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            //dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                $("#status").html(res);
            }
        })

    }

    //Изменение плательщика наряда
    function changeNewPayer(){
        //!!!Получение данных из GET тест
        //console.log(params["data"]);
        //выведет в консоль значение  GET-параметра data
        //console.log(params);

        let get_data_str = "";

        let params = window
            .location
            .search
            .replace("?","")
            .split("&")
            .reduce(
                function(p,e){
                    let a = e.split('=');
                    p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                    return p;
                },
                {}
            );

        for (let key in params) {
            if (key.indexOf("client_id") == -1){
                get_data_str = get_data_str + "&" + key + "=" + params[key];
            }
        }
        // console.log(get_data_str);

        //!!! переход window.location.href - это правильное использование
        window.location.href = "payment_from_alien_add.php?client_id="+$("#new_payer_id").val() + get_data_str;
    }

    //Изменение пациента, кому выдаем сертификат именной
    function changeCertificateNameMaster(){
        //!!!Получение данных из GET тест
        //console.log(params["data"]);
        //выведет в консоль значение  GET-параметра data
        //console.log(params);

        let get_data_str = "";

        let params = window
            .location
            .search
            .replace("?","")
            .split("&")
            .reduce(
                function(p,e){
                    let a = e.split('=');
                    p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                    return p;
                },
                {}
            );

        for (let key in params) {
            if (key.indexOf("client_id") == -1){
                get_data_str = get_data_str + "&" + key + "=" + params[key];
            }
        }
        // console.log(get_data_str);

        //!!! переход window.location.href - это правильное использование
        window.location.href = "cert_name_cell.php?client_id="+$("#new_payer_id").val() + get_data_str;
    }

    //Изменение сертификата именного, который выдаем
    function changeCertificateNameId(cert_id){
        //!!!Получение данных из GET тест
        //console.log(params["data"]);
        //выведет в консоль значение  GET-параметра data
        //console.log(params);

        let get_data_str = "";

        let params = window
            .location
            .search
            .replace("?","")
            .split("&")
            .reduce(
                function(p,e){
                    let a = e.split('=');
                    p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                    return p;
                },
                {}
            );

        for (let key in params) {
            if (key.indexOf("cert_id") == -1){
                get_data_str = get_data_str + "&" + key + "=" + params[key];
            }
        }
        // console.log(get_data_str);

        //!!! переход window.location.href - это правильное использование
        window.location.href = "cert_name_cell.php?cert_id="+cert_id+ get_data_str;
    }

    //Загрузка элементов склада
    function getScladItems (cat_id, start, limit, free=true, pick=false, pick_id=-1, search_data=''){
        //Для позиции ВООБЩЕ СОВСЕМ БЕЗ категории free == 'true'

        var link = "get_sclad_items_f.php";

        var reqData = {
            cat_id: cat_id,
            start: start,
            limit: limit,
            free: free,
            search_data: search_data
        };
        // console.log(reqData);

        //Если надо выделить группу c id == pick_id
        if (pick){
            //Сначала очищаем у всех окраску
            $(".droppable").css({"background-color": ""});

            //Теперь покрасим
            $("#cat_"+pick_id).css({"background-color": "rgba(131, 219, 83, 0.5)"});
        }

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res.q);

                if (res.result == 'success') {

                    if (cat_id == 0){
                        if (free){
                            //Если поиск по фразе делаем
                            if (search_data.length > 0){
                                $("#cat_name_show").html("Результат поиска");
                                $("#cat_name_show").show();
                            }
                        }else {
                            $("#cat_name_show").html("Вне категории");
                            $("#cat_name_show").show();
                        }
                    }else {
                        $("#cat_name_show").html($("#cat_" + cat_id).attr("cat_name"));
                        $("#cat_name_show").show();
                    }

                    if (res.count > 0) {
                        $("#sclad_items_rezult").html(res.data);
                    }else{
                        $("#sclad_items_rezult").html('<span style="color: red; font-weight: bold; font-size: 80%; margin-left: 20px;">Ничего не найдено</span>');
                    }

                }
            }
        })
    }

    //Загрузка категорий склада
    function getScladCategories (){

        var link = "get_sclad_categories_f.php";

        var reqData = {
            type: 5
        };
        //console.log(reqData);

        //Если надо выделить группу

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                if (res.result == 'success') {

                    $("#sclad_cat_rezult").html(res.data);


                    //!!! Правильный пример контекстного меню (правильный? точно? ну пока работает)

/*                    var menuArea = document.querySelector(".tree");

                    //if(menuArea){
                        menuArea.addEventListener( "contextmenu", event => {
                            event.preventDefault();

                            contextMenuShow(0, 0, event, "sclad_cat");

                        }, false);
                    //}*/

                }
            }
        })
    }

    //Показываем блок для подтверждения переноса
    function showMoveApprove(item, target){
        //console.log(item);
        //console.log(target);
        // console.log(item.elem.id+" in "+target.id);

        item.elem.style.display = 'none';

        if (item.elem.id.split('_')[0] == 'catSclad') {
            moveScladItemInCategory(0, item.elem.id.split('_')[1], target.id.split('_')[1]);
        }
        if (item.elem.id.split('_')[0] == 'itemSclad') {
            moveScladItemInCategory(item.elem.id.split('_')[1], 0, target.id.split('_')[1]);
        }

        if (item.elem.id.split('_')[0] == 'catPrice') {
            movePriceItemInCategory(0, item.elem.id.split('_')[1], target.id.split('_')[1]);
        }
        if (item.elem.id.split('_')[0] == 'itemPrice') {
            movePriceItemInCategory(item.elem.id.split('_')[1], 0, target.id.split('_')[1]);
        }

        // $('#overlay').show();
        //
        // var item_name = $("#item_name_"+item.elem.id.split('_')[1]).html();
        // var target_name = $("#cat_"+target.id.split('_')[1]).html();
        //
        // var buttonsStr = '<input type="button" class="b" value="Ok" onclick="alert(888);">';
        //
        // // Создаем меню:
        // var menu = $('<div/>', {
        //     class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
        // })
        //     .appendTo('#overlay')
        //     .append(
        //         $('<div/>')
        //             .css({
        //                 "height": "100%",
        //                 "border": "1px solid #AAA",
        //                 "position": "relative",
        //             })
        //             .append('<span style="margin: 5px;"><i>Подтвердите перемещение</i></span>')
        //             .append(
        //                 $('<div/>')
        //                     .css({
        //                         "position": "absolute",
        //                         "width": "100%",
        //                         "margin": "auto",
        //                         "top": "-10px",
        //                         "left": "0",
        //                         "bottom": "0",
        //                         "right": "0",
        //                         "height": "50%",
        //                     })
        //                     .append('<div style="margin: 10px;"><b style="color: orangered;"><i>'+item_name+'</i></b> в <b style="color: darkolivegreen;">'+target_name+'</b>')
        //             )
        //             .append(
        //                 $('<div/>')
        //                     .css({
        //                         "position": "absolute",
        //                         "bottom": "2px",
        //                         "width": "100%",
        //                     })
        //                     .append(buttonsStr+
        //                         '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove(); ">'
        //                     )
        //             )
        //     );
        //
        // menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

    }

    //Перемещаем позицию в другую категорию
    function moveScladItemInCategory (item_id, cat_id, target_cat_id){

        var link = "move_sclad_items_in_cat_f.php";

        var reqData = {
            item_id: item_id,
            cat_id: cat_id,
            target_cat_id: target_cat_id
        };
        //console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                if (res.result == 'success') {

                    // $("#sclad_items_rezult").html(res.data);

                    //Если надо выделить группу
                    // if (pick){
                    //     //Сначала очищаем у всех окраску
                    //     $(".droppable").css({"background-color": ""});
                    //
                    //     //Теперь покрасим
                    //     $("#cat_"+pick_id).css({"background-color": "rgba(131, 219, 83, 0.5)"});
                    // }

                    getScladCategories ();

                }
            }
        })

    }

    //Показываем блок для добавления категории склада
    // function showScladCategoryAdd(id){
    //
    //     // item.elem.style.display = 'none';
    //     //
    //     // if (item.elem.id.split('_')[0] == 'cat') {
    //     //     moveScladItemInCategory(0, item.elem.id.split('_')[1], target.id.split('_')[1]);
    //     // }
    //     // if (item.elem.id.split('_')[0] == 'item') {
    //     //     moveScladItemInCategory(item.elem.id.split('_')[1], 0, target.id.split('_')[1]);
    //     // }
    //
    //     $('#overlay').show();
    //
    //     // var item_name = $("#item_name_"+item.elem.id.split('_')[1]).html();
    //     // var target_name = $("#cat_"+target.id.split('_')[1]).html();
    //
    //     var buttonsStr = '<input type="button" class="b" value="Ok" onclick="alert(888);">';
    //
    //     // Создаем меню:
    //     var menu = $('<div/>', {
    //         class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
    //     })
    //         .appendTo('#overlay')
    //         .append(
    //             $('<div/>')
    //                 .css({
    //                     "height": "100%",
    //                     "border": "1px solid #AAA",
    //                     "position": "relative",
    //                 })
    //                 .append('<span style="margin: 5px;"><i>Новая категория</i></span>')
    //                 .append(
    //                     $('<div/>')
    //                         .css({
    //                             "position": "absolute",
    //                             "width": "100%",
    //                             "margin": "auto",
    //                             "top": "-10px",
    //                             "left": "0",
    //                             "bottom": "0",
    //                             "right": "0",
    //                             "height": "50%",
    //                         })
    //                         .append('<div style="margin: 10px;"><b style="color: orangered;"><i>'+id+'</i></b> в <b style="color: darkolivegreen;">'+id+'</b>')
    //                 )
    //                 .append(
    //                     $('<div/>')
    //                         .css({
    //                             "position": "absolute",
    //                             "bottom": "2px",
    //                             "width": "100%",
    //                         })
    //                         .append(buttonsStr+
    //                             '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove(); ">'
    //                         )
    //                 )
    //         );
    //
    //     menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
    //
    // }

    //Показываем блок для добавления позиции склада
    function showScladCatItemAdd(targetId, type){
        // console.log(type);

        var descr = 'Новая позиция';

        if (type == 'category'){
            descr = 'Новая категория';
        }

        //Если позиция, спросим еще, про единицы измерения
        var unit_select = '';

        if (type == 'item'){
            unit_select =
                '<div style="margin-top: 20px;">' +
                    '<select name="unit_sel" id="unit_sel" style="width: 200px;">'+
                        '<option value="0">Выберите ед. измерения</option>' +
                        '<option value="pc">штуки</option>' +
                        '<option value="gr">граммы</option>'+
                        '<option value="ml">милилитры</option>'+
                        '<option value="sh">шприцы</option>'+
                    '</select>' +
                '</div>';
        }

        var link = "get_sclad_cat_show_f.php";

        var reqData = {
            targetId: targetId
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                if (res.result == 'success') {


                    $('#overlay').show();

                    //var res = '12313213';

                    var buttonsStr = '<input type="button" class="b" value="Ok" onclick="scladCatItemAdd('+targetId+', \''+type+'\');">';

                    if (type == 'category'){
                        buttonsStr = '<input type="button" class="b" value="Ok" onclick="scladCatItemAdd('+targetId+', \''+type+'\');">';
                    }

                    // Создаем меню:
                    var menu = $('<div/>', {
                        class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
                    })
                        .css({
                            "height": "300px"
                        })
                        .appendTo('#overlay')
                        .append(
                            $('<div/>')
                                .css({
                                    "height": "100%",
                                    "border": "1px solid #AAA",
                                    "position": "relative"
                                })
                                .append('<span style="margin: 5px;"><i>'+descr+'</i></span>')
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "width": "100%",
                                            "margin": "auto",
                                            "top": "-110px",
                                            "left": "0",
                                            "bottom": "0",
                                            "right": "0",
                                            "height": "50%"
                                        })
                                        .append('<div style="margin-top: 3px;"><span style="font-size:90%; color: #333; ">Введите название</span><br><input name="newCatItemName" id="newCatItemName" type="text" value="" style="width: 250px; font-size: 120%;">')
                                        .append(unit_select)
                                        .append(res.data)
                                )
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "bottom": "2px",
                                            "width": "100%"
                                        })
                                        .append('<div id="existCatItem" class="error"></div>')
                                        .append(buttonsStr+
                                            '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove(); ">'
                                        )
                                )
                        );

                    menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню


                }
            }
        })
    }


    //Показываем блок для добавления позиции склада
    function showScladCatItemEdit(id, type){
        // console.log(type);

        $(".context-menu").remove();

        //Если позиция, спросим еще, про единицы измерения
        var unit_select = '';

        if (type == 'category') {
            var descr = 'Редактировать категорию';
            var oldName = $("#cat_" + id).attr("cat_name");
        }

        if (type == 'item') {
            var descr = 'Редактировать позицию';
            var oldName = $("#item_name_"+id).attr("item_name");

            //console.log(oldName);

            unit_select =
                '<div style="margin-top: 20px;">' +
                '<select name="unit_sel" id="unit_sel" style="width: 200px;">'+
                '<option value="0">Выберите ед. измерения</option>' +
                '<option value="pc">штуки</option>' +
                '<option value="gк">граммы</option>'+
                '<option value="ml">милилитры</option>'+
                '<option value="sh">шприцы</option>'+
                '</select>' +
                '</div>';
        }

        if (oldName.length > 0){

            $('#overlay').show();

            var buttonsStr = '<input type="button" class="b" value="Ok" onclick="scladCatItemEdit('+id+', \''+type+'\');">';

            // Создаем меню:
            var menu = $('<div/>', {
                class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
            })
                .css({
                    "height": "300px"
                })
                .appendTo('#overlay')
                .append(
                    $('<div/>')
                        .css({
                            "height": "100%",
                            "border": "1px solid #AAA",
                            "position": "relative"
                        })
                        .append('<span style="margin: 5px;"><i>'+descr+'</i></span>')
                        .append(
                            $('<div/>')
                                .css({
                                    "position": "absolute",
                                    "width": "100%",
                                    "margin": "auto",
                                    "top": "-110px",
                                    "left": "0",
                                    "bottom": "0",
                                    "right": "0",
                                    "height": "50%"
                                })
                                .append('<div style="margin-top: 3px;"><span style="font-size:90%; color: #333; ">Введите новое название</span><br><input name="newCatItemName" id="newCatItemName" type="text" value="'+oldName+'" style="width: 250px; font-size: 120%;">')
                                .append(unit_select)
                        )
                        .append(
                            $('<div/>')
                                .css({
                                    "position": "absolute",
                                    "bottom": "2px",
                                    "width": "100%"
                                })
                                .append('<div id="existCatItem" class="error"></div>')
                                .append(buttonsStr+
                                    '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove(); ">'
                                )
                        )
                );

            menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

            //Выделяем пункт в select единиц измерения (Если позиция)
            if (type == 'item') {
                //!!! Поиск по аттрибуту в DOM
                //!!! Выбор пунтка в select
                var item_unit = $('[item_unit_' + id + ']').attr('item_unit_' + id);

                document.querySelector('#unit_sel').value = item_unit;
            }

        }

    }

    //Добавляем категорию или позицию в склад
    function scladCatItemAdd(targetId, type){
        //console.log();

        hideAllErrors();

        var newCatItemName = $("#newCatItemName").val();
        //console.log(newCatItemName);

        //Если будет позиция, то указываем еще и ед.изм.
        var item_units = false;
        var item_units_val = 0;

        if (type == 'item'){
            if ($("#unit_sel").val() == 0) {
                item_units = false;
            }else{
                item_units = true;
                item_units_val = $("#unit_sel").val();
            }
        }else{
            item_units = true;
        }

        if (newCatItemName.trim().length > 0){
            if (item_units) {

                var link = "fl_sclad_cat_item_add_f.php";

                var reqData = {
                    name: newCatItemName.trim(),
                    type: type,
                    targetId: targetId,
                    item_units_val: item_units_val
                };
                //console.log(reqData);

                $.ajax({
                    url: link,
                    global: false,
                    type: "POST",
                    dataType: "JSON",
                    data: reqData,
                    cache: false,
                    beforeSend: function () {

                    },
                    success: function (res) {
                        //console.log (res);

                        $('.center_block').remove();
                        $('#overlay').hide();

                        if (res.result == 'success') {
                            // console.log (res);

                            //Если категория, перезагрузим их
                            if (type == 'category') {
                                getScladCategories();
                            }

                            //Если позиция, загрузим позиции этой категории
                            if (type == 'item') {
                                getScladItems(targetId, 0, 1000, false, true, targetId);
                            }

                        } else {
                            $("#existCatItem").html(res.data);
                            $("#existCatItem").show();
                        }
                    }
                })
            }else{
                $("#existCatItem").html('<span style="color: red; font-weight: bold;">Выберите единицы измерения</span>');
                $("#existCatItem").show();
            }
        }else{
            $("#existCatItem").html('<span style="color: red; font-weight: bold;">Ничего не ввели</span>');
            $("#existCatItem").show();
        }

    }


    //Редактируем имя категории/позиции
    function scladCatItemEdit(id, type){
        //console.log();

        hideAllErrors();

        //!!! Объявим локальный объект для этой ф-ции. потом может исправим и вынесем выше в для всех
        //Еденицы измерения
        let units = {
            pc: 'шт.',
            gr: 'г.',
            ml: 'мл.',
        }

        var newCatItemName = $("#newCatItemName").val();
        //console.log(newCatItemName);

        //Если будет позиция, то указываем еще и ед.изм.
        var item_units = false;
        var item_units_val = 0;

        if (type == 'item'){
            if ($("#unit_sel").val() == 0) {
                item_units = false;
            }else{
                item_units = true;
                item_units_val = $("#unit_sel").val();
            }
        }else{
            item_units = true;
        }
        //console.log(item_units);

        if (newCatItemName.trim().length > 0){
            //console.log(newCatItemName.trim().length);

            if (item_units) {

                var link = "fl_sclad_cat_item_edit_f.php";

                var reqData = {
                    name: newCatItemName.trim(),
                    id: id,
                    type: type,
                    item_units_val: item_units_val
                };
                //console.log(reqData);

                $.ajax({
                    url: link,
                    global: false,
                    type: "POST",
                    dataType: "JSON",
                    data: reqData,
                    cache: false,
                    beforeSend: function () {

                    },
                    success: function (res) {
                        //console.log (res);

                        $('.center_block').remove();
                        $('#overlay').hide();

                        if (res.result == 'success') {
                            //console.log (res);

                            getScladCategories ();
                            //getScladItems (cat, 0, 1000, true);

                            if (type == 'item') {
                                //Обновим имя, не обновляя страницу
                                $('#item_name_'+id).html(newCatItemName);
                                //Обновим ед.изм., не обновляя страницу
                                $('[item_unit_'+id+']').attr('item_unit_'+id, item_units_val);
                                $('[item_unit_'+id+']').html(units[item_units_val]);
                            }

                        } else {
                            $("#existCatItem").html(res.data);
                            $("#existCatItem").show();
                        }
                    }
                })
            }else {
                $("#existCatItem").html('<span style="color: red; font-weight: bold;">Выберите единицы измерения</span>');
                $("#existCatItem").show();
            }
        }else{
            $("#existCatItem").html('<span style="color: red; font-weight: bold;">Ничего не ввели</span>');
            $("#existCatItem").show();
        }

    }

    //Добавление в сессию данных по складским позициям, с которыми будем работать (ID)
    function addScladItemsSetINSession(item_id, status){
        // console.log(item_id);
        // console.log(status);

        var link = "addScladItemsSetINSession.php";

        var reqData = {
            item_id: item_id,
            status: status
        };
        // console.log(reqData);

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
                    // console.log (res);

                    //Показать выбранные позиции
                    fillScladItemsInSet ();

                }else{
                    //--
                }
            }
        })
    }

    //Показывает выбранные позиции из сессии
    function fillScladItemsInSet (){
        var link = "fill_sclad_items_in_set_f.php";

        //Хоть что-то передадим
        var reqData = {
            type: 0
        };
        // console.log(reqData);

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
                    // console.log (res);

                    if (res.count > 0) {
                        $("#sclad_items_in_set_rezult").html(res.data);
                        $("#sclad_items_in_set").show();
                    }else{
                        $("#sclad_items_in_set_rezult").html('<span style="color: red; font-weight: bold; font-size: 80%; margin-left: 20px;">Ничего нет</span>');
                        $("#sclad_items_in_set").hide();
                    }

                    $("#itemInSetCount").html(res.count);

                }else{
                    //--
                }
            }
        })
    }

    //Удалить текущую позицию из набора (используем на странице самого склада + приходная накладная (создание и редактирование))
    function deleteScladItemsFromSet(item_id=0, ind=0, reload=false, edit=false, prihod_id=0){
         // console.log(ind);
         // console.log(edit);
         // console.log(prihod_id);
         //console.log(reload);

        let reqData ={};

        //Если не надо перезагружать страницу, значит мы скорее всего тут sclad.php
        if (!reload) {
            var link = "delete_sclad_item_from_set_f.php";

            reqData = {
                item_id: item_id
            };
        //А если надо перезагружать страницу, значит мы скорее всего тут например тут sclad_prihod_add.php или *edit.php
        }else{
            var link = "delete_sclad_item_from_prihod_data_f.php";

            reqData = {
                item_id: item_id,
                ind: ind,
                edit: edit,
                prihod_id: prihod_id
            };
            //console.log(reqData);
        }
        console.log(reqData);

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
            // действие, при ответе с сервера
            success: function(res){

                //Если не надо перезагружать страницу, значит мы скорее всего тут sclad.php
                if (!reload) {
                    //Показать выбранные позиции
                    fillScladItemsInSet();

                    if (item_id != 0) {
                        $("#selected_item_" + item_id).prop("checked", false);
                    } else {
                        $(".select_item").each(function () {
                            $(this).prop("checked", false);
                        })
                    }
                //А если надо перезагружать страницу, значит мы скорее всего тут например тут sclad_prihod_add.php или *edit.php
                }else{
                    location.reload();
                }

                // if(res.result == "success"){
                //     //console.log(111);
                // }
            }
        });
    }

    //Удалить текущую позицию из набора
    function copyScladItemsFromSet(item_id=0, ind=0, edit=false, prihod_id=0){
        // console.log(ind);

        var link = "copy_sclad_item_from_prihod_data_f.php";

        var reqData = {
            item_id: item_id,
            ind: ind,
            edit: edit,
            prihod_id: prihod_id
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
            // действие, при ответе с сервера
            success: function(res){


                location.reload();

            }
        });
    }

    //Изменяем кол-во в позиции на приходе
    function changeQuantityScladItemPrihod(ind, itemId, dataObj, edit=false, prihod_id=0){
        //console.log(ind);
        //console.log(itemId);
        //console.log(dataObj);

        var link = "add_quantity_sclad_item_prihod_f.php";

        //количество
        var quantity = dataObj.value;
        //console.log(quantity);

        if (!isNaN(quantity)){
            if (quantity >= 0) {
                var reqData = {
                    item_id: itemId,
                    ind: ind,
                    quantity: quantity,
                    edit: edit,
                    prihod_id: prihod_id
                };
                //console.log(reqData);

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
                        //console.log(res.data);

                        //changeSumScladItemPrihod(ind, itemId);

                    }
                });
            }else{
                $(dataObj).addClass("input_error");
            }
        }else{
            $(dataObj).addClass("input_error");
        }
    }

    //Изменяем цену позиции на приходе
    function changePriceScladItemPrihod(ind, itemId, dataObj, edit=false, prihod_id=0){
        //console.log(ind);
        //console.log(itemId);
        //console.log(dataObj);

        var link = "add_price_sclad_item_prihod_f.php";

        //количество
        var price = dataObj.value;
        // console.log(price);
        // console.log(price > 0);

        if (!isNaN(price)) {

            var reqData = {
                item_id: itemId,
                ind: ind,
                price: price,
                edit: edit,
                prihod_id: prihod_id
            };
            //console.log(reqData);

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
                    // console.log(res.data);

                    //changeSumScladItemPrihod(ind, itemId);

                }
            });
        }else{
            $(dataObj).addClass("input_error");
        }
    }

    //Изменяем сумму позиции, а потом общую
    function changeSumScladItemPrihod(ind, item_id){

        //$(".sclad_item_prihod_count").each(function(){
            // console.log($(this).val());
            // console.log($("#price_"+item_id+"_"+ind).val());
            // console.log($("#price_"+item_id+"_"+ind).val() * $(this).val());

            // if ($(this).val() > 0){
                $("#summ_"+item_id+"_"+ind).html( number_format($("#price_"+item_id+"_"+ind).val() * $("#sclad_item_prihod_count_"+item_id+"_"+ind).val(), 2, '.', ''));
            // }
        //})

        //Общая сумма
        let summ = 0;

        $(".sclad_item_prihod_summ").each(function() {
            summ += parseFloat($(this).html());
        });
        $("#itemInSetSumm").html(number_format(summ, 2, '.', ''));

    }

    //Изменяем тип гарантии позиции (гарантия, срок годности или нихера)
    function changeExpGarantTypeScladItemPrihod(ind, itemId, dataObj, edit=false, prihod_id=0){
        //console.log(dataObj.val());
        //console.log(this);

        var link = "add_exp_garant_type_sclad_item_prihod_f.php";

        //тип (гарантия или срок годности или ничего)
        var eg_type = dataObj.value;
        //console.log(eg_type);

        var reqData = {
            item_id: itemId,
            ind: ind,
            eg_type: eg_type,
            edit: edit,
            prihod_id: prihod_id
        };
        //console.log(reqData);

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
            // действие, при ответе с сервера
            success: function(res){
                // console.log(res.data);

                //Ничего не делаем, потому что все и так хорошо?

            }
        });
    }

    //Изменяем дату гарантии позиции (гарантия, срок годности)
    function changeExpGarantDateScladItemPrihod(ind, itemId, newDate){
        // console.log(newDate);
        //console.log(this);

        var link = "add_exp_garant_date_sclad_item_prihod_f.php";

        //дата
        // var eg_date = dataObj.value;
        var eg_date = newDate;
        //console.log(eg_date);

        var reqData = {
            item_id: itemId,
            ind: ind,
            eg_date: eg_date,
            prihod_id: $("#prihod_id").val(),
            edit: $("#prihod_edit").val()
        };
        // console.log('777');
        //console.log(reqData);

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
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res.data);

                //Ничего не делаем, потому что все и так хорошо?

            }
        });
    }

    //Делаем что-то после изменения даты в календаре
    function doSomeThingAfterChangeCalendar(class_arr, id, curDate, newDate) {
        //console.log(arguments);

        //Будем тут работать только по конкретным условиям (не на всех страницах это надо)

        //Если есть класс .eg_date, значит работаем "на складе", например приход
        if(class_arr.indexOf("eg_date") != -1){


            //Если у нас есть нужный объект, то работаем
            if (typeof(eg_date_arr) != "undefined") {
                //console.log(eg_date_arr);

                let item_id = id.split("_")[1];
                let ind = id.split("_")[2];

                if (!(item_id in eg_date_arr)){
                    eg_date_arr[item_id] = [];
                }
                if (!(ind in  eg_date_arr[item_id])){
                    eg_date_arr[item_id][ind] = $(this).val();
                }

                changeExpGarantDateScladItemPrihod(ind, item_id, newDate);
            }
        }
    }

    //Проверяем и добавляем приход
    function Ajax_sclad_prihod_add(edit = false){
        //console.log(edit);

        hideAllErrors();

        let all_good = true;

        $(".sclad_item_prihod_count").each(function(){
            // console.log(typeof(Number($(this).val())));
            // console.log(Number($(this).val()));
            // console.log(isNaN($(this).val()));

            if (!isNaN($(this).val())){
                if (Number($(this).val()) > 0){

                    $(this).removeClass("input_error");

                }else{
                    all_good = false;

                    $(this).addClass("input_error");
                    // input_error
                    //return false;
                }
            }else{
                //ошибка
            }
        })
        // console.log(all_good);

        if ($("#provider").val().length == 0){
            all_good = false;
            $("#provider").addClass("input_error");
        }else{
            $("#provider").removeClass("input_error");
        }

        if ($("#prov_doc").val().length == 0){
            all_good = false;
            $("#prov_doc").addClass("input_error");
        }else{
            $("#prov_doc").removeClass("input_error");
        }

        if (all_good){

            //Добавляем приходную накладную
            let link = "sclad_prihod_add_f.php";

            if (edit){
                link = "sclad_prihod_edit_f.php";
            }

            //Надо что-то передать
            let reqData = {
                provider_name: $("#provider").val(),
                prov_doc: $("#prov_doc").val(),
                filial_id: $("#SelectFilial").val(),
                prihod_time: $("#iWantThisDate2").val(),
                comment: '',
                prihod_id: $("#prihod_id").val(),
                summ: $("#itemInSetSumm").html()
            };
            // console.log(reqData);

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
                // действие, при ответе с сервера
                success: function(res){
                    // $('#errrror').html(res);
                    // console.log(res.data);

                    //Переедем в накладную?
                    //window.open('sclad_prihod.php?id='+res.data);
                    document.location.href = 'sclad_prihod.php?id='+res.data;
                }
            });

        }else{
            $('#errror').html('<span style="color: red; font-weight: bold;">Проверьте введённые данные.</span>');
        }
    }

    //Отмена действий на складе (отмена прихода, перемещения, списания, ...)
    function Ajax_sclad_cancel(from, to){

        //!!!Добавить потом сюда условия всякие
        //if (from == 'prihod_add'){
            var link = "sclad_prihod_cancel_f.php";
        //}

        //Надо что-то передать
        var reqData = {
            id: 1
        };
        // console.log(reqData);

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
            // действие, при ответе с сервера
            success: function(res){
                // $('#errrror').html(res);
                //console.log(res.data);

                //Переедем по ссылке
                document.location.href = to;
            }
        });
    }

    //Показываем блок с поиском позиции на складе для добавления её в список на приход
    function showAddNewScladItemsSetINSession(){

        //$(".context-menu").remove();

        //Если позиция, спросим еще, про единицы измерения
        var unit_select = '';

        var descr = 'Добавить позицию';

        $('#overlay').show();

        //var buttonsStr = '<input type="button" class="b" value="Ok" onclick="addNewScladItemsSetINSession();">';
        var buttonsStr = '';

        // Создаем меню:
        var menu = $('<div/>', {
            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
        })
            .css({
                "height": "300px"
            })
            .appendTo('#overlay')
            .append(
                $('<div/>')
                    .css({
                        "height": "100%",
                        "border": "1px solid #AAA",
                        "position": "relative"
                    })
                    .append('<span style="margin: 5px;"><i>'+descr+'</i></span>')
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "width": "100%",
                                "margin": "auto",
                                "top": "-110px",
                                "left": "0",
                                "bottom": "0",
                                "right": "0",
                                "height": "50%"
                            })
                            .append('<div style="margin-top: 3px;"><span style="font-size:90%; color: #333; ">Введите название</span><br>' +
                                '<input type="text" name="sclad_item_search" id="sclad_item_search" placeholder="Введите для поиска" value="" class="sclad_item_search" autocomplete="off" style="width: 250px; font-size: 120%;">' +
                                '<ul id="search_result_sclad_item" class="search_result_sclad_item"></ul>')
                            .append(unit_select)
                    )
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "bottom": "2px",
                                "width": "100%"
                            })
                            .append('<div id="existCatItem" class="error"></div>')
                            .append(buttonsStr+
                                '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove(); ">'
                            )
                    )
            );

        menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
    }

    function addNewScladItemsSetINSession(itemId){
        //console.log($("#prihod_edit").val());

        let link = "add_new_sclad_item_from_prihod_data_f.php";

        let reqData = {
            item_id: itemId,
            prihod_id: $("#prihod_id").val(),
            edit: $("#prihod_edit").val()
        };
        //console.log(reqData);

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
            // действие, при ответе с сервера
            success: function(res){
                // console.log(res.data);

                location.reload();

            }
        });
    }

    //Показываем блок с кнопками Для проведения наряда
    function showPrihodClose(prihod_id){
        //console.log(mode);
        let rys = false;

        rys = confirm("Провести приходную накладную?");

        if (rys) {

            let link = "prihod_close_f.php";

            let reqData = {
                prihod_id: prihod_id
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
                            window.location.href = "sclad_prihod.php?id="+prihod_id;
                        }, 200);
                    } else {

                    }
                }
            })
        }
    }

    //Показываем блок с кнопками Для снятия проведения приходной накладной
    function showPrihodOpen(prihod_id){
        //console.log(prihod_id);

        // убираем класс ошибок
        hideAllErrors ();

        var rys = false;

        rys = confirm("Снять отметку о проведении приходной накладной? Со склада будет списано нужное количество.");

        if (rys) {

            var link = "prihod_open_f.php";

            var reqData = {
                prihod_id: prihod_id
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
                    // console.log (res);

                    if (res.result == "success") {
                        setTimeout(function () {
                            window.location.href = "sclad_prihod.php?id="+prihod_id;
                        }, 200);
                    } else {
                        $("#errror").html('<div class="query_neok">'+res.data+'</div>')
                    }
                }
            })
        }
    }

    //Показываем блок для добавления статуса звонка
    function showChangePnoneCallMark(client_id, status){
        // console.log(client_id);

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        //Сегодняшняя дата
        let today = getTodayDate();

        let descr = 'Информация о звонке';
        let descr2 = '';
        let whenRecall = '';

        //Отметка о телефонном звонке
        if (status == 8) {
            descr2 = '<i class="fa fa-phone-square" style="color: red; font-size: 120%;" title="Не звонить"></i> Не звонить';
        }
        if (status == 6) {
            descr2 = '<i class="fa fa-phone-square" style="color: orange; font-size: 120%;" title="Не дозвонились"></i> Не дозвонились';
        }
        if (status == 7) {
            descr2 = '<i class="fa fa-phone-square" style="color: blue; font-size: 120%;" title="Записались"></i> Записались';
        }
        if (status == 5) {
            descr2 = '<i class="fa fa-phone-square" style="color: #b35bff; font-size: 120%;" title="Перезвонить"></i> Перезвонить';
            whenRecall = '<div style="margin-top: 10px;">Дата, когда перезвонить <input type="text" id="recallDate" name="recallDate" class="dateс" style="color: rgb(30, 30, 30); font-size: 12px; border: 1px solid rgba(0,220,220,1);" value="'+today+'" onfocus="this.select();_Calendar.lcs(this)"'+
                ' onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off"></div>'
        }
        if (status == 4) {
            descr2 = '<i class="fa fa-phone-square" style="color: #93021e; font-size: 120%;" title="Записались"></i> Записались';
        }
        if (status == 3) {
            descr2 = '<i class="fa fa-phone-square" style="color: #b1ffad; font-size: 120%;" title="Записались"></i> Записались';
        }

        let buttonsStr = '<input type="button" class="b" value="Ok" onclick="Ajax_changePnoneCallMark('+client_id+', '+status+');">';

        $('#overlay').show();

        // Создаем меню:
        var menu = $('<div/>', {
            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
        })
            .css({
                "height": "300px"
            })
            .appendTo('#overlay')
            .append(
                $('<div/>')
                    .css({
                        "height": "100%",
                        "border": "1px solid #AAA",
                        "position": "relative"
                    })
                    .append('<div style="margin: 5px;"><i>'+descr+'</i></div>')
                    .append('<div style="margin: 5px;"><i>'+descr2+'</i></div>')
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "width": "100%",
                                "margin": "auto",
                                "top": "-110px",
                                "left": "0",
                                "bottom": "0",
                                "right": "0",
                                "height": "50%"
                            })
                            .append('<div style="margin-top: 40px;">Дата звонка <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="color: rgb(30, 30, 30); font-size: 12px; border: 1px solid rgba(0,220,220,1);" value="'+today+'" onfocus="this.select();_Calendar.lcs(this)"'+
                                ' onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off"></div>')
                            .append('<div style="margin-top: 10px;"><span style="font-size:90%; color: #333; ">Введите комментарий</span><br>' +
                                '<textarea name="phoneCallComment" id="phoneCallComment" cols="35" rows="5"></textarea></div>')
                            .append(whenRecall)
                    )
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "bottom": "2px",
                                "width": "100%"
                            })
                            .append('<div id="existonCatItem" class="error"></div>')
                            .append(buttonsStr+
                                '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove(); ">'
                            )
                    )
            );

        menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

    }

    //Проверяем и добавляем приход
    function Ajax_changePnoneCallMark(client_id, status){
        //console.log(edit);

        hideAllErrors();

        //Добавляем
        let link = "change_pnone_call_mark_f.php";

        //Надо что-то передать
        let reqData = {
            client_id: client_id,
            status: status,
            call_time: $("#iWantThisDate2").val(),
            comment: $("#phoneCallComment").val()
        };

        if (status == 5){
            reqData['recall_date'] = $("#recallDate").val()
        }

        // console.log(reqData);

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
            // действие, при ответе с сервера
            success: function(res) {
                // $('#errrror').html(res);
                if (res.result == "success") {
                    location.reload();
                }
            }
        });


    }


    //Удаляем замечание сотруднику
    function Ajax_RemarkToEmployeeDelete (id){
        //console.log();

        let rys = false;

        rys = confirm("Вы собираетесь удалить замечание. \n\nВы уверены?");

        if (rys) {

            let link = "delete_remark_to_employee_f.php";
            //console.log(link);

            let reqData = {
                remark_to_employee_id: id
            };
            //console.log(reqData);

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
                    // console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        alert(res.data);
                        //$("#overlay").hide();
                        $('#errrror').html('<div class="query_neok">' + res.data + '</div>');
                    }
                }
            });
        }
    }

    //Отметка о ДР если поздравили или нет
    function changeCongrat(dataObj, worker_id, congr_id, congr_status, year){
        //console.log(dataObj);

        let link = "changeCongrat_f.php";
        //console.log(link);

        let reqData = {
            worker_id: worker_id,
            congr_id: congr_id,
            congr_status: congr_status,
            year: year
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
                // console.log(res);

                if (res.result == "success") {
                    //location.reload();
                    let new_congr_id = res.data

                    // console.log(new_congr_id);

                    if (congr_status == 1){
                        $(dataObj).parent().html('<i class="fa fa-check-square" aria-hidden="true" style="color: #00DCDC; cursor: pointer; text-shadow: 1px 1px 4px #fbff00, 0px 0px 10px #c3ffbc52;" onclick="changeCongrat(this, ' + worker_id + ', ' + new_congr_id + ',  0, ' + year + ')" title="Уже поздравили"></i>');
                    }else{
                        $(dataObj).parent().html('<span id="congrat_button"><i class="fa fa-check-square" aria-hidden="true" style="color: rgb(216 216 216); cursor: pointer; text-shadow: none;" onclick="changeCongrat(this, ' + worker_id + ', 0, 1, ' + year + ')" title="Еще не поздравили"></i></span>');
                    }

                } else {
                    // alert(res.data);
                    // //$("#overlay").hide();
                    // $('#errrror').html('<div class="query_neok">' + res.data + '</div>');
                }
            }
        });
    }


    //Загрузка категорий нового прайса 2021
    function getPriceCategories (){

        var link = "get_price2_categories_f.php";

        var reqData = {
            type: 5
        };
        //console.log(reqData);

        //Если надо выделить группу

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                if (res.result == 'success') {

                    $("#price_cat_rezult").html(res.data);


                    //!!! Правильный пример контекстного меню (правильный? точно? ну пока работает)

                    /*                    var menuArea = document.querySelector(".tree");

                                        //if(menuArea){
                                            menuArea.addEventListener( "contextmenu", event => {
                                                event.preventDefault();

                                                contextMenuShow(0, 0, event, "sclad_cat");

                                            }, false);
                                        //}*/

                }
            }
        })
    }


    //Показываем блок для добавления позиции нового прайса 2021
    function showPriceCatItemAdd(targetId, type){
        // console.log(type);

        let descr = 'Новая позиция';

        if (type == 'category'){
            descr = 'Новая категория';
        }

        //Если позиция, спросим еще, про единицы измерения
        let unit_select = '';

        if (type == 'item'){
            // unit_select =
            //     '<div style="margin-top: 20px;">' +
            //     '<select name="unit_sel" id="unit_sel" style="width: 200px;">'+
            //     '<option value="0">Выберите ед. измерения</option>' +
            //     '<option value="pc">штуки</option>' +
            //     '<option value="gr">граммы</option>'+
            //     '<option value="ml">милилитры</option>'+
            //     '<option value="sh">шприцы</option>'+
            //     '</select>' +
            //     '</div>';
        }

        let link = "get_price2_cat_show_f.php";

        let reqData = {
            targetId: targetId
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                if (res.result == 'success') {


                    $('#overlay').show();

                    //var res = '12313213';

                    var buttonsStr = '<input type="button" class="b" value="Ok" onclick="priceCatItemAdd('+targetId+', \''+type+'\');">';

                    if (type == 'category'){
                        buttonsStr = '<input type="button" class="b" value="Ok" onclick="priceCatItemAdd('+targetId+', \''+type+'\');">';
                    }

                    // Создаем меню:
                    let menu = $('<div/>', {
                        class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
                    })
                        .css({
                            "height": "300px"
                        })
                        .appendTo('#overlay')
                        .append(
                            $('<div/>')
                                .css({
                                    "height": "100%",
                                    "border": "1px solid #AAA",
                                    "position": "relative"
                                })
                                .append('<span style="margin: 5px;"><i>'+descr+'</i></span>')
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "width": "100%",
                                            "margin": "auto",
                                            "top": "-110px",
                                            "left": "0",
                                            "bottom": "0",
                                            "right": "0",
                                            "height": "50%"
                                        })
                                        .append('<div style="margin-top: 3px;"><span style="font-size:90%; color: #333; ">Введите название</span><br><input name="newCatItemName" id="newCatItemName" type="text" value="" style="width: 250px; font-size: 120%;">')
                                        .append(unit_select)
                                        .append(res.data)
                                )
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "bottom": "2px",
                                            "width": "100%"
                                        })
                                        .append('<div id="existCatItem" class="error"></div>')
                                        .append(buttonsStr+
                                            '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove(); ">'
                                        )
                                )
                        );

                    menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню


                }
            }
        })
    }

    //Добавляем категорию или позицию новый прайс 2021
    function priceCatItemAdd(targetId, type){
        //console.log();

        hideAllErrors();

        let newCatItemName = $("#newCatItemName").val();
        //console.log(newCatItemName);

        //Если будет позиция, то указываем еще и ед.изм.
        let item_units = false;
        let item_units_val = 0;

        if (type == 'item'){
            if ($("#unit_sel").val() == 0) {
                item_units = false;
            }else{
                item_units = true;
                item_units_val = $("#unit_sel").val();
            }
        }else{
            item_units = true;
        }

        if (newCatItemName.trim().length > 0){
            if (item_units) {

                let link = "fl_price2_cat_item_add_f.php";

                let reqData = {
                    name: newCatItemName.trim(),
                    type: type,
                    targetId: targetId,
                    item_units_val: item_units_val
                };
                //console.log(reqData);

                $.ajax({
                    url: link,
                    global: false,
                    type: "POST",
                    dataType: "JSON",
                    data: reqData,
                    cache: false,
                    beforeSend: function () {

                    },
                    success: function (res) {
                        //console.log (res);

                        $('.center_block').remove();
                        $('#overlay').hide();

                        if (res.result == 'success') {
                            // console.log (res);

                            //Если категория, перезагрузим их
                            if (type == 'category') {
                                getPriceCategories();
                            }

                            //Если позиция, загрузим позиции этой категории
                            if (type == 'item') {
                                getPriceItems(targetId, 0, 1000, false, true, targetId);
                            }

                        } else {
                            $("#existCatItem").html(res.data);
                            $("#existCatItem").show();
                        }
                    }
                })
            }else{
                $("#existCatItem").html('<span style="color: red; font-weight: bold;">Выберите единицы измерения</span>');
                $("#existCatItem").show();
            }
        }else{
            $("#existCatItem").html('<span style="color: red; font-weight: bold;">Ничего не ввели</span>');
            $("#existCatItem").show();
        }

    }

    //Загрузка элементов нового прайса 2021
    function getPriceItems (cat_id, start, limit, free=true, pick=false, pick_id=-1, search_data=''){
        //Для позиции ВООБЩЕ СОВСЕМ БЕЗ категории free == 'true'

        let link = "get_price2_items_f.php";

        let reqData = {
            cat_id: cat_id,
            start: start,
            limit: limit,
            free: free,
            search_data: search_data
        };
        // console.log(reqData);

        //Если надо выделить группу c id == pick_id
        if (pick){
            //Сначала очищаем у всех окраску
            $(".droppable").css({"background-color": ""});

            //Теперь покрасим
            $("#cat_"+pick_id).css({"background-color": "rgba(131, 219, 83, 0.5)"});
        }

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res.q);

                if (res.result == 'success') {

                    if (cat_id == 0){
                        if (free){
                            //Если поиск по фразе делаем
                            if (search_data.length > 0){
                                $("#cat_name_show").html("Результат поиска");
                                $("#cat_name_show").show();
                            }
                        }else {
                            $("#cat_name_show").html("Вне категории");
                            $("#cat_name_show").show();
                        }
                    }else {
                        $("#cat_name_show").html($("#cat_" + cat_id).attr("cat_name"));
                        $("#cat_name_show").show();
                    }

                    if (res.count > 0) {
                        $("#price_items_rezult").html(res.data);
                    }else{
                        $("#price_items_rezult").html('<span style="color: red; font-weight: bold; font-size: 80%; margin-left: 20px;">Ничего не найдено</span>');
                    }

                }
            }
        })
    }

    //Показываем блок для добавления позиции нового прайса 2021
    function showPriceCatItemEdit(id, type){
        // console.log(type);

        $(".context-menu").remove();

        //Если позиция, спросим еще, про единицы измерения
        let unit_select = '';

        //if (type == 'category') {
            let descr = 'Редактировать категорию';
            let oldName = $("#cat_" + id).attr("cat_name");
        //}

        if (type == 'item') {
            descr = 'Редактировать позицию';
            oldName = $("#item_name_"+id).attr("item_name");

            //console.log(oldName);

            // unit_select =
            //     '<div style="margin-top: 20px;">' +
            //     '<select name="unit_sel" id="unit_sel" style="width: 200px;">'+
            //     '<option value="0">Выберите ед. измерения</option>' +
            //     '<option value="pc">штуки</option>' +
            //     '<option value="gк">граммы</option>'+
            //     '<option value="ml">милилитры</option>'+
            //     '<option value="sh">шприцы</option>'+
            //     '</select>' +
            //     '</div>';
        }

        if (oldName.length > 0){

            $('#overlay').show();

            let buttonsStr = '<input type="button" class="b" value="Ok" onclick="priceCatItemEdit('+id+', \''+type+'\');">';

            // Создаем меню:
            let menu = $('<div/>', {
                class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
            })
                .css({
                    "height": "300px"
                })
                .appendTo('#overlay')
                .append(
                    $('<div/>')
                        .css({
                            "height": "100%",
                            "border": "1px solid #AAA",
                            "position": "relative"
                        })
                        .append('<span style="margin: 5px;"><i>'+descr+'</i></span>')
                        .append(
                            $('<div/>')
                                .css({
                                    "position": "absolute",
                                    "width": "100%",
                                    "margin": "auto",
                                    "top": "-110px",
                                    "left": "0",
                                    "bottom": "0",
                                    "right": "0",
                                    "height": "50%"
                                })
                                .append('<div style="margin-top: 3px;"><span style="font-size:90%; color: #333; ">Введите новое название</span><br><input name="newCatItemName" id="newCatItemName" type="text" value="'+oldName+'" style="width: 250px; font-size: 120%;">')
                                .append(unit_select)
                        )
                        .append(
                            $('<div/>')
                                .css({
                                    "position": "absolute",
                                    "bottom": "2px",
                                    "width": "100%"
                                })
                                .append('<div id="existCatItem" class="error"></div>')
                                .append(buttonsStr+
                                    '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove(); ">'
                                )
                        )
                );

            menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

            //Выделяем пункт в select единиц измерения (Если позиция)
            if (type == 'item') {
                //!!! Поиск по аттрибуту в DOM
                //!!! Выбор пунтка в select
                let item_unit = $('[item_unit_' + id + ']').attr('item_unit_' + id);

                document.querySelector('#unit_sel').value = item_unit;
            }

        }

    }

    //Редактируем имя категории/позиции
    function priceCatItemEdit(id, type){
        //console.log();

        hideAllErrors();

        //!!! Объявим локальный объект для этой ф-ции. потом может исправим и вынесем выше в для всех
        //Еденицы измерения
        let units = {
            pc: 'шт.',
            gr: 'г.',
            ml: 'мл.',
        }

        let newCatItemName = $("#newCatItemName").val();
        //console.log(newCatItemName);

        //Если будет позиция, то указываем еще и ед.изм.
        let item_units = false;
        let item_units_val = 0;

        if (type == 'item'){
            if ($("#unit_sel").val() == 0) {
                item_units = false;
            }else{
                item_units = true;
                item_units_val = $("#unit_sel").val();
            }
        }else{
            item_units = true;
        }
        //console.log(item_units);

        if (newCatItemName.trim().length > 0){
            //console.log(newCatItemName.trim().length);

            if (item_units) {

                let link = "fl_price2_cat_item_edit_f.php";

                let reqData = {
                    name: newCatItemName.trim(),
                    id: id,
                    type: type,
                    item_units_val: item_units_val
                };
                //console.log(reqData);

                $.ajax({
                    url: link,
                    global: false,
                    type: "POST",
                    dataType: "JSON",
                    data: reqData,
                    cache: false,
                    beforeSend: function () {

                    },
                    success: function (res) {
                        //console.log (res);

                        $('.center_block').remove();
                        $('#overlay').hide();

                        if (res.result == 'success') {
                            //console.log (res);

                            getPriceCategories ();
                            //getScladItems (cat, 0, 1000, true);

                            if (type == 'item') {
                                //Обновим имя, не обновляя страницу
                                $('#item_name_'+id).html(newCatItemName);
                                //Обновим ед.изм., не обновляя страницу
                                $('[item_unit_'+id+']').attr('item_unit_'+id, item_units_val);
                                $('[item_unit_'+id+']').html(units[item_units_val]);
                            }

                        } else {
                            $("#existCatItem").html(res.data);
                            $("#existCatItem").show();
                        }
                    }
                })
            }else {
                $("#existCatItem").html('<span style="color: red; font-weight: bold;">Выберите единицы измерения</span>');
                $("#existCatItem").show();
            }
        }else{
            $("#existCatItem").html('<span style="color: red; font-weight: bold;">Ничего не ввели</span>');
            $("#existCatItem").show();
        }

    }

    //Перемещаем позицию в другую категорию
    function movePriceItemInCategory (item_id, cat_id, target_cat_id){

        let link = "move_price2_items_in_cat_f.php";

        let reqData = {
            item_id: item_id,
            cat_id: cat_id,
            target_cat_id: target_cat_id
        };
        //console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {

            },
            success: function (res) {
                //console.log (res);

                if (res.result == 'success') {

                    // $("#sclad_items_rezult").html(res.data);

                    //Если надо выделить группу
                    // if (pick){
                    //     //Сначала очищаем у всех окраску
                    //     $(".droppable").css({"background-color": ""});
                    //
                    //     //Теперь покрасим
                    //     $("#cat_"+pick_id).css({"background-color": "rgba(131, 219, 83, 0.5)"});
                    // }

                    getPriceCategories ();

                }
            }
        })

    }

    //Функция получения данных по лампам
    function reportLamps() {

        let link = "reportLamps_f.php";

        let reqData = {
            month_start: $("#month_start").val(),
            year_start: $("#year_start").val(),
            month_end: $("#month_end").val(),
            year_end: $("#year_end").val(),
            filial_id: $("#SelectFilial").val(),
        }
        // console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
                $('#res_table_tmpl').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success: function (res) {
                 // console.log(res);
                // console.log(res.query);

                if (res.result == "success") {
                    // console.log(res.data_count);
                    // console.log(res.spr_lamps_j);

                    //очищаем
                    $("#res_table_tmpl").html('');
                    //помещаем
                    $("#res_table_tmpl").append('<div style="padding: 2px; width: 75%; background-color: azure; border: 1px solid #CCC; margin-bottom: 5px;"><canvas id="canvas" height="300" width="700" style="background-color: white; border: 1px solid #CCC;"></canvas></div>');

                    //заполняем данные
                    let color, lamp_datas = [];

                    for (lamp_id in res.data_count) {
                        let temp_lamp_datas = {};

                        color = randColor2();

                        temp_lamp_datas['label'] = res.spr_lamps_j[lamp_id];
                        // console.log(res.spr_lamps_j[lamp_id]);
                        temp_lamp_datas['backgroundColor'] = color;
                        temp_lamp_datas['borderColor'] = color;
                        temp_lamp_datas['fill'] = false;
                        temp_lamp_datas['data'] = [];

                        res.data_count[lamp_id].forEach(function(data){
                            // console.log(data['x']);
                            // console.log(data['y']);

                            temp_lamp_datas['data'].push({'x': data['x'], 'y': data['y']})
                        })

                        lamp_datas.push(temp_lamp_datas);
                    }
                    //console.log(lamp_datas);


                    let config = {
                        type: 'line',
                        data: {
                            // labels: [ // Date Objects
                            //     '2021-03-01',
                            //     '2021-03-05',
                            //     '2021-03-10',
                            //     '2021-03-12',
                            //     '2021-03-15',
                            //     '2021-03-25',
                            //     '2021-03-30'
                            // ],
                            datasets: lamp_datas,
                        },
                        options: {
                            title: {
                                text: 'Отчёт'
                            },
                            scales: {
                                xAxes: [{
                                    type: 'time',
                                    /*time: {
                                        parser: timeFormat,
                                        // round: 'day'
                                        tooltipFormat: 'll HH:mm'
                                    },*/
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Дата'
                                    }
                                }],
                                yAxes: [{
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Значение'
                                    }
                                }]
                            },
                        }
                    };
                    //console.log(config);

                    //Рисуем
                    let ctx = document.getElementById('canvas').getContext('2d');
                    window.myLine = new Chart(ctx, config);



                } else {
                    //--
                    $('#res_table_tmpl').html(res.data);
                }
            }
        })
    }




