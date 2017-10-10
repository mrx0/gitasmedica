

$(document).ready(function(){

	//var  map_class = $('.map');
	
	//$.each(map_class, function(){
		//var R = Raphael($(this).attr('id'), 600, 150);

    	if($('*').is('.map_exist')) {

            var R = Raphael('map1', 600, 150);


            var i = 1,
                console = 0;
            colors1 = {},
                t_menu_arr1 = {},
                t_menu_arrA1 = {},
                area = {},
                col_arr_temp = {};

            var oAreas = $(".mapArea1");

            $.each(oAreas, function () {
                if ($(this).attr("t_status") == 'yes') {
                    console = 1;
                } else {
                    console = 0;
                }
                area[i] = R.path($(this).attr("data-path1")).attr(JSON.parse("{" + $(this).attr("status-path") + "," + $(this).attr("fill-color1") + "}"));

                //получаем цвет из аттрибута
                col_arr_temp = $(this).attr("fill-color1").split(':');
                var col = col_arr_temp[1].replace(new RegExp('"', 'g'), '');
                //получаем меню из аттрибута
                var t_menu_temp1 = $(this).attr("t_menu1");
                //получаем меню из аттрибута2
                var t_menu_tempA1 = $(this).attr("t_menuA1");
                //col = col.replace('#', '');

                //собираем массив цыетов
                colors1[i] = col;
                //собираем массив менюшек2
                t_menu_arr1 [i] = t_menu_temp1;
                //собираем массив менюшек
                t_menu_arrA1 [i] = t_menu_tempA1;
                //t = document.getElementById('time_now');
                //t.innerHTML = colors1[2];

                i++;
            });


            for (var j = 1; j < i; j++) {
                (function (o, j) {
                    o[0].style.cursor = "pointer";

                    o[0].onmouseover = function () {
                        var color1 = "#ff0000";
                        o.animate({fill: color1}, 250);

                        //меню при наведении

                        var color1 = "#F00";
                        o.animate({fill: color1}, 250);

                        var point2 = o[0].getBBox();

                        $('#map1').next('.point2').remove();

                        $('#map1').after($('<div />').addClass('point2'));

                        //текст для меню
                        //t_menu = 'Номер зуба...<br /><img src="img/tooth_state/1.png" border="0" />Отсутствует<br /><img src="img/tooth_state/2.png" border="0" />Молочный<br /><img src="img/tooth_state/3.png" border="0" />Удалён<br /><img src="img/tooth_state/4.png" border="0" />Имплантант<br /><img src="img/tooth_state/5.png" border="0" />Форм. десны<br /><img src="img/tooth_state/6.png" border="0" />Коронка<br /><img src="img/tooth_state/7.png" border="0" />Культ. вкладка<br /><img src="img/tooth_state/8.png" border="0" />Бюгел. протез<br /><img src="img/tooth_state/9.png" border="0" />Мост<br /><img src="img/tooth_state/10.png" border="0" />Искусственный<br /><img src="img/tooth_state/11.png" border="0" />Чужая коронка<br /><img src="img/tooth_state/12.png" border="0" />Чужой мост<br /><img src="img/tooth_state/13.png" border="0" />Чужой бюгел.<br /><img src="img/tooth_state/14.png" border="0" />Полный съем.<br /><img src="img/tooth_state/15.png" border="0" />Частич. съем.<br /><img src="img/tooth_state/16.png" border="0" />Чужой пол.<br /><img src="img/tooth_state/17.png" border="0" />Чужой час.<br />';
                        t_menu1 = i;

                        $('.point2')

                        //.html('Номер зуба...<br /><img src="img/tooth_state/1.png" border="0" />Отсутствует<br /><img src="img/tooth_state/2.png" border="0" />Молочный<br /><img src="img/tooth_state/3.png" border="0" />Удалён<br /><img src="img/tooth_state/4.png" border="0" />Имплантант<br /><img src="img/tooth_state/5.png" border="0" />Форм. десны<br /><img src="img/tooth_state/6.png" border="0" />Коронка<br /><img src="img/tooth_state/7.png" border="0" />Культ. вкладка<br /><img src="img/tooth_state/8.png" border="0" />Бюгел. протез<br /><img src="img/tooth_state/9.png" border="0" />Мост<br /><img src="img/tooth_state/10.png" border="0" />Искусственный<br /><img src="img/tooth_state/11.png" border="0" />Чужая коронка<br /><img src="img/tooth_state/12.png" border="0" />Чужой мост<br /><img src="img/tooth_state/13.png" border="0" />Чужой бюгел.<br /><img src="img/tooth_state/14.png" border="0" />Полный съем.<br /><img src="img/tooth_state/15.png" border="0" />Частич. съем.<br /><img src="img/tooth_state/16.png" border="0" />Чужой пол.<br /><img src="img/tooth_state/17.png" border="0" />Чужой час.<br />')
                            .html(t_menu_arrA1 [j])

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
                        var color1 = colors1[j];
                        o.animate({fill: color1}, 250);
                    };


                })(area[j], j);
            }
        }
	//});
}); 


