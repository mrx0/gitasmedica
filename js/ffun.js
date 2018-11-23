

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
                    summ:Summ,
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
                    cert_id: cert_id,
                    summ: Summ,
                    date_in: date_in,
                    comment: comment,
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
                    summ:Summ,
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
            payment_id = document.getElementById("payment_id").value;
        }

        var Summ = document.getElementById("summ").value;
        var invoice_id = document.getElementById("invoice_id").value;

        var client_id = document.getElementById("client_id").value;
        var date_in = document.getElementById("date_in").value;
        //console.log(date_in);

        var comment = document.getElementById("comment").value;
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
                    summ: Summ,
                    date_in: date_in,
                    comment: comment,
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
                    newInput.maxLength = 3;
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
                        if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (newInput.value > 100) || (isNaN(parseInt(newInput.value, 10)))) {
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
                                    val: newVal,
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

            /*$.ajax({
                url: "fl_addWorkersIDsINSessionForPrint.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    workersIDarr: workersIDs_arr
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    console.log(res);

                    /*if (res.result == "success") {
                        //console.log(res);

                        //document.location.href = "fl_addNewTabel.php";
                        //window.open("fl_addNewTabel.php", 'newTabelwindow', 'width=800, height=800, scrollbars=yes,resizable=yes,menubar=no,toolbar=yes,status=yes');

                        iOpenNewWindow("fl_addINExistTabel.php", 'oldTabelwindow', 'width=800, height=800, scrollbars=yes,resizable=yes,menubar=no,toolbar=yes,status=yes');
                    }*/
            /*    }
            });*/

        }).wait(function(runNext){
            //console.log(1);

            setTimeout(function(){
                var elems = document.getElementsByClassName('rezult_item');
                //console.log(elems);
                var arr = jQuery.makeArray(document.getElementsByClassName('rezult_item'));
                //console.log(arr);

                arr.sort(function (a, b) {
                    a = $(a).attr('fio');
                    //console.log(a);
                    b = $(b).attr('fio');
                    //console.log(b);
                    return a.localeCompare(b);
                });
                console.log(arr);

                $(arr).appendTo("#rezult");

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
                    summCalcs: $(".summCalcsNPaid").html(),
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
                    console.log(res);

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
                    console.log(res);

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
                    console.log(res);

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
    function  fl_Ajax_deduction_add(deduction_id, tabel_id, mode, deductionData){

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

                    document.location.href = "fl_tabel.php?id="+tabel_id;
                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу надбавку в табель
    function  fl_Ajax_surcharge_add(surcharge_id, tabel_id, mode, surchargeData){

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

                    document.location.href = "fl_tabel.php?id="+tabel_id;
                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу выплату в табель
    function  fl_Ajax_paidout_add(paidout_id, tabel_id, mode, paidoutData){

        var link = "fl_paidout_add_f.php";

        if (mode == 'edit'){
            link = "fl_paidout_edit_f.php";
        }

        paidoutData['paidout_id'] = paidout_id;

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data:paidoutData,

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

                    document.location.href = "fl_tabel.php?id="+tabel_id;
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
    function fl_showDeductionAdd (deduction_id, tabel_id, type, mode){
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

                    fl_Ajax_deduction_add(deduction_id, tabel_id, mode, deductionData);

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
    function fl_showSurchargeAdd (surcharge_id, tabel_id, type, mode){
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

                    fl_Ajax_surcharge_add(surcharge_id, tabel_id, mode, surchargeData);

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
    function fl_showPaidoutAdd (paidout_id, tabel_id, type, mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var paidout_summ = $('#paidout_summ').val();
        var descr = $('#descr').val();

        var paidoutData = {
            tabel_id:tabel_id,
            type:type,
            paidout_summ:paidout_summ,
            descr:descr
        };

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: {paidout_summ:paidout_summ},

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                if(res.result == 'success'){

                    fl_Ajax_paidout_add(paidout_id, tabel_id, mode, paidoutData);

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


    $(document).ready(function() {
        //console.log(123);


        //Рабочий пример клика на элементе после подгрузки загрузки его в DOM
        $("body").on("click", ".chkBoxCalcs", function(){
            var checked_status = $(this).prop("checked");
            //console.log(checked_status);
            //console.log($(this).parent());

            if (checked_status){
                $(this).parent().parent().parent().css({"background-color": "#83DB53"});
            }else{
                $(this).parent().parent().parent().css({"background-color": "rgb(255, 255, 255);"});
            }
        });


        $("body").on("click", ".checkAll", function(){
            var checked_status = $(this).is(":checked");
            var thisId = $(this).attr("id");

            $("."+thisId).each(function() {
                if (checked_status){
                    $(this).prop("checked", true);
                    $(this).parent().parent().parent().css({"background-color": "#83DB53"});
                }else{
                    $(this).prop("checked", false);
                    $(this).parent().parent().parent().css({"background-color": "rgb(255, 255, 255);"});
                }
            });
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
            }
        });
    }

    //Получаем необработанные расчетные листы
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
                            $("#filial_"+reqData['office']).css({
                                "pointer-events": "none",
                                "cursor": "default",
                                "background-color": "rgba(140, 137, 137, 0.7)",
                            })
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

        var now = new Date();
        var year = now.getFullYear();
        var month = now.getMonth();
        month = Number(month)+1;
        if (Number(month) < 10){
            month = "0"+month;
        }
        //console.log(month);

        var needCalcObj = thisObj.parent().find('.tableDataNPaidCalcs')
        var needTabelObj = thisObj.parent().find('.tableTabels')


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

                //!!! Хороший пример пацзы в цикле
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

    //!!!! тест разбор
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

        var summ = 0;

        $(".allSumm").each(function(){
            summ += Number($(this).html());
        });
        $(".allSummInput").each(function(){
            summ += Number($(this).val());
        });
        
        summ = summ - $(".summMinus").val();

        $("#allsumm").html(summ);

    }

    //Добавление ежедневного отчёта в бд
    function fl_createDailyReport_add(){

        //убираем ошибки
        hideAllErrors ();

        var link = "fl_createDailyReport_add_f.php";

        var filial_id = $("#SelectFilial").val();

        var reqData = {
            date: $("#iWantThisDate2").val(),
            filial_id: filial_id,
            allsumm: $("#allsumm").html(),
            SummNal: $("#SummNal").html(),
            SummBeznal: $("#SummBeznal").html(),
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
            summMinusNal: $("#summMinusNal").val()
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
                    }, 300);
                }else{
                    //console.log('error');
                    $('#errrror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Получение отчёта по какому-то дню из филиала и заполнение отчета
    function fl_getDailyReports(thisObj){

        //Дата
        var date = (thisObj.find(".reportDate").html().replace(/\s{2,}/g, ''));
        //console.log(date);

        //Блоки, где будут:
        //- z-отчет
        //var zReport = (thisObj.find(".zReport"));
        //- общая сумма
        var allSumm = (thisObj.find(".allSumm"));
        //- сумма нал
        var SummNal = (thisObj.find(".SummNal"));
        //- сумма безнал
        var SummBezal = (thisObj.find(".SummBezal"));
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

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);
                    if (res.count > 0){
                        //console.log(res.data[0]);

                        thisObj.css({
                            "color": "#333"
                        });

                        //zReport.html              (number_format(res.data[0].summ, 0, '.', ' '));
                        allSumm.html                (number_format(res.data[0].summ, 0, '.', ' '));
                        SummNal.html                (number_format(res.data[0].cashbox_nal, 0, '.', ' '));
                        SummBezal.html              (number_format(res.data[0].cashbox_beznal, 0, '.', ' '));
                        SummCertNal.html            (number_format(res.data[0].cashbox_cert_nal, 0, '.', ' '));
                        SummCertBeznal.html         (number_format(res.data[0].cashbox_cert_beznal, 0, '.', ' '));
                        ortoSummNal.html            (number_format(res.data[0].temp_orto_nal, 0, '.', ' '));
                        ortoSummBeznal.html         (number_format(res.data[0].temp_orto_beznal, 0, '.', ' '));
                        specialistSummNal.html      (number_format(res.data[0].temp_specialist_nal, 0, '.', ' '));
                        specialistSummBeznal.html   (number_format(res.data[0].temp_specialist_beznal, 0, '.', ' '));
                        analizSummNal.html          (number_format(res.data[0].temp_analiz_nal, 0, '.', ' '));
                        analizSummBeznal.html       (number_format(res.data[0].temp_analiz_beznal, 0, '.', ' '));
                        solarSummNal.html           (number_format(res.data[0].temp_solar_nal, 0, '.', ' '));
                        solarSummBeznal.html        (number_format(res.data[0].temp_solar_beznal, 0, '.', ' '));
                        summMinusNal.html           (number_format(res.data[0].temp_giveoutcash, 0, '.', ' '));
                    }else{
                        //console.log(res.count);

                        allSumm.html('-');
                        SummNal.html('-');
                        SummBezal.html('-');
                        //zReport.html('-');
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
                }else{
                    //console.log('error');
                    $('#errrror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }


