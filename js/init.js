$(document).ready(function(){

	//var  map_class = $('.map');
	
	//$.each(map_class, function(){
		//var R = Raphael($(this).attr('id'), 600, 150);

    	if($('*').is('.map_exist')) {

            var R = Raphael('map', 600, 150);


            var i = 1,
                console = 0;
            colors = {},
                t_menu_arr = {},
                t_menu_arrA = {},
                area = {},
                col_arr_temp = {};

            var oAreas = $(".mapArea");

            $.each(oAreas, function () {
                if ($(this).attr("t_status") == 'yes') {
                    console = 1;
                } else {
                    console = 0;
                }
                area[i] = R.path($(this).attr("data-path")).attr(JSON.parse("{" + $(this).attr("status-path") + "," + $(this).attr("fill-color") + "}"));

                //получаем цвет из аттрибута
                col_arr_temp = $(this).attr("fill-color").split(':');
                var col = col_arr_temp[1].replace(new RegExp('"', 'g'), '');
                //получаем меню из аттрибута
                var t_menu_temp = $(this).attr("t_menu");
                //получаем меню из аттрибута2
                var t_menu_tempA = $(this).attr("t_menuA");
                //col = col.replace('#', '');

                //собираем массив цветов
                colors[i] = col;
                //собираем массив менюшек2
                t_menu_arr [i] = t_menu_temp;
                //собираем массив менюшек
                t_menu_arrA [i] = t_menu_tempA;
                //t = document.getElementById('time_now');
                //t.innerHTML = colors[2];

                i++;
            });


            for (var j = 1; j < i; j++) {
                (function (o, j) {
                    o[0].style.cursor = "pointer";

                    o[0].onmouseover = function () {
                        var color = "#ff0000";
                        o.animate({fill: color}, 250);

                        //меню при наведении

                        //var color = "rgba(255, 0, 0, 0.75)";
                        var color = "#F00";
                        o.animate({fill: color}, 250);

                        var point2 = o[0].getBBox();

                        $('#map').next('.point2').remove();

                        $('#map').after($('<div />').addClass('point2'));

                        //текст для меню
                        //t_menu = 'Номер зуба...<br /><img src="img/tooth_state/1.png" border="0" />Отсутствует<br /><img src="img/tooth_state/2.png" border="0" />Молочный<br /><img src="img/tooth_state/3.png" border="0" />Удалён<br /><img src="img/tooth_state/4.png" border="0" />Имплантант<br /><img src="img/tooth_state/5.png" border="0" />Форм. десны<br /><img src="img/tooth_state/6.png" border="0" />Коронка<br /><img src="img/tooth_state/7.png" border="0" />Культ. вкладка<br /><img src="img/tooth_state/8.png" border="0" />Бюгел. протез<br /><img src="img/tooth_state/9.png" border="0" />Мост<br /><img src="img/tooth_state/10.png" border="0" />Искусственный<br /><img src="img/tooth_state/11.png" border="0" />Чужая коронка<br /><img src="img/tooth_state/12.png" border="0" />Чужой мост<br /><img src="img/tooth_state/13.png" border="0" />Чужой бюгел.<br /><img src="img/tooth_state/14.png" border="0" />Полный съем.<br /><img src="img/tooth_state/15.png" border="0" />Частич. съем.<br /><img src="img/tooth_state/16.png" border="0" />Чужой пол.<br /><img src="img/tooth_state/17.png" border="0" />Чужой час.<br />';
                        t_menu = i;

                        $('.point2')

                        //.html('Номер зуба...<br /><img src="img/tooth_state/1.png" border="0" />Отсутствует<br /><img src="img/tooth_state/2.png" border="0" />Молочный<br /><img src="img/tooth_state/3.png" border="0" />Удалён<br /><img src="img/tooth_state/4.png" border="0" />Имплантант<br /><img src="img/tooth_state/5.png" border="0" />Форм. десны<br /><img src="img/tooth_state/6.png" border="0" />Коронка<br /><img src="img/tooth_state/7.png" border="0" />Культ. вкладка<br /><img src="img/tooth_state/8.png" border="0" />Бюгел. протез<br /><img src="img/tooth_state/9.png" border="0" />Мост<br /><img src="img/tooth_state/10.png" border="0" />Искусственный<br /><img src="img/tooth_state/11.png" border="0" />Чужая коронка<br /><img src="img/tooth_state/12.png" border="0" />Чужой мост<br /><img src="img/tooth_state/13.png" border="0" />Чужой бюгел.<br /><img src="img/tooth_state/14.png" border="0" />Полный съем.<br /><img src="img/tooth_state/15.png" border="0" />Частич. съем.<br /><img src="img/tooth_state/16.png" border="0" />Чужой пол.<br /><img src="img/tooth_state/17.png" border="0" />Чужой час.<br />')
                            .html(t_menu_arrA [j])

                            //.prepend($('<a />').attr('href', '#').addClass('close').text('Close'))
                            .css({
                                left: point2.width,
                                top: point2.height
                            })
                            .stop(true, true).fadeIn();

                        //конец меню при наведении

                    };

                    o[0].onmouseout = function () {
                        $('.point2').stop(true, true).remove();
                        var color = colors[j];
                        o.animate({fill: color}, 250);
                    };

                    o[0].onclick = function () {
                        if (console == 1) {
                            var color = "#F00";
                            o.animate({fill: color}, 250);

                            //alert (t_menu_arr[j]);
                            $('.point').each(function () {
                                this.remove();
                            });

                            var point = o[0].getBBox();

                            $('#map').next('.point').remove();

                            $('#map').after($('<div />').addClass('point'));

                            //текст для меню
                            //t_menu = 'Номер зуба...<br /><img src="img/tooth_state/1.png" border="0" />Отсутствует<br /><img src="img/tooth_state/2.png" border="0" />Молочный<br /><img src="img/tooth_state/3.png" border="0" />Удалён<br /><img src="img/tooth_state/4.png" border="0" />Имплантант<br /><img src="img/tooth_state/5.png" border="0" />Форм. десны<br /><img src="img/tooth_state/6.png" border="0" />Коронка<br /><img src="img/tooth_state/7.png" border="0" />Культ. вкладка<br /><img src="img/tooth_state/8.png" border="0" />Бюгел. протез<br /><img src="img/tooth_state/9.png" border="0" />Мост<br /><img src="img/tooth_state/10.png" border="0" />Искусственный<br /><img src="img/tooth_state/11.png" border="0" />Чужая коронка<br /><img src="img/tooth_state/12.png" border="0" />Чужой мост<br /><img src="img/tooth_state/13.png" border="0" />Чужой бюгел.<br /><img src="img/tooth_state/14.png" border="0" />Полный съем.<br /><img src="img/tooth_state/15.png" border="0" />Частич. съем.<br /><img src="img/tooth_state/16.png" border="0" />Чужой пол.<br /><img src="img/tooth_state/17.png" border="0" />Чужой час.<br />';
                            t_menu = i;

                            $('.point')

                            //.html('Номер зуба...<br /><img src="img/tooth_state/1.png" border="0" />Отсутствует<br /><img src="img/tooth_state/2.png" border="0" />Молочный<br /><img src="img/tooth_state/3.png" border="0" />Удалён<br /><img src="img/tooth_state/4.png" border="0" />Имплантант<br /><img src="img/tooth_state/5.png" border="0" />Форм. десны<br /><img src="img/tooth_state/6.png" border="0" />Коронка<br /><img src="img/tooth_state/7.png" border="0" />Культ. вкладка<br /><img src="img/tooth_state/8.png" border="0" />Бюгел. протез<br /><img src="img/tooth_state/9.png" border="0" />Мост<br /><img src="img/tooth_state/10.png" border="0" />Искусственный<br /><img src="img/tooth_state/11.png" border="0" />Чужая коронка<br /><img src="img/tooth_state/12.png" border="0" />Чужой мост<br /><img src="img/tooth_state/13.png" border="0" />Чужой бюгел.<br /><img src="img/tooth_state/14.png" border="0" />Полный съем.<br /><img src="img/tooth_state/15.png" border="0" />Частич. съем.<br /><img src="img/tooth_state/16.png" border="0" />Чужой пол.<br /><img src="img/tooth_state/17.png" border="0" />Чужой час.<br />')
                                .html(DrawTeethMapMenu(t_menu_arr[j]))

                                .prepend($('<a />').attr('href', '#').addClass('close').text('Close'))
                                //.prepend($('<img />').attr('src', 'flags/'+arr[this.id]+'.png'))
                                .css({
                                    //left: point.x+(point.width)+60,
                                    //top: point.y+(point.height)+120
                                    left: point.x + (point.width) + 60,
                                    top: point.y + (point.height) + 120
                                })
                                .stop(true, true).fadeIn();
                        }
                    };


                })(area[j], j);
            }
        }
	//});
});
