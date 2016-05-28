$(function(){
	    
	//Живой поиск
	$('.who_fc').bind("change keyup input click", function() {
		if(this.value.length > 2){
			$.ajax({
				url: "FastSearchNameFC.php", //Путь к обработчику
				//statbox:"status",
				type:"POST",
				data:
				{
					'searchdata':this.value
				},
				response: 'text',
				success: function(data){
					//$(".search_result_fc").html(data).fadeIn(); //Выводим полученые данные в списке
					document.getElementById("search_result_fc2").innerHTML = data; //Выводим полученые данные в списке
					//document.getElementById("search_result_fc2").innerHTML = data;
				}
			})
	    }else{
			document.getElementById("search_result_fc2").innerHTML = '';
			//var elemFC2 = $("#search_result_fc2"); 
			//elemFC2.hide(); 
		}
	})
	    
	$(".search_result_fc").hover(function(){
		$(".who_fc").blur(); //Убираем фокус с input
	})
})