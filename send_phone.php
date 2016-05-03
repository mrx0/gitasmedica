<?php

//send_phone.php
//Форма отправки данных контактых для правого столбца

	echo '
		<li style="background-color: #FEFAE4; border: 1px solid #F07D00; padding: 0px 10px 10px; margin-top:20px;">
			<h2>Отправить заявку</h2>
			<div style="margin:15px 4px 12px;">Отправьте нам ваши данные, и мы обязательно с вами свяжемся</div>
			<form id="mailform" method="POST" onsubmit=\'
										ajax({
											url:"send.php",
											statbox:"send_rez",
											method:"POST",
											data:
											{
												name:document.getElementById("name").value,
												phone:document.getElementById("phone").value,
											},
											success:function(data){
												document.getElementById("send_rez").innerHTML=data;
												document.getElementById("mailform").reset();
											}
											})
											;return false;\'
			>
				<input type="text" id="name" name="name" required placeholder="Введите ваше имя"/>
				<input type="text" id="phone" name="phone" required placeholder="email или телефон"/>
				<input type="submit" value="Заказать звонок"/>
			</form>
			<div id="send_rez"></div>
		</li>';
		
?>