/*

jQuery(document).ready(function(){

	//var  map_class = $('.map');
	
	//$.each(map_class, function(){
		//var R = Raphael($(this).attr('id'), 600, 150);
		var R = Raphael('map2', 600, 150);
		


		var i = 1,
		console = 0;
		colors2 = {},
		t_menu_arr2 = {},
		t_menu_arrA2 = {},
		area = {},
		col_arr_temp = {};

		var oAreas = $(".mapArea2");

		$.each(oAreas, function(){
			if ($(this).attr("t_status") == 'yes'){
				console = 1;
			}else{
				console = 0;
			}
				area[i] = R.path($(this).attr("data-path2")).attr(JSON.parse("{"+$(this).attr("status-path")+","+$(this).attr("fill-color2")+"}"));
				
				//получаем цвет из аттрибута
				col_arr_temp = $(this).attr("fill-color2").split(':');
				var col = col_arr_temp[1].replace(new RegExp('"', 'g'), '');
				//получаем меню из аттрибута
				var t_menu_temp2 = $(this).attr("t_menu2");
				//получаем меню из аттрибута2
				var t_menu_tempA2 = $(this).attr("t_menuA2");
				//col = col.replace('#', '');
				
				//собираем массив цыетов
				colors2[i] = col;
				//собираем массив менюшек2
				t_menu_arr2 [i] = t_menu_temp2;
				//собираем массив менюшек
				t_menu_arrA2 [i] = t_menu_tempA2;
				//t = document.getElementById('time_now');
				//t.innerHTML = colors[2];
				
				i++;
		});
		

		for (var j=1; j<i; j++) {
			(function (o, j) {
				o[0].style.cursor = "pointer";
				
				o[0].onmouseover = function () {
					var color2 = "#ff0000";
					o.animate({fill:color2}, 250);
					
					//меню при наведении
					
					var color2 = "#F00";
					o.animate({fill: color2}, 250);
					
					var point2 = o[0].getBBox();
					
					$('#map1').next('.point2').remove();
					
					$('#map1').after($('<div />').addClass('point2'));
					
					//текст для меню
					//t_menu = 'Номер зуба...<br /><img src="img/tooth_state/1.png" border="0" />Отсутствует<br /><img src="img/tooth_state/2.png" border="0" />Молочный<br /><img src="img/tooth_state/3.png" border="0" />Удалён<br /><img src="img/tooth_state/4.png" border="0" />Имплантант<br /><img src="img/tooth_state/5.png" border="0" />Форм. десны<br /><img src="img/tooth_state/6.png" border="0" />Коронка<br /><img src="img/tooth_state/7.png" border="0" />Культ. вкладка<br /><img src="img/tooth_state/8.png" border="0" />Бюгел. протез<br /><img src="img/tooth_state/9.png" border="0" />Мост<br /><img src="img/tooth_state/10.png" border="0" />Искусственный<br /><img src="img/tooth_state/11.png" border="0" />Чужая коронка<br /><img src="img/tooth_state/12.png" border="0" />Чужой мост<br /><img src="img/tooth_state/13.png" border="0" />Чужой бюгел.<br /><img src="img/tooth_state/14.png" border="0" />Полный съем.<br /><img src="img/tooth_state/15.png" border="0" />Частич. съем.<br /><img src="img/tooth_state/16.png" border="0" />Чужой пол.<br /><img src="img/tooth_state/17.png" border="0" />Чужой час.<br />';
					t_menu2 = i;
					
					$('.point2')
					
					//.html('Номер зуба...<br /><img src="img/tooth_state/1.png" border="0" />Отсутствует<br /><img src="img/tooth_state/2.png" border="0" />Молочный<br /><img src="img/tooth_state/3.png" border="0" />Удалён<br /><img src="img/tooth_state/4.png" border="0" />Имплантант<br /><img src="img/tooth_state/5.png" border="0" />Форм. десны<br /><img src="img/tooth_state/6.png" border="0" />Коронка<br /><img src="img/tooth_state/7.png" border="0" />Культ. вкладка<br /><img src="img/tooth_state/8.png" border="0" />Бюгел. протез<br /><img src="img/tooth_state/9.png" border="0" />Мост<br /><img src="img/tooth_state/10.png" border="0" />Искусственный<br /><img src="img/tooth_state/11.png" border="0" />Чужая коронка<br /><img src="img/tooth_state/12.png" border="0" />Чужой мост<br /><img src="img/tooth_state/13.png" border="0" />Чужой бюгел.<br /><img src="img/tooth_state/14.png" border="0" />Полный съем.<br /><img src="img/tooth_state/15.png" border="0" />Частич. съем.<br /><img src="img/tooth_state/16.png" border="0" />Чужой пол.<br /><img src="img/tooth_state/17.png" border="0" />Чужой час.<br />')
					.html(t_menu_arrA2 [j])
					
					//.prepend($('<a />').attr('href', '#').addClass('close').text('Close'))
					.css({
						left: point2.width,
						top: point2.height
					})
					.stop( true , true ).fadeIn();
					
					//конец меню при наведении
					
				};
							
				o[0].onmouseout = function () {
					var color2 = colors2[j];
					o.animate({fill: color2}, 250);
					$('.point2').stop( true , true ).remove();
				};
				
			})(area[j], j);
		}
	//});
}); */