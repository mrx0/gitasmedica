<?php
	
//testreg2.php
//	
	
    require_once 'header.php';

	if (isset($_POST['filial']) && ($_POST['filial'] != 0) && (($_SESSION['permissions'] == 4) || ($_SESSION['permissions'] == 7))){
		$_SESSION['filial'] = $_POST['filial'];
		echo '<h1>Вы успешно вошли в систему!</h1><a href="index.php">Главная страница</a>
				<script type="text/javascript">
					setTimeout(function () {
						window.location.href = "index.php";
					}, 1000);
				</script>';
	}else{
		//если пароли не сошлись
		exit ('<h1>Что-то пошло не так</h1><a href="enter.php">Вернуться и попытаться ещё</a>');
	}
	
	require_once 'footer.php';
	
?>