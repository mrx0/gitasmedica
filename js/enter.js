	
	
	document.addEventListener("DOMContentLoaded", function(event) { 
	
		var sbmtfrm = document.getElementById("sbmtfrm");
		sbmtfrm.onclick = function(){
			//alert(1245);
			
			var login = document.getElementById("login").value; 
			var password = document.getElementById("password").value;	
			//console.log(document.getElementById("office"));
			
			if (document.getElementById("office") == null){
				office = -1;
			}else{
				var office = document.getElementById("office").value;
			}
			
			var errror = document.getElementById("errror");
			errror.innerHTML = '';
			var ch_office = document.getElementById("ch_office");
			ch_office.innerHTML = '';
			
			$.ajax({
				url:"auth.php",
				global: false, 
				type: "POST", 
				dataType: "JSON",
				data:
				{
					login: login,
					password: password,
					office: office,
					
				},
				cache: false,
				beforeSend: function() {
				},
				// действие, при ответе с сервера
				success: function(res){
					//alert(res);
					//alert(res.result);
					//$('.center_block').remove();
					//$('#overlay').hide();
					
					if(res.result == "success"){
						errror.innerHTML = '<span class="query_ok">'+res.data+'</span>';
						setTimeout(function () {
						   window.location.href = "index.php";
						}, 1000);
						//$('#data').hide();
						/*$('#invoices').html('<ul id="invoices" style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
												'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Добавлен новый наряд</li>'+
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
											'</ul>'+
											'<ul id="invoices" style="margin-left: 6px; margin-bottom: 4px; display: inline-block; vertical-align: middle;">'+
												'<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
													'<a href="add_order.php?client_id='+client+'" class="b">Добавить оплату/ордер</a>'+
												'</li>'+
											'</ul>');*/
					}else{
						if(res.result == "office"){ 
							//alert(6544);
							ch_office.innerHTML = ''+res.data+'';
						}else{
							errror.innerHTML = '<span class="query_neok">'+res.data+'</span>';
						}
					}

				}
			});
		}
	});
		