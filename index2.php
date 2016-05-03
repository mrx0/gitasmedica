<?php

//index.php
//Главная
//!Тут есть скрипт дропдаун
	require_once 'header.php';

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';

			echo '
					<div class="wrapper-demo">
						<div id="dd2" class="wrapper-dropdown-2 b" tabindex="2">Приоритет
							<ul class="dropdown">
								<li><a href="index.php?priority=3">Высокий</a></li>
								<li><a href="index.php?priority=2">Средний</a></li>
								<li><a href="index.php?priority=1">Низкий</a></li>
							</ul>
						</div></div>
	
			<script type="text/javascript">

				function DropDown(el) {
					this.dd = el;
					this.initEvents();
				}
				DropDown.prototype = {
					initEvents : function() {
						var obj = this;

						obj.dd.on(\'click\', function(event){
							$(this).toggleClass(\'active\');
							event.stopPropagation();
						});	
					}
				}

				$(function() {

					var dd = new DropDown( $(\'#dd\') );

					$(document).click(function() {
						// all dropdowns
						$(\'.wrapper-dropdown-2\').removeClass(\'active\');
					});

				});

				function DropDown(el) {
					this.dd2 = el;
					this.initEvents();
				}
				DropDown.prototype = {
					initEvents : function() {
						var obj = this;

						obj.dd2.on(\'click\', function(event){
							$(this).toggleClass(\'active\');
							event.stopPropagation();
						});	
					}
				}

				$(function() {

					var dd2 = new DropDown( $(\'#dd2\') );

					$(document).click(function() {
						// all dropdowns
						$(\'.wrapper-dropdown-2\').removeClass(\'active\');
					});

				});

			</script>
			
			';
	}
		
	require_once 'footer.php';

?>