
    function ShowSettingsAddTempZapis(filial, filial_name, kab, year, month, day, smena, time, period, worker_id, worker_name, patient_name, description, insured, pervich, noch, zapis_id, type, add_or_edit){
        document.getElementById("errror").innerHTML="";
        //alert(period);

        $("#ShowSettingsAddTempZapis").show();
        $("#overlay").show();

        //alert(month_date);
        window.scrollTo(0,0);

        //document.getElementById("Ajax_add_TempZapis").disabled = false;

        if (add_or_edit == 'edit'){
            document.getElementById("search_client").disabled = true;
        }
        if (add_or_edit == 'add'){
            document.getElementById("search_client").disabled = false;
        }

        //var wt =  document.getElementById("wt").value;

        $("#filial").val(filial);
        $("#year").val(year);
        $("#month").val(month);
        $("#day").val(day);
        $("#start_time").val(time);
        $("#wt").val(period);
        $("#worker_id").val(worker_id);
        if (zapis_id != 0) {
            $("#zapis_id").val(zapis_id);
        }

        $("#filial_name").html(filial_name);

        if (worker_id == 0){
            $("#search_client2").val("");
        }else{
            $("#search_client2").val(worker_name);
        }

        if (patient_name.length > 0) {
            $("#search_client").val(patient_name);
        }
        if (description.length > 0) {
            $("#description").val(description);
        }

        $("#kab").html(kab);
        //$("#month_date").html(day+'.'+month+'.'+year);

        if (Number(day) < 10) day='0'+day;
        if (Number(month) < 10) month='0'+month;

        $("#month_date").val(day+'.'+month+'.'+year);
        $("#month_date_smena").html(smena);

        if (pervich == 1){
            var pervich_checkbox = document.getElementById("pervich");
            pervich_checkbox.checked = true;
        }
        if (insured == 1){
            var insured_checkbox = document.getElementById("insured");
            insured_checkbox.checked = true;
        }
        if (noch == 1){
            var noch_checkbox = document.getElementById("noch");
            noch_checkbox.checked = true;
        }

        var change_hours = period/60|0;
        var change_minutes = period%60;

        $("#change_hours").val(change_hours);
        $("#change_minutes").val(change_minutes);

        var real_time_h = time/60|0;
        var real_time_m = time%60;
        if (real_time_m < 10) real_time_m = "0"+real_time_m;

        var real_time_h_end = (time+period)/60|0;
        if (real_time_h_end > 23) real_time_h_end = real_time_h_end - 24;
            real_time_m_end = (time+period)%60;
        if (real_time_m_end < 10) real_time_m_end = '0'+real_time_m_end;

        $("#work_time_h").val(real_time_h);
        $("#work_time_m").val(real_time_m);

        $("#work_time_h_end").html(real_time_h_end);
        $("#work_time_m_end").html(real_time_m_end);

        var Ajax_add_TempZapis_button = '<input type="button" class="b" value="OK" onclick="if (iCanManage) Ajax_add_TempZapis('+type+')" id="Ajax_add_TempZapis">';
        var month_date_change = '';

        if (add_or_edit == 'edit'){
            Ajax_add_TempZapis_button = '<input type="button" class="b" value="OK" onclick="if (iCanManage) Ajax_edit_TempZapis('+type+')" id="Ajax_add_TempZapis">';
            month_date_change = '1';
        }

        $("#Ajax_add_TempZapis_button").html(Ajax_add_TempZapis_button);
        $("#month_date_change").html(month_date_change);

        //var next_time_start_rez = 0;

        /*$.ajax({
            dataType: "json",
            async: false,
            // метод отправки
            type: "POST",
            // путь до скрипта-обработчика
            url: "get_next_zapis.php",
            // какие данные будут переданы
            data: {
                type: type,

                wt: period,

                day: day,
                month: month,
                year: year,

                filial: filial,
                kab: kab,

                start_time: time,

                datatable: "zapis"
            },
            // действие, при ответе с сервера
            success: function(next_zapis_data){
                //console.log (next_zapis_data.yo);
                //document.getElementById("kab").innerHTML=nex_zapis_data;
                next_time_start_rez = next_zapis_data.next_time_start;
                next_time_end_rez = next_zapis_data.next_time_end;
                //next_zapis_data;

            }
        });

        //alert(next_time_start_rez);

        if (next_time_start_rez != 0){

            //if ((time+period > next_time_start_rez) || (time == next_time_start_rez)){
            if (((time+period > next_time_start_rez) && (time+period < next_time_end_rez)) || ((time >= next_time_start_rez) && (time < next_time_end_rez))){
                //document.getElementById("exist_zapis").innerHTML=\'<span style="color: red">Дальше есть запись</span>\';

                var raznica_vremeni = Math.abs(next_time_start_rez - time);

                document.getElementById("change_hours").value = raznica_vremeni/60|0;
                document.getElementById("change_minutes").value = raznica_vremeni%60;

                change_hours = raznica_vremeni/60|0;
                change_minutes = raznica_vremeni%60;

                var end_time = time+change_hours*60+change_minutes;


                var real_time_h_end = end_time/60|0;
                if (real_time_h_end > 23) real_time_h_end = real_time_h_end - 24;
                real_time_m_end = end_time%60;
                if (real_time_m_end < 10) real_time_m_end = "0"+real_time_m_end;

                document.getElementById("work_time_h_end").innerHTML=real_time_h_end;
                document.getElementById("work_time_m_end").innerHTML=real_time_m_end;

                document.getElementById("wt").value=change_hours*60+change_minutes;

                document.getElementById("Ajax_add_TempZapis").disabled = true;
            }else{
                //if (time+period < next_time_start_rez){
                document.getElementById("exist_zapis").innerHTML="";
                document.getElementById("Ajax_add_TempZapis").disabled = false;
            }
        }else{
            document.getElementById("exist_zapis").innerHTML="";
            document.getElementById("Ajax_add_TempZapis").disabled = false;
        }*/



    }

    function HideSettingsAddTempZapis(){
        $('#ShowSettingsAddTempZapis').hide();
        $('#overlay').hide();
        $("#wt").value = 0;
        $("#change_hours").val(0);
        $("#change_minutes").val(30);

        $("#search_client").val('');
        $("#search_client2").val('');

        $("#description").val('');
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
                day:$('#day').val(),
                month:$('#month').val(),
                year:$('#year').val(),
                smena:smena,
                    datatable:"'.$datatable.'"
                },
            // действие, при ответе с сервера
            success: function(workers){
                document.getElementById("ShowWorkersHere").innerHTML=workers;
            }
        });
    }