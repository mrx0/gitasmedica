

    //Ждем ждём ожидание
    //Взято с Хабра https://habrahabr.ru/post/134823/
    //first — первая функция,которую нужно запустить
    wait = function(first){
        //класс для реализации вызова методов по цепочке
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

        $.ajax({
            url:"ajax_show_result_cashbox_f.php",
            global: false,
            type: "POST",
            data:
                {
                    datastart: document.getElementById("datastart").value,
                    dataend: document.getElementById("dataend").value,

                    filial: document.getElementById("filial").value,

                    summtype: summtype,

                    /*zapisTypeAll: zapisTypeAll,
                    zapisTypeStom: zapisTypeStom,
                    zapisTypeCosm: zapisTypeCosm,*/

                    certificatesShow: certificatesShow,

                },
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

        var rys = confirm("Удалить оплату?");

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

    //Удалить расчет
    function fl_deleteCalculateItem(id, client_id, invoice_id){
        //console.log(id);

        var rys = false;

        var rys = confirm("Удалить расчетный лист?");

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

        var rys = confirm("Вы собираетесь удалить затраты на материалы.\nЭто необратимое действие.\nРасчётный лист будет пересчитан.\nВы уверены?");

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

        var rys = confirm("Сбросить на значения по умолчанию?");

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
        ;
    };

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
                            document.location.href = "fl_addNewTabel.php";
                        }else{
                            //console.log(12333);
                            document.location.href = "fl_addINExistTabel.php";
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
                    document.location.href = "fl_tabels.php";
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
                    document.location.href = "fl_tabels.php";
                }else{
                    $('#errror').html(res.data);
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

        var rys = confirm("Вы хотите удалить РЛ из табеля. \n\nВы уверены?");
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

        var rys = confirm("Вы хотите удалить Вычет из табеля. \n\nВы уверены?");
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

        var rys = confirm("Вы хотите удалить Надбавку из табеля. \n\nВы уверены?");
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
    function fl_showDeductionAdd (deduction_id, tabel_id, mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var deduction_summ = $('#deduction_summ').val();
        var descr = $('#descr').val();

        var deductionData = {
            tabel_id:tabel_id,
            deduction_summ:deduction_summ,
            descr:descr
        };

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data:deductionData,

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
    function fl_showSurchargeAdd (surcharge_id, tabel_id, mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var surcharge_summ = $('#surcharge_summ').val();
        var descr = $('#descr').val();

        var surchargeData = {
            tabel_id:tabel_id,
            surcharge_summ:surcharge_summ,
            descr:descr
        };

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data:surchargeData,

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

        var rys = confirm("Вы собираетесь провести табель.\nПосле этого изменить его не получится.\nВы уверены?");
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

        var rys = confirm("Вы уверены, что хотите\nснять отметку о проведении табеля?");
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

        var rys = confirm("Вы уверены, что хотите удалить \nночные смены из табеля?");
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

        var rys = confirm("Вы уверены, что хотите удалить \n\"пустые\" смены из табеля?");
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