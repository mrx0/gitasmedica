$(function(){
	    
	//Живой поиск
	$('.who2').bind("change keyup input click", function() {

        // if(this.value.length > 0){
        //     $(".button_in_input").show();
        // }else {
        //     $(".button_in_input").hide();
        // }

		if(this.value.length > 2){
			$.ajax({
				url: "FastSearchNameW.php", //Путь к обработчику
				//statbox:"status",
				type:"POST",
				data:
				{
					'searchdata2':this.value
				},
				response: 'text',
				success: function(data){
					$(".search_result2").html(data).fadeIn(); //Выводим полученые данные в списке
				}
			})
	    }else{
			var elem1 = $("#search_result2");
			elem1.hide(); 
		}
	});
	    
	$(".search_result2").hover(function(){
		$(".who2").blur(); //Убираем фокус с input
	});
	    
    //При выборе результата поиска, прячем список и заносим выбранный результат в input
    $(".search_result2").on("click", "li", function(){
        //console.log(this.firstChild);

        //Раньше было так
        //s_user = $(this).text();
        //Теперь так, потому что добавили должность во вложенном тэге, поэтому стараемся брать только текст без вложенных тэгов, пример !!!
        s_user = this.firstChild.textContent;

		$(".who2").val(s_user);

        //$(".who").val(s_user).attr('disabled', 'disabled'); //деактивируем input, если нужно

        $(".search_result2").fadeOut();

    });

	//Если click за пределами результатов поиска - убираем эти результаты
	$(document).click(function(e){
		var elem = $("#search_result2");

		if(e.target!=elem[0]&&!elem.has(e.target).length){
			elem.hide(); 
		} 
	})
});