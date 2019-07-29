

    //Ждем ждём ожидание
    //Взято с Хабра https://habrahabr.ru/post/134823/
    //first — первая функция,которую нужно запустить
    wait = function(first){
        //класс для реализации вызова методов по цепочке #поочередный вызов
        return new (function(){
            var self = this;
            var callback = function(){
                var args;
                if(self.deferred.length) {
                    /* превращаем массив аргументов
                     в обычный массив */
                    args = [].slice.call(arguments);

                    /* делаем первым аргументом функции-обертки
                     коллбек вызова следующей функции */
                    args.unshift(callback);

                    //вызываем первую функцию в стеке функций
                    self.deferred[0].apply(self, args);

                    //удаляем запущенную функцию из стека
                    self.deferred.shift();
                }
            }
            this.deferred = []; //инициализируем стек вызываемых функций

            this.wait = function(run){
                //добавляем в стек запуска новую функцию
                this.deferred.push(run);

                //возвращаем this для вызова методов по цепочке
                return self;
            }

            first(callback); //запуск первой функции
        });
    }

    //Для добавления суммы в оплате наряда
	$('#addSummInPayment').click(function () {

		var lefttopay = Number(document.getElementById("leftToPay").innerHTML);
		var available = Number(document.getElementById("addSummInPayment").innerHTML);
		//console.log(lefttopay);
		//console.log(available);

		var rezult = 0;

		if (available >= lefttopay) {
            rezult = lefttopay;
		}else{
            //rezult = lefttopay - available;
            rezult = available;
        }

		document.getElementById("summ").value = rezult;

	});

    //Показываем блок с суммами и кнопками Для оплаты наряда
    function showPaymentAdd(mode){
        //console.log(mode);

        var Summ = document.getElementById("summ").value;

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    summ:Summ
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){
                    //$('#overlay').show();

                    //var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_payment_add(\'add\')">';

                    /*if (mode == 'edit'){
                        buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_payment_add(\'edit\')">';
                    }*/

                    if (mode == 'add'){
                       Ajax_payment_add('add');
                    }

                    if (mode == 'edit'){
                        Ajax_payment_add('edit');
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

    function Ajax_payment_add_cert(mode){
        //console.log(mode);

        var payment_id = 0;

        var link = "payment_cert_add_f.php";

        if (mode == 'edit'){
            link = "payment_cert_edit_f.php";
            payment_id = document.getElementById("payment_id").value;
        }

        var Summ = $("#summ").html();
        //console.log(Summ);
        var invoice_id = $("#invoice_id").val();
        //console.log(invoice_id);

        var filial_id = $("#filial_id").val();

        var client_id = $("#client_id").val();
        //console.log(client_id);
        var date_in = $("#date_in").val();
        //console.log(date_in);

        //!!!тут сделано только для одного сертификата, если надо переделать, то тут
        var cert_id = $(".cert_pay").attr('cert_id');
        //console.log(cert_id);

        var comment = $("#comment").val();
        //console.log(comment);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    client_id: client_id,
                    invoice_id: invoice_id,
                    filial_id: filial_id,
                    cert_id: cert_id,
                    summ: Summ,
                    date_in: date_in,
                    comment: comment
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //!!! перенести вывод ошибки нормально, а то
                //$('#errror').html(res.data); не работает, которое ниже
                //Приходится смотреть через консоль
                console.log(res);
                
                $('.center_block').remove();
                $('#overlay').hide();

                if(res.result == "success"){
                    //$('#data').hide();
                    $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
                        /*'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Оплата наряда прошла успешно</li>'+*/
                        '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">'+res.data+'</li>'+
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

    //Показываем блок с суммами и кнопками Для оплаты наряда сертификатом
    function showPaymentAddCert (mode){
        //console.log(mode);

        var Summ = $("#summ").html();

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    summ:Summ
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){
                    //$('#overlay').show();

                    //var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_payment_add(\'add\')">';

                    /*if (mode == 'edit'){
                        buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_payment_add(\'edit\')">';
                    }*/

                    if (mode == 'add'){
                        Ajax_payment_add_cert('add');
                    }

                    if (mode == 'edit'){
                        Ajax_payment_add_cert('edit');
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

    //Добавляем/редактируем в базу оплату
    function Ajax_payment_add(mode){
        //console.log(mode);

        var payment_id = 0;

        var link = "payment_add_f.php";

        if (mode == 'edit'){
            link = "payment_edit_f.php";
            payment_id = $("#payment_id").val();
        }

        var Summ = $("#summ").val();
        var invoice_id = $("#invoice_id").val();
        var filial_id = $("#filial_id").val();

        var client_id = $("#client_id").val();
        var date_in = $("#date_in").val();
        //console.log(date_in);

        var comment = $("#comment").val();
        //console.log(comment);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    client_id: client_id,
                    invoice_id: invoice_id,
                    filial_id: filial_id,
                    summ: Summ,
                    date_in: date_in,
                    comment: comment
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                console.log(res);   //!!! не убирай это, или сделай отображение ошибок


                $('.center_block').remove();
                $('#overlay').hide();

                if(res.result == "success"){
                    //$('#data').hide();
                    $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
                        /*'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Оплата наряда прошла успешно</li>'+*/
                        '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">'+res.data+'</li>'+
                        '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
                        '<a href="finance_account.php?client_id='+client_id+'" class="b">Управление счётом</a>'+
                        //'<a href="invoice.php?id='+invoice_id+'" class="b">Вернуться в наряд</a>'+
                        '</li>'+
                        '</ul>');
                }else{
                    $('#errror').html(res.data);
                }
            }
        });
    }

    //Выборка касса
    function Ajax_show_result_stat_cashbox(){

        var link = "ajax_show_result_cashbox_f.php";

        var summtype = $("input[name=summType]:checked").val();

        /*var zapisTypeAll = $("input[id=zapisTypeAll]:checked").val();
        if (zapisTypeAll === undefined){
            zapisTypeAll = 0;
        }
        var zapisTypeStom = $("input[id=zapisTypeStom]:checked").val();
        if (zapisTypeStom === undefined){
            zapisTypeStom = 0;
        }
        var zapisTypeCosm = $("input[id=zapisTypeCosm]:checked").val();
        if (zapisTypeCosm === undefined){
            zapisTypeCosm = 0;
        }*/

        var certificatesShow = $("input[id=certificatesShow]:checked").val();
        if (certificatesShow === undefined){
            certificatesShow = 0;
        }

        var reqData = {
            datastart: $("#datastart").val(),
            dataend: $("#dataend").val(),

            filial: $("#filial").val(),

            summtype: summtype,

            /*zapisTypeAll: zapisTypeAll,
             zapisTypeStom: zapisTypeStom,
             zapisTypeCosm: zapisTypeCosm,*/

            certificatesShow: certificatesShow
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            data: reqData,
            cache: false,
            beforeSend: function() {
                $('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                $('#qresult').html(data);

                $( "#tabs_w" ).tabs();
            }
        })
    }
    //Удалить текущую проплату
    function deletePaymentItem(id, client_id, invoice_id){
        //console.log(id);

        var rys = false;

        rys = confirm("Удалить оплату?");

        if (rys) {

            $.ajax({
                url: "payment_del_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    client_id: client_id,
                    invoice_id: invoice_id,
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    if (res.result == "success") {
                        location.reload();
                        //console.log(res.data);
                    }
                    if (res.result == "error") {
                        alert(res.data);
                    }
                    //console.log(data.data);

                }
            });
        }
    }

    //Удалить табель
    function fl_deleteTabelItem(tabel_id){
        //console.log(id);

        var rys = false;

        rys = confirm("Вы хотите удалить табель. \nЭто необратимо. Все РЛ будут откреплены.\nВсе прикрепленные документы будут удалены\n\nВы уверены?");

        if (rys) {

            $.ajax({
                url: "fl_tabel_del_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: tabel_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (data) {
                    /*if(data.result == "success"){

                     }*/
                    //console.log(data.data);
                    //location.reload();

                    //!!! переадресация на вкладку, может когда-нибудь организую
                    //http://localhost/gitasmedica/fl_tabels.php#tabs-5_324
                    window.location.href = "fl_tabels.php";
                }
            });

        }
    }

    //Удалить расчет
    function fl_deleteCalculateItem(id, client_id, invoice_id){
        //console.log(id);

        var rys = false;

        rys = confirm("Удалить расчетный лист?");

        if (rys) {

            $.ajax({
                url: "fl_check_calculate_in_tabel_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    calculate_id: id,
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if(res.result == "success"){
                        if (res.data == 0){
                            console.log(res);

                            $.ajax({
                                url: "fl_calculate_del_f.php",
                                global: false,
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    id: id,
                                    client_id: client_id,
                                    invoice_id: invoice_id,
                                },
                                cache: false,
                                beforeSend: function () {
                                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                                },
                                // действие, при ответе с сервера
                                success: function (data) {
                                    /*if(data.result == "success"){

                                     }*/
                                    //console.log(data.data);
                                    //location.reload();
                                    window.location.href = "invoice.php?id=" + invoice_id;
                                }
                            });

                        }
                    }
                    if(res.result == "error"){
                        alert("Расчётный лист добавлен в табель #"+res.data+".\n\nНельзя удалить.\n\nОбратитесь к руководителю.");
                        $("#tabel_info").html("<div class='query_neok'><a href='fl_tabel.php?id="+res.data+"' class='ahref'>Перейти в табель #"+res.data+"</a></div>");
                    }
                }
            });
        }
    }

    //Удалить затраты на материалы
    function fl_deleteMaterialConsumption(id, invoice_id){
        //console.log(id);

        var rys = false;

        rys = confirm("Вы собираетесь удалить затраты на материалы.\nЭто необратимое действие.\nРасчётный лист будет пересчитан.\nВы уверены?");

        if (rys) {

            $.ajax({
                url: "fl_delete_material_consumption_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    mat_cons_id: id,
                    invoice_id: invoice_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res.data2);
                    /*if(data.result == "success"){

                     }*/
                    //location.reload();
                    window.location.href = "invoice.php?id=" + invoice_id;
                }
            });
        }
    }

    //Сбросить проценты персональные на по умолчанию
    //function fl_changePersonalPercentCatdefault(workerID, catID, typeID){
    function fl_changePersonalPercentCatdefault(workerID){
        /*console.log(workerID);
        console.log(catID);
        console.log(typeID);*/

        var rys = false;

        rys = confirm("Сбросить на значения по умолчанию?");

        if (rys) {

            $.ajax({
                url: "fl_change_personal_percent_cat_default_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    worker_id: workerID,
                    //cat_id: catID,
                    //type: typeID,
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (data) {
                    if (data.result == "success") {
                        //console.log(data.data);
                        location.reload();
                    }
                }
            });
        }
    }

    //Перерасчёт расчёта
    function fl_reloadPercentsCalculate(workerID){

        var rys = false;

        /*var rys = confirm("Расчитать сумму заново?");

        if (rys) {

            $.ajax({
                url: "fl_reload_percents_calculate_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    worker_id: workerID,
                    //cat_id: catID,
                    //type: typeID,
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (data) {
                    if (data.result == "success") {
                        //console.log(data.data);
                        location.reload();
                    }
                }
            });
        }*/
    }

    //Для изменений в процентах персональных
    var changePersonalPercentCat_elems = document.getElementsByClassName("changePersonalPercentCat"), newInput;
    //console.log(elems);

    if (changePersonalPercentCat_elems.length > 0) {
        for (var i = 0; i < changePersonalPercentCat_elems.length; i++) {
            var el = changePersonalPercentCat_elems[i];
            el.addEventListener("click", function () {
                //var thisID = this.id;
                var workerID = this.getAttribute("worker_id");
                //console.log(this.getAttribute("worker_id"));
                var catID = this.getAttribute("cat_id");
                //console.log(this.getAttribute("cat_id"));
                var typeID = this.getAttribute("type_id");
                //console.log(this.getAttribute("type_id"));

                var thisVal = this.innerHTML;
                var newVal = thisVal;
                //console.log(this);
                //console.log(workerID);
                //console.log(catID);
                //console.log(typeID);
                //console.log(thisVal);
                //console.log(isNaN(thisVal));

                var inputs = this.getElementsByTagName("input");
                if (inputs.length > 0) return;
                if (!newInput) {

                    /*buttonDiv = document.createElement("div");
                    //buttonDiv.innerHTML = '<i class="fa fa-check" aria-hidden="true" title="Применить" style="margin-right: 4px;"></i> <i class="fa fa-refresh" aria-hidden="true" title="По умолчанию" style="color: red;"></i>';
                    buttonDiv.innerHTML = '<i class="fa fa-refresh" aria-hidden="true" title="По умолчанию" style="color: red;"></i>';
                    buttonDiv.style.position = "absolute";
                    buttonDiv.style.right = "-9px";
                    buttonDiv.style.top = "1px";
                    buttonDiv.style.fontSize = "12px";
                    buttonDiv.style.color = "green";
                    buttonDiv.style.border = "1px solid #BFBCB5";
                    buttonDiv.style.backgroundColor = "#FFF";
                    buttonDiv.style.padding = "0 6px";

                    buttonDiv.id = "changePersonalPercentCatdefault";*/

                    newInput = document.createElement("input");
                    newInput.type = "text";
                    newInput.maxLength = 5;
                    newInput.setAttribute("size", 20);
                    newInput.style.width = "40px";
                    newInput.addEventListener("blur", function () {
                        //console.log(newInput.parentNode.getAttribute("worker_id"));

                        workerID = newInput.parentNode.getAttribute("worker_id");
                        catID = newInput.parentNode.getAttribute("cat_id");
                        typeID = newInput.parentNode.getAttribute("type_id");

                        //Попытка обработать клика на кнопке для сброса на значения по умолчанию - провалилась, всегда сбрасывается на по умолчанию
                        //var changePersonalPercentCatdefault = document.getElementById("changePersonalPercentCatdefault");
                        //console.log(changePersonalPercentCatdefault.innerHTML);

                        //changePersonalPercentCatdefault.addEventListener("click", fl_changePersonalPercentCatdefault(workerID, catID, typeID), false);

                        //Новые данные
                        //if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (newInput.value > 100) || (isNaN(parseInt(newInput.value, 10)))) {
                        if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (isNaN(parseInt(newInput.value, 10)))) {
                            //newInput.parentNode.innerHTML = 0;
                            newInput.parentNode.innerHTML = thisVal;
                            newVal = thisVal;
                        } else {
                            newInput.parentNode.innerHTML = parseInt(newInput.value, 10);
                            newVal = parseInt(newInput.value, 10);
                        }
                        //console.log(this);
                        //console.log(workerID);

                        //console.log(thisVal == newVal);

                        if (Number(thisVal) != Number(newVal)) {

                            $.ajax({
                                url: "fl_change_personal_percent_cat_f.php",
                                global: false,
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    worker_id: workerID,
                                    cat_id: catID,
                                    type: typeID,
                                    val: newVal
                                },
                                cache: false,
                                beforeSend: function () {
                                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                                },
                                // действие, при ответе с сервера
                                success: function (res) {
                                    if (res.result == "success") {
                                        //console.log(data);
                                        $('#infoDiv').html(res.data);
                                        $('#infoDiv').show();
                                        setTimeout(function () {
                                            $('#infoDiv').hide('slow');
                                            $('#infoDiv').html();
                                        }, 1000);

                                        //location.reload();
                                    }

                                }
                            });
                        }
                    }, false);
                }

                //newInput.value = this.firstChild.innerHTML;
                newInput.value = thisVal;
                this.innerHTML = "";
                //this.appendChild(buttonDiv);
                this.appendChild(newInput);
                //newInput.innerHTML = ('<i class="fa fa-check" aria-hidden="true"></i>');
                newInput.focus();
                newInput.select();
            }.bind(el), false);
        }
    }

    //Функция для поочередного вывода на экран табелей для печати
    function fl_printCheckedWorkersTabels (){
        //console.log (calcIDForTabelINarr());


        wait(function(runNext){

            blockWhileWaiting (true);

            setTimeout(function(){
                runNext(calcIDForTabelINarr());
            }, 500);

        }).wait(function(runNext, workersIDs_arr){
            //используем аргументы из предыдущего вызова
            //console.log(workersIDs_arr.main_data)

            setTimeout(function(){

                var link = "fl_tabel_print_all.php";

                //console.log($('#SelectMonth').val());
                //console.log($('#SelectYear').val());

                var month = $('#SelectMonth').val();
                var year = $('#SelectYear').val();
                var office = $('#SelectFilialp').val();

                hideAllErrors ();
                $('#rezult').html('');


                workersIDs_arr.main_data.forEach(function(w_id, i, arr) {
                    //console.log(w_id);

                    var reqData = {
                        worker_id: w_id,
                        month: month,
                        year: year,
                        office: office
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
                            //console.log(res.tabel_ids);

                            if (res.result == "success") {
                                //console.log(res);
                                //console.log(JSON.parse(res.tabel_ids));

                                $('#rezult').append(res.data);

                                var tabel_ids = JSON.parse(res.tabel_ids);

                                tabel_ids.forEach(function(tabel_id, j, arr2) {
                                    fl_tabulation (tabel_id);
                                    //console.log(tabel_id);
                                })

                            } else if (res.result == "empty") {

                            } else {
                                $('#errror').html(res.data);
                            }
                        }
                    });
                });

                runNext();

            }, 1500);

        }).wait(function(runNext){
            //console.log(1);

            setTimeout(function(){
                var elems = document.getElementsByClassName('rezult_item');
                //console.log(elems);
                var arr = $.makeArray(document.getElementsByClassName('rezult_item'));
                //console.log(arr);

                arr.sort(function (a, b) {
                    a = $(a).attr('fio');
                    //console.log(a);
                    b = $(b).attr('fio');
                    //console.log(b);
                    return a.localeCompare(b);
                });
                //console.log(arr);

                for(var i=0; i<arr.length; i++){
                    //console.log(arr[i]);
                    //console.log(i);
                    //console.log((i+1)% 3);
                    //console.log(typeof (arr[i]));
                    //console.log(arr.classList.contains("rezult_item"));

                    if ((i+1)% 3 == 0){
                        //console.log(i);
                        //console.log(arr[i]);
                        //console.log(arr[i].classList.contains("rezult_item"));

                        arr[i].classList.add("rezult_item3print");

                    }
                }

                $(arr).appendTo("#rezult");
                //console.log(arr.length);


            }, 1500);

            blockWhileWaiting (false);

        });
    }

    //Собираем ID отмеченных РЛ в массив
    function calcIDForTabelINarr() {
        var ids_arr = {};
        var chkBoxData_arr = {};
        var calcIDForTabel_arr = {};
        calcIDForTabel_arr.data = [];
        calcIDForTabel_arr.main_data = [];

        $(".chkBoxCalcs").each(function(){
            if ($(this).attr("checked")){

                ids_arr = $(this).attr("name").split("_");
                //console.log(ids_arr[1]);

                //chkBoxData_arr  = $(this).attr("chkBoxData").split("_");
                //console.log(chkBoxData_arr);

                //var calcIDForTabel = ids_arr[1];

                calcIDForTabel_arr.data = $(this).attr("chkBoxData");
                calcIDForTabel_arr.main_data[calcIDForTabel_arr.main_data.length] = ids_arr[1];
                //console.log(ids_arr[1]);

            }
        });

        //console.log(calcIDForTabel_arr);

        return calcIDForTabel_arr;
    }

    //
    function fl_addNewTabelIN (newTabel){

        wait(function(runNext){

            setTimeout(function(){
                runNext(calcIDForTabelINarr());
            }, 1500);

        }).wait(function(runNext, calcIDForTabel_arr){
            //используем аргументы из предыдущего вызова
            //console.log(calcIDForTabel_arr)

            $.ajax({
                url: "fl_addCalcsIDsINSessionForTabel.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    calcArr: calcIDForTabel_arr
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        //console.log(res);
                        if (newTabel) {
                            //document.location.href = "fl_addNewTabel.php";
                            var openedWindow = iOpenNewWindow('fl_addNewTabel.php', 'newTabelwindow', 'width=800, height=800, scrollbars=yes,resizable=yes,menubar=no,toolbar=yes,status=yes');
                        }else{
                            //console.log(12333);
                            var openedWindow = iOpenNewWindow("fl_addINExistTabel.php", 'oldTabelwindow', 'width=800, height=800, scrollbars=yes,resizable=yes,menubar=no,toolbar=yes,status=yes');

                        }

                    }

                }
            });

        });
    }


    //
    function menuForAddINNewTabel(res, type_id, worker_id, filial_id, newTabel, noch, clear, dopData){
        // console.log(res);
        // console.log(newTabel);
        // console.log(noch);
        // console.log(dopData);
        // console.log(JSON.stringify(dopData));

        var buttonsStr = '';

        if (newTabel) {
            buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="fl_addNewTabel2(' + type_id + ', ' + worker_id + ', ' + filial_id + ')">';
        }else{
            buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="fl_addInExistTabel2(' + type_id + ', ' + worker_id + ', ' + filial_id + ')">';
        }
        //Если создаём пустой табель
        if (clear){
            buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="fl_addNewTabelClear(' + type_id + ', ' + worker_id + ', ' + filial_id + ')">';
        }

        //Если оформляем ночь
        if (noch){
            buttonsStr = "<input type='button' class='b' value='Далее' onclick='fl_addNewNoch(" + type_id + ", " + worker_id + ", " + filial_id + ", " + (JSON.stringify(dopData)) + ")'>";
        }

        // Создаем меню:
        var menu = $('<div/>', {
            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
        }).css({
            "top": "120px",
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
    Ajax_change_shed
    //
    function menuForAddINExistNewTabel(){

    }


    //Добавить расчетные листы в новый табель, попутно создать этот новый табель (Пока только диалоговое окно)
    function fl_addNewTabelIN2 (newTabel, type_id, worker_id, filial_id){

        /*if (newTabel) {
            var link = "fl_getCalcsFromSession_f.php";
        }else{
            var link = "fl_getCalcsFromSessionForExistTabel_f.php";
        }*/

        var link = "fl_getCalcsFromSession_f.php";

        var reqData = {
            newTabel: newTabel?1:0
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
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success: function (res) {
                //console.log (res);
                //console.log (res.length);

                if (res.length > 0) {
                    $('#overlay').show();

                    menuForAddINNewTabel(res, type_id, worker_id, filial_id, newTabel, false, false, {});
                }else{
                    $('#errrror').html('<div class="query_neok">Ошибка #34. Ничего не выбрано. Обновите выбор РЛ</div>');
                }
            }
        })
    }

    //Функция создания пустого табеля
    function fl_addNewClearTabelIN (newTabel, type_id, worker_id, filial_id){

        var link = "fl_menuForClearTabel_f.php";

        var reqData = {
            type_id: type_id,
            worker_id: worker_id,
            filial_id: filial_id
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

                    menuForAddINNewTabel(res, type_id, worker_id, filial_id, newTabel, false, true, {});
                }else{
                    $('#errrror').html('<div class="query_neok">Ошибка #52. Что-то пошло не так.</div>');
                }
            }
        })
    }

    //Рассчет ночи
    function fl_addNoch (noch, type_id, worker_id, filial_id){

        /*if (newTabel) {
            var link = "fl_getCalcsFromSession_f.php";
        }else{
            var link = "fl_getCalcsFromSessionForExistTabel_f.php";
        }*/

        var link = "fl_getCalcsFromSession_f.php";

        var reqData = {
            newTabel: 0
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
                // console.log (res);
                // console.log (res.length);

                if (res.length > 0) {
                    $('#overlay').show();

                    menuForAddINNewTabel(res, type_id, worker_id, filial_id, 0, noch, false, {});
                }else{
                    $('#errrror').html('<div class="query_neok">Ошибка #48. Ничего не выбрано. Обновите выбор РЛ</div>');
                }
            }
        })
    }

    //Рассчет ночи 2.0
    function fl_addReportNoch (day, month, year, type_id, worker_id, filial_id, filial_summ, zp_summ, invoice_ids){
        // console.log(day);
        // console.log(month);
        // console.log(year);
        // console.log(type_id);
        // console.log(worker_id);
        // console.log(filial_id);
        // console.log(filial_summ);
        // console.log(zp_summ);
        // console.log(invoice_ids);

        var link = "fl_getTabels_noch_f.php";

        var dopData = {
            day: day,
            month: month,
            year: year,
            summ: zp_summ
        };
        //console.log(dopData);

        var reqData = {
            type_id: type_id,
            worker_id: worker_id,
            filial_id: filial_id,
            dopData: dopData
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
                // console.log (res);
                // console.log (res.length);

                if (res.length > 0) {
                    $('#overlay').show();

                    menuForAddINNewTabel(res, type_id, worker_id, filial_id, 0, true, false, dopData);
                }else{
                    $('#errrror').html('<div class="query_neok">Ошибка #49. Нет табелей. Табель ассистенту можно добавить в <a href="fl_tabels2.php" class="ahref">Отчёте по часам</a></div>');
                }
            }
        })
    }

    //Добавляем в базу табель из сессии
    function fl_addNewTabel(){

        var link = "fl_tabel_add_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    tabelMonth: $("#tabelMonth").val(),
                    tabelYear: $("#tabelYear").val(),
                    summCalcs: $(".summCalcsNPaid").html()
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res.data);

                if(res.result == "success"){
                    //document.location.href = "fl_tabels.php";
                    window.close('newTabelwindow');
                }else{
                    $('#errror').html(res.data);
                }
            }
        });
    }

    //Добавляем в базу новый табель и РЛ в него из сессии
    function fl_addNewTabel2(type_id, worker_id, filial_id){
        //console.log($(".summCalcsForTabel").html());

        var link = "fl_tabel_add2_f.php";

        var reqData = {
            tabelMonth: $("#tabelMonth").val(),
            tabelYear: $("#tabelYear").val(),
            summCalcs: $(".summCalcsForTabel").html()
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
                //console.log(res);

                if(res.result == "success"){
                    //console.log(res);

                    //document.location.href = "fl_tabels.php";
                    //window.close('newTabelwindow');

                    $("#overlay").hide();
                    $(".center_block").remove();

                    setTimeout(function () {
                        refreshOnlyThisTab($("#refreshID_"+type_id+"_"+worker_id+"_"+filial_id+""), type_id, worker_id, filial_id);
                    }, 1000);


                }else{
                    //console.log(res);

                    $('#errror').html(res.data);
                    $("#overlay").hide();
                    $(".center_block").remove();
                }
            }
        });
    }

    //Добавляем в базу новый ПУСТОЙ табель без РЛ
    function fl_addNewTabelClear(type_id, worker_id, filial_id){
        //console.log($(".summCalcsForTabel").html());

        var link = "fl_tabel_add3_f.php";

        var reqData = {
            type_id: type_id,
            worker_id:  worker_id,
            filial_id: filial_id,
            tabelMonth: $("#tabelMonth").val(),
            tabelYear: $("#tabelYear").val()
        };
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
                //console.log(res);

                if(res.result == "success"){
                    //console.log(res);

                    //document.location.href = "fl_tabels.php";
                    //window.close('newTabelwindow');

                    $("#overlay").hide();
                    $(".center_block").remove();

                    setTimeout(function () {
                        refreshOnlyThisTab($("#refreshID_"+type_id+"_"+worker_id+"_"+filial_id+""), type_id, worker_id, filial_id);
                    }, 1000);


                }else{
                    //console.log(res);

                    $('#errror').html(res.data);
                    $("#overlay").hide();
                    $(".center_block").remove();
                }
            }
        });
    }

    //Добавляем в существующий табель РЛ из сессии
    function fl_addInExistTabel(){

        var link = "fl_add_in_tabel_f.php";
        //console.log(link);

        var tabelForAdding = $('input[name=tabelForAdding]:checked').val();
        //console.log(tabelForAdding);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    tabelForAdding: tabelForAdding
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == "success"){
                    //document.location.href = "fl_tabels.php";
                    window.close('oldTabelwindow');
                }else{
                    $('#errror').html(res.data);
                }
            }
        });
    }

    //Добавляем в существующий табель РЛ из сессии
    function fl_addInExistTabel2(type_id, worker_id, filial_id){

        var link = "fl_add_in_tabel2_f.php";
        //console.log(link);

        var tabelForAdding = $('input[name=tabelForAdding]:checked').val();
        var tabel_noch_mark = $('input[name=tabelForAdding]:checked').attr("tabel_noch_mark");

        //console.log(tabelForAdding);
        //console.log($('input[name=tabelForAdding]:checked').attr("tabel_noch_mark"));

        var reqData = {
            summCalcs: $(".summCalcsForTabel").html(),
            tabel_noch_mark: tabel_noch_mark,
            tabelForAdding: tabelForAdding
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
                //console.log(res);

                if(res.result == "success"){
                    //console.log(res);

                    //document.location.href = "fl_tabels.php";
                    //window.close('newTabelwindow');

                    $("#overlay").hide();
                    $(".center_block").remove();

                    setTimeout(function () {
                        refreshOnlyThisTab($("#refreshID_"+type_id+"_"+worker_id+"_"+filial_id+""), type_id, worker_id, filial_id);
                    }, 1000);


                }else{
                    $('#errror').html(res.data);
                    //console.log(res);
                }
            }
        });
    }

    //Добавляем в базу рассчет ночи
    function fl_addNewNoch(type_id, worker_id, filial_id, dopData){
        //console.log($(".summCalcsForTabel").html());
        //console.log(dopData);

        var link = "fl_add_new_noch2_f.php";

        var tabelForAdding = $('input[name=tabelForAdding]:checked').val();

        var reqData = {
            type_id: type_id,
            worker_id: worker_id,
            filial_id: filial_id,
            dopData: dopData,
            tabelForAdding: tabelForAdding
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
                //console.log(res);

                if(res.result == "success"){
                    //console.log(res);

                    setTimeout(function () {
                        location.reload()
                    }, 100);

                }else{
                    //console.log(res);

                    $('#errror').html(res.data);
                    $("#overlay").hide();
                    $(".center_block").remove();
                }
            }
        });
    }

    //Добавляем в базу новый ночной табель и сразу же добавляем туда отчёт за указанную дату
    function fl_addNewNochTabel(type_id, worker_id, filial_id, dopData){
        // console.log(type_id);
        // console.log(worker_id);
        // console.log(filial_id);
        // console.log(dopData);

        var link = "fl_add_new_noch_tabel_f.php";

        //var tabelForAdding = $('input[name=tabelForAdding]:checked').val();

        var reqData = {
            type_id: type_id,
            worker_id: worker_id,
            filial_id: filial_id,
            dopData: dopData
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
                //console.log(res);

                if(res.result == "success"){
                    //console.log(res);

                    setTimeout(function () {
                        location.reload()
                    }, 100);

                }else{
                    //console.log(res);

                    $('#errror').html(res.data);
                    $("#overlay").hide();
                    $(".center_block").remove();
                }
            }
        });
    }



    //Удаляем все выделенные РЛ из программы в разделе Важный отчет
    function fl_deleteMarkedCalculates (thisObj){
        //console.log(thisObj);
        //console.log(thisObj.parent());

        wait(function(runNext){

            setTimeout(function(){
                runNext(calcIDForTabelINarr());
            }, 1500);

        }).wait(function(runNext, calcIDForTabel_arr){
            //используем аргументы из предыдущего вызова
            //console.log(calcIDForTabel_arr);
            //console.log(typeof (calcIDForTabel_arr));
            //console.log(calcIDForTabel_arr.main_data.length);

            if (calcIDForTabel_arr.main_data.length > 0) {
                var rys = false;

                rys = confirm("Вы хотите удалить выделенные РЛ. \nЭто необратимо. Все РЛ будут полностью удалены\nиз программы.\n\nВы уверены?");

                if (rys) {
                    $.ajax({
                        url: "fl_deleteCalcsByIDsFromDB.php",
                        global: false,
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            calcArr: calcIDForTabel_arr.main_data
                        },
                        cache: false,
                        beforeSend: function () {
                            //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                        },
                        // действие, при ответе с сервера
                        success: function (res) {
                            //console.log(res);

                            if (res.result == "success") {
                                //console.log(res);

                                var tableArr = calcIDForTabel_arr.data.split('_');
                                /*console.log(tableArr[1]);
                                 console.log(tableArr[2]);
                                 console.log(tableArr[3]);*/

                                refreshOnlyThisTab(thisObj, tableArr[1],tableArr[2],tableArr[3]);
                            }
                        }
                    });
                }
            }
        });
    }

    //Перерасчет зп (если меняли процент) во всех выделенных РЛ из программы в разделе Важный отчет
    function fl_reloadPercentsMarkedCalculates (thisObj){
        //console.log(thisObj);
        //console.log(thisObj.parent());

        wait(function(runNext){

            setTimeout(function(){
                runNext(calcIDForTabelINarr());
            }, 1500);

        }).wait(function(runNext, calcIDForTabel_arr){
            //используем аргументы из предыдущего вызова
            //console.log(calcIDForTabel_arr);

            if (calcIDForTabel_arr.main_data.length > 0) {

                if (calcIDForTabel_arr.main_data.length > 10){
                    alert("Рассчитать можно не более 10 РЛ за раз.");
                }else {
                    var rys = false;

                    rys = confirm("Вы собираетесь перерасчитать выделенные РЛ. \n\nВы уверены?");

                    if (rys) {
                        $.ajax({
                            url: "fl_reloadPercentsMarkedCalculates.php",
                            global: false,
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                calcArr: calcIDForTabel_arr
                            },
                            cache: false,
                            beforeSend: function () {
                                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                            },
                            // действие, при ответе с сервера
                            success: function (res) {
                                //console.log(res);

                                if (res.result == "success") {
                                    //console.log(res);

                                    var tableArr = calcIDForTabel_arr.data.split('_');

                                    refreshOnlyThisTab(thisObj, tableArr[1],tableArr[2],tableArr[3]);
                                }
                            }
                        });
                    }
                }
            }
        });
    }

    //Показываем блок с ночными сменами
    function Ajax_NightSmenaAddINTabel (tabel_id, nightSmenaCount){
        //console.log(tabel_id);

        var link = "fl_add_night_smena_in_tabel_f.php";
        //console.log(link);

        var Data = {
            tabel_id: tabel_id,
            count: nightSmenaCount
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: Data,
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == "success"){
                    location.reload();
                }else{
                    $('#errror').html(res.data);
                }
            }
        });
    }

    //Показываем блок с ночными сменами
    function Ajax_emptySmenaAddINTabel (tabel_id, emptySmens){
        //console.log(tabel_id);

        var link = "fl_add_empty_smena_in_tabel_f.php";
        //console.log(link);

        var Data = {
            tabel_id: tabel_id,
            count: emptySmens
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: Data,
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == "success"){
                    location.reload();
                }else{
                    $('#errror').html(res.data);
                }
            }
        });
    }

    //Показываем блок с ночными сменами
    function showNightSmenaAddINTabel (tabel_id, nightSmenaCount){
        //console.log(tabel_id);
        $('#overlay').show();

        var buttonsStr = '<input type="button" class="b" value="Добавить" onclick="Ajax_NightSmenaAddINTabel('+tabel_id+', '+nightSmenaCount+')">';

        /*if (mode == 'edit'){
            buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_add(\'edit\')">';
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
                        "position": "relative"
                    })
                    .append('<span style="margin: 5px;"><i>Проверьте и нажмите добавить</i></span>')
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
                            .append('<div style="margin: 10px;">Кол-во ночных смен: <span class="calculateInsInvoice">'+nightSmenaCount+'</span></div>')
                            .append('<div style="margin: 10px;">Общая сумма: <span class="calculateInvoice">'+nightSmenaCount*1000+'</span> руб.</div>')
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

    }

    //Показываем блок с "пустыми" сменами
    function showEmptySmenaAddINTabel (tabel_id){
        //console.log(tabel_id);

        var emptySmens = $('#emptySmens').val();
        //console.log(emptySmens);

        if (emptySmens.length > 0) {

            if (!isNaN(emptySmens)) {

                if (emptySmens > 0) {

                    emptySmens = Number(emptySmens);

                    $('#overlay').show();

                    var buttonsStr = '<input type="button" class="b" value="Добавить" onclick="Ajax_emptySmenaAddINTabel('+tabel_id+', '+emptySmens+')">';

                    /*if (mode == 'edit'){
                     buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_add(\'edit\')">';
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
                                    "position": "relative"
                                })
                                .append('<span style="margin: 5px;"><i>Проверьте и нажмите добавить</i></span>')
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
                                        .append('<div style="margin: 10px;">Кол-во "пустых" смен: <span class="calculateInsInvoice">' + emptySmens + '</span></div>')
                                        .append('<div style="margin: 10px;">Общая сумма: <span class="calculateInvoice">' + emptySmens * 250 + '</span> руб.</div>')
                                )
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "bottom": "2px",
                                            "width": "100%"
                                        })
                                        .append(buttonsStr +
                                            '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                                        )
                                )
                        );


                    menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
                }
            }
        }

    }

    //Удаляем РЛ из табеля
    function fl_deleteCalculateFromTabel(tabel_id, calculate_id){

        var link = "fl_deleteCalcFromTabel_f.php";

        var rys = false;

        rys = confirm("Вы хотите удалить РЛ из табеля. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    tabel_id: tabel_id,
                    calculate_id: calculate_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Удаляем ночной отчет из табеля
    function fl_deleteNightFromTabel(tabel_id, tabel_night_id){

        var link = "fl_deleteNightFromTabel_f.php";

        var rys = false;

        rys = confirm("Вы хотите удалить отчёт по ночи из табеля. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    tabel_id: tabel_id,
                    tabel_night_id: tabel_night_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Удаляем Вычет из табеля
    function fl_deleteDeductionFromTabel(tabel_id, deduction_id){

        var link = "fl_deleteDeductionFromTabel_f.php";

        var rys = false;

        rys = confirm("Вы хотите удалить Вычет из табеля. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    tabel_id: tabel_id,
                    deduction_id: deduction_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Удаляем надбавку из табеля
    function fl_deleteSurchargeFromTabel(tabel_id, surcharge_id){

        var link = "fl_deleteSurchargeFromTabel_f.php";

        var rys = false;

        rys = confirm("Вы хотите удалить Надбавку из табеля. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    tabel_id: tabel_id,
                    surcharge_id: surcharge_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Удаляем выплату из табеля
    function fl_deletePaidoutFromTabel(tabel_id, paidout_id){

        var link = "fl_deletePaidoutFromTabel_f.php";

        var rys = false;

        rys = confirm("Вы хотите удалить Выплату из табеля. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            var noch = $('#noch').val();

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    tabel_id: tabel_id,
                    paidout_id: paidout_id,

                    noch: noch
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Добавляем/редактируем в базу вычет из табеля
    function  fl_Ajax_deduction_add(deduction_id, tabel_id, mode, deductionData, link_res){

        var link = "fl_deduction_add_f.php";

        if (mode == 'edit'){
            link = "fl_deduction_edit_f.php";
        }

        deductionData['deduction_id'] = deduction_id;

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data:deductionData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
                //console.log(res.data);

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    blockWhileWaiting (true);

                    document.location.href = link_res+"?id="+tabel_id;
                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу надбавку в табель
    function  fl_Ajax_surcharge_add(surcharge_id, tabel_id, mode, surchargeData, link_res){

        var link = "fl_surcharge_add_f.php";

        if (mode == 'edit'){
            link = "fl_surcharge_edit_f.php";
        }

        surchargeData['surcharge_id'] = surcharge_id;

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data:surchargeData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
                //console.log(res.data);

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    blockWhileWaiting (true);

                    document.location.href = link_res+"?id="+tabel_id;
                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу выплату в табель
    function  fl_Ajax_paidout_add(paidout_id, tabel_id, mode, paidoutData, link_res, variant){

        if (variant == 1) {
            var link = "fl_paidout_add_f.php";
            if (mode == 'edit') {
                link = "fl_paidout_edit_f.php";
            }
        }

        if (variant == 2) {
            var link = "fl_paidout_add2_f.php";
            if (mode == 'edit') {
                link = "fl_paidout_edit2_f.php";
            }
        }

        paidoutData['paidout_id'] = paidout_id;
        //console.log(paidoutData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data: paidoutData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
                console.log(res.data);
                //$('#data').html(res)

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    blockWhileWaiting (true);

                    if (paidoutData['deploy']) {
                        deployTabel(tabel_id);
                        document.location.href = link_res+"?id="+tabel_id;
                    }else {
                        document.location.href = link_res + "?id=" + tabel_id;
                    }
                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу расход материалов для наряда
    function fl_Ajax_MaterialsConsumptionAdd(invoice_id, mode){

        var link = "fl_material_consumption_add_f.php";

        if (mode == 'edit'){
         link = "fl_material_consumption_edit_f.php";
        }

        var matConsData = {
            invoice_id:invoice_id,
            descr: $('#descr').val(),
            summ: $('#mat_cons_pos_summ_all').val()
        };

        var error_marker = false;

        var positionsArr = {};

        wait(function(runNext){

            setTimeout(function(){

                $(".materials_consumption_pos").each(function(){
                    //console.log($(this).attr("positionID"));
                    //console.log($(this).val());
                    //console.log($(this).parent().parent().find('.invoiceItemPriceItog').text());

                    var position_id = Number($(this).attr("positionID"));
                    var invoiceItemPriceItog = Number($(this).parent().parent().find('.invoiceItemPriceItog').text());
                    var materials_consumption_sum = Number($(this).val());

                    var checked_status = $(this).parent().find('.chkMatCons').prop("checked");
                    //console.log(checked_status);

                    if (checked_status) {

                        if (invoiceItemPriceItog < materials_consumption_sum) {
                            $('#errrror').html('<div class="query_neok">Расход не может быть больше стоимости позиции.</div>');
                            //console.log(position_id);

                            $('#overlay').hide();
                            $('.center_block').remove();

                            error_marker = true;

                            return false;
                        } else {
                            //console.log(position_id);

                            positionsArr[position_id] = {};
                            positionsArr[position_id]['mat_cons_sum'] = materials_consumption_sum;

                        }
                    }
                });

                runNext(positionsArr, error_marker);

            }, 1500);

        }).wait(function(runNext, positionsArr, error_marker){
            //используем аргументы из предыдущего вызова

            if (!error_marker) {
                //console.log(positionsArr)

                matConsData["positionsArr"] = positionsArr;

                $.ajax({
                    url: link,
                    global: false,
                    type: "POST",
                    dataType: "JSON",

                    data: matConsData,

                    cache: false,
                    beforeSend: function() {
                        $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                    },
                    // действие, при ответе с сервера
                    success:function(res){
                        //console.log(res.data);
                        /*$('#errrror').html(res);*/

                        if(res.result == 'success') {
                            //console.log('success');
                            //$('#data').html(res.data);

                            blockWhileWaiting (true);

                            document.location.href = "invoice.php?id="+invoice_id;
                        }else{
                            //console.log('error');
                            $('#overlay').hide();
                            $('.center_block').remove();

                            $('#errror').html(res.data);
                            //$('#errrror').html('');
                        }
                    }
                });

            }
        });
    }

    // Добавляем/редактируем в базу расход материалов для наряда
    function fl_showMaterialsConsumptionAdd(invoice_id, mode){
        //console.log(invoice_id);

        var Summ = $("#mat_cons_pos_summ_all").val();

        if (Summ > 0) {

            $('#overlay').show();


            /*var SummIns = 0;
             var SummInsBlock = '';*/

            /*if (invoice_type == 5){
             SummIns = $("#calculateInsInvoice").html();
             SummInsBlock = '<div>Страховка: <span class="calculateInsInvoice">'+SummIns+'</span> руб.</div>';
             }*/

            var buttonsStr = '<input type="button" class="b" value="Применить" onclick="$(this).prop(\'disabled\', true ); fl_Ajax_MaterialsConsumptionAdd('+invoice_id+', \'add\')">';


            if (mode == 'edit') {
                buttonsStr = '<input type="button" class="b" value="Применить" onclick="$(this).prop(\'disabled\', true ); fl_Ajax_MaterialsConsumptionAdd('+invoice_id+', \'edit\')">';
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
                        .append('<span style="margin: 5px;"><i>Проверьте сумму расходов на материалы.</i></span>')
                        .append('<br><br><span style="margin: 5px; color: red"><i>Внимание! Расчётный лист будет пересчитан.</i></span>')
                        .append(
                            $('<div/>')
                                .css({
                                    "position": "absolute",
                                    "width": "100%",
                                    "margin": "auto",
                                    "top": "25px",
                                    "left": "0",
                                    "bottom": "0",
                                    "right": "0",
                                    "height": "50%",
                                })
                                .append('<div style="margin: 15px;">Сумма: <span class="calculateInvoice">' + Summ + '</span> руб.</div>')
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

    //Промежуточная функция для вычета
    function fl_showDeductionAdd (deduction_id, tabel_id, type, link, mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var deduction_summ = $('#deduction_summ').val();
        var descr = $('#descr').val();

        var deductionData = {
            tabel_id: tabel_id,
            type: type,
            deduction_summ: deduction_summ,
            descr: descr
        };

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: {deduction_summ: deduction_summ},

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                if(res.result == 'success'){

                    fl_Ajax_deduction_add(deduction_id, tabel_id, mode, deductionData, link);

                    // в случае ошибок в форме
                }else{
                    // перебираем массив с ошибками
                    for(var errorField in res.text_error){
                        // выводим текст ошибок
                        $('#'+errorField+'_error').html(res.text_error[errorField]);
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

    //Промежуточная функция для надбавки
    function fl_showSurchargeAdd (surcharge_id, tabel_id, type, link, mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var surcharge_summ = $('#surcharge_summ').val();
        var descr = $('#descr').val();

        var surchargeData = {
            tabel_id:tabel_id,
            type:type,
            surcharge_summ:surcharge_summ,
            descr:descr
        };

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: {surcharge_summ:surcharge_summ},

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                if(res.result == 'success'){

                    fl_Ajax_surcharge_add(surcharge_id, tabel_id, mode, surchargeData, link);

                    // в случае ошибок в форме
                }else{
                    // перебираем массив с ошибками
                    for(var errorField in res.text_error){
                        // выводим текст ошибок
                        $('#'+errorField+'_error').html(res.text_error[errorField]);
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

    //Промежуточная функция для выплаты
    function fl_showPaidoutAdd (paidout_id, tabel_id, type, worker_id, month, year, link, mode, deploy, variant){
        //console.log(mode);
        //deploy - провести или нет
        //variant - какой вариант использовать. либо где по позициям или по всем суммам

        //убираем ошибки
        hideAllErrors ();

        var filials_subtractions = {};
        //Соберём суммы для вычетов со всех филиалов
        $('.filial_subtraction').each(function(){
            if ($(this).val() > 0) {
                filials_subtractions[$(this).attr('filial_id')] = Number($(this).val());
            }
        });
        // console.log(filials_subtractions);
        // console.log(JSON.stringify(filials_subtractions));

        var paidout_summ = $('#paidout_summ').val();
        var descr = $('#descr').val();
        var noch = $('#noch').val();
        var filial_id = $('#SelectFilial').val();

        var paidoutData = {
            tabel_id: tabel_id,
            type: type,
            worker_id: worker_id,
            month: month,
            year: year,
            paidout_summ: paidout_summ,
            noch: noch,
            descr:descr,
            filial_id: filial_id,
            deploy: deploy,
            subtractions: filials_subtractions
        };
        //console.log(paidoutData);

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: {paidout_summ: paidout_summ},

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                if(res.result == 'success'){

                        fl_Ajax_paidout_add(paidout_id, tabel_id, mode, paidoutData, link, variant);

                    // в случае ошибок в форме
                }else{
                    // перебираем массив с ошибками
                    for(var errorField in res.text_error){
                        // выводим текст ошибок
                        $('#'+errorField+'_error').html(res.text_error[errorField]);
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

    //Провести табель
    function deployTabel (tabel_id){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var deployData = {
            tabel_id:tabel_id
        };

        var link = "fl_deployTabel_f.php";

        var rys = false;

        rys = confirm("Вы собираетесь провести табель.\nПосле этого изменить его не получится.\nВы уверены?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",

                data:deployData,

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

    //Снять отметку о Проведении табеля
    function deployTabelDelete (tabel_id){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var Data = {
            tabel_id:tabel_id
        };

        var link = "fl_deployTabel_delete_f.php";

        var rys = false;

        rys = confirm("Вы уверены, что хотите\nснять отметку о проведении табеля?");
        //console.log(885);

        if (rys) {

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

    //Удалить ночные смены из табеля
    function nightSmenaTabelDelete (tabel_id){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var Data = {
            tabel_id:tabel_id
        };

        var link = "fl_nightSmenaTabel_delete_f.php";

        var rys = false;

        rys = confirm("Вы уверены, что хотите удалить \nночные смены из табеля?");
        //console.log(885);

        if (rys) {

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

    //Удалить пустые смены из табеля
    function emptySmenaTabelDelete (tabel_id){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var Data = {
            tabel_id:tabel_id
        };

        var link = "fl_emptySmenaTabel_delete_f.php";

        var rys = false;

        rys = confirm("Вы уверены, что хотите удалить \n\"пустые\" смены из табеля?");
        //console.log(885);

        if (rys) {

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


    //
    function changeAllMaterials_consumption_pos() {

        var materials_consumption_pos_all_summ = 0;

        $(".materials_consumption_pos").each(function() {

            var checked_status = $(this).parent().find('.chkMatCons').prop("checked");
            //console.log(checked_status);

            if (checked_status) {
                if (!isNaN(Number($(this).val()))) {
                    if (Number($(this).val()) > 0) {
                        $(this).val(Number($(this).val()));
                        materials_consumption_pos_all_summ += Number($(this).val());
                    } else {
                        $(this).val(0);
                    }
                } else {
                    $(this).val(0);
                }
            }
        });

        $(".materials_consumption_pos_all").val(materials_consumption_pos_all_summ);

    }

    //Добавление в сессию данных по рассчетным листам, которые надо добавить, (ID)
    function fl_addCalcsIDsINSessionForTabel(calc_id_arr, add_status, type, worker_id, filial_id){
        //console.log(calc_id_arr);
        //console.log(add_status);
        //console.log(type);
        //console.log(worker_id);
        //console.log(filial_id);

        var link = "fl_addCalcsIDsINSessionForTabel2.php";

        var reqData = {
            calc_id_arr: calc_id_arr,
            add_status: add_status,
            type: type,
            worker_id: worker_id,
            filial_id: filial_id
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

                }else{
                    //--
                }
            }
        })

    }

    //Чистим все отмеченные checkbox и сессионный данные
    function clearAllChecked(){

        fl_addCalcsIDsINSessionForTabel([], 0, 0, 0, 0);

        $('input:checked').prop('checked', false);

        $('.calculateBlockItem').each(function() {
            if ($(this).attr("worker_mark") == 1){
                $(this).css({'background-color': '#FFF'});
            }else{
                $(this).css({'background-color': 'rgba(255, 141, 141, 0.2)'});
            }
        });
    }



    $(document).ready(function() {
        //console.log(123);


        //Рабочий пример клика на элементе после подгрузки загрузки его в DOM
        $("body").on("click", ".chkBoxCalcs", function(){
            var checked_status = $(this).prop("checked");
            //console.log(checked_status);
            //console.log($(this).parent());
            //console.log($(this).parent().parent().parent().attr("doctor_mark"));

            var add_status = 0;
            var calc_id_arr = [];

            //Меняем цвет блока
            if (checked_status){
                $(this).parent().parent().parent().css({"background-color": "#83DB53"});
                add_status = 1;
            }else{
                    //console.log($(this).parent().parent().parent().attr("worker_mark"));

                if ($(this).parent().parent().parent().attr("worker_mark") == 1) {
                    $(this).parent().parent().parent().css({"background-color": "rgb(255, 255, 255);"});
                }else{
                    $(this).parent().parent().parent().css({"background-color": "rgba(255, 141, 141, 0.2);"});
                }
            }

            //Получим ID расчетного листа
            //console.log($(this).attr("name").split("_"));
            //!!! Лишнее действие, надо бы посмотреть и переделать потом без split, чтоб данные писались в DOM сразу в виде чистого ID
            var ids_arr = $(this).attr("name").split("_");
            //Массив с ID расчетных листов
            calc_id_arr.push(ids_arr[1]);
            //console.log(calc_id);
            //console.log(checked_status);

            //Дополнительные данные
            var data_arr = $(this).attr("chkBoxData").split("_");

            //Добавим в сессию данные
            fl_addCalcsIDsINSessionForTabel(calc_id_arr, add_status, data_arr[1], data_arr[2], data_arr[3]);

        });


        $("body").on("click", ".checkAll", function(){

            var add_status = 0;
            var calc_id_arr = [];

            var checked_status = $(this).is(":checked");
            var thisId = $(this).attr("id");

            $("."+thisId).each(function() {
                if (checked_status){
                    $(this).prop("checked", true);
                    $(this).parent().parent().parent().css({"background-color": "#83DB53"});
                    add_status = 1;
                }else{
                    $(this).prop("checked", false);
                    if ($(this).parent().parent().parent().attr("worker_mark") == 1) {
                        $(this).parent().parent().parent().css({"background-color": "rgb(255, 255, 255);"});
                    }else{
                        $(this).parent().parent().parent().css({"background-color": "rgba(255, 141, 141, 0.2);"});
                    }
                }

                //Получим ID расчетного листа
                var ids_arr = $(this).attr("name").split("_");
                //Массив с ID расчетных листов
                calc_id_arr.push(ids_arr[1]);

            });

            //Дополнительные данные
            var data_arr = $(this).attr("chkBoxData").split("_");

            //Добавим в сессию данные
            fl_addCalcsIDsINSessionForTabel(calc_id_arr, add_status, data_arr[1], data_arr[2], data_arr[3]);

        });

        //Рабочий пример клика на элементе после подгрузки его в DOM
        $("body").on("click", ".radioBtnCalcs", function () {
            var checked_status = $(this).prop("checked");
            //console.log(checked_status);
            //console.log($(this).parent());

            $(".radioBtnCalcs").each(function() {
                $(this).parent().parent().parent().css({"background-color": ""});
            });

            if (checked_status) {
                $(this).parent().parent().parent().css({"background-color": "#83DB53"});
            } else {
                $(this).parent().parent().parent().css({"background-color": ""});
            }
        });

        //Для расчета затрат на материалы
        $("body").on("change", ".materials_consumption_pos", function () {
            //console.log($(this).val());

            changeAllMaterials_consumption_pos ();

        });

        $("body").on("change", ".materials_consumption_pos_all", function () {
            //console.log($(this).val());

            if (!isNaN(Number($(this).val()))) {
                //console.log($('input[type=checkbox]:checked').length);

                $(this).val(Number($(this).val()));

                var mat_cons_pos_summ_all = Number($(this).val());
                var chkBoxsCount = $('input[type=checkbox]:checked').length;

                var ostatok = mat_cons_pos_summ_all % chkBoxsCount;
                var mat_cons_pos_summ = Math.floor(mat_cons_pos_summ_all/chkBoxsCount);
                //console.log(mat_cons_pos_summ);

                var first_count = true;

                $(".materials_consumption_pos").each(function() {

                    var checked_status = $(this).parent().find('.chkMatCons').prop("checked");
                    //console.log(checked_status);

                    if (checked_status) {
                        if (first_count == true) {
                            $(this).val(mat_cons_pos_summ+ostatok);
                            first_count = false
                        }else{
                            $(this).val(mat_cons_pos_summ);
                        }
                    }else{
                        $(this).val(0);
                    }

                });

            }else{
                $(this).val(0);
            }

        });

        $("body").on("click", ".chkMatCons", function () {

            changeAllMaterials_consumption_pos ();

        });

    });

    //Получаем необработанные расчетные листы
    function getCalculatesfunc (thisObj, reqData){
        $.ajax({
            url:"fl_get_calculates_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data:reqData,

            cache: false,
            beforeSend: function() {
                thisObj.html("<div style='width: 120px; height: 32px; padding: 5px 10px 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...<br>загрузка<br>расч. листов</span></div>");
            },
            success:function(res){
                //console.log(res);
                //thisObj.html(res);

                if(res.result == 'success'){

                    ids = thisObj.attr("id");
                    ids_arr = ids.split("_");
                    //console.log(ids_arr);

                    permission = ids_arr[0];
                    worker = ids_arr[1];
                    office = ids_arr[2];

                    if (res.status == 1){
                        thisObj.html(res.data);

                        //Показываем оповещения на фио и филиале
                        //$("#tabs_notes_"+permission+"_"+worker).show();
                        //$("#tabs_notes_"+permission+"_"+worker+"_"+office).show();
                        //console.log("#tabs_notes_"+permission+"_"+worker+"_"+office);

                        $("#tabs_notes_"+permission+"_"+worker).css("display", "inline-block");
                        $("#tabs_notes_"+permission+"_"+worker+"_"+office).css("display", "inline-block");

                        thisObj.parent().find(".summCalcsNPaid").html(res.summCalc);

                    }else{
                        //$("#tabs_notes_"+permission+"_"+worker).css("display", "none");
                        $("#tabs_notes_"+permission+"_"+worker+"_"+office).css("display", "none");
                    }

                    if (res.status == 0){
                        thisObj.html("Нет данных по необработанным расчетным листам");

                        //Спрячем пустые вкладки, где нет данных

                        //console.log($(".tabs-"+permission+"_"+worker+"_"+office).css("display"));

                        //$(".tabs-"+permission+"_"+worker+"_"+office).hide();
                    }
                }

                if(res.result == 'error'){
                    thisObj.html(res.data);


                }

                //!!! тест. Разблокируем страницу, когда все загрузилось
                //blockWhileWaiting (false);
            }
        });
    }

    //Получаем необработанные расчетные листы v2.0
    function getCalculatesfunc2 (reqData){
        //  console.log(reqData);

        $.ajax({
            url:"fl_get_calculates2_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //thisObj.html("<div style='width: 120px; height: 32px; padding: 5px 10px 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...<br>загрузка<br>расч. листов</span></div>");
            },
            success:function(res){
                //console.log(res);
                //console.log(reqData.worker);
                // if (reqData.worker == 492) {
                //     console.log(res.query);
                // }
                //$("#tabs-"+reqData.permission+"_"+reqData.worker).html(res);

                if(res.result == 'success'){
                    //$("#tabs-"+reqData.permission+"_"+reqData.worker).html(res);

                    //!!! Размер объекта JS
                    //console.log(Object.keys(res.data).length);

                    if (Object.keys(res.data).length > 0){

                        var data = res.data;

                        for(var filial_id in data){
                            //console.log(filial_id);
                            //console.log(data[filial_id]);
                            //console.log("#"+reqData.permission+"_"+reqData.worker+"_"+filial_id);

                            $("#"+reqData.permission+"_"+reqData.worker+"_"+filial_id).html(data[filial_id].data);

                            //Показываем оповещения на фио и филиале
                            $("#tabs_notes_"+reqData.permission+"_"+reqData.worker).css("display", "inline-block");
                            $("#tabs_notes_"+reqData.permission+"_"+reqData.worker+"_"+filial_id).css("display", "inline-block");

                        }
                    }

                }

/*                if(res.result == 'success'){

                    ids = thisObj.attr("id");
                    ids_arr = ids.split("_");
                    //console.log(ids_arr);

                    permission = ids_arr[0];
                    worker = ids_arr[1];
                    office = ids_arr[2];

                    if (res.status == 1){
                        thisObj.html(res.data);

                        //Показываем оповещения на фио и филиале
                        $("#tabs_notes_"+permission+"_"+worker).css("display", "inline-block");
                        $("#tabs_notes_"+permission+"_"+worker+"_"+office).css("display", "inline-block");

                        thisObj.parent().find(".summCalcsNPaid").html(res.summCalc);

                    }else{
                        $("#tabs_notes_"+permission+"_"+worker+"_"+office).css("display", "none");
                    }

                    if (res.status == 0){
                        thisObj.html("Нет данных по необработанным расчетным листам");

                        //Спрячем пустые вкладки, где нет данных

                        //console.log($(".tabs-"+permission+"_"+worker+"_"+office).css("display"));

                        //$(".tabs-"+permission+"_"+worker+"_"+office).hide();
                    }
                }

                if(res.result == 'error'){
                    thisObj.html(res.data);


                }*/

                //!!! тест. Разблокируем страницу, когда все загрузилось
                //blockWhileWaiting (false);
            }
        });
    }

    //Получаем табели
    function getTabelsfunc (thisObj, reqData){
        //console.log (reqData);

        $.ajax({
            url:"fl_get_tabels_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: reqData,

            cache: false,
            beforeSend: function() {
                thisObj.html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...<br>загрузка табелей</span></div>");
            },
            success:function(res){
                //console.log(res);
                //thisObj.html(res);

                if(res.result == 'success'){

                    ids = thisObj.attr("id");
                    ids_arr = ids.split("_");
                    //console.log(ids_arr);

                    permission = ids_arr[0];
                    worker = ids_arr[1];
                    office = ids_arr[2];

                    if (res.status == 1){
                        thisObj.html(res.data);

                        //Показываем оповещения на фио и филиале
                        /*$("#tabs_notes2_"+permission+"_"+worker).show();
                         $("#tabs_notes2_"+permission+"_"+worker+"_"+office).show();*/
                        //console.log("#tabs_notes_"+permission+"_"+worker+"_"+office);
                        if (res.notDeployCount > 0){
                            $("#tabs_notes2_"+permission+"_"+worker).css("display", "inline-block");
                            $("#tabs_notes2_"+permission+"_"+worker+"_"+office).css("display", "inline-block");
                        }else{
                            //$("#tabs_notes2_"+permission+"_"+worker).css("display", "none");
                            $("#tabs_notes2_"+permission+"_"+worker+"_"+office).css("display", "none");
                        }

                        //
                        thisObj.parent().find(".summTabelNPaid").html(res.summCalc);

                    }

                    if (res.status == 0){
                        thisObj.html("Нет данных по табелям");


                        //!!! доделать тут чтоб правильно прятались или нет вкладки
                        //Спрячем пустые вкладки, где нет данных

                        //!!! пока костыль такой
                        if (reqData['own_tabel']){
                            //console.log($("#filial_"+reqData['office']).css("display"));

                            //$("#filial_"+reqData['office']).hide();


                            // $("#filial_"+reqData['office']).css({
                            //     "pointer-events": "none",
                            //     "cursor": "default",
                            //     "background-color": "rgba(140, 137, 137, 0.7)",
                            // })
                        }
                    }
                }

                if(res.result == "error"){
                    thisObj.html(res.data);
                }
            }
        });
    }


    //Обновим данные в табеле, но только в данной вкладке
    function refreshOnlyThisTab(thisObj, permission_id, worker_id, office_id){
        //console.log(permission_id+' _ '+worker_id+' _ '+office_id);
        //console.log(thisObj.parent());

        hideAllErrors ();

        var now = new Date();
        var year = now.getFullYear();
        var month = now.getMonth();
        month = Number(month)+1;
        if (Number(month) < 10){
            month = "0"+month;
        }
        //console.log(month);

        var needCalcObj = thisObj.parent().find('.tableDataNPaidCalcs');
        var needTabelObj = thisObj.parent().find('.tableTabels');


        var reqData = {
            permission: permission_id,
            worker: worker_id,
            office: office_id,
            month: month,
            year: year,
            own_tabel: false
        };

        getCalculatesfunc (needCalcObj, reqData);

        getTabelsfunc (needTabelObj, reqData);

    }

    //Расчет табеля, подстановки данных
    function fl_tabulation (tabel_id){
        //console.log();

        var pay_plus = 0;
        var pay_minus = 0;
        var pay_plus_part = 0;
        var pay_minus_part = 0;

        wait(function(runNext){

            setTimeout(function(){

                $('.pay_plus_part1_'+tabel_id).each(function() {
                    pay_plus_part += Number($(this).html());
                    //console.log(Number($(this).html()));
                });
                //console.log(pay_plus_part);

                runNext(pay_plus_part);

            }, 100);

        }).wait(function(runNext, pay_plus_part){
            //используем аргументы из предыдущего вызова

            $('.pay_plus1_'+tabel_id).html(pay_plus_part);
            pay_plus += pay_plus_part;
            pay_plus_part = 0;

            setTimeout(function(){

                $('.pay_minus_part1_'+tabel_id).each(function() {
                    pay_minus_part += Number($(this).html());
                    //console.log(Number($(this).html()));
                });
                //console.log(pay_minus_part);

                runNext(pay_plus, pay_plus_part, pay_minus_part);

            }, 100);

        }).wait(function(runNext, pay_plus, pay_plus_part, pay_minus_part){
            //используем аргументы из предыдущего вызова

            $('.pay_minus1_'+tabel_id).html(pay_minus_part);
            pay_minus += pay_minus_part;
            pay_minus_part = 0;

            setTimeout(function(){

                $('.pay_plus_part2_'+tabel_id).each(function() {
                    pay_plus_part += Number($(this).html());
                    //console.log(Number($(this).html()));
                });
                //console.log(pay_plus_part);

                runNext(pay_plus, pay_minus, pay_plus_part, pay_minus_part);

            }, 100);

        }).wait(function(runNext, pay_plus, pay_minus, pay_plus_part, pay_minus_part){
            //используем аргументы из предыдущего вызова

            $('.pay_plus2_'+tabel_id).html(pay_plus_part);
            pay_plus += pay_plus_part;

            setTimeout(function(){

                $('.pay_minus_part2_'+tabel_id).each(function() {
                    pay_minus_part += Number($(this).html());
                    //console.log(Number($(this).html()));
                });
                //console.log(pay_plus_part);

                runNext(pay_plus, pay_minus, pay_plus_part, pay_minus_part);

            }, 100);

        }).wait(function(runNext, pay_plus, pay_minus, pay_plus_part, pay_minus_part){

            $('.pay_minus2_'+tabel_id).html(pay_minus_part);
            pay_minus += pay_minus_part;

            $('.pay_must_'+tabel_id).html(pay_plus - pay_minus);

        });
    }

    //Приказ №8 перерасчёт - этап 2 реализация
    function fl_prikazNomerVosem_JustDoIt(tabel_id, newPercent, controlCategories){
        //console.log (tabel_id);
        //console.log (newPercent);
        //console.log (controlCategories);

        var link = "fl_prikazNomerVosem_JustDoIt_f.php";

        var reqData = {
            tabel_id: tabel_id,
            newPercent: newPercent,
            controlCategories: controlCategories
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                $('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                console.log(res);

                var calc_ids_arr = Array.from(res.data);

                //!!! Хороший пример паузы в цикле (пауза в цикле)
                //Не использовать, если есть вариант, что массив изменится во время
                //И если обязательно индексы цифровые и по порядку
                if (calc_ids_arr.length > 0) {

                    var foo = function (i) {
                        $("#prikazNomerVosem").html("<i>Обновляем данные для РЛ</i>: #<b>"+calc_ids_arr[i]+"</b><br>");

                        window.setTimeout(function () {
                            //console.log(calc_ids_arr[i]);

                            link = "fl_reloadPercentsMarkedCalculates.php";

                            reqData.tabel_id = tabel_id;
                            reqData.newPercent = newPercent;
                            reqData.controlCategories = controlCategories;

                            //Так как функция, находящаяся в fl_reloadPercentsMarkedCalculates.php
                            //Работает по-ебаному (лень просто переделывать, лепим костыли),
                            //а именно: ей нужно скормить перемменную вида chkBox_5_400_16
                            //В которой хранятся тип (стом, косм...)/5, ID работника/400, филиал/16)
                            //То создадим такую ебаную переменную reqData.data =)

                            reqData.data = 'chkBox_6_000_00';

                            reqData.main_data = [];

                            reqData.main_data[reqData.main_data.length] = calc_ids_arr[i];

                            //По каждому из id пересчитываем РЛ
                            $.ajax({
                                url: link,
                                global: false,
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    calcArr: reqData
                                },
                                cache: false,
                                beforeSend: function () {
                                },
                                // действие, при ответе с сервера
                                success: function (res) {
                                    //console.log(res);

                                    if (res.result == "success") {
                                        //$("#prikazNomerVosem").append("<i>Новый РЛ</i>: <b>"+res.newCalcID+"</b> <i>создан<br></i>");
                                    }
                                }
                            });

                            if (i < calc_ids_arr.length-1){
                                foo(i + 1);
                            } else {
                                //По окончании цикла, который выше, чего-то делаем
                                //console.log("Обновляем сумму табеля.");

                                $("#prikazNomerVosem").html("Обновляем сумму табеля.");

                                link = "fl_updateTabelBalance_f.php";

                                //А тут мне пришлось создать отдельный файл с функцией, которая тупо передаёт
                                //дальше ID табеля и тот пересчитывает свою сумму.
                                //По каждому из id пересчитываем РЛ
                                $.ajax({
                                    url: link,
                                    global: false,
                                    type: "POST",
                                    dataType: "JSON",
                                    data: {
                                        tabel_id: tabel_id
                                    },
                                    cache: false,
                                    beforeSend: function () {
                                    },
                                    // действие, при ответе с сервера
                                    success: function (res) {
                                        //console.log(res);

                                        if (res.result == "success") {
                                            location.reload();
                                        }else{
                                            console.log(res.data);
                                        }
                                    }
                                });
                            }
                        }, 1000);
                    };
                    foo(0);
                }
            }
        });

    }

    //Приказ №8 перерасчёт - этап 1 подготовка
    function prikazNomerVosem(worker_id, tabel_id){

        var rys = true;

        rys = confirm("Внимание\nВсе расчётные листы в табеле и общая сумма\nбудут пересчитаны в соответствии\nс приказом №8.\n\nВы уверены?");;

        if (rys) {
            //console.log(worker_id);

            var link = "fl_prikazNomerVosem.php";

            var reqData = {
                worker_id: worker_id,
                tabel_id: tabel_id
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
                    //console.log(res);

                    //$("#prikazNomerVosem").html(res);

                    if(res.result == "success"){
                        //console.log(JSON.stringify(res.controlCategories));

                        $('#overlay').show();

                        var buttonsStr = '<input type="button" class="b" value="Применить" onclick="fl_prikazNomerVosem_JustDoIt('+tabel_id+', '+res.newPaymentPercent+', '+JSON.stringify(res.controlCategories)+');">';

                        // Создаем меню:
                        var menu = $('<div/>', {
                            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
                        }).css({"height": "200px"})
                            .appendTo('#overlay')
                            .append(
                                $('<div/>')
                                    .css({
                                        "height": "100%",
                                        "border": "1px solid #AAA",
                                        "position": "relative"
                                    })
                                    .append('<span style="margin: 5px;"><i>Проверьте и нажмите применить</i></span>')
                                    .append(
                                        $('<div/>')
                                            .css({
                                                "position": "absolute",
                                                "width": "100%",
                                                "margin": "auto",
                                                "top": "40px",
                                                "left": "0",
                                                "bottom": "0",
                                                "right": "0",
                                                "height": "80%"
                                            })
                                            .append('<div id="waitProcess">' +
                                                '<div style="margin: 5px; font-size: 90%;">Общая сумма выручки: <span class="calculateInsInvoice">'+res.allSumm+'</span> руб.</div>' +
                                                '<div style="margin: 5px; font-size: 90%;">Сумма за эпиляции: <span class="calculateInvoice">'+res.controlCategoriesSumm+'</span> руб. (<span class="calculateInvoice">'+res.controlPercent+'%</span>)</div>' +
                                                '<div style="margin: 20px; font-size: 90%;">Новый процент за эпиляции: <span class="calculateOrder">'+res.newPaymentPercent+' %</span> </div>' +
                                                '</div>' +
                                                '<div id="prikazNomerVosem" style="margin: 10px;"></div>')
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

                    }else{
                        console.log(res);
                        
                    }
                }
            });
        }
    }

    //!!!!тест разбор
    /*var openedWindow;
    function iOpenNewWindow(url, name, options){


        openedWindow = window.open(url, name, options);

        if (openedWindow.focus){
            openedWindow.focus();
        }

        WaitForCloseWindow(openedWindow);
    }

    function WaitForCloseWindow(openedWindow){
        if(!openedWindow.closed){
            setTimeout("WaitForCloseWindow(openedWindow)", 300);
        }else{
            alert(" Closed!");
        }
    }*/

    //Суммируем все поля в отчете
    function calculateDailyReportSumm(){

        var summNal = 0;
        var summBeznal = 0;
        var summ = 0;

        //Готовые суммы из отчёта "Касса" нал
        $(".allSummNal").each(function(){
            summNal += Number($(this).html());
            summ += Number($(this).html());
        });
        //Готовые суммы из отчёта "Касса" безнал
        $(".allSummBeznal").each(function(){
            summBeznal += Number($(this).html());
            summ += Number($(this).html());
        });

        //Суммы ручной ввод  из отчёта "Касса" нал
        $(".allSummInputNal").each(function(){
            summNal += Number($(this).val());
            summ += Number($(this).val());
        });
        //Суммы ручной ввод  из отчёта "Касса" безнал
        $(".allSummInputBeznal").each(function(){
            summBeznal += Number($(this).val());
            summ += Number($(this).val());
        });

        //Общая сумма по кассе до вычета расходов, должно совпасть с Z-отчетом
        $("#allsummKassa").html(number_format(summ, 2, '.', ' '));

        //summ = summ - $(".summMinus").val();
        summ = summ - $(".summMinus").html();

        $("#SummNal").html(number_format(summNal, 2, '.', ' '));
        $("#SummBeznal").html(number_format(summBeznal, 2, '.', ' '));

        //Остаток наличных по кассе минус расходы
        summNal = summNal - Number($(".summMinus").html());
        $("#SummNalOstatok").html(number_format(summNal, 2, '.', ' '));

        //Общая сумма без аренды
        $("#allsumm").html(number_format(summ, 2, '.', ' '));

        //Итоговые сумма
        $(".itogSummInputNal").each(function(){
            summ += Number($(this).val());
            summNal += Number($(this).val());
        });
        //console.log(summ);

        $("#itogSummShow").html(number_format(summ, 2, '.', ' '));
        $("#itogSumm").val(number_format(summ, 2, '.', ' '));

        //Остаток наличных по кассе минус расходы + аренда
        $("#itogSummNalShow").html(number_format(summ-summBeznal, 2, '.', ' '));

    }

    //!!! Эта функция и следующая - фактически одинаковые
    //Добавление ежедневного отчёта в бд
    function fl_createDailyReport_add(){
        //console.log($("#allsumm").html().replace(/\s{2,}/g, ''));

        //убираем ошибки
        hideAllErrors ();

        var link = "fl_createDailyReport_add_f.php";

        var filial_id = $("#SelectFilial").val();

        var reqData = {
            date: $("#iWantThisDate2").val(),
            filial_id: filial_id,
            itogSumm: $("#itogSumm").val(),
            arenda: $("#arendaNal").val(),
            zreport: $("#zreport").val(),
            allsumm: $("#allsumm").html(),
            SummNal: $("#SummNal").html(),
            SummBeznal: $("#SummBeznal").html(),
            SummNalStomCosm: $("#SummNalStomCosm").html(),
            SummBeznalStomCosm: $("#SummBeznalStomCosm").html(),
            CertCount: $("#CertCount").html(),
            SummCertNal: $("#SummCertNal").html(),
            SummCertBeznal: $("#SummCertBeznal").html(),
            ortoSummNal: $("#ortoSummNal").val(),
            ortoSummBeznal: $("#ortoSummBeznal").val(),
            specialistSummNal: $("#specialistSummNal").val(),
            specialistSummBeznal: $("#specialistSummBeznal").val(),
            analizSummNal: $("#analizSummNal").val(),
            analizSummBeznal: $("#analizSummBeznal").val(),
            solarSummNal: $("#solarSummNal").val(),
            solarSummBeznal: $("#solarSummBeznal").val(),
            summMinusNal: $("#summMinusNal").html()/*,
            bankSummNal: $("#bankSummNal").html(),
            directorSummNal: $("#directorSummNal").html()*/
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
                //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == 'success') {
                    //console.log('success');
                    $('#data').html(res.data);
                    setTimeout(function () {
                        //window.location.replace('stat_cashbox.php');
                        window.location.replace('fl_consolidated_report_admin.php?filial_id='+filial_id);
                        //console.log('client.php?id='+id);
                    }, 500);
                }else{
                    //console.log('error');
                    $('#errrror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Редактирование ежедневного отчёта в бд
    function fl_editDailyReport_add(report_id){

        //убираем ошибки
        hideAllErrors ();

        var link = "fl_editDailyReport_add_f.php";

        var filial_id = $("#SelectFilial").val();

        var reqData = {
            report_id: report_id,
            itogSumm: $("#itogSumm").val(),
            arenda: $("#arendaNal").val(),
            zreport: $("#zreport").val(),
            allsumm: $("#allsumm").html(),
            SummNal: $("#SummNal").html(),
            SummBeznal: $("#SummBeznal").html(),
            SummNalStomCosm: $("#SummNalStomCosm").html(),
            SummBeznalStomCosm: $("#SummBeznalStomCosm").html(),
            CertCount: $("#CertCount").html(),
            SummCertNal: $("#SummCertNal").html(),
            SummCertBeznal: $("#SummCertBeznal").html(),
            ortoSummNal: $("#ortoSummNal").val(),
            ortoSummBeznal: $("#ortoSummBeznal").val(),
            specialistSummNal: $("#specialistSummNal").val(),
            specialistSummBeznal: $("#specialistSummBeznal").val(),
            analizSummNal: $("#analizSummNal").val(),
            analizSummBeznal: $("#analizSummBeznal").val(),
            solarSummNal: $("#solarSummNal").val(),
            solarSummBeznal: $("#solarSummBeznal").val(),
            //summMinusNal: $("#summMinusNal").val()
            summMinusNal: $("#summMinusNal").html()
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == 'success') {
                    //console.log('success');
                    $('#data').html(res.data);
                    setTimeout(function () {
                        //window.location.replace('stat_cashbox.php');
                        window.location.replace('fl_consolidated_report_admin.php?filial_id='+filial_id);
                        //console.log('client.php?id='+id);
                    }, 500);
                }else{
                    //console.log('error');
                    $('#errrror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавление рабочих часов сотрудникам на филиале
    function fl_createSchedulerReport_add(){
        //console.log($("#allsumm").html().replace(/\s{2,}/g, ''));

        //убираем ошибки
        hideAllErrors ();

        var errors = false;

        //Соберём данные по часам
        var workerHoursValues_arr = {};
        var workerTypesValues_arr = {};

        $(".workerHoursValue").each(function(){
            // console.log($(this).attr('worker_id'));
            // console.log($(this).val());

            if (isNaN($(this).val())){
                errors = true;

                //console.log("#hours_"+$(this).attr('worker_id')+"_num_error");

                $("#hours_"+$(this).attr('worker_id')+"_num_error").html("В этом поле ошибка");
                $("#hours_"+$(this).attr('worker_id')+"_num_error").show();
            }else{
                //Часов должно быть хоть сколько-нибудь
                if ($(this).val() > 0) {
                    workerHoursValues_arr[$(this).attr('worker_id')] = $(this).val();
                    workerTypesValues_arr[$(this).attr('worker_id')] = $(this).attr('worker_type');
                }else{
                    errors = true;
                }
            }
        });

        if (!errors) {
            //console.log(workerHoursValues_arr);
            //console.log(workerTypesValues_arr);

            var link = "fl_createSchedulerReport_add_f.php";

            var filial_id = $("#SelectFilial").val();

            var reqData = {
                date: $("#iWantThisDate2").val(),
                filial_id: filial_id,
                workers_hours_data: workerHoursValues_arr,
                workers_types_data: workerTypesValues_arr
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
                    //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res.data);

                    //!!! Переделать тут нормально
                    if (res.result == 'success') {
                        //console.log('success');
                        $('#data').html(res.data);
                        setTimeout(function () {
                            //window.location.replace('stat_cashbox.php');
                            //window.location.replace('scheduler3.php?filial_id='+filial_id);
                            window.location.href = 'scheduler3.php?filial_id=' + filial_id;
                            //console.log('client.php?id='+id);
                        }, 500);
                    } else {
                        //console.log('error');
                        $('#errrror').html(res.data);
                        //$('#errrror').html('');
                    }
                }
            });
        }else{
            $("#errrror").html('<div class="query_neok">Ошибка, что-то заполнено не так. Часов должно быть большо 0.</div>')
        }
    }

    //Редактирование рабочих часов сотрудникам на филиале
    function fl_editSchedulerReport_add(report_ids){
        //console.log(report_ids);

        //убираем ошибки
        hideAllErrors ();

        //Соберём данные по часам
        var workerHoursValues_arr = {};
        var workerTypesValues_arr = {};

        $(".workerHoursValue").each(function(){
            // console.log($(this).attr('worker_id'));
            // console.log(Number($(this).val().replace(',', '.')));

            workerHoursValues_arr[$(this).attr('worker_id')] = $(this).val().replace(',', '.');
            workerTypesValues_arr[$(this).attr('worker_id')] = $(this).attr('worker_type');

        });
        //console.log(workerHoursValues_arr);

        var link = "fl_editSchedulerReport_add_f.php";

        var filial_id = $("#SelectFilial").val();

        var reqData = {
            report_ids: report_ids,
            date: $("#iWantThisDate2").val(),
            filial_id: filial_id,
            workers_hours_data: workerHoursValues_arr,
            workers_types_data: workerTypesValues_arr
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
                //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == 'success') {
                    //console.log('success');
                    $('#data').html(res.data);
                    setTimeout(function () {
                        //window.location.replace('stat_cashbox.php');
                        //window.location.replace('scheduler3.php?filial_id='+filial_id);
                        window.location.href = 'scheduler3.php?filial_id='+filial_id;
                        //console.log('client.php?id='+id);
                    }, 500);
                }else{
                    //console.log('error');
                    $('#errrror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Удалить часы за смену по id
    function fl_deleteSchedulerReportItem(report_id){
        //console.log(report_id);

        var rys = false;

        rys = confirm("Вы хотите удалить часы сотрудника за смену. \nЭто необратимо.\n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "scheduler_report_del_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    report_id: report_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    if (res.result == "success") {
                        location.reload();
                        //console.log(res.data);
                    }
                    if (res.result == "error") {
                        //alert(res.data);
                    }
                    //console.log(data.data);

                }
            });
        }
    }



    //Подсчет итогов за месяц
    function fl_getDailyReportsSummAllMonth(){

        $("#itogSummAllMonth").html(0);
        $("#arendaAllMonth").html(0);               $("#arendaAllMonthItog").html(0);       $("#arendaAllMonthItog2").html(0);
        $("#zReportAllMonth").html(0);
        $("#allSummAllMonth").html(0);
        $("#SummNalAllMonth").html(0);              $("#SummNalAllMonthItog").html(0);      $("#SummNalAllMonthItog2").html(0);
        $("#SummBeznalAllMonth").html(0);           $("#SummBeznalAllMonthItog").html(0);
        $("#SummNalStomCosmMonth").html(0);
        $("#SummBeznalStomCosmAllMonth").html(0);
        $("#SummCertNalAllMonth").html(0);
        $("#SummCertBeznalAllMonth").html(0);
        $("#ortoSummNalAllMonth").html(0);
        $("#ortoSummBeznalAllMonth").html(0);
        $("#specialistSummNalAllMonth").html(0);
        $("#specialistSummBeznalAllMonth").html(0);
        $("#analizSummNalAllMonth").html(0);
        $("#analizSummBeznalAllMonth").html(0);
        $("#solarSummNalAllMonth").html(0);
        $("#solarSummBeznalAllMonth").html(0);
        $("#summMinusNalAllMonth").html(0);

        $("#summGiveoutInBank").html(0);
        $("#summGiveoutDirector").html(0);

        //- Итог общий
        $(".itogSumm").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var itogSummAllMonth = Number($("#itogSummAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#itogSummAllMonth").html(number_format((itogSummAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- Итог общий нал
        $(".itogSummNal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var itogSummNalAllMonth = Number($("#itogSummNalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#itogSummNalAllMonth").html(number_format((itogSummNalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- Аренда
        $(".arenda").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var arendaAllMonth = Number($("#arendaAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#arendaAllMonth").html(number_format((arendaAllMonth + thisSumm), 2, '.', ' '));

                $("#arendaAllMonthItog").html(number_format((arendaAllMonth + thisSumm), 2, '.', ' '));
                $("#arendaAllMonthItog2").html(number_format((arendaAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- z-отчет
        $(".zReport").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var zReportAllMonth = Number($("#zReportAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#zReportAllMonth").html(number_format((zReportAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- общая сумма
        $(".allSumm").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var allSummAllMonth = Number($("#allSummAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#allSummAllMonth").html(number_format((allSummAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- сумма нал
        $(".SummNal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var SummNalAllMonth = Number($("#SummNalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#SummNalAllMonth").html(number_format((SummNalAllMonth + thisSumm), 2, '.', ' '));

                $("#SummNalAllMonthItog").html(number_format((SummNalAllMonth + thisSumm), 2, '.', ' '));
                $("#SummNalAllMonthItog2").html(number_format((SummNalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- сумма безнал
        $(".SummBeznal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var SummBeznalAllMonth = Number($("#SummBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#SummBeznalAllMonth").html(number_format((SummBeznalAllMonth + thisSumm), 2, '.', ' '));

                $("#SummBeznalAllMonthItog").html(number_format((SummBeznalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- ордеры нал
        $(".SummNalStomCosm").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var SummNalStomCosmAllMonth = Number($("#SummNalStomCosmAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#SummNalStomCosmAllMonth").html(number_format((SummNalStomCosmAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- ордеры безнал
        $(".SummBeznalStomCosm").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var SummBeznalStomCosmAllMonth = Number($("#SummBeznalStomCosmAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#SummBeznalStomCosmAllMonth").html(number_format((SummBeznalStomCosmAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- сертификаты нал
        $(".SummCertNal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var SummCertNalAllMonth = Number($("#SummCertNalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#SummCertNalAllMonth").html(number_format((SummCertNalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- сертификаты безнал
        $(".SummCertBeznal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var SummCertBeznalAllMonth = Number($("#SummCertBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#SummCertBeznalAllMonth").html(number_format((SummCertBeznalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- орто нал
        $(".ortoSummNal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var ortoSummNalAllMonth = Number($("#ortoSummNalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#ortoSummNalAllMonth").html(number_format((ortoSummNalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- орто безнал
        $(".ortoSummBeznal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var ortoSummBeznalAllMonth = Number($("#ortoSummBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#ortoSummBeznalAllMonth").html(number_format((ortoSummBeznalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- специалисты нал
        $(".specialistSummNal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var specialistSummNalAllMonth = Number($("#specialistSummNalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#specialistSummNalAllMonth").html(number_format((specialistSummNalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- специалисты безнал
        $(".specialistSummBeznal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var specialistSummBeznalAllMonth = Number($("#specialistSummBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#specialistSummBeznalAllMonth").html(number_format((specialistSummBeznalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- анализы нал
        $(".analizSummNal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var analizSummNalAllMonth = Number($("#analizSummNalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#analizSummNalAllMonth").html(number_format((analizSummNalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- анализы безнал
        $(".analizSummBeznal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var analizSummBeznalAllMonth = Number($("#analizSummBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#analizSummBeznalAllMonth").html(number_format((analizSummBeznalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- солярий нал
        $(".solarSummNal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var solarSummNalAllMonth = Number($("#solarSummNalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#solarSummNalAllMonth").html(number_format((solarSummNalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- солярий безнал
        $(".solarSummBeznal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var solarSummBeznalAllMonth = Number($("#solarSummBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#solarSummBeznalAllMonth").html(number_format((solarSummBeznalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- расход
        $(".summMinusNal").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));
            //console.log(Number($(this).html()));
            //console.log(Number($(this).html().replace(/\s{1,}/g, '')));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var summMinusNalAllMonth = Number($("#summMinusNalAllMonth").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#summMinusNalAllMonth").html(number_format((summMinusNalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- Выдачи в банк
        $(".giveout_inBank").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));
            //console.log(Number($(this).html()));
            //console.log(Number($(this).html().replace(/\s{1,}/g, '')));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var summMinusNalAllMonth = Number($("#summGiveoutInBank").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#summGiveoutInBank").html(number_format((summMinusNalAllMonth + thisSumm), 2, '.', ' '));

            }
        });
        //- Выдачи АНу
        $(".giveout_director").each(function(){
            //console.log($(this).html().replace(/\s{1,}/g, ''));
            //console.log(Number($(this).html()));
            //console.log(Number($(this).html().replace(/\s{1,}/g, '')));

            if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                var summMinusNalAllMonth = Number($("#summGiveoutDirector").html().replace(/\s{1,}/g, ''));
                var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                $("#summGiveoutDirector").html(number_format((summMinusNalAllMonth + thisSumm), 2, '.', ' '));

            }
        });

        //- Вся выручка
        $("#summAllMonth").html(number_format(
            (Number(
                $("#SummNalAllMonthItog").html().replace(/\s{1,}/g, '')
                )
            +
            Number(
                $("#arendaAllMonthItog").html().replace(/\s{1,}/g, '')
            )
            +
            Number(
                $("#SummBeznalAllMonthItog").html().replace(/\s{1,}/g, '')
            )
            )
            , 2, '.', ' ')
        );
        //- Все расходы
        $("#summMinusAllMonth").html(number_format(
            (Number(
                    $("#summMinusNalAllMonth").html().replace(/\s{1,}/g, '')
                )
                +
                Number(
                    $("#summGiveoutInBank").html().replace(/\s{1,}/g, '')
                )
                +
                Number(
                    $("#summGiveoutDirector").html().replace(/\s{1,}/g, '')
                )
            )
            , 2, '.', ' ')
        );
        //- Итог наличка
        $("#ostatokNalAllMonth").html(number_format(
            (Number(
                    $("#SummNalAllMonthItog2").html().replace(/\s{1,}/g, '')
                )
                +
                Number(
                    $("#arendaAllMonthItog2").html().replace(/\s{1,}/g, '')
                )
                -
                Number(
                    $("#summMinusAllMonth").html().replace(/\s{1,}/g, '')
                )
            )
            , 2, '.', ' ')
        );

        //Промежуточные (примерные) итоги показываем
        //$("#interimReport").html();
        $("#interimReport").show();

    }

    //Получение отчёта по какому-то дню из филиала и заполнение отчета
    function fl_getDailyReports(thisObj){

        //Дата
        var date = (thisObj.find(".reportDate").html().replace(/\s{2,}/g, ''));
        //console.log(date);
        //console.log(getTodayDate());

        //Блоки, где будут:
        //- Итог общий
        var itogSumm = (thisObj.find(".itogSumm"));
        //- Итог общий нал
        var itogSummNal = (thisObj.find(".itogSummNal"));
        //- Аренда
        var arenda = (thisObj.find(".arenda"));
        //- z-отчет
        var zReport = (thisObj.find(".zReport"));
        //- общая сумма
        var allSumm = (thisObj.find(".allSumm"));
        //- сумма нал
        var SummNal = (thisObj.find(".SummNal"));
        //- сумма безнал
        var SummBeznal = (thisObj.find(".SummBeznal"));
        //- сумма нал стом+косм
        var SummNalStomCosm = (thisObj.find(".SummNalStomCosm"));
        //- сумма безнал стом+косм
        var SummBeznalStomCosm = (thisObj.find(".SummBeznalStomCosm"));
        //- сертификаты нал
        var SummCertNal = (thisObj.find(".SummCertNal"));
        //- сертификаты безнал
        var SummCertBeznal = (thisObj.find(".SummCertBeznal"));
        //- орто нал
        var ortoSummNal = (thisObj.find(".ortoSummNal"));
        //- орто безнал
        var ortoSummBeznal = (thisObj.find(".ortoSummBeznal"));
        //- специалисты нал
        var specialistSummNal = (thisObj.find(".specialistSummNal"));
        //- специалисты безнал
        var specialistSummBeznal = (thisObj.find(".specialistSummBeznal"));
        //- анализы нал
        var analizSummNal = (thisObj.find(".analizSummNal"));
        //- анализы безнал
        var analizSummBeznal = (thisObj.find(".analizSummBeznal"));
        //- солярий нал
        var solarSummNal = (thisObj.find(".solarSummNal"));
        //- солярий безнал
        var solarSummBeznal = (thisObj.find(".solarSummBeznal"));
        //- расход
        var summMinusNal = (thisObj.find(".summMinusNal"));

        //- Выдачи в банк
        var giveout_inBank = (thisObj.find(".giveout_inBank"));
        //- Выдачи АНу
        var giveout_director = (thisObj.find(".giveout_director"));

        //убираем ошибки
        hideAllErrors ();

        var link = "fl_getDailyReports_f.php";

        var reqData = {
            date: date,
            filial_id: $("#SelectFilial").val()
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);
                //console.log(res.count);
                //console.log(date == getTodayDate());

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);
                    //console.log(res.count);
                    // console.log(res.data);
                    // console.log(res.giveout_bank);

                    if (res.count > 0){
                        //console.log(res.data);
                        //console.log(Object.size(res.data));
                        //console.log(Object.size(res.data));

                        thisObj.css({
                            "color": "#333"
                        });

                        //Закрываю, потому что буду отслеживать по count
                        //if (Object.size(res.data) > 0){}

                        var data = res.data[0];

                        //Если массив отчета не пустой
                        //if (date == getTodayDate()){
                        if (Object.size(data) > 0){



                            itogSumm.html               (number_format(data.itogSumm, 2, '.', ' ')).css({"text-align": "right"});
                            arenda.html                 (number_format(data.arenda, 0, '.', ' ')).css({"text-align": "right"});
                            zReport.html                (number_format(data.zreport, 2, '.', ' ')).css({"text-align": "right", "color": "rgb(18, 0, 255)"});
                            allSumm.html                (number_format(data.summ, 2, '.', ' ')).css({"text-align": "right"});
                            SummNal.html                (number_format(data.nal, 0, '.', ' ')).css({"text-align": "right"});
                            SummBeznal.html             (number_format(data.beznal, 0, '.', ' ')).css({"text-align": "right"});
                            SummNalStomCosm.html        (number_format(data.cashbox_nal, 0, '.', ' ')).css({"text-align": "right"});
                            SummBeznalStomCosm.html     (number_format(data.cashbox_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            SummCertNal.html            (number_format(data.cashbox_cert_nal, 0, '.', ' ')).css({"text-align": "right"});
                            SummCertBeznal.html         (number_format(data.cashbox_cert_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            ortoSummNal.html            (number_format(data.temp_orto_nal, 0, '.', ' ')).css({"text-align": "right"});
                            ortoSummBeznal.html         (number_format(data.temp_orto_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            specialistSummNal.html      (number_format(data.temp_specialist_nal, 0, '.', ' ')).css({"text-align": "right"});
                            specialistSummBeznal.html   (number_format(data.temp_specialist_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            analizSummNal.html          (number_format(data.temp_analiz_nal, 0, '.', ' ')).css({"text-align": "right"});
                            analizSummBeznal.html       (number_format(data.temp_analiz_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            solarSummNal.html           (number_format(data.temp_solar_nal, 0, '.', ' ')).css({"text-align": "right"});
                            solarSummBeznal.html        (number_format(data.temp_solar_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            summMinusNal.html           (number_format(data.temp_giveoutcash, 2, '.', ' ')).css({"text-align": "right"});

                            //Итог наличные
                            var itog_summ_nal = Number(data.nal) + Number(data.arenda) - Number(data.temp_giveoutcash);
                            console.log(data.nal);
                            console.log(data.arenda);
                            console.log(data.temp_giveoutcash);

                            itogSummNal.html            (number_format(itog_summ_nal, 0, '.', ' ')).css({"text-align": "right"});

                            //Прописываем статус отчета
                            $(thisObj).find(".reportDate").attr('status', data.status);
                            //И id
                            $(thisObj).find(".reportDate").attr('report_id', data.id);


                        }else{

                            thisObj.html('<div class="cellTime cellsTimereport reportDate" status="0" report_id="0" style="text-align: center; cursor: pointer; color: #333;">'+date+'</div>' +
                            '<div class="cellText" style="color: rgb(48, 185, 91); font-weight: normal; padding-left: 35px;"><i>Отчёт был заполнен и добавлен в архив, для изменений обратитесь к руководителю.</i></div>');

                        }

                        //Меняем цвет, если проверено
                        if (data.status == 7) {
                            $(thisObj).css({"background-color": "rgba(216, 255, 196, 0.98)"});
                            //блокируем ссылки
                            summMinusNal.css("pointer-events", "none");
                        }

                    }else{
                        //console.log(res.count);

                        itogSumm.html('-');
                        itogSummNal.html('-');
                        arenda.html('-');
                        zReport.html('-');
                        allSumm.html('-');
                        SummNal.html('-');
                        SummBeznal.html('-');
                        SummCertNal.html('-');
                        SummCertBeznal.html('-');
                        ortoSummNal.html('-');
                        ortoSummBeznal.html('-');
                        specialistSummNal.html('-');
                        specialistSummBeznal.html('-');
                        analizSummNal.html('-');
                        analizSummBeznal.html('-');
                        solarSummNal.html('-');
                        solarSummBeznal.html('-');
                        summMinusNal.html('-');
                    }

                    //Выдачи
                    if (res.giveout_bank > 0) {
                        giveout_inBank.html(number_format(res.giveout_bank, 0, '.', ' ')).css({"text-align": "right"});
                    }else{
                        giveout_inBank.html('-');
                    }
                    if (res.giveout_director > 0) {
                        giveout_director.html(number_format(res.giveout_director, 0, '.', ' ')).css({"text-align": "right"});
                    }else{
                        giveout_director.html('-');
                    }

                    //console.log(data);
                    //Если есть объект
                    if (data !== undefined) {
                        //Если в объекте есть ключ
                        //if ('status' in data) {
                            //Если ключ равен значению
                            if (data.status == 7) {
                                //блокируем ссылки

                                // console.log(giveout_inBank);
                                // console.log(giveout_director);
                                //console.log(giveout_inBank.html());
                                //console.log(giveout_director.html());

                                giveout_inBank.css("pointer-events", "none");
                                giveout_director.css("pointer-events", "none");
                            }
                        //}
                    }

                }else{
                    //console.log('error');
                    $('#errrror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Удаление ежедневного отчёта администраторов
    function fl_delete_consRepEdit(id){

        var reqData = {
            report_id: id
        };

        var link = "fl_deleteDailyReport_f.php";

        var rys = false;

        rys = confirm("Вы действительно хотите удалить отчёт?");

        if (rys) {

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

                    if (res.result == "success") {
                        location.reload();
                        //console.log(res.data);
                    }
                    if (res.result == "error") {
                        alert(res.data);
                    }
                    //console.log(data.data);

                }
            });
        }
    }

    //Установить статус проверено в ежедневный отчет администраторов
    function fl_check_consRepAdm(id){

        var reqData = {
            report_id: id
        };

        var link = "fl_checkDailyReport_f.php";

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

                if (res.result == "success") {
                    location.reload();
                    //console.log(res.data);
                }
                if (res.result == "error") {
                    alert(res.data);
                }
                //console.log(data.data);

            }
        });

    }

    //Снять статус проверено в ежедневный отчет администраторов
    function fl_uncheck_consRepEdit(id){

        var reqData = {
            report_id: id
        };

        var link = "fl_uncheckDailyReport_f.php";

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

                if (res.result == "success") {
                    location.reload();
                    //console.log(res.data);
                }
                if (res.result == "error") {
                    alert(res.data);
                }
                //console.log(data.data);

            }
        });

    }

    //Добавить ежедневный отчет администраторов
    function fl_add_consRepAdm(event){
        //console.log(event);

        var target = $(event.target);
        console.log(target);

        /*var reqData = {
            report_id: id
        };

        var link = "fl_uncheckDailyReport_f.php";

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

                if (res.result == "success") {
                    location.reload();
                    //console.log(res.data);
                }
                if (res.result == "error") {
                    alert(res.data);
                }
                //console.log(data.data);

            }
        });*/

    }

    //Редактирование оклада / добавление новой строчки
    var el = document.getElementById("currentSalary"), newInput;

    if(el) {
        el.addEventListener("click", function () {
            //console.log(el);

            $("#addSalaryOptions").html("<span id='newSalarySave' class='button_tiny' style='color: rgb(125, 125, 125); font-size: 11px; cursor: pointer;'><i class='fa fa-check' aria-hidden='true' style='color: green;' title='Сохранить'></i> Применить</span> <span id='newSalaryCancel' class='button_tiny' style='color: rgb(125, 125, 125); font-size: 11px; cursor: pointer;'><i class='fa fa-times' aria-hidden='true' style='color: red;'></i> Отменить</span>");
            $("#addSalaryDate").show();
            $("#salaryText").html("Введите новое значение:");

            var thisVal = this.innerHTML;
            var newVal = thisVal;

            var inputs = this.getElementsByTagName("input");
            if (inputs.length > 0) return;
            if (!newInput) {

                newInput = document.createElement("input");
                newInput.type = "text";
                newInput.maxLength = 7;
                newInput.setAttribute("size", 20);
                newInput.style.width = "80px";
                newInput.style.fontSize = "18px";

                //Клик вне поля
                //newInput.addEventListener("blur", function () {

                $("body").on("click", "#newSalaryCancel", function (event) {
                    //console.log("blur");

                    //$("#textAfterSalary").html("руб.");
                    $("#addSalaryOptions").html("");
                    $("#addSalaryDate").hide();
                    $("#salaryText").html("Текущий оклад:");

                    newInput.parentNode.innerHTML = thisVal;
                    newVal = thisVal;
                });
                //}, false);

                $("body").on("click", "#newSalarySave", function (event) {
                    //alert();

                    newVal = parseInt(newInput.value, 10);

                    var link = $("#pass").val()+".php";
                    //console.log(link);

                    if (link == "fl_add_new_salary_f.php") {
                        var reqData = {
                            worker_id: $("#worker_id").val(),
                            date_from: $("#iWantThisDate2").val(),
                            summ: newVal
                        };
                    }

                    if (link == "fl_add_new_salary_category_f.php") {
                        var reqData = {
                            category_id: $("#category_id").val(),
                            filial_id: $("#filial_id").val(),
                            permission_id: $("#permission_id").val(),
                            date_from: $("#iWantThisDate2").val(),
                            summ: newVal
                        };
                    }
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
                            if (res.result == "success") {
                                //console.log(res.data);

                                setTimeout(function () {
                                    location.reload();
                                }, 1000);

                            }

                        }
                    });

                });

            }

            //newInput.value = this.firstChild.innerHTML;
            newInput.value = thisVal;
            this.innerHTML = "";
            //this.appendChild(buttonDiv);
            this.appendChild(newInput);
            //newInput.innerHTML = ('<i class="fa fa-check" aria-hidden="true"></i>');
            newInput.focus();
            newInput.select();

        }.bind(el), false);
    }

    //Редактирование налога / добавление новой строчки
    //Для изменений в процентах персональных
    var changeTax_elems = document.getElementsByClassName("changeCurrentTax"), newInput;
    //console.log(elems);

    if (changeTax_elems.length > 0) {
        for (var i = 0; i < changeTax_elems.length; i++) {
            var el = changeTax_elems[i];
            el.addEventListener("click", function () {
                //var thisID = this.id;
                var workerID = this.getAttribute("worker_id");
                //console.log(this.getAttribute("worker_id"));
                //var catID = this.getAttribute("cat_id");
                //console.log(this.getAttribute("cat_id"));
                //var typeID = this.getAttribute("type_id");
                //console.log(this.getAttribute("type_id"));

                var thisVal = this.innerHTML;
                var newVal = thisVal;
                //console.log(this);
                //console.log(workerID);
                //console.log(catID);
                //console.log(typeID);
                //console.log(thisVal);
                //console.log(isNaN(thisVal));

                var inputs = this.getElementsByTagName("input");
                if (inputs.length > 0) return;
                if (!newInput) {

                    /*buttonDiv = document.createElement("div");
                     //buttonDiv.innerHTML = '<i class="fa fa-check" aria-hidden="true" title="Применить" style="margin-right: 4px;"></i> <i class="fa fa-refresh" aria-hidden="true" title="По умолчанию" style="color: red;"></i>';
                     buttonDiv.innerHTML = '<i class="fa fa-refresh" aria-hidden="true" title="По умолчанию" style="color: red;"></i>';
                     buttonDiv.style.position = "absolute";
                     buttonDiv.style.right = "-9px";
                     buttonDiv.style.top = "1px";
                     buttonDiv.style.fontSize = "12px";
                     buttonDiv.style.color = "green";
                     buttonDiv.style.border = "1px solid #BFBCB5";
                     buttonDiv.style.backgroundColor = "#FFF";
                     buttonDiv.style.padding = "0 6px";

                     buttonDiv.id = "changePersonalPercentCatdefault";*/

                    newInput = document.createElement("input");
                    newInput.type = "text";
                    newInput.maxLength = 8;
                    newInput.setAttribute("size", 20);
                    newInput.style.width = "50px";
                    newInput.addEventListener("blur", function () {
                        //console.log(newInput.parentNode.getAttribute("worker_id"));

                        workerID = newInput.parentNode.getAttribute("worker_id");
                        //catID = newInput.parentNode.getAttribute("cat_id");
                        //typeID = newInput.parentNode.getAttribute("type_id");

                        //Попытка обработать клика на кнопке для сброса на значения по умолчанию - провалилась, всегда сбрасывается на по умолчанию
                        //var changePersonalPercentCatdefault = document.getElementById("changePersonalPercentCatdefault");
                        //console.log(changePersonalPercentCatdefault.innerHTML);

                        //changePersonalPercentCatdefault.addEventListener("click", fl_changePersonalPercentCatdefault(workerID, catID, typeID), false);

                        //Новые данные
                        //if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (newInput.value > 100) || (isNaN(parseInt(newInput.value, 10)))) {
                        if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (isNaN(parseInt(newInput.value, 10)))) {
                            //newInput.parentNode.innerHTML = 0;
                            newInput.parentNode.innerHTML = thisVal;
                            newVal = thisVal;
                        } else {
                            newInput.parentNode.innerHTML = number_format(parseFloat(newInput.value, 10), 2, '.', '');
                            newVal = number_format(parseFloat(newInput.value, 10), 2, '.', '');
                        }
                        //console.log(this);
                        //console.log(workerID);

                        //console.log(thisVal == newVal);

                        if (Number(thisVal) != Number(newVal)) {
                            //console.log(newVal);

                            $.ajax({
                                url: "fl_change_personal_tax_f.php",
                                global: false,
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    worker_id: workerID,
                                    //cat_id: catID,
                                    //type: typeID,
                                    val: newVal
                                },
                                cache: false,
                                beforeSend: function () {
                                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                                },
                                // действие, при ответе с сервера
                                success: function (res) {
                                    if (res.result == "success") {
                                        //console.log(res);

                                        $('#infoDiv').html(res.data);
                                        $('#infoDiv').show();
                                        setTimeout(function () {
                                            $('#infoDiv').hide('slow');
                                            $('#infoDiv').html();
                                        }, 1000);

                                        //location.reload();
                                    }

                                }
                            });
                        }
                    }, false);
                }

                //newInput.value = this.firstChild.innerHTML;
                newInput.value = thisVal;
                this.innerHTML = "";
                //this.appendChild(buttonDiv);
                this.appendChild(newInput);
                //newInput.innerHTML = ('<i class="fa fa-check" aria-hidden="true"></i>');
                newInput.focus();
                newInput.select();
            }.bind(el), false);
        }
    }

    /*$("body").on("click", "#click_id", function(){
        alert('1234');
    });*/


    function deleteThisSalary(salary_id, type){
        //console.log(type);

        var rys = false;

        rys = confirm("Вы хотите удалить оклад. \nЭто необратимо.\nПо умолчанию будет оклад с более поздней датой.\n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "salary_del_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: salary_id,
                    type: type
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    if (res.result == "success") {
                        location.reload();
                        //console.log(res.data);
                    }
                    if (res.result == "error") {
                        //alert(res.data);
                    }
                    //console.log(data.data);

                }
            });
        }
    }

    //Функция изменения процента от выручки
    function Ajax_revenue_percent_change (type, filial_id, category){

        var value = $("#revenuePercent").val();
        value =  value.replace(',', '.');
        //console.log(value);

        if (!isNaN(value)) {
            if (value.length > 0){

                var link = "fl_revenue_percent_change_f.php";

                var reqData = {
                    permission: type,
                    filial_id: filial_id,
                    category: category,
                    value: value
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
                                location.reload()
                            }, 200);
                        } else {

                        }
                    }
                })

            }else {
            }
        }else{
            //console.log(value);
        }
    }

    //Показывает окно для изменения процента от выручки
    function revenuePercentChangeShow (haveValue, type, type_name, filial_id, filial_name, category, category_name, value){
        //console.log(mode);
        $('#overlay').show();


        var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_revenue_percent_change('+type+', '+filial_id+', '+category+')">';

        // if (haveValue){
        // }

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
                    .append('<div style="margin: 5px;"><i><b>'+filial_name+'</b></i></div>')
                    .append('<div style="margin: 5px;">'+type_name+'</div>')
                    .append('<div style="margin: 5px;">Категория: <i>'+category_name+'</i></div>')
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
                            .append('<div style="margin: 50px;"><input type="text" id="revenuePercent" value="'+value+'">%</div>')
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

        $("#revenuePercent").focus();
    }


    //Рассчёт общих часов сотрудников за месяц
    function calculateWorkerHours(){
        $(".workerItem").each(function() {
            var worker_id = ($(this).attr("worker_id"));
            //console.log(worker_id);

            var summHours = 0;

            $(".dayHours_"+worker_id).each(function() {
                summHours += parseFloat($(this).html(), 10) || 0;
                //summHours += $(this).html();
                //console.log($(this).html());
                //console.log(parseInt($(this).html(), 10));
            });
            //console.log(summHours);

            //Выведем кол-во часов
            $("#allMonthHours_"+worker_id).html(summHours);

            //Берем норму смен этого месяца для этого сотрудника
            //!!! Хотя норма для всех одинакова по сути... короче бред тут каждый раз брать одно и то же с разных мест

            var normaSmen = parseInt($("#allMonthNorma_"+worker_id).html(), 10) || 0;
            //console.log(normaSmen);

            //var hoursMonthPercent = 0;
            var hoursMonthPercent = summHours*100/normaSmen;

            $("#hoursMonthPercent_"+worker_id).html(number_format(hoursMonthPercent, 2, '.', ' '));

            $("#schedulerResult_"+worker_id).css({
                "background-image": "linear-gradient(to right, " + Colorize(Number(hoursMonthPercent.toFixed(0)), 1) + " " + Number(hoursMonthPercent.toFixed(0)) + "%, rgba(255, 255, 255, 0) 0%)"
            });
            //console.log("linear-gradient(to right, " + Colorize(hoursMonthPercent.toFixed(0)) + " " + hoursMonthPercent.toFixed(0) + "%, rgba(255, 255, 255, 0) 0%)");

        });
    }

    //Получение выручек всех филиалов и расчет зп
    function fl_calculateZP (month, year, typeW){
        // console.log(month);
        // console.log(year);
        // console.log(typeW);

        var link = "fl_calculateZP_f.php";

        var reqData = {
            month: month,
            year: year,
            typeW: typeW
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

                    $(".filialMoney").each(function(){
                        //console.log($(this).attr("filial_id"));

                        var filial_id = $(this).attr("filial_id");
                        var worker_id = $(this).attr("w_id");

                        //Если есть прикрепление к филиалу
                        if (filial_id > 0){
                            //console.log(filial_id);
                            //console.log(res.data[filial_id]);

                            $(this).html(number_format(res.data[filial_id], 2, '.', ' '));

                            $("#w_id_"+worker_id).attr("filialMoney", number_format(res.data[filial_id], 2, '.', ''));
                        }else{
                            //$(this).html('<span style="color: rgb(243, 0, 0);">не прикреплен</span>');
                            $(this).html('0.00');

                            $("#w_id_"+worker_id).attr("filialMoney", number_format(res.data[filial_id], 2, '.', ''));
                        }
                    });

                    $(".itogZP").each(function(){

                        var worker_id = $(this).attr("w_id");

                        var oklad = Number($(this).attr("oklad"));
                        var w_percentHours = Number($(this).attr("w_percentHours"));
                        var worker_revenue_percent = Number($(this).attr("worker_revenue_percent"));
                        var filialMoney = Number($(this).attr("filialMoney"));
                        //console.log(w_percentHours);

                        if (w_percentHours > 0){

                            var zp_temp = 0;
                            var revenue_summ = 0;

                            //Администраторы
                            // if (typeW == 4) {
                            //     zp_temp = (oklad * w_percentHours) / 100;
                            // }
                            //Ассистенты
                            // if (typeW == 7) {
                            //     var norma_smen = Number($("#w_norma_"+worker_id).html());
                            //     //console.log(norma_smen);
                            //     zp_temp = (oklad * norma_smen * w_percentHours) / 100;
                            // }

                            zp_temp = (oklad * w_percentHours) / 100;

                            revenue_summ = (((filialMoney / 100) * worker_revenue_percent) / 100) * w_percentHours;

                            var itogZP = zp_temp + revenue_summ;
                            //console.log(itogZP);

                            $("#zp_temp_"+worker_id).html(number_format(zp_temp, 2, '.', ''));
                            $("#w_revenue_summ_"+worker_id).html(number_format(revenue_summ, 2, '.', ''));
                            //console.log("#zp_temp_"+worker_id);
                            $(this).html(number_format(itogZP, 0, '.', ''));
                        }else{
                            $(this).html(number_format(itogZP, 0, '.', ''));
                        }

                        //Раскрасим часы рабочие
                        $("#w_hours_"+worker_id).css({
                            "background-image": "linear-gradient(to right, " + Colorize(Number(w_percentHours.toFixed(0)), .5) + " " + Number(w_percentHours.toFixed(0)) + "%, rgba(255, 255, 255, 0) 0%)"
                        });
                    })

                }else{
                    //--
                }
            }
        })
    }

    //Рассчет зп для fl_tabels3.php
    //для санитарок, дворников, уборщиц
    function fl_calculateZP2 (month, year, typeW){
        // console.log(month);
        // console.log(year);
        // console.log(typeW);

        $(".itogZP").each(function(){

            var worker_id = $(this).attr("w_id");

            var oklad = Number($(this).attr("oklad"));
            var w_percentHours = Number($(this).attr("w_percentHours"));
            var worker_revenue_percent = Number($(this).attr("worker_revenue_percent"));
            var filialMoney = Number($(this).attr("filialMoney"));
            console.log(w_percentHours);

            if (w_percentHours > 0){

                var zp_temp = 0;
                var revenue_summ = 0;

                //Администраторы
                // if (typeW == 4) {
                //     zp_temp = (oklad * w_percentHours) / 100;
                // }
                //Ассистенты
                // if (typeW == 7) {
                //     var norma_smen = Number($("#w_norma_"+worker_id).html());
                //     //console.log(norma_smen);
                //     zp_temp = (oklad * norma_smen * w_percentHours) / 100;
                // }

                zp_temp = (oklad * w_percentHours) / 100;
                //console.log(zp_temp);

                //revenue_summ = (((filialMoney / 100) * worker_revenue_percent) / 100) * w_percentHours;
                //console.log(revenue_summ);

                var itogZP = zp_temp + revenue_summ;
                console.log(itogZP);

                $("#zp_temp_"+worker_id).html(number_format(zp_temp, 2, '.', ''));
                $("#w_revenue_summ_"+worker_id).html(number_format(revenue_summ, 2, '.', ''));
                //console.log("#zp_temp_"+worker_id);
                $(this).html(number_format(itogZP, 0, '.', ''));
            }else{
                $(this).html(number_format(itogZP, 0, '.', ''));
            }

            //Раскрасим часы рабочие
            $("#w_hours_"+worker_id).css({
                "background-image": "linear-gradient(to right, " + Colorize(Number(w_percentHours.toFixed(0)), .5) + " " + Number(w_percentHours.toFixed(0)) + "%, rgba(255, 255, 255, 0) 0%)"
            });
        })


        // $(".itogZP").each(function(){
        //
        //     //var worker_id = $(this).attr("w_id");
        //
        //     var oklad = Number($(this).attr("oklad"));
        //     //console.log(oklad);
        //
        //     var itogZP = oklad;
        //     //console.log(itogZP);
        //
        //     $(this).html(number_format(itogZP, 0, '.', ''));
        // })
    }


    //Получение табелей за этот месяц
    function fl_getAllTabels (month, year, typeW){
        // console.log(month);
        // console.log(year);

        var link = "fl_getAllTabels_f.php";

        var reqData = {
            month: month,
            year: year,
            typeW: typeW
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
                    //console.log (res.data);

                    for(var worker_id in res.data){
                        // console.log (res.data[worker_id]);
                        // console.log (res.data[worker_id]['summ']);
                        // console.log (Number($('#w_id_' + worker_id).html()));
                        // console.log (res.data[worker_id]['summ'] == Number($('#w_id_' + worker_id).html()));


                        if (res.data[worker_id]['status'] == 7){
                            $("#worker_" + worker_id).html("<a href='fl_tabel.php?id=" + res.data[worker_id]['id'] + "' class='ahref'><i class='fa fa-file-text' aria-hidden='true' style='color: rgba(13,236,109,0.98); font-size: 130%;' title='Табель проведён'></i></a> " +
                                "");
                        }else {
                            if (res.data[worker_id]['summ'] == Number($('#w_id_' + worker_id).html())) {
                                $("#worker_" + worker_id).html("<a href='fl_tabel.php?id=" + res.data[worker_id]['id'] + "' class='ahref'><i class='fa fa-file-text' aria-hidden='true' style='color: rgba(215, 34, 236, 0.98); font-size: 130%;' title='Табель не проведён'></i></a> " +
                                    "");
                            } else {
                                $("#worker_" + worker_id).html("<a href='fl_tabel2.php?id=" + res.data[worker_id]['id'] + "' class='ahref'><i class='fa fa-file-text' aria-hidden='true' style='color: rgba(236,31,0,0.98); font-size: 130%;' title='Обновите данные табели'></i></a> " +
                                    "<i class='fa fa-refresh' aria-hidden='true' style='color: rgb(218, 133, 9); font-size: 100%; cursor: pointer;' title='Обновить' onclick=\'refreshTabelForWorkerFromSchedulerReport("+res.data[worker_id]['id']+", "+worker_id+");\'></i>");
                            }
                        }
                    }

                }else{
                    //--
                }
            }
        })
    }

    //Добавление нового табеля админа, ассиста
    function addNewTabelForWorkerFromSchedulerReport(worker_id, filial_id, type){
        // console.log(tabel_id);
        // console.log(worker_id);
        // console.log($("#w_id_"+worker_id).attr("oklad"));
        // console.log($("#w_id_"+worker_id).attr("w_percenthours"));
        // console.log($("#w_id_"+worker_id).attr("worker_revenue_percent"));
        //  console.log(Number($("#zp_temp_" + worker_id).html()));
        // console.log($("#w_id_"+worker_id).attr("filialmoney"));
        // console.log($("#w_id_"+worker_id).attr("worker_category_id"));
        // console.log($("#w_id_"+worker_id).attr("w_hours"));
        // console.log(Number($("#w_id_"+worker_id).html()));

        var rys = false;

        rys = confirm("Добавить новый табель?");

        if (rys) {

            var link = "fl_addNewTabelForWorkerFromSchedulerReport_f.php";

            var reqData = {
                worker_id: worker_id,
                filial_id: filial_id,
                type: type,
                month: $("#iWantThisMonth").val(),
                year: $("#iWantThisYear").val(),
                oklad: $("#w_id_" + worker_id).attr("oklad"),
                w_percenthours: $("#w_id_" + worker_id).attr("w_percenthours"),
                worker_revenue_percent: $("#w_id_" + worker_id).attr("worker_revenue_percent"),
                per_from_salary: Number($("#zp_temp_" + worker_id).html()),
                filialmoney: $("#w_id_" + worker_id).attr("filialmoney"),
                w_revenue_summ: Number($("#w_revenue_summ_"+worker_id).html()),
                worker_category_id: $("#w_id_" + worker_id).attr("worker_category_id"),
                w_hours: $("#w_id_" + worker_id).attr("w_hours"),
                summ: Number($("#w_id_" + worker_id).html())
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
                    //$("#errrror").html(res);

                    if (res.result == "success") {
                        //console.log(res.data);

                        location.reload();

                        //fl_getAllTabels ($("#iWantThisMonth").val(), $("#iWantThisYear").val(), type);
                    } else {
                        $("#errrror").html(res.data);
                        $('html, body').scrollTop(0);
                    }
                }
            })
        }

    }

    //Обновление данных табеля
    function refreshTabelForWorkerFromSchedulerReport(tabel_id, worker_id){
        // console.log(tabel_id);
        // console.log(worker_id);
        // console.log($("#w_id_"+worker_id).attr("oklad"));
        // console.log($("#w_id_"+worker_id).attr("w_percenthours"));
        // console.log($("#w_id_"+worker_id).attr("worker_revenue_percent"));
        //  console.log(Number($("#zp_temp_" + worker_id).html()));
        // console.log($("#w_id_"+worker_id).attr("filialmoney"));
        // console.log($("#w_id_"+worker_id).attr("worker_category_id"));
        // console.log($("#w_id_"+worker_id).attr("w_hours"));
        // console.log(Number($("#w_id_"+worker_id).html()));

        var rys = false;

        rys = confirm("Вы собираетесь обновить данные в табеле. \n\nВы уверены?");

        if (rys) {

            var link = "fl_refreshTabelForWorkerFromSchedulerReport_f.php";

            var reqData = {
                tabel_id: tabel_id,
                worker_id: worker_id,
                oklad: $("#w_id_" + worker_id).attr("oklad"),
                w_percenthours: $("#w_id_" + worker_id).attr("w_percenthours"),
                worker_revenue_percent: $("#w_id_" + worker_id).attr("worker_revenue_percent"),
                per_from_salary: Number($("#zp_temp_" + worker_id).html()),
                filialmoney: $("#w_id_" + worker_id).attr("filialmoney"),
                worker_category_id: $("#w_id_" + worker_id).attr("worker_category_id"),
                w_hours: $("#w_id_" + worker_id).attr("w_hours"),
                summ: Number($("#w_id_" + worker_id).html())
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
                        //console.log(res.data);

                        location.reload();
                    } else {
                        //--
                    }
                }
            })
        }
    }

    //Добавляем/редактируем в базу выплату в банку
    function  fl_Ajax_add_in_bank(mode, reqData){

        var link = "fl_addInBank_f.php";

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
            success:function(res){
                //console.log(res.data);
                //$('#data').html(res)

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    blockWhileWaiting (true);
                    document.location.href = "fl_consolidated_report_admin.php?filial_id=" + reqData.filial_id;

                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу выплату динектору
    function  fl_Ajax_add_to_director(mode, reqData){

        var link = "fl_addToDirector_f.php";

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
            success:function(res){
                //console.log(res.data);
                //$('#data').html(res)

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    blockWhileWaiting (true);
                    document.location.href = "fl_consolidated_report_admin.php?filial_id=" + reqData.filial_id;

                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Промежуточная функция для выплаты в банк
    function fl_showAjaxAddInBank (mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var filial_id = $('#SelectFilial').val();
        var date = $("#iWantThisDate2").val();
        var comment = $('#comment').val();
        var summ = $('#summ').val();

        var reqData = {
            filial_id: filial_id,
            date: date,
            summ: summ,
            comment: comment
        };

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: {summ: summ},

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                if(res.result == 'success'){

                    fl_Ajax_add_in_bank(mode, reqData);

                    // в случае ошибок в форме
                }else{
                    // перебираем массив с ошибками
                    for(var errorField in res.text_error){
                        // выводим текст ошибок
                        $('#'+errorField+'_error').html(res.text_error[errorField]);
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

    //Промежуточная функция для выплаты директору
    function fl_showAjaxAddToDirector (mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var filial_id = $('#SelectFilial').val();
        var date = $("#iWantThisDate2").val();
        var comment = $('#comment').val();
        var summ = $('#summ').val();

        var reqData = {
            filial_id: filial_id,
            date: date,
            summ: summ,
            comment: comment
        };

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: {summ: summ},

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                if(res.result == 'success'){

                    fl_Ajax_add_to_director(mode, reqData);

                    // в случае ошибок в форме
                }else{
                    // перебираем массив с ошибками
                    for(var errorField in res.text_error){
                        // выводим текст ошибок
                        $('#'+errorField+'_error').html(res.text_error[errorField]);
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